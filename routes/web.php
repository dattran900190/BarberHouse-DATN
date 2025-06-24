<?php

use App\Http\Controllers\RefundRequestController;
use App\Http\Controllers\Client\WalletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VolumeController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\PointController;
use App\Http\Controllers\PointHistoryController;
use App\Http\Controllers\BarberScheduleController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\Client\ClientPostController;
use App\Http\Controllers\Client\ClientBranchController;
use App\Http\Controllers\UserRedeemedVoucherController;
use App\Http\Controllers\Client\ClientProductController;
use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;

// ==== Auth ====
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ==== Trang chủ ====
Route::get('/', [HomeController::class, 'index'])->name('home');

// Giỏ hàng
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::put('/cart/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::put('/cart/update-variant/{cartItem}', [CartController::class, 'updateVariant'])->name('cart.update.variant');


Route::get('/dat-lich', [ClientAppointmentController::class, 'index'])->name('dat-lich');
Route::post('/dat-lich', [ClientAppointmentController::class, 'store'])->name('dat-lich.store');
Route::get('/get-barbers-by-branch/{branch_id}', [ClientAppointmentController::class, 'getBarbersByBranch'])->name('getBarbersByBranch');
Route::get('/get-available-barbers-by-date/{branch_id}/{date}/{time?}/{service_id?}', [ClientAppointmentController::class, 'getAvailableBarbersByDate']);
Route::get('/cai-dat-tai-khoan', [ProfileController::class, 'index'])->name('cai-dat-tai-khoan');

// == Lịch sử đặt lịch ==
Route::get('/lich-su-dat-lich', [ClientAppointmentController::class, 'appointmentHistory'])->name('client.appointmentHistory');
Route::get('/chi-tiet-dat-lich', [ClientAppointmentController::class, 'detailAppointmentHistory'])->name('client.detailAppointmentHistory');


Route::get('/thanh-toan', function () {
    return view('client.checkout');
});

// web.php
Route::get('/chi-nhanh', [ClientBranchController::class, 'index'])->name('client.branch');
Route::get('/chi-nhanh/{id}', [ClientBranchController::class, 'detail'])->name('client.detailBranch');

// Đặt route danh sách
Route::get('/bai-viet', [ClientPostController::class, 'index'])->name('client.posts');
Route::get('/bai-viet-chi-tiet/{id}', [ClientPostController::class, 'detail'])->name('client.detailPost');

// == Sản phẩm ==
Route::get('/san-pham', [ClientProductController::class, 'index'])->name('client.product');
Route::get('/chi-tiet-san-pham/{id}', [ClientProductController::class, 'show'])->name('client.product.detail');

// == Thợ cắt tóc ==
Route::get('/tho-cat', [ClientBarberController::class, 'index'])->name('client.listBarber');
Route::get('/tho-cat/{slug}', [ClientBarberController::class, 'show'])->name('client.detailBarber');

// == Đổi điểm ==
Route::get('/doi-diem', [PointController::class, 'redeemForm'])->name('client.redeem');
Route::post('/doi-diem', [PointController::class, 'redeem'])->name('client.redeem.store');

// == Lịch sử đơn hàng ==
Route::get('/lich-su-don-hang', [ClientOrderController::class, 'index'])->name('client.orderHistory');
Route::get('/chi-tiet-don-hang', [ClientOrderController::class, 'show'])->name('client.detailOrderHistory');

// == ví tài khoản ==
Route::get('refunds', [WalletController::class, 'index'])->name('client.detailWallet');
Route::get('refunds/create', [WalletController::class, 'create'])->name('client.wallet');
Route::post('refunds', [WalletController::class, 'store'])->name('client.wallet.store');

Route::middleware(['auth', 'role'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
        Route::get('barber-schedules/branch/{branchId}', [BarberScheduleController::class, 'showBranch'])
            ->name('barber_schedules.showBranch');

        Route::middleware('branch.admin')->group(function () {
            Route::resource('barber_schedules', BarberScheduleController::class)
                ->except(['index', 'show']);
        });

        // Nếu bạn vẫn muốn index và show có thể xem được bình thường cho tất cả user đăng nhập
        Route::resource('barber_schedules', BarberScheduleController::class)
            ->only(['index', 'show']);
    })->name('dashboard');

    // Hiển thị giao diện danh sách Thợ cắt tóc
    Route::resource('barbers', BarberController::class);

    Route::resource('refunds', RefundRequestController::class);

    // ==== Đơn hàng ====
    Route::resource('orders', OrderController::class);

    // ==== Lịch sử điểm ====
    Route::get('/point_histories', [PointHistoryController::class, 'index'])->name('point_histories.index');
    Route::get('/point_histories/user/{id}', [PointHistoryController::class, 'userHistory'])->name('point_histories.user');

    // ==== Dịch vụ ====
    Route::resource('services', ServiceController::class);

    // ==== Bình luận ====
    Route::resource('reviews', ReviewController::class);


    Route::resource('user_redeemed_vouchers', UserRedeemedVoucherController::class);

    // ==== Thanh toán ====
    Route::resource('payments', PaymentController::class);

    // ==== Đặt lịch ====
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');

    // ==== Bài viết ====
    Route::resource('posts', PostController::class);

    // ==== Danh muc ====
    Route::resource('product_categories', ProductCategoryController::class);

    // ==== Checkins ====
    Route::resource('checkins', CheckinController::class);

    // ==== Volums ====
    Route::resource('volumes', VolumeController::class)->names('admin.volumes');
    // ==== Banner ====
    Route::resource('banners', BannerController::class);

    // ==== Chi nhánh ====
    Route::resource('branches', BranchController::class);
    // ==== Lịch trình ====
    Route::resource('barber_schedules', BarberScheduleController::class);
    Route::get('barber-schedules/branch/{branchId}', [BarberScheduleController::class, 'showBranch'])
        ->name('barber_schedules.showBranch');
    Route::get('barber-schedules/create/{branchId}', [BarberScheduleController::class, 'create'])
        ->name('barber_schedules.createForBranch');

    // ==== Người dùng ====
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // ==== Mã giảm giá ====
    Route::resource('promotions', PromotionController::class);

    // ==== Sản phẩm ====
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/products/search', [ProductController::class, 'search'])->name('admin.products.search');
});

// ==== profile ====
Route::get('/profile', [ProfileController::class, 'index'])->name('client.profile');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('client.update');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('client.password');
