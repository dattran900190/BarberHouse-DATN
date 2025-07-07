<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
   public function submitReview(Request $request, Appointment $appointment)
{
      // ✅ Kiểm tra người dùng đã đăng nhập hay chưa
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn cần đăng nhập để đánh giá.'
        ], 401);
    }

    // ✅ Kiểm tra quyền sở hữu lịch hẹn & trạng thái
    if ($appointment->user_id !== Auth::id() || $appointment->status !== 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'Bạn không thể đánh giá lịch hẹn này.'
        ], 403);
    }
    Log::debug('Submit review attempt by user: ' . Auth::id());

    // if ($appointment->user_id !== Auth::id() || $appointment->status !== 'completed') {
    //     Log::warning('Unauthorized review attempt.', [
    //         'user_id' => Auth::id(),
    //         'appointment_user_id' => $appointment->user_id,
    //         'appointment_status' => $appointment->status
    //     ]);

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Bạn không thể đánh giá lịch hẹn này.'
    //     ], 403);
    // }

    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    Review::create([
        'user_id' => Auth::id(),
        'barber_id' => $appointment->barber_id,
        'appointment_id' => $appointment->id,
        'rating' => $validated['rating'],
        'comment' => $validated['comment'] ?? '',
        'is_visible' => true,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Đã gửi đánh giá thành công!'
    ]);
}

    // public function submitReview(Request $request, Appointment $appointment)
//     {
//         $request->validate([
//             'rating' => 'required|integer|min:1|max:5',
//             'comment' => 'nullable|string|max:1000',
//         ]);

    //         // Kiểm tra quyền
//         if ($appointment->user_id !== Auth::id()) {
//             return response()->json(['success' => false, 'message' => 'Không có quyền.'], 403);
//         }

    //         // Tạo hoặc cập nhật đánh giá
//         $review = Review::updateOrCreate(
//             [
//                 'user_id' => Auth::id(),
//                 'appointment_id' => $appointment->id,
//             ],
//             [
//                 'barber_id' => $appointment->barber_id,
//                 'rating' => $request->rating,
//                 'comment' => $request->comment,
//                 'is_visible' => true,
//             ]
//         );

    //         return response()->json(['success' => true, 'message' => 'Gửi đánh giá thành công.']);
//     }
}


