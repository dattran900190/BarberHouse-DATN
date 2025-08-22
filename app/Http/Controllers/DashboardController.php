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
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        // Lấy danh sách chi nhánh cho thống kê doanh thu
        $branchesForRevenue = Branch::all();

        // Lấy chi nhánh được chọn từ request (nếu không chọn thì mặc định null)
        $selectedBranchRevenue = $request->input('branch_revenue_id');
        $selectedBranchName = 'Chọn chi nhánh'; // Mặc định là 'Chọn chi nhánh'

        // Doanh thu theo chi nhánh (chỉ dịch vụ)
        if ($selectedBranchRevenue) {
            $selectedBranch = Branch::find($selectedBranchRevenue);
            if ($selectedBranch) {
                $selectedBranchName = $selectedBranch->name;

                $branchTodayRevenue = Appointment::whereDate('created_at', $today)
                    ->where('status', 'completed')
                    ->where('branch_id', $selectedBranchRevenue)
                    ->sum('total_amount');
            } else {
                $branchTodayRevenue = 0;
            }
        } else {
            // Mặc định: tất cả chi nhánh
            $branchTodayRevenue = Appointment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount');
            $selectedBranchName = 'Chọn chi nhánh';
        }

        // Đếm tổng lượt đặt lịch hoàn thành
        $totalBookings = Appointment::where('status', 'completed')->count();

        // Đếm tổng số đăng ký người dùng
        $totalRegistrations = User::count();

        // Tổng doanh thu dịch vụ (chỉ tính những appointment hoàn thành)
        $serviceRevenue = Appointment::where('status', 'completed')->sum('total_amount');

        // Tổng doanh thu sản phẩm (chỉ tính những đơn hàng hoàn thành - đã thanh toán)
        $productRevenue = Order::where('status', 'completed')->sum('total_money');

        $today = Carbon::today();
        $todayServiceRevenue = Appointment::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayProductRevenue = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_money');

        // Tổng doanh thu hôm nay (sản phẩm + dịch vụ)
        $todayRevenue = $todayServiceRevenue + $todayProductRevenue;



        // Lấy thống kê bổ sung cho doanh thu hôm nay
        $todayServiceCount = Appointment::whereDate('appointment_time', $today)
            ->where('status', 'completed')
            ->count();

        $todayProductOrderCount = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        // Lấy tuần hiện tại cho hiển thị
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Format ngày hiển thị
        $weekRange = $startOfWeek->format('d/m') . ' đến ' . $endOfWeek->format('d/m');

        // Xử lý filter cho hiệu suất nhân viên
        $user = Auth::user();
        $selectedPerformanceMonth = $request->input('performance_month', date('n'));
        $selectedPerformanceBranch = $request->input('performance_branch');

        // Thống kê hiệu suất nhân viên
        $barberStatsQuery = Barber::withCount([
            'appointments as cut_count' => function ($q) use ($selectedPerformanceMonth) {
                if ($selectedPerformanceMonth) {
                    $q->whereMonth('appointment_time', $selectedPerformanceMonth)
                        ->whereYear('appointment_time', date('Y'))
                        ->where('status', 'completed');
                } else {
                    // Mặc định lấy tuần hiện tại
                    $startOfWeek = Carbon::now()->startOfWeek();
                    $endOfWeek = Carbon::now()->endOfWeek();
                    $q->whereBetween('appointment_time', [$startOfWeek, $endOfWeek])
                        ->where('status', 'completed');
                }
            }
        ])
            ->withAvg('reviews as avg_rating', 'rating');

        // Lọc theo chi nhánh cho hiệu suất nhân viên
        if ($selectedPerformanceBranch) {
            $barberStatsQuery->where('branch_id', $selectedPerformanceBranch);
        } elseif ($user->role === 'admin_branch') {
            $barberStatsQuery->where('branch_id', $user->branch_id);
        }

        $barberStats = $barberStatsQuery->orderByDesc('cut_count')
            ->take(5)
            ->get()
            ->map(function ($barber) {
                $barber->avg_rating = $barber->avg_rating ? number_format($barber->avg_rating, 1) : '0.0';
                return $barber;
            });

        // Thống kê ngày nghỉ của thợ (Top 5)
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
        // Lấy parameters từ request
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'total_sold');

        // Xử lý per_page
        $perPageValue = $perPage === 'all' ? 999999 : (int)$perPage;

        // Sản phẩm bán chạy
        $topProductsQuery = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed'); // Chỉ tính đơn hàng hoàn thành
            })
            ->with('productVariant.product')
            ->groupBy('product_variant_id');

        // Thêm search filter cho top products
        if ($search) {
            $topProductsQuery->whereHas('productVariant.product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Thêm sorting cho top products
        switch ($sortBy) {
            case 'name':
                $topProductsQuery->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->orderBy('products.name', 'asc');
                break;
            case 'price':
                $topProductsQuery->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->orderBy('product_variants.price', 'desc');
                break;
            default: // total_sold
                $topProductsQuery->orderByDesc('total_sold');
        }

        $topProducts = $topProductsQuery->paginate($perPageValue, ['*'], 'top_page');

        // Sản phẩm ít bán
        $lowSellingQuery = ProductVariant::select(
            'product_variants.id',
            'product_variants.product_id',
            'product_variants.price', // Thêm price để hiển thị
            DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
        )
            ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'completed');
            })
            ->with('product')
            ->groupBy('product_variants.id', 'product_variants.product_id', 'product_variants.price');

        // Thêm search filter cho low selling products  
        if ($search) {
            $lowSellingQuery->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Thêm sorting cho low selling products
        switch ($sortBy) {
            case 'name':
                $lowSellingQuery->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->orderBy('products.name', 'asc');
                break;
            case 'price':
                $lowSellingQuery->orderBy('product_variants.price', 'asc');
                break;
            case 'created_at':
                $lowSellingQuery->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->orderBy('products.created_at', 'desc');
                break;
            default: // total_sold
                $lowSellingQuery->orderBy('total_sold', 'asc');
        }

        $lowSellingProducts = $lowSellingQuery->paginate($perPageValue, ['*'], 'low_page');

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
                'barberStats' => $barberStats->map(function ($barber) {
                    return [
                        'name' => $barber->name,
                        'cut_count' => $barber->cut_count,
                        'avg_rating' => $barber->avg_rating,
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
            'selectedPerformanceMonth',
            'selectedPerformanceBranch',
            'branches',
            'branchesForRevenue',
            'selectedBranchRevenue',
            'selectedBranchName',
            'branchTodayRevenue'

        ));
    }


    public function filterServices(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Only AJAX requests allowed'], 400);
        }

        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'usage_count');
        $perPage = $request->get('per_page', 10);

        $perPageValue = $perPage === 'all' ? 999999 : (int)$perPage;

        // Top dịch vụ phổ biến
        $topServicesQuery = Appointment::select('service_id', DB::raw('COUNT(*) as usage_count'))
            ->where('status', 'completed')
            ->groupBy('service_id')
            ->with('service');

        if ($search) {
            $topServicesQuery->whereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        switch ($sortBy) {
            case 'name':
                $topServicesQuery->join('services', 'appointments.service_id', '=', 'services.id')
                    ->orderBy('services.name', 'asc');
                break;
            case 'price':
                $topServicesQuery->join('services', 'appointments.service_id', '=', 'services.id')
                    ->orderBy('services.price', 'desc');
                break;
            default:
                $topServicesQuery->orderByDesc('usage_count');
        }

        $topServices = $topServicesQuery->take($perPageValue)->get();

        // Dịch vụ ít dùng
        $lowUsageQuery = Service::select(
            'services.id',
            'services.name',
            'services.price',
            DB::raw('COALESCE(COUNT(appointments.id), 0) as usage_count')
        )
            ->leftJoin('appointments', function ($join) {
                $join->on('services.id', '=', 'appointments.service_id')
                    ->where('appointments.status', '=', 'completed');
            })
            ->groupBy('services.id', 'services.name', 'services.price');

        if ($search) {
            $lowUsageQuery->where('services.name', 'like', "%{$search}%");
        }

        switch ($sortBy) {
            case 'name':
                $lowUsageQuery->orderBy('services.name', 'asc');
                break;
            case 'price':
                $lowUsageQuery->orderBy('services.price', 'desc');
                break;
            default:
                $lowUsageQuery->orderBy('usage_count', 'asc');
        }

        $lowUsageServices = $lowUsageQuery->take($perPageValue)->get();

        // Render HTML partials
        $topServicesHtml = view('partials.top-services', compact('topServices'))->render();
        $lowServicesHtml = view('partials.low-services', compact('lowUsageServices'))->render();

        return response()->json([
            'success' => true,
            'topServicesHtml' => $topServicesHtml,
            'lowServicesHtml' => $lowServicesHtml,
            'topCount' => $topServices->count(),
            'lowCount' => $lowUsageServices->count()
        ]);
    }
    public function getBranchRevenue($id)
    {
        $today = Carbon::today();

        if ($id) {
            $branch = Branch::find($id);
            if (!$branch) {
                return response()->json(['error' => 'Chi nhánh không tồn tại'], 404);
            }

            $revenue = Appointment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->where('branch_id', $id)
                ->sum('total_amount');

            return response()->json([
                'branchName' => $branch->name,
                'revenue' => number_format($revenue) . ' VNĐ'
            ]);
        } else {
            $revenue = Appointment::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount');

            return response()->json([
                'branchName' => 'Tất cả chi nhánh',
                'revenue' => number_format($revenue) . ' VNĐ'
            ]);
        }
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
    // Thêm method này vào DashboardController

    public function filterProducts(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Only AJAX requests allowed'], 400);
        }

        try {
            // Lấy parameters từ request
            $search = $request->get('search');
            $perPage = $request->get('per_page', 10);
            $sortBy = $request->get('sort_by', 'total_sold');

            // Xử lý per_page
            $perPageValue = $perPage === 'all' ? 999999 : (int)$perPage;

            // Sản phẩm bán chạy
            $topProductsQuery = OrderItem::select('product_variant_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereHas('order', function ($q) {
                    $q->where('status', 'completed');
                })
                ->with('productVariant.product.category')
                ->groupBy('product_variant_id');

            // Thêm search filter cho top products
            if ($search) {
                $topProductsQuery->whereHas('productVariant.product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Thêm sorting cho top products
            switch ($sortBy) {
                case 'name':
                    $topProductsQuery->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->orderBy('products.name', 'asc');
                    break;
                case 'price':
                    $topProductsQuery->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                        ->orderBy('product_variants.price', 'desc');
                    break;
                case 'created_at':
                    $topProductsQuery->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->orderBy('products.created_at', 'desc');
                    break;
                default: // total_sold
                    $topProductsQuery->orderByDesc('total_sold');
            }

            $topProducts = $topProductsQuery->take($perPageValue)->get();

            // Sản phẩm ít bán
            $lowSellingQuery = ProductVariant::select(
                'product_variants.id',
                'product_variants.product_id',
                'product_variants.price',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            )
                ->leftJoin('order_items', 'product_variants.id', '=', 'order_items.product_variant_id')
                ->leftJoin('orders', function ($join) {
                    $join->on('order_items.order_id', '=', 'orders.id')
                        ->where('orders.status', '=', 'completed');
                })
                ->with('product.category')
                ->groupBy('product_variants.id', 'product_variants.product_id', 'product_variants.price');

            // Thêm search filter cho low selling products
            if ($search) {
                $lowSellingQuery->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Thêm sorting cho low selling products
            switch ($sortBy) {
                case 'name':
                    $lowSellingQuery->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->orderBy('products.name', 'asc');
                    break;
                case 'price':
                    $lowSellingQuery->orderBy('product_variants.price', 'asc');
                    break;
                case 'created_at':
                    $lowSellingQuery->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->orderBy('products.created_at', 'desc');
                    break;
                default: // total_sold
                    $lowSellingQuery->orderBy('total_sold', 'asc');
            }

            $lowSellingProducts = $lowSellingQuery->take($perPageValue)->get();

            // Render HTML partials
            $topProductsHtml = view('partials.top-products', compact('topProducts'))->render();
            $lowProductsHtml = view('partials.low-products', compact('lowSellingProducts'))->render();

            return response()->json([
                'success' => true,
                'topProductsHtml' => $topProductsHtml,
                'lowProductsHtml' => $lowProductsHtml,
                'topCount' => $topProducts->count(),
                'lowCount' => $lowSellingProducts->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in filterProducts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lọc dữ liệu: ' . $e->getMessage()
            ], 500);
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
