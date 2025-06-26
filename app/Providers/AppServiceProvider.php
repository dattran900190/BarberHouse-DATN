<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Appointment;
use App\Models\ProductCategory;
use App\Observers\AppointmentObserver;
use Illuminate\Support\Facades\View;

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

        // số lượng lịch chưa xác nhận
        // View::composer('vendor.adminlte.partials.sidebar.menu', function ($view) {
        //     $pendingCount = Appointment::where('status', 'pending')->count();
        //     $view->with('pendingCount', $pendingCount);
        // });
    }
}
