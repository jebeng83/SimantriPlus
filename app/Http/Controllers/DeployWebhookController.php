<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = $this->resolveDeploySecret();
        if ($secret === '') {
            Log::warning('Deploy webhook ditolak: DEPLOY_WEBHOOK_SECRET belum di-set.');

            return response()->json(['message' => 'Webhook belum dikonfigurasi'], 500);
        }

        if (! $this->hasValidSignature($request, $secret)) {
            Log::warning('Deploy webhook ditolak: signature tidak valid.');

            return response()->json(['message' => 'Signature tidak valid'], 403);
        }

        $event = (string) $request->header('X-GitHub-Event', '');

        if ($event === 'ping') {
            return response()->json(['message' => 'Webhook aktif'], 200);
        }

        if ($event !== 'push') {
            return response()->json(['message' => 'Event diabaikan'], 202);
        }

        $branch = (string) config('app.deploy_webhook_branch', 'master');
        $expectedRef = 'refs/heads/' . $branch;
        $actualRef = (string) $request->input('ref', '');

        if ($actualRef !== $expectedRef) {
            return response()->json(['message' => 'Branch diabaikan'], 202);
        }

        $scriptPath = $this->resolvePath((string) config('app.deploy_script_path', 'deploy/deploy.sh'));
        $logPath = $this->resolvePath((string) config('app.deploy_log_path', storage_path('logs/deploy.log')));

        if (! is_file($scriptPath)) {
            Log::error('Deploy webhook gagal: script deploy tidak ditemukan.', ['script_path' => $scriptPath]);

            return response()->json(['message' => 'Script deploy tidak ditemukan'], 500);
        }

        try {
            $this->runInBackground($scriptPath, $logPath);
        } catch (\Throwable $e) {
            $queuePath = $this->resolvePath((string) config('app.deploy_queue_path', storage_path('app/deploy-webhook.queue')));
            $this->queueDeployRequest($queuePath, [
                'repository' => (string) $request->input('repository.full_name', ''),
                'ref' => $actualRef,
                'pusher' => (string) $request->input('pusher.name', ''),
                'queued_at' => date('c'),
                'reason' => $e->getMessage(),
            ]);

            Log::warning('Deploy webhook fallback ke antrean file.', [
                'error' => $e->getMessage(),
                'queue_path' => $queuePath,
            ]);

            return response()->json([
                'message' => 'Deploy diantrekan (fallback mode). Jalankan consumer deploy di server.',
            ], 202);
        }

        Log::info('Deploy webhook diterima.', [
            'repository' => (string) $request->input('repository.full_name', ''),
            'ref' => $actualRef,
            'pusher' => (string) $request->input('pusher.name', ''),
        ]);

        return response()->json(['message' => 'Deploy dijalankan'], 200);
    }

    protected function hasValidSignature(Request $request, string $secret): bool
    {
        $signature = (string) $request->header('X-Hub-Signature-256', '');
        if (! str_starts_with($signature, 'sha256=')) {
            return false;
        }

        $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expectedSignature, $signature);
    }

    protected function resolvePath(string $path): string
    {
        if ($path === '') {
            return '';
        }

        return str_starts_with($path, '/') ? $path : base_path($path);
    }

    protected function runInBackground(string $scriptPath, string $logPath): void
    {
        $logDir = dirname($logPath);
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $command = sprintf(
            'nohup bash %s >> %s 2>&1 &',
            escapeshellarg($scriptPath),
            escapeshellarg($logPath)
        );

        if ($this->isFunctionAvailable('popen') && $this->isFunctionAvailable('pclose')) {
            $handle = @popen($command, 'r');
            if (is_resource($handle)) {
                @pclose($handle);
                return;
            }
        }

        if ($this->isFunctionAvailable('proc_open') && $this->isFunctionAvailable('proc_close')) {
            $process = @proc_open(
                ['/bin/sh', '-c', $command],
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes
            );

            if (is_resource($process)) {
                foreach ($pipes as $pipe) {
                    if (is_resource($pipe)) {
                        fclose($pipe);
                    }
                }
                @proc_close($process);
                return;
            }
        }

        if ($this->isFunctionAvailable('exec')) {
            @exec($command);
            return;
        }

        throw new \RuntimeException('Fungsi eksekusi shell tidak tersedia di server (exec/proc_open/popen).');
    }

    protected function isFunctionAvailable(string $function): bool
    {
        if (! function_exists($function)) {
            return false;
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        return ! in_array($function, $disabled, true);
    }

    protected function queueDeployRequest(string $queuePath, array $payload): void
    {
        $queueDir = dirname($queuePath);
        if (! is_dir($queueDir)) {
            @mkdir($queueDir, 0755, true);
        }

        @file_put_contents($queuePath, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    protected function resolveDeploySecret(): string
    {
        $configSecret = trim((string) config('app.deploy_webhook_secret', ''));
        if ($configSecret !== '') {
            return $configSecret;
        }

        return $this->readDotEnvValue('DEPLOY_WEBHOOK_SECRET');
    }

    protected function readDotEnvValue(string $key): string
    {
        $envPath = base_path('.env');
        if (! is_file($envPath) || ! is_readable($envPath)) {
            return '';
        }

        $lines = @file($envPath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return '';
        }

        $prefix = $key . '=';

        foreach ($lines as $line) {
            $trimmed = trim((string) $line);

            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            if (! str_starts_with($trimmed, $prefix)) {
                continue;
            }

            $value = trim(substr($trimmed, strlen($prefix)));

            if ($value === '') {
                return '';
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            return trim($value);
        }

        return '';
    }
}
