<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function chatAI(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer'  => env('APP_URL'),
            'X-Title'       => env('APP_NAME'),
            'Content-Type'  => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'deepseek/deepseek-chat', 
            'messages' => [
                ['role' => 'system', 'content' => 'Bạn là trợ lý hỗ trợ khách hàng cho website Barber House.'],
                ['role' => 'user', 'content' => $request->message],
            ],
        ]);

        $data = $response->json();
        $reply = $data['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi chưa có câu trả lời.';

        return response()->json(['reply' => $reply]);
    }
}
