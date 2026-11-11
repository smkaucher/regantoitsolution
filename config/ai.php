<?php

// Website assistant. With no API key it answers from the site's own data (free, zero tokens).
// To use a real model, set these in .env. Any OpenAI-compatible free tier works, e.g.:
//   Groq:       AI_BASE_URL=https://api.groq.com/openai/v1        AI_MODEL=llama-3.1-8b-instant
//   Gemini:     AI_BASE_URL=https://generativelanguage.googleapis.com/v1beta/openai   AI_MODEL=gemini-2.0-flash
//   Cloudflare Workers AI: set CLOUDFLARE_ACCOUNT_ID and AI_API_KEY (an API token with "Workers AI" permission);
//               base URL and the small free-tier model below are filled in automatically.
//   OpenRouter: AI_BASE_URL=https://openrouter.ai/api/v1          AI_MODEL=meta-llama/llama-3.1-8b-instruct:free
$cf = env('CLOUDFLARE_ACCOUNT_ID');

return [
    'base_url' => env('AI_BASE_URL', $cf ? "https://api.cloudflare.com/client/v4/accounts/{$cf}/ai/v1" : 'https://api.groq.com/openai/v1'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', $cf ? '@cf/meta/llama-3.3-70b-instruct-fp8-fast' : 'llama-3.1-8b-instant'),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 350),
    'timeout' => (int) env('AI_TIMEOUT', 25),
];
