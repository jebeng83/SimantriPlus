<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use LZCompressor\LZString;

trait BpjsTraits
{
    public function requestGetBpjs($suburl)
    {

        try {

            $xTimestamp = $this->craeteTimestamp();
            $res = Http::withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'user_key' => env('BPJS_USER_KEY'),
            ])->get(env('BPJS_ICARE_BASE_URL') . $suburl);
            dd($res);
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {

            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['data'] = $e->getMessage();
            return response()->json($statusError, 400);
        }
    }
    public function requestPostBpjs($suburl, $request)
    {
        try {
            $xTimestamp = $this->craeteTimestamp();
            $res = Http::timeout(60)->accept('application/json')->withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'user_key' => env('BPJS_USER_KEY'),
            ])->post(env('BPJS_ICARE_BASE_URL') . $suburl, $request);
            // dd($res->json());
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {
            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['message'] = $e->getMessage();
            return response()->json($statusError, 400);
        }
    }
    public function requestPutBpjs($suburl, $request)
    {

        try {
            $data['request'] = $request->all();
            $xTimestamp = $this->craeteTimestamp();
            $res = Http::accept('application/json')->withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'user_key' => env('BPJS_USER_KEY'),
            ])->withBody(json_encode($data), 'json')->put(env('BPJS_BASE_URL') . $suburl);
            //return $res;
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {

            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['data'] = $e;
            return response()->json($statusError, 400);
        }
    }
    public function requestDeleteBpjs($suburl, $request)
    {

        try {

            $data['request'] = $request->all();
            $xTimestamp = $this->craeteTimestamp();
            $res = Http::accept('application/json')->withHeaders([
                'X-cons-id' => env('BPJS_CONS_ID'),
                'X-timestamp' => $xTimestamp,
                'X-signature' => $this->createSign($xTimestamp, env('BPJS_CONS_ID')),
                'user_key' => env('BPJS_USER_KEY'),
            ])->withBody(json_encode($data), 'json')->delete(env('BPJS_BASE_URL') . $suburl, 'json');
            return $this->responseDataBpjs($res->json(), $xTimestamp);
        } catch (\Exception $e) {
            $statusError['flag'] = 'RSB Middleware Webservice';
            $statusError['result'] = 'Communication Errors With BPJS Kesehatan Webservice';
            $statusError['data'] = $e;
            return response()->json($statusError, 400);
        }
    }
    private function responseDataBpjs($res, $xTimestamp)
    {
        $statusResponse['flag'] = 'BPJS Kesehatan Webservice';
        $statusResponse['code'] = $res['metaData']['code'];
        $statusResponse['message'] = $res['metaData']['message'];
        $statusResponse['data'] = ($res['response'] == null) ? $res['response']  : $this->decodeResponse($res['response'], $this->createKeyForDecode($xTimestamp));
        return response()->json($statusResponse, 200);
    }
    private function craeteTimestamp()
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        return $tStamp;
    }
    private function createSign($tStamp, $dataPar)
    {
        $data = $dataPar;
        $secretKey = env('BPJS_CONS_PWD');

        $signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);
        // base64 encode�
        $encodedSignature = base64_encode($signature);
        return $encodedSignature;
    }
    private function createKeyForDecode($tStamp)
    {
        $consid = env('BPJS_CONS_ID');
        $conspwd = env('BPJS_CONS_PWD');
        return $consid . $conspwd . $tStamp;
    }
    private function decodeResponse($value, $key)
    {

        $data = $this->stringDecrypt($key, $value);
        $data = $this->decompress($data);
        return json_decode($data, true);
    }
    private function stringDecrypt($key, $string)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('sha256', $key));
        $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return $output;
    }
    private function decompress($string)
    {
        return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
    }
}
