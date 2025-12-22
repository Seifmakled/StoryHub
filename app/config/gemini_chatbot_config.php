<?php

// OpenRouter chatbot configuration
// 1) Get an API key from https://openrouter.ai/
// 2) Set it below (local dev) OR set an environment variable OPENROUTER_API_KEY.

$envKey = getenv('OPENROUTER_API_KEY');
$envKey = is_string($envKey) ? trim($envKey) : '';

// Local dev fallback (gitignored): app/config/openrouter_key.local.php
$localKey = '';
$localKeyPath = __DIR__ . '/openrouter_key.local.php';
if (is_file($localKeyPath)) {
    try {
        $loaded = require $localKeyPath;
        $localKey = is_string($loaded) ? trim($loaded) : '';
    } catch (Throwable $e) {
        $localKey = '';
    }
}

return [
    'enabled' => true,

    // Prefer environment variable so you don't commit secrets.
    // If not set, it will use app/config/openrouter_key.local.php (gitignored).
    'api_key' => $envKey !== '' ? $envKey : $localKey,

    // OpenRouter base URL
    'api_base' => 'https://openrouter.ai/api/v1',

    // OpenRouter model name.
    // Pick any model available to your OpenRouter account.
    // A reasonable default that often works well:
    'model' => 'google/gemini-2.0-flash-001',

    // A small “system” instruction for the assistant.
    'system_prompt' => "You are StoryHub's helpful assistant. Help users use the site (writing, publishing, profiles, likes/saves/comments). If you don't know something, ask a clarifying question. Keep answers concise.",

    // Basic rate limiting per session (seconds between requests)
    'min_seconds_between_messages' => 2,
];
