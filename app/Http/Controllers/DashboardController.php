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
use App\Models\Service;
use App\Models\Branch;
use App\Models\BarberSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Đếm tổng lượt đặt lịch hoàn thành
        $totalBookings = Appointment::where('status', 'completed')->count();

        // Đếm tổng số đăng ký người dùng
        $totalRegistrations = User::count();

        // Tổng doanh thu dịch vụ (chỉ tính những appointment hoàn thành)
        $serviceRevenue = Appointment::where('status', 'completed')->sum('total_amount');

        // Tổng doanh thu sản phẩm (chỉ tính những đơn hàng hoàn thành - đã thanh toán)
        $productRevenue = Order::where('status', 'completed')->sum('total_money');

        // Doanh thu hôm nay
        $today = Carbon::today();
        $todayServiceRevenue = Appointment::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayProductRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_money');

        // Tổng doanh thu hôm nay (sản phẩm + dịch vụ)
        $todayRevenue = $todayServiceRevenue + $todayProductRevenue;

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
            ->take(6)
            ->get()
            ->map(function ($barber) {
                $barber->avg_rating = $barber->avg_rating ? number_format($barber->avg_rating, 1) : '0.0';
                return $barber;
            });

        // Thống kê ngày nghỉ của thợ (Top 5)
        $user = Auth::user();
        $selectedLeaveMonth = $request->input('leave_month', date('n'));
        $selectedBranch = $request->input('leave_branch');

        $leaveMonth = $selectedLeaveMonth ? Carbon::createFromDate(date('Y'), $selectedLeaveMonth, 1) : Carbon::now()->startOfMonth();
        $leaveMonthEnd = $leaveMonth->copy()->endOfMonth();

        // Lấy danh sách chi nhánh
        if ($user->role === 'admin_branch') {
            $branches = Branch::where('id', $user->branch_id)->get();
            $selectedBranch = $user->branch_id;
        } else {
            $branches = Branch::all();
        }

        // Query thống kê ngày nghỉ
        $barberLeavesQuery = Barber::select('id', 'name')
            ->with(['schedules' => function ($q) use ($leaveMonth, $leaveMonthEnd) {
                $q->whereBetween('schedule_date', [$leaveMonth, $leaveMonthEnd])
                    ->whereIn('status', ['off', 'holiday']);
            }]);

        // Lọc theo chi nhánh
        if ($selectedBranch) {
            $barberLeavesQuery->where('branch_id', $selectedBranch);
        } elseif ($user->role === 'admin_branch') {
            $barberLeavesQuery->where('branch_id', $user->branch_id);
        }

        $barberLeaves = $barberLeavesQuery->get()
            ->map(function ($barber) {
                $barber->total_off = $barber->schedules->count();
                return $barber;
            })
            ->sortByDesc('total_off')
            ->take(5);

        $upcomingAppointments = Appointment::where('appointment_time', '>=', now())
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Top sản phẩm bán chạy (10 sản phẩm)
        $topProducts = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed'); // Chỉ tính đơn hàng hoàn thành
            })
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

        $orderTransactions = Order::select('id', 'order_code as code', 'created_at', 'total_money as amount', 'status')
            ->addSelect(DB::raw("'order' as type"))
            ->latest()
            ->take(10)
            ->get();

        $appointmentTransactions = Appointment::select('id', 'appointment_code as code', 'created_at', 'total_amount as amount', 'status')
            ->addSelect(DB::raw("'appointment' as type"))
            ->latest()
            ->take(10)
            ->get();

        // Gộp 2 collection lại và sort theo created_at giảm dần
        $latestTransactions = $orderTransactions
            ->merge($appointmentTransactions)
            ->sortByDesc('created_at')
            ->take(10)
            ->values(); // đảm bảo chỉ lấy 10 bản ghi mới nhất sau khi gộp

        // Xử lý filter cho biểu đồ ngày/tuần
        $weekStart = $request->input('week_start');
        $weekEnd = $request->input('week_end');

        if (!$weekStart || !$weekEnd) {
            // Nếu không chọn khoảng ngày, mặc định lấy 21 ngày gần nhất
            $weekStart = Carbon::now()->subDays(30)->toDateString();
            $weekEnd = Carbon::now()->toDateString();
        }
        $viewWeekStart = $weekStart;
        $viewWeekEnd = $weekEnd;
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

        // Nếu là AJAX request, chỉ trả về dữ liệu JSON cho biểu đồ
        if ($request->get('ajax')) {
            return response()->json([
                'success' => true,
                'weekLabels' => $weekLabels,
                'weekServiceRevenue' => $weekServiceRevenue,
                'weekProductRevenue' => $weekProductRevenue,
                'monthLabels' => $monthLabels,
                'monthServiceRevenue' => $monthServiceRevenue,
                'monthProductRevenue' => $monthProductRevenue,
                'barberLeaves' => $barberLeaves->map(function ($barber) {
                    return [
                        'name' => $barber->name,
                        'total_off' => $barber->total_off,
                    ];
                })->values()->toArray(),
            ]);
        }

        return view('admin.dashboard', compact(
            'totalBookings',
            'totalRegistrations',
            'serviceRevenue',
            'productRevenue',
            'todayRevenue',
            'todayServiceRevenue',
            'todayProductRevenue',
            'barberStats',
            'barberLeaves',
            'weekRange',
            'upcomingAppointments',
            'topProducts',
            'lowSellingProducts',
            'topServices',
            'lowUsageServices',
            'latestTransactions',
            // Biểu đồ tuần
            'weekLabels',
            'weekServiceRevenue',
            'weekProductRevenue',
            'weekStart',
            'weekEnd',
            'viewWeekStart',
            'viewWeekEnd',
            // Biểu đồ tháng
            'monthLabels',
            'monthServiceRevenue',
            'monthProductRevenue',
            'selectedMonth',
            'year',
            'availableMonths',
            'selectedLeaveMonth',
            'selectedBranch',
            'branches'
        ));
    }

    private function generateWeekChart($weekStart, $weekEnd, &$labels, &$serviceRevenue, &$productRevenue)
    {
        if ($weekStart && $weekEnd) {
            $start = Carbon::parse($weekStart);
            $end = Carbon::parse($weekEnd);
        } else {
            // Mặc định: 7 ngày gần nhất (thay vì tuần hiện tại để linh hoạt hơn)
            $start = Carbon::now()->subDays(6);
            $end = Carbon::now();
        }

        // Đảm bảo không vượt quá ngày hiện tại
        $today = Carbon::today();
        if ($end->gt($today)) {
            $end = $today;
        }

        // Hiển thị theo từng ngày trong khoảng thời gian
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $labels[] = $date->format('d/m'); // Format ngắn gọn hơn

            $serviceRevenue[] = Appointment::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount') ?: 0; // Đảm bảo trả về 0 thay vì null

            $productRevenue[] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_money') ?: 0; // Đảm bảo trả về 0 thay vì null
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
                    ->sum('total_amount') ?: 0;

                $productRevenue[] = Order::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('total_money') ?: 0;
            }
        } else {
            // Hiển thị theo tháng (chỉ các tháng đã qua và tháng hiện tại)
            for ($i = 1; $i <= $currentMonth; $i++) {
                $labels[] = 'T' . $i; // Format ngắn gọn hơn

                $serviceRevenue[] = Appointment::whereMonth('created_at', $i)
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->sum('total_amount') ?: 0;

                $productRevenue[] = Order::whereMonth('created_at', $i)
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->sum('total_money') ?: 0;
            }
        }
    }

    /**
     * API endpoint riêng cho AJAX requests (tùy chọn)
     * Bạn có thể tạo route riêng cho điều này nếu muốn
     */
    public function getChartData(Request $request)
    {
        $weekStart = $request->input('week_start');
        $weekEnd = $request->input('week_end');
        $selectedMonth = $request->input('selected_month');
        $year = $request->input('year', date('Y'));

        $weekLabels = [];
        $weekServiceRevenue = [];
        $weekProductRevenue = [];
        $this->generateWeekChart($weekStart, $weekEnd, $weekLabels, $weekServiceRevenue, $weekProductRevenue);

        $monthLabels = [];
        $monthServiceRevenue = [];
        $monthProductRevenue = [];
        $this->generateMonthChart($selectedMonth, $year, $monthLabels, $monthServiceRevenue, $monthProductRevenue);

        return response()->json([
            'success' => true,
            'weekLabels' => $weekLabels,
            'weekServiceRevenue' => $weekServiceRevenue,
            'weekProductRevenue' => $weekProductRevenue,
            'monthLabels' => $monthLabels,
            'monthServiceRevenue' => $monthServiceRevenue,
            'monthProductRevenue' => $monthProductRevenue,
        ]);
    }
}
