<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Barber;
use App\Models\Appointment;
use App\Models\OrderItem;
use App\Models\ProductVariant;
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

        // Lấy tuần hiện tại cho hiển thị
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Format ngày hiển thị
        $weekRange = $startOfWeek->format('d/m') . ' đến ' . $endOfWeek->format('d/m');

        // Thống kê hiệu suất nhân viên theo tuần hiện tại
        $barberStats = Barber::withCount([
            'appointments as cut_count' => function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('appointment_time', [$startOfWeek, $endOfWeek])
                    ->where('status', 'completed');
            }
        ])
            ->withAvg('reviews as avg_rating', 'rating')
            ->orderByDesc('cut_count')
            ->take(5)
            ->get()
            ->map(function ($barber) {
                $barber->avg_rating = $barber->avg_rating ? number_format($barber->avg_rating, 1) : '0.0';
                return $barber;
            });

        $upcomingAppointments = Appointment::where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Top sản phẩm bán chạy (10 sản phẩm)
        $topProducts = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_sold')
            ->take(10)
            ->with('productVariant.product')
            ->get();

        // Sản phẩm ít bán (5 sản phẩm có trong kho nhưng bán ít nhất)
        $lowSellingProducts = ProductVariant::select(
            'product_variants.id',
            'product_variants.product_id',
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
        )
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'completed');
            })
            ->with('product')
            ->groupBy('product_variants.id', 'product_variants.product_id')
            ->orderBy('total_sold', 'asc')
            ->take(5)
            ->get();

        $latestTransactions = Order::latest()->take(5)->get();

        // Xử lý filter cho biểu đồ
        $filterType = $request->input('filter_type', 'month'); // month, date_range
        $month = $request->input('month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $labels = [];
        $serviceRevenuePerPeriod = [];
        $productRevenuePerPeriod = [];

        if ($filterType === 'date_range' && $startDate && $endDate) {
            // Lọc theo khoảng ngày
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $diffInDays = $start->diffInDays($end);

            if ($diffInDays <= 31) {
                // Hiển thị theo ngày nếu khoảng thời gian <= 31 ngày
                for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                    $labels[] = $date->format('d/m');

                    $serviceRevenuePerPeriod[] = Appointment::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_amount');

                    $productRevenuePerPeriod[] = Order::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_money');
                }
            } else {
                // Hiển thị theo tháng nếu khoảng thời gian > 31 ngày
                $currentMonth = $start->copy()->startOfMonth();
                $endMonth = $end->copy()->startOfMonth();

                while ($currentMonth <= $endMonth) {
                    $labels[] = $currentMonth->format('m/Y');

                    $monthStart = $currentMonth->copy()->startOfMonth();
                    $monthEnd = $currentMonth->copy()->endOfMonth();

                    $serviceRevenuePerPeriod[] = Appointment::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->where('status', 'completed')
                        ->sum('total_amount');

                    $productRevenuePerPeriod[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->where('status', 'completed')
                        ->sum('total_money');

                    $currentMonth->addMonth();
                }
            }
        } else {
            // Mặc định: hiển thị theo tháng trong năm
            $currentYear = date('Y');

            if ($month) {
                // Hiển thị theo ngày trong tháng được chọn
                $selectedMonth = Carbon::createFromDate($currentYear, $month, 1);
                $daysInMonth = $selectedMonth->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date = Carbon::createFromDate($currentYear, $month, $day);
                    $labels[] = $date->format('d/m');

                    $serviceRevenuePerPeriod[] = Appointment::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_amount');

                    $productRevenuePerPeriod[] = Order::whereDate('created_at', $date)
                        ->where('status', 'completed')
                        ->sum('total_money');
                }
            } else {
                // Hiển thị cả năm theo tháng
                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = 'Tháng ' . $i;

                    $serviceRevenuePerPeriod[] = Appointment::whereMonth('created_at', $i)
                        ->whereYear('created_at', $currentYear)
                        ->where('status', 'completed')
                        ->sum('total_amount');

                    $productRevenuePerPeriod[] = Order::whereMonth('created_at', $i)
                        ->whereYear('created_at', $currentYear)
                        ->where('status', 'completed')
                        ->sum('total_money');
                }
            }
        }

        return view('admin.dashboard', compact(
            'totalBookings',
            'totalRegistrations',
            'serviceRevenue',
            'productRevenue',
            'todayRevenue',
            'barberStats',
            'weekRange',
            'upcomingAppointments',
            'topProducts',
            'lowSellingProducts',
            'latestTransactions',
            'labels',
            'serviceRevenuePerPeriod',
            'productRevenuePerPeriod',
            'filterType',
            'month',
            'startDate',
            'endDate'
        ));
    }
}
