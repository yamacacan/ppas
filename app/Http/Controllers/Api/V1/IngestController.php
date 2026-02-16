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
        $errors = [];
        
        try {
            switch ($type) {
                case 'activities':
                    $uniqueUsers = [];
                    foreach ($items as $index => $item) {
                        try {
                            if (empty($item['created_at_utc'])) {
                                $item['created_at_utc'] = now();
                            }
                            $item['received_at'] = now();
                            Activity::create($item);
                            $processedCount++;

                            // Collect unique users to ensure they exist in computer_users table
                            if (!empty($item['username']) && !empty($item['motherboard_uuid'])) {
                                $userKey = $item['username'] . '|' . $item['motherboard_uuid'];
                                if (!isset($uniqueUsers[$userKey])) {
                                    $uniqueUsers[$userKey] = [
                                        'username' => $item['username'],
                                        'motherboard_uuid' => $item['motherboard_uuid']
                                    ];
                                }
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Item {$index}: " . $e->getMessage();
                            Log::error("Failed to create activity at index {$index}", [
                                'error' => $e->getMessage(),
                                'item' => $item
                            ]);
                        }
                    }

                    // Ensure computer users exist
                    foreach ($uniqueUsers as $u) {
                        \App\Models\ComputerUser::firstOrCreate(
                            ['username' => $u['username'], 'motherboard_uuid' => $u['motherboard_uuid']],
                            ['name' => null]
                        );
                    }
                    break;

                case 'hardware':
                    foreach ($items as $index => $item) {
                        try {
                            SystemHardware::create($item);
                            $processedCount++;

                            // Ensure computer user exists and has hostname
                            if (!empty($item['username']) && !empty($item['motherboard_uuid'])) {
                                \App\Models\ComputerUser::updateOrCreate(
                                    ['username' => $item['username'], 'motherboard_uuid' => $item['motherboard_uuid']],
                                    ['hostname' => $item['hostname'] ?? null]
                                );
                            }
                        } catch (\Exception $e) {
                            $errors[] = "Item {$index}: " . $e->getMessage();
                            Log::error("Failed to create hardware at index {$index}", [
                                'error' => $e->getMessage(),
                                'item' => $item
                            ]);
                        }
                    }
                    break;
                case 'apps':
                    foreach ($items as $index => $item) {
                        try {
                            InstalledApp::create($item);
                            $processedCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Item {$index}: " . $e->getMessage();
                            Log::error("Failed to create app at index {$index}", [
                                'error' => $e->getMessage(),
                                'item' => $item
                            ]);
                        }
                    }
                    break;
                case 'browser':
                    foreach ($items as $index => $item) {
                        try {
                            BrowserData::create($item);
                            $processedCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Item {$index}: " . $e->getMessage();
                            Log::error("Failed to create browser data at index {$index}", [
                                'error' => $e->getMessage(),
                                'item' => $item
                            ]);
                        }
                    }
                    break;
                default:
                    return response()->json(['error' => 'Unknown data type: ' . $type], 400);
            }
        } catch (\Exception $e) {
            Log::error("Ingest processing failed", [
                'error' => $e->getMessage(),
                'type' => $type,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Processing failed',
                'message' => $e->getMessage(),
                'processed' => $processedCount
            ], 500);
        }

        $client->update(['last_used_at' => now()]);

        $response = [
            'success' => true,
            'processed' => $processedCount,
            'total' => count($items),
            'client' => $client->name
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['partial_success'] = true;
        }

        return response()->json($response, !empty($errors) && $processedCount === 0 ? 400 : 200);
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