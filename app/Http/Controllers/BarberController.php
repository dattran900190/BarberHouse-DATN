<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = [
            [
                'name' => 'Nguyễn Văn A',
                'profile' => 'Chuyên fade, undercut, tạo kiểu hiện đại.',
                'skill_level' => 'Chuyên nghiệp',
                'avatar' => 'https://via.placeholder.com/100',
                'rating_avg' => 4.8,
            ],
            [
                'name' => 'Trần Văn B',
                'profile' => 'Kinh nghiệm 5 năm, từng làm tại 30Shine.',
                'skill_level' => 'Trung bình',
                'avatar' => 'https://via.placeholder.com/100',
                'rating_avg' => 4.3,
            ],
            [
                'name' => 'Lê Văn C',
                'profile' => 'Phong cách trẻ trung, năng động.',
                'skill_level' => 'Cao cấp',
                'avatar' => 'https://via.placeholder.com/100',
                'rating_avg' => 4.9,
            ],
        ];

        return view('admin.barbers.index', compact('barbers'));
    }
}
