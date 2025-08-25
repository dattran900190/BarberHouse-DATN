<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Banner;
use App\Models\Setting;
use App\Models\Appointment;
use App\Models\RefundRequest;
use App\Models\ProductCategory;
use App\Models\Review;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Observers\AppointmentObserver;
use App\Observers\ReviewObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Đăng ký broadcasting routes với middleware auth
        Broadcast::routes(['middleware' => ['auth']]);

        // Xác thực kênh riêng tư cho từng user
        Broadcast::channel('user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });
        Carbon::setLocale('vi');

        // Kích hoạt phân trang dùng Bootstrap
        Paginator::useBootstrap();
        Review::observe(ReviewObserver::class);

        // Đăng ký observer cho Appointment
        Appointment::observe(AppointmentObserver::class);

        if (Schema::hasTable('product_categories')) {
            View::share('globalCategories', ProductCategory::all());
        }

        View::composer('admin.*', function ($view) {
            // Kiểm tra role của user hiện tại
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'admin_branch' && $user->branch_id) {
                    // Admin chi nhánh chỉ thấy lịch hẹn của chi nhánh mình
                    $pendingCount = Appointment::where('status', 'pending')
                        ->where('branch_id', $user->branch_id)
                        ->count();
                } else {
                    // Admin chính thấy tất cả lịch hẹn
                    $pendingCount = Appointment::where('status', 'pending')->count();
                }
            } else {
                $pendingCount = 0;
            }
            $view->with('pendingCount', $pendingCount);
        });

        View::composer('admin.*', function ($view) {
            $pendingRefundCount = RefundRequest::where('refund_status', 'pending')->count();
            $view->with('pendingRefundCount', $pendingRefundCount);
        });

        // Thêm biến số lượng đơn hàng chờ xác nhận cho mọi view
        View::composer('*', function ($view) {
            $pendingOrderCount = \App\Models\Order::where('status', 'pending')->count();
            $view->with('pendingOrderCount', $pendingOrderCount);
        });

        // banners
        View::composer('client.*', function ($view) {
            $banners = Banner::where('is_active', 1)
                ->orderBy('id', 'desc')
                ->get();
            $view->with('banners', $banners);
        });

        // Chia sẻ các liên kết mạng xã hội với tất cả các view
        View::composer('*', function ($view) {
            $social_links = Setting::where('type', 'url')->get()->pluck('value', 'key');
            $imageSettings = Setting::where('type', 'image')->pluck('value', 'key');
            $view->with([
                'social_links' => $social_links,
                'imageSettings' => $imageSettings,
            ]);
        });
    }
}
