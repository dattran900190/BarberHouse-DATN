<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatbotLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminChatController extends Controller
{
    /**
     * Hiển thị trang quản lý chat
     */
    public function index(Request $request)
    {
        $query = ChatbotLog::with('user');

        // Lọc theo user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo từ khóa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('reply', 'like', "%{$search}%");
            });
        }

        // Nhóm theo user_id và lấy tin nhắn mới nhất của mỗi user
        $chatLogs = $query->select('user_id', DB::raw('MAX(id) as latest_id'), DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy('user_id')
            ->orderBy('latest_created_at', 'desc')
            ->paginate(20);

        // Lấy thông tin chi tiết cho các log mới nhất
        $latestLogIds = $chatLogs->pluck('latest_id')->toArray();
        $detailedLogs = ChatbotLog::with('user')->whereIn('id', $latestLogIds)->get()->keyBy('id');

        // Gán thông tin chi tiết vào collection
        $chatLogs->getCollection()->transform(function ($item) use ($detailedLogs) {
            $detailedLog = $detailedLogs->get($item->latest_id);
            if ($detailedLog) {
                $item->user = $detailedLog->user;
                $item->message = $detailedLog->message;
                $item->reply = $detailedLog->reply;
                $item->created_at = $detailedLog->created_at;
                $item->updated_at = $detailedLog->updated_at;
            }
            return $item;
        });
        $users = User::orderBy('name')->get();

        // Thống kê tổng quan
        $stats = $this->getChatStats($request);

        return view('admin.chatbot.index', compact('chatLogs', 'users', 'stats'));
    }

    /**
     * Hiển thị chi tiết một cuộc hội thoại
     */
    public function show($id)
    {
        $log = ChatbotLog::with('user')->findOrFail($id);

        // Lấy tất cả tin nhắn của user này
        $allLogs = ChatbotLog::where('user_id', $log->user_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chatbot.show', compact('log', 'allLogs'));
    }

    /**
     * Xóa một chat log
     */
    public function destroy($id)
    {
        try {
            $chatLog = ChatbotLog::findOrFail($id);
            $chatLog->delete();
            return redirect()->route('chatbot.index')->with('success', 'Đã xóa chat thành công!');
        } catch (\Exception $e) {
            return redirect()->route('chatbot.index')->with('error', 'Không thể xóa chat log!');
        }
    }

    /**
     * Xóa một tin nhắn riêng lẻ
     */
    public function destroyMessage($id)
    {
        try {
            $chatLog = ChatbotLog::findOrFail($id);
            $chatLog->delete();
            return response()->json(['success' => true, 'message' => 'Đã xóa tin nhắn thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa tin nhắn!'], 500);
        }
    }

    /**
     * Xóa nhiều chat log
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:chatbot_logs,id'
        ]);

        try {
            ChatbotLog::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => 'Đã xóa ' . count($request->ids) . ' chat log thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa chat log!'], 500);
        }
    }

    /**
     * Lấy thống kê chat
     */
    private function getChatStats(Request $request)
    {
        $query = ChatbotLog::query();

        // Lọc theo ngày nếu có
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_chats' => $query->count(),
            'total_users' => $query->distinct('user_id')->count(),
            'today_chats' => $query->whereDate('created_at', Carbon::today())->count(),
            'this_week_chats' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'this_month_chats' => $query->whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Thống kê theo ngày trong tuần
        $weeklyStats = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $stats['weekly_stats'] = $weeklyStats;

        return $stats;
    }

    /**
     * Export chat logs
     */
    public function export(Request $request)
    {
        $query = ChatbotLog::with('user');

        // Áp dụng các filter như index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $chatLogs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'chat_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($chatLogs) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, ['ID', 'User ID', 'User Name', 'Message', 'Reply', 'Created At']);

            foreach ($chatLogs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user_id,
                    $log->user ? $log->user->name : 'Guest',
                    $log->message,
                    $log->reply,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
