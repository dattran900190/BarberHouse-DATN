<?php

namespace App\Filters;

use App\Models\RefundRequest;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;

class RefundRequestCountFilter implements FilterInterface
{
    public function transform($item)
    {
        // Kiểm tra nếu mục menu là "Hoàn tiền"
        if (isset($item['text']) && $item['text'] === 'Hoàn tiền') {
            $pendingCount = RefundRequest::where('refund_status', 'pending')->count();
            $item['label'] = $pendingCount > 0 ? $pendingCount : null; // Chỉ hiển thị label nếu > 0
            $item['label_classes'] = 'badge bg-danger' . ($pendingCount === 0 ? ' hidden' : ''); // Sử dụng class badge của AdminLTE
        }
        return $item;
    }
}