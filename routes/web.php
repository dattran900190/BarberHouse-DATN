<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PostController;
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
    return view('welcome');
})->name('home');
Route::middleware(['auth', 'role'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Hiển thị giao diện danh sách Thợ cắt tóc
    Route::resource('barbers', BarberController::class);

    Route::resource('orders', OrderController::class);

    
    // ==== Dịch vụ ====
    Route::resource('services', ServiceController::class);

    // ==== Bình luận ====
    Route::resource('reviews', ReviewController::class);

    // ==== Thanh toán ====
    Route::resource('payments', PaymentController::class);

    // ==== Đặt lịch ====
    Route::resource('appointments', AppointmentController::class);

    // ==== Bài viết ====
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('admin.posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('admin.posts.destroy');

    // ==== Chi nhánh ====
    Route::get('/branches', [BranchController::class, 'index'])->name('admin.branches.index');
    Route::get('/branches/create', [BranchController::class, 'create'])->name('admin.branches.create');
    Route::post('/branches', [BranchController::class, 'store'])->name('admin.branches.store');
    Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('admin.branches.show');
    Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('admin.branches.edit');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('admin.branches.update');
    Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');
    Route::get('/branches/search', [BranchController::class, 'search'])->name('admin.branches.search');

    // ==== Người dùng ====
    Route::resource('users', UserController::class);
    // Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    // Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    // Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    // Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    // Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    // Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    // Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    // Route::get('/users/search', [UserController::class, 'search'])->name('admin.users.search');

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
