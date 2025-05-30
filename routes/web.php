<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ==== Auth ====
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ==== Trang chủ ====


Route::get('/', function () {
    return view('client.home');
})->name('home');
Route::get('/dat-lich', function () {
    return view('client.booking');
});

Route::get('/gio-hang', function () {
    return view('client.cart');
});

Route::get('/thanh-toan', function () {
    return view('client.checkout');
});

Route::get('/chi-nhanh', function () {
    return view('client.branch');
});

Route::get('/chi-tiet-chi-nhanh', function () {
    return view('client.detailBranch');
});

Route::get('/bai-viet', function () {
    return view('client.post');
});

Route::get('/chi-tiet-bai-viet', function () {
    return view('client.detailPost');
});

Route::get('/san-pham', function () {
    return view('client.product');
});

Route::get('/chi-tiet-san-pham', function () {
    return view('client.detailProduct');
});


Route::middleware(['auth', 'role'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Hiển thị giao diện danh sách Thợ cắt tóc
    Route::resource('barbers', BarberController::class);

    Route::resource('orders', OrderController::class);


    // ==== Dịch vụ ====
    Route::resource('services', ServiceController::class);

  // ==== Dịch vụ ====
    Route::resource('services', ServiceController::class);

    // ==== Bình luận ====
    Route::resource('reviews', ReviewController::class);

    // ==== Thanh toán ====
    Route::resource('payments', PaymentController::class);

    // ==== Đặt lịch ====
    Route::resource('appointments', AppointmentController::class);

    // ==== Bài viết ====

    Route::resource('posts', PostController::class);
 // ==== Danh muc ====
    Route::resource('product_categories', ProductCategoryController::class);


    // ==== Chi nhánh ====
    Route::resource('branches', BranchController::class);

    // ==== Người dùng ====
    Route::resource('users', UserController::class);

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
