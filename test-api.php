<?php

// Bu script API ingest işlemini simüle eder.
// Kullanım: php test-api.php --token=TOKEN --key=KEY --type=activities|hardware|apps|browser

$options = getopt("", ["token:", "key:", "type:"]);
$token = $options['token'] ?? null;
$key = $options['key'] ?? null;
$type = $options['type'] ?? 'activities';

if (!$token || !$key) {
    die("Hata: --token ve --key parametreleri zorunludur.\n");
}

$baseUrl = "http://localhost:8000/api/v1/ingest";

// Örnek veri üretimi
$data = [];
if ($type === 'activities') {
    $data = [
        'type' => 'activities',
        'data' => [
            [
                'activity_type' => 'ProcessChanged',
                'process_name' => 'chrome.exe',
                'title' => 'Google Search - Testing API',
                'start_time_utc' => date('Y-m-d H:i:s'),
                'username' => 'testuser',
                'created_at_utc' => date('Y-m-d H:i:s'),
                'motherboard_uuid' => 'TEST-UUID-1234'
            ]
        ]
    ];
} elseif ($type === 'hardware') {
    $data = [
        'type' => 'hardware',
        'data' => [
            [
                'hostname' => 'TEST-PC',
                'username' => 'testuser',
                'domain' => 'WORKGROUP',
                'user_sid' => 'S-1-5-21-XXX',
                'os_version' => 'Windows 11',
                'collected_at' => date('Y-m-d H:i:s'),
                'motherboard_uuid' => 'TEST-UUID-1234'
            ]
        ]
    ];
}

$jsonData = json_encode($data);

// Şifreleme (AES-256-CBC)
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
$paddedKey = str_pad($key, 32, "\0");
$encrypted = openssl_encrypt($jsonData, 'aes-256-cbc', $paddedKey, OPENSSL_RAW_DATA, $iv);
$payload = base64_encode($iv . $encrypted);

echo "Simüle ediliyor: $type\n";
echo "Token: $token\n";
echo "Payload: " . substr($payload, 0, 50) . "...\n";

// HTTP POST
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['payload' => $payload]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Api-Token: ' . $token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Hata: ' . curl_error($ch) . "\n";
} else {
    echo "HTTP Status: $httpCode\n";
    echo "Response: $response\n";
}

curl_close($ch);
