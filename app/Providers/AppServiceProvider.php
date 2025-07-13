<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Appointment;
use App\Models\ProductCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Observers\AppointmentObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        // Kích hoạt phân trang dùng Bootstrap
        Paginator::useBootstrap();

        // Đăng ký observer cho Appointment
        Appointment::observe(AppointmentObserver::class);
        View::share('globalCategories', ProductCategory::all());

        View::composer('admin.*', function ($view) {
            $pendingCount = Appointment::where('status', 'pending')->count();
            $view->with('pendingCount', $pendingCount);
        });

        // Thêm biến số lượng đơn hàng chờ xác nhận cho mọi view
        View::composer('*', function ($view) {
            $pendingOrderCount = \App\Models\Order::where('status', 'pending')->count();
            $view->with('pendingOrderCount', $pendingOrderCount);
        });
    }
}
