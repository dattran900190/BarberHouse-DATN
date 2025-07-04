<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Appointment $appointment)
{
    if ($appointment->user_id !== Auth::id() || $appointment->status !== 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'Bạn không thể đánh giá lịch hẹn này.'
        ], 403);
    }

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


}

