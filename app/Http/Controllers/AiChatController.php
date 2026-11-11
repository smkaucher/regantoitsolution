<?php

namespace App\Http\Controllers;

use App\Services\SiteAssistant;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function send(Request $request, SiteAssistant $assistant)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:30',
            'history.*.role' => 'in:user,assistant',
            'history.*.content' => 'nullable|string|max:8000',
        ]);

        return response()->json($assistant->replyData($data['message'], $data['history'] ?? []));
    }
}
