<?php

namespace App\Http\Controllers;

use DateTime;
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
    public function index(Request $request)
    {
        $totalBookings = Appointment::where('status', 'completed')->count();
        $totalRegistrations = User::count();

        $serviceRevenue = Appointment::where('status', 'completed')->sum('total_amount');
        $productRevenue = Order::where('status', 'completed')->sum('total_money');

        $today = Carbon::today();
        $todayRevenue = Appointment::whereDate('created_at', $today)->sum('total_amount') +
            Order::whereDate('created_at', $today)->sum('total_money');

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

        $upcomingAppointments = Appointment::where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        $topProducts = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('productVariant.product')
            ->get();

        $latestTransactions = Order::latest()->take(5)->get();
        $month = $request->input('month');
        $labels = [];
        $serviceRevenuePerMonth = [];
        $productRevenuePerMonth = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;

            $serviceRevenuePerMonth[] = Appointment::whereMonth('created_at', $i)
                ->where('status', 'completed')
                ->sum('total_amount');

            $productRevenuePerMonth[] = Order::whereMonth('created_at', $i)
                ->where('status', 'completed')
                ->sum('total_money');
        }


        return view('admin.dashboard', compact(
            'totalBookings',
            'totalRegistrations',
            'serviceRevenue',
            'productRevenue',
            'todayRevenue',
            'barberStats',
            'upcomingAppointments',
            'topProducts',
            'latestTransactions',
            'labels',
            'serviceRevenuePerMonth',
            'productRevenuePerMonth'
        ));
    }
}
