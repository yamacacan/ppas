<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\Activity;
use App\Models\BrowserData;
use App\Models\SystemHardware;
use App\Models\InstalledApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IngestController extends Controller
{
    public function ingest(Request $request)
    {
        $token = $request->header('X-Api-Token');
        if (!$token) {
            return response()->json(['error' => 'Missing API Token'], 401);
        }

        $client = ApiClient::where('token', $token)->where('is_active', true)->first();
        if (!$client) {
            return response()->json(['error' => 'Invalid or inactive API Token'], 401);
        }

        $encryptedData = $request->input('payload');
        if (!$encryptedData) {
            return response()->json(['error' => 'Missing encrypted payload'], 400);
        }

        try {
            $decryptedJson = $this->decrypt($encryptedData, $client->aes_key);
            $data = json_decode($decryptedJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON after decryption');
            }
        } catch (\Exception $e) {
            Log::error("API Decryption failed for client {$client->name}: " . $e->getMessage());
            return response()->json(['error' => 'Decryption/JSON error'], 400);
        }

        $type = $data['type'] ?? null;
        $items = $data['data'] ?? [];

        if (!$type || empty($items)) {
            return response()->json(['error' => 'Invalid data structure'], 400);
        }

        $processedCount = 0;
        
        switch ($type) {
            case 'activities':
                foreach ($items as $item) {
                    Activity::create($item);
                    $processedCount++;
                }
                break;
            case 'hardware':
                foreach ($items as $item) {
                    SystemHardware::create($item);
                    $processedCount++;
                }
                break;
            case 'apps':
                foreach ($items as $item) {
                    InstalledApp::create($item);
                    $processedCount++;
                }
                break;
            case 'browser':
                foreach ($items as $item) {
                    BrowserData::create($item);
                    $processedCount++;
                }
                break;
            default:
                return response()->json(['error' => 'Unknown data type: ' . $type], 400);
        }

        $client->update(['last_used_at' => now()]);

        return response()->json([
            'success' => true,
            'processed' => $processedCount,
            'client' => $client->name
        ]);
    }

    protected function decrypt($payload, $key)
    {
        $payload = base64_decode($payload);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($payload, 0, $iv_length);
        $encrypted = substr($payload, $iv_length);
        $key = str_pad($key, 32, "\0");
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }
}