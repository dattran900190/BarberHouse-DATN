<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;

class ChatController extends Controller
{
    public function chatAI(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // Kiểm tra giới hạn cho khách vãng lai
        if (!Auth::check()) {
            $guestChatCount = ChatbotLog::whereNull('user_id')
                ->whereDate('created_at', today())
                ->count();

            // Nếu là request kiểm tra giới hạn
            if ($request->message === 'check_limit') {
                $remainingQuestions = max(0, 3 - $guestChatCount);
                return response()->json([
                    'remaining_questions' => $remainingQuestions,
                    'total_questions' => 3
                ]);
            }

            if ($guestChatCount >= 3) {
                return response()->json([
                    'reply' => 'Bạn đã sử dụng hết 3 câu hỏi miễn phí trong ngày. Vui lòng đăng ký tài khoản để tiếp tục sử dụng dịch vụ hỗ trợ không giới hạn!',
                    'limit_reached' => true
                ]);
            }
        }

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

        // Lưu lịch sử chat vào database (không lưu khi kiểm tra giới hạn)
        if ($request->message !== 'check_limit') {
            try {
                ChatbotLog::create([
                    'user_id' => Auth::check() ? Auth::id() : null, // Lưu ID user nếu đã đăng nhập
                    'message' => $request->message,
                    'reply' => $reply,
                ]);
            } catch (\Exception $e) {
                // Log lỗi nếu không thể lưu vào database
                Log::error('Không thể lưu chat log: ' . $e->getMessage());
            }
        }

        return response()->json(['reply' => $reply]);
    }

    /**
     * Lấy lịch sử chat của user
     */
    public function getChatHistory(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'User chưa đăng nhập'], 401);
        }

        $chatHistory = ChatbotLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(50) // Lấy 50 tin nhắn gần nhất
            ->get();

        return response()->json(['chat_history' => $chatHistory]);
    }

    /**
     * Xóa lịch sử chat của user
     */
    public function clearChatHistory(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'User chưa đăng nhập'], 401);
        }

        try {
            ChatbotLog::where('user_id', $userId)->delete();
            return response()->json(['message' => 'Đã xóa lịch sử chat']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Không thể xóa lịch sử chat'], 500);
        }
    }
}
