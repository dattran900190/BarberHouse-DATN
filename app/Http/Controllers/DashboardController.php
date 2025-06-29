<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class DashboardController
{
    public function index()
    {
        // Lấy số lượng lịch đang chờ
        $pendingCount = Appointment::where('status', 'pending')->count();


        // Truyền biến $pendingCount sang view
        return view('admin.dashboard', compact('pendingCount'));
    }
}
