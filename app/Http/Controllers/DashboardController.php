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
use App\Models\Service; // Thêm model Service
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
            ->take(10)
            ->get();

        // Top dịch vụ được sử dụng nhiều nhất
        $topServices = Appointment::select('service_id', DB::raw('COUNT(*) as usage_count'))
            ->where('status', 'completed')
            ->groupBy('service_id')
            ->orderByDesc('usage_count')
            ->take(10)
            ->with('service')
            ->get();

        // Dịch vụ ít được sử dụng (các dịch vụ có trong hệ thống nhưng ít được đặt)
        $lowUsageServices = Service::select(
            'services.id',
            'services.name',
            'services.price',
            DB::raw('COALESCE(COUNT(appointments.id), 0) as usage_count')
        )
            ->leftJoin('appointments', function ($join) {
                $join->on('services.id', '=', 'appointments.service_id')
                    ->where('appointments.status', '=', 'completed');
            })
            ->groupBy('services.id', 'services.name', 'services.price')
            ->orderBy('usage_count', 'asc')
            ->take(10)
            ->get();

        $latestTransactions = Order::latest()->take(8)->get();

        // Xử lý filter cho biểu đồ ngày/tuần
        $weekStart = $request->input('week_start');
        $weekEnd = $request->input('week_end');

        $weekLabels = [];
        $weekServiceRevenue = [];
        $weekProductRevenue = [];
        $this->generateWeekChart($weekStart, $weekEnd, $weekLabels, $weekServiceRevenue, $weekProductRevenue);

        // Xử lý filter cho biểu đồ tháng
        $selectedMonth = $request->input('selected_month');
        $year = $request->input('year', date('Y'));

        $monthLabels = [];
        $monthServiceRevenue = [];
        $monthProductRevenue = [];
        $this->generateMonthChart($selectedMonth, $year, $monthLabels, $monthServiceRevenue, $monthProductRevenue);

        // Tạo danh sách tháng có dữ liệu (chỉ các tháng đã qua và tháng hiện tại)
        $currentMonth = date('n');
        $availableMonths = range(1, $currentMonth);

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
            'topServices', // Thêm biến mới
            'lowUsageServices', // Thêm biến mới
            'latestTransactions',
            // Biểu đồ tuần
            'weekLabels',
            'weekServiceRevenue',
            'weekProductRevenue',
            'weekStart',
            'weekEnd',
            // Biểu đồ tháng
            'monthLabels',
            'monthServiceRevenue',
            'monthProductRevenue',
            'selectedMonth',
            'year',
            'availableMonths'
        ));
    }

    private function generateWeekChart($weekStart, $weekEnd, &$labels, &$serviceRevenue, &$productRevenue)
    {
        if ($weekStart && $weekEnd) {
            $start = Carbon::parse($weekStart);
            $end = Carbon::parse($weekEnd);
        } else {
            // Mặc định: tuần hiện tại
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        }

        // Hiển thị theo từng ngày trong tuần
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $labels[] = $date->format('d/m (D)'); // Ví dụ: 15/7 (Mon)

            $serviceRevenue[] = Appointment::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $productRevenue[] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_money');
        }
    }

    private function generateMonthChart($selectedMonth, $year, &$labels, &$serviceRevenue, &$productRevenue)
    {
        $currentYear = date('Y');
        $currentMonth = date('n');

        if ($selectedMonth && $selectedMonth <= $currentMonth && $year == $currentYear) {
            // Hiển thị theo ngày trong tháng được chọn
            $monthObj = Carbon::createFromDate($year, $selectedMonth, 1);
            $daysInMonth = $monthObj->daysInMonth;
            $today = Carbon::today();

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::createFromDate($year, $selectedMonth, $day);

                // Chỉ hiển thị đến ngày hiện tại nếu là tháng hiện tại
                if ($selectedMonth == $currentMonth && $date->gt($today)) {
                    break;
                }

                $labels[] = $date->format('d/m');

                $serviceRevenue[] = Appointment::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total_amount');

                $productRevenue[] = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total_money');
            }
        } else {
            // Hiển thị theo tháng (chỉ các tháng đã qua và tháng hiện tại)
            for ($i = 1; $i <= $currentMonth; $i++) {
                $labels[] = 'Tháng ' . $i;

                $serviceRevenue[] = Appointment::whereMonth('created_at', $i)
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->sum('total_amount');

                $productRevenue[] = Order::whereMonth('created_at', $i)
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->sum('total_money');
            }
        }
    }
}
