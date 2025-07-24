<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Setting;
use App\Models\Appointment;
use App\Models\RefundRequest;
use App\Models\ProductCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Observers\AppointmentObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

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
        Broadcast::routes(['middleware' => ['auth']]);

        // Xác thực kênh riêng tư
        Broadcast::channel('private-user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });
        
        // Kích hoạt phân trang dùng Bootstrap
        Paginator::useBootstrap();

        // Đăng ký observer cho Appointment
        Appointment::observe(AppointmentObserver::class);
        View::share('globalCategories', ProductCategory::all());

        View::composer('admin.*', function ($view) {
            $pendingCount = Appointment::where('status', 'pending')->count();
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
