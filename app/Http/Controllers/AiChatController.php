<?php

namespace App\Http\Controllers;

use App\Services\SiteAssistant;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function send(Request $request, SiteAssistant $assistant)
    {
        $data = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:12',
            'history.*.role' => 'in:user,assistant',
            'history.*.content' => 'string|max:800',
        ]);

        return response()->json(['reply' => $assistant->reply($data['message'], $data['history'] ?? [])]);
    }
}
