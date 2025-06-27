<?php

namespace App\Filters;

use App\Models\Appointment;
use Illuminate\Support\Facades\View;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class AppointmentCountFilter implements FilterInterface
{
    public function transform($menu)
    {
        // // Đếm số lượng lịch hẹn chưa xác nhận
        // $pendingCount = Appointment::where('status', 'pending')->count();

        // // Tìm mục "Quản lý đặt lịch" và thêm badge
        // foreach ($menu as &$item) {
        //     // Kiểm tra xem $item là mảng và có khóa 'text'
        //     if (is_array($item) && isset($item['text']) && $item['text'] === 'Quản lý đặt lịch') {
        //         if ($pendingCount > 0) {
        //             $item['text'] .= ' <span id="pending-appointment-count" class="badge badge-danger">' . $pendingCount . '</span>';
        //         } else {
        //             $item['text'] .= ' <span id="pending-appointment-count" class="badge badge-danger" style="display: none;">0</span>';
        //         }
        //         break;
        //     }
        // }

        // Đếm số lượng lịch hẹn chưa xác nhận
        $pendingCount = Appointment::where('status', 'pending')->count();

        // Truyền biến pendingCount vào tất cả các view
        View::share('pendingCount', $pendingCount);

        return $menu;
    }

   
}