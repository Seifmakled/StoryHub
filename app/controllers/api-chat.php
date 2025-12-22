<?php

// Gemini Chat API
// Endpoint: index.php?url=api-chat

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$cfgPath = __DIR__ . '/../config/gemini_chatbot_config.php';
$cfg = is_file($cfgPath) ? require $cfgPath : [];

if (!is_array($cfg) || empty($cfg['enabled'])) {
    http_response_code(503);
    echo json_encode(['error' => 'Chatbot disabled']);
    exit;
}

$apiKey = trim((string)($cfg['api_key'] ?? ''));
if ($apiKey === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Missing OpenRouter API key']);
    exit;
}

if (!function_exists('curl_init')) {
    http_response_code(500);
    echo json_encode(['error' => 'PHP cURL extension is not enabled (curl_init missing). Enable cURL in your PHP/php.ini and restart Apache.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$body = json_decode($raw, true);
if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$message = trim((string)($body['message'] ?? ''));
$history = $body['history'] ?? [];
if ($message === '' || mb_strlen($message) > 2000) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required (max 2000 chars)']);
    exit;
}

// Very small rate limit per session
$minSeconds = (int)($cfg['min_seconds_between_messages'] ?? 2);
$now = time();
if ($minSeconds > 0 && isset($_SESSION['chat_last_ts']) && ($now - (int)$_SESSION['chat_last_ts']) < $minSeconds) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests. Please wait a moment.']);
    exit;
}
$_SESSION['chat_last_ts'] = $now;

$contents = [];
if (is_array($history)) {
    $history = array_slice($history, -10);
    foreach ($history as $h) {
        if (!is_array($h)) continue;
        $role = (($h['role'] ?? '') === 'model' || ($h['role'] ?? '') === 'assistant') ? 'assistant' : 'user';
        $text = trim((string)($h['text'] ?? ''));
        if ($text === '') continue;
        $contents[] = ['role' => $role, 'content' => $text];
    }
}
$contents[] = ['role' => 'user', 'content' => $message];

$model = (string)($cfg['model'] ?? 'google/gemini-2.0-flash-001');
$systemPrompt = (string)($cfg['system_prompt'] ?? '');

$apiBase = rtrim((string)($cfg['api_base'] ?? 'https://openrouter.ai/api/v1'), '/');
$url = $apiBase . '/chat/completions';

$messages = [];
if ($systemPrompt !== '') {
    $messages[] = ['role' => 'system', 'content' => $systemPrompt];
}
foreach ($contents as $c) {
    $messages[] = $c;
}

$payload = [
    'model' => $model,
    'messages' => $messages,
    'temperature' => 0.7,
    'max_tokens' => 400,
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
        'HTTP-Referer: ' . ((isset($_SERVER['HTTP_HOST']) ? ('http://' . $_SERVER['HTTP_HOST']) : 'http://localhost')),
        'X-Title: StoryHub',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Chat request failed', 'details' => $curlErr]);
    exit;
}

$data = json_decode($response, true);
if ($httpCode < 200 || $httpCode >= 300) {
    $msg = 'OpenRouter API error';
    if (is_array($data) && isset($data['error']['message'])) $msg = (string)$data['error']['message'];
    http_response_code(502);
    echo json_encode(['error' => $msg]);
    exit;
}

$text = '';
if (is_array($data) && isset($data['choices'][0]['message']['content'])) {
    $text = trim((string)$data['choices'][0]['message']['content']);
}
if ($text === '') {
    $text = 'Sorry — I could not generate a response.';
}

echo json_encode(['reply' => $text]);
