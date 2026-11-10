<?php

// Website assistant. With no API key it answers from the site's own data (free, zero tokens).
// To use a real model, set these in .env. Any OpenAI-compatible free tier works, e.g.:
//   Groq:       AI_BASE_URL=https://api.groq.com/openai/v1        AI_MODEL=llama-3.1-8b-instant
//   Gemini:     AI_BASE_URL=https://generativelanguage.googleapis.com/v1beta/openai   AI_MODEL=gemini-2.0-flash
//   OpenRouter: AI_BASE_URL=https://openrouter.ai/api/v1          AI_MODEL=meta-llama/llama-3.1-8b-instruct:free
return [
    'base_url' => env('AI_BASE_URL', 'https://api.groq.com/openai/v1'),
    'api_key' => env('AI_API_KEY'),
    'model' => env('AI_MODEL', 'llama-3.1-8b-instant'),
    'max_tokens' => (int) env('AI_MAX_TOKENS', 300),
    'timeout' => (int) env('AI_TIMEOUT', 15),
];
