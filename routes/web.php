<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::middleware(['auth', 'role'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    // Hiển thị giao diện danh sách Dịch vụ
    Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index');
    // Hiển thị giao diện danh sách Thợ cắt tóc
    Route::resource('barbers', BarberController::class);
    // 
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('/posts', [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('admin.posts.store');
    Route::get('/admin/posts/{post}', [PostController::class, 'show'])->name('admin.posts.show');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('admin.posts.destroy');
});

// Hiển thị giao diện danh sách Chi nhánh
Route::get('/admin/branches', [BranchController::class, 'index'])->name('admin.branches.index');
Route::get('/admin/branches/create', [BranchController::class, 'create'])->name('admin.branches.create');
Route::post('/admin/branches', [BranchController::class, 'store'])->name('admin.branches.store');
Route::get('/admin/branches/{branch}', [BranchController::class, 'show'])->name('admin.branches.show');
Route::get('/admin/branches/{branch}/edit', [BranchController::class, 'edit'])->name('admin.branches.edit');
Route::put('/admin/branches/{branch}', [BranchController::class, 'update'])->name('admin.branches.update');
Route::delete('/admin/branches/{branch}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');
Route::get('/admin/branches/search', [BranchController::class, 'search'])->name('admin.branches.search');


// Hiển thị giao diện danh sách Dịch vụ
Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
Route::get('/admin/services/{service_id}', [ServiceController::class, 'show'])->name('admin.services.show');
Route::get('/admin/services/{service_id}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
Route::put('/admin/services/{service_id}', [ServiceController::class, 'update'])->name('admin.services.update');
Route::delete('/admin/services/{service_id}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
Route::get('/admin/services/search', [ServiceController::class, 'search'])->name('admin.services.search');


// Hiển thị giao diện danh sách người dùng
Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
Route::get('/admin/users/search', [UserController::class, 'search'])->name('admin.users.search');

// Route riêng lẻ cho quản lý sản phẩm (không yêu cầu đăng nhập)
Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
Route::get('/admin/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
Route::get('/admin/products/search', [ProductController::class, 'search'])->name('admin.products.search');
