<?php

namespace App\Filters;

use App\Models\Appointment;
use Illuminate\Support\Facades\View;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class AppointmentCountFilter implements FilterInterface
{
    public function transform($item)
    {
        if (isset($item['text']) && $item['text'] === 'Quản lý đặt lịch') {
            $pendingCount = Appointment::where('status', 'pending')->count();
            $item['label'] = $pendingCount;
            // $item['label_color'] = 'danger';
            $item['label_classes'] = 'pending-appointment-count' . ($pendingCount === 0 ? ' hidden' : '');
        }
        return $item;
    }
}
