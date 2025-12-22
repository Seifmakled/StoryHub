# Add a 3rd‑party AI Chatbot (Embed)

This project supports a site‑wide chatbot widget by pasting a provider embed snippet.

## 1) Choose a provider
Any provider that gives you an **HTML/JS embed snippet** will work.
Examples: Chatbase, Botpress Cloud Webchat, Tidio (AI), Crisp (AI add‑on), Intercom Fin, etc.

## 2) Paste the embed code
Open:
- `app/config/chatbot_config.php`

Set:
- `'enabled' => true`
- `'embed_html' => '...PASTE YOUR SNIPPET HERE...'`

Important:
- Paste **exactly** what the provider gives you.
- The snippet is rendered as raw HTML/JS.

### Tip for multi‑line snippets in PHP
Use a nowdoc to paste multi‑line scripts safely:

```php
return [
  'enabled' => true,
  'embed_html' => <<<'HTML'
<!-- Paste provider snippet here -->
<script>
  // provider script
</script>
HTML
];
```

## 3) Verify
Reload any page. The chat widget should appear.

## Disable
Set `'enabled' => false`.
