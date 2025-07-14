<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Barber;
use App\Models\Appointment;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBookings = Appointment::where('status', 'completed')->count();
        $totalRegistrations = User::count();

        // Doanh thu từ lịch hẹn hoàn thành
        $serviceRevenue = Appointment::where('status', 'completed')->sum('total_amount');

        // Doanh thu sản phẩm từ đơn hàng hoàn thành
        $productRevenue = Order::where('status', 'completed')->sum('total_money');

        // Doanh thu hôm nay
        $today = Carbon::today();
        $todayRevenue = Appointment::whereDate('created_at', $today)->sum('total_amount') +
            Order::whereDate('created_at', $today)->sum('total_money');

        // Hiệu suất nhân viên tuần này
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $barberStats = Barber::withCount([
            'appointments as cut_count' => function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('appointment_time', [$startOfWeek, $endOfWeek])
                    ->where('status', 'completed');
            }
        ])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('cut_count')
            ->take(5)
            ->get();

        // Lịch hẹn sắp tới
        $upcomingAppointments = Appointment::where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Top sản phẩm bán chạy
        $topProducts = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('productVariant.product')
            ->get();

        // Giao dịch gần đây
        $latestTransactions = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBookings',
            'totalRegistrations',
            'serviceRevenue',
            'productRevenue',
            'todayRevenue',
            'barberStats',
            'upcomingAppointments',
            'topProducts',
            'latestTransactions'
        ));
    }
}
