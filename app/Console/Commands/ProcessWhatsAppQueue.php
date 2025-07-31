<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppGatewayService;
use App\Models\WaOutbox;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:process-queue 
                            {--limit=10 : Maximum number of messages to process}
                            {--status=pending : Status of messages to process (pending, failed)}
                            {--retry-failed : Retry failed messages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process WhatsApp message queue from wa_outbox table';

    /**
     * WhatsApp Gateway Service instance
     *
     * @var WhatsAppGatewayService
     */
    protected $whatsappService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WhatsAppGatewayService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $status = $this->option('status');
        $retryFailed = $this->option('retry-failed');

        $this->info('Starting WhatsApp queue processing...');
        $this->info("Processing {$limit} messages with status: {$status}");

        try {
            // Get messages from queue
            $query = WaOutbox::query();
            
            if ($retryFailed) {
                $query->where('status', 'failed');
                $this->info('Retrying failed messages...');
            } else {
                $query->where('status', $status);
            }
            
            $messages = $query->orderBy('tanggal_jam', 'asc')
                            ->limit($limit)
                            ->get();

            if ($messages->isEmpty()) {
                $this->info('No messages found in queue.');
                return 0;
            }

            $this->info("Found {$messages->count()} messages to process.");
            
            $processed = 0;
            $failed = 0;
            $success = 0;

            // Process each message
            foreach ($messages as $message) {
                $this->line("Processing message ID: {$message->nomor} to {$message->nowa}");
                
                try {
                    // Update status to processing
                    $message->update(['status' => 'processing']);
                    
                    $result = null;
                    
                    // Send message based on type
                    if ($message->type === 'text') {
                        $result = $this->whatsappService->sendDirectMessage(
                            $message->nowa,
                            $message->pesan
                        );
                    } elseif ($message->type === 'document' && $message->file) {
                        $result = $this->whatsappService->sendDirectDocument(
                            $message->nowa,
                            $message->file,
                            $message->pesan
                        );
                    } else {
                        throw new \Exception('Invalid message type or missing file for document type');
                    }
                    
                    // Update message status based on result
                    if ($result && isset($result['success']) && $result['success']) {
                        $message->update([
                            'status' => 'sent',
                            'success' => 1,
                            'response' => json_encode($result)
                        ]);
                        $success++;
                        $this->info("✓ Message sent successfully to {$message->nowa}");
                    } else {
                        $message->update([
                            'status' => 'failed',
                            'success' => 0,
                            'response' => json_encode($result ?? ['error' => 'Unknown error'])
                        ]);
                        $failed++;
                        $this->error("✗ Failed to send message to {$message->nowa}");
                    }
                    
                } catch (\Exception $e) {
                    $message->update([
                        'status' => 'failed',
                        'success' => 0,
                        'response' => json_encode(['error' => $e->getMessage()])
                    ]);
                    $failed++;
                    $this->error("✗ Error processing message ID {$message->nomor}: {$e->getMessage()}");
                    Log::error('WhatsApp Queue Processing Error', [
                        'message_id' => $message->nomor,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                $processed++;
                
                // Add small delay between messages to avoid rate limiting
                usleep(500000); // 0.5 second delay
            }
            
            // Display summary
            $this->info('\n--- Processing Summary ---');
            $this->info("Total processed: {$processed}");
            $this->info("Successful: {$success}");
            $this->info("Failed: {$failed}");
            
            if ($success > 0) {
                $this->info('✓ Queue processing completed successfully!');
            }
            
            if ($failed > 0) {
                $this->warn("⚠ {$failed} messages failed to send. Check logs for details.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Fatal error during queue processing: {$e->getMessage()}");
            Log::error('WhatsApp Queue Processing Fatal Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}