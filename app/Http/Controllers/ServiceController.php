<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = [
            [
                'name' => 'Cắt tóc nam',
                'description' => 'Kiểu tóc nam hiện đại, gọn gàng và cá tính.',
                'price' => 50000,
                'duration' => 30, // phút
                'is_combo' => false,
                'image' => 'https://via.placeholder.com/120x80?text=Haircut',
            ],
            [
                'name' => 'Gội đầu thư giãn',
                'description' => 'Gội đầu kết hợp massage đầu, cổ, vai.',
                'price' => 30000,
                'duration' => 20,
                'is_combo' => false,
                'image' => 'https://via.placeholder.com/120x80?text=Shampoo',
            ],
            [
                'name' => 'Combo VIP: Cắt + Gội + Cạo mặt',
                'description' => 'Gói combo cao cấp dành cho khách hàng VIP.',
                'price' => 100000,
                'duration' => 60,
                'is_combo' => true,
                'image' => 'https://via.placeholder.com/120x80?text=Combo+VIP',
            ],
        ];

        return view('admin.services.index', compact('services'));
    }
}
