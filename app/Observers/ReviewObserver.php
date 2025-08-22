<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\Barber;

class ReviewObserver
{
    /**
     * Xử lý sự kiện "created" của Review.
     */
    public function created(Review $review)
    {
        $this->updateBarberRating($review->barber_id);
    }

    /**
     * Xử lý sự kiện "updated" của Review.
     */
    public function updated(Review $review)
    {
        $this->updateBarberRating($review->barber_id);
    }

    /**
     * Xử lý sự kiện "deleted" của Review (nếu cần).
     */
    public function deleted(Review $review)
    {
        $this->updateBarberRating($review->barber_id);
    }

    /**
     * Cập nhật rating_avg cho Barber dựa trên tất cả các review.
     */
    protected function updateBarberRating($barberId)
    {
        $barber = Barber::find($barberId);
        if ($barber) {
            // Tính trung bình rating từ các review có is_visible = true
            $averageRating = Review::where('barber_id', $barberId)
                ->where('is_visible', true)
                ->avg('rating');

            // Cập nhật rating_avg cho barber
            $barber->rating_avg = $averageRating ? number_format($averageRating, 1) : 0;
            $barber->save();
        }
    }
}