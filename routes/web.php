<?php

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VolumeController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\ChatController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\PointController;
use App\Http\Controllers\PointHistoryController;
use App\Http\Controllers\Client\WalletController;
use App\Http\Controllers\CustomerImageController;
use App\Http\Controllers\RefundRequestController;
use App\Http\Controllers\BarberScheduleController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\BarberStatisticsController;
use App\Http\Controllers\Client\ClientPostController;
use App\Http\Controllers\Client\ClientBranchController;
use App\Http\Controllers\UserRedeemedVoucherController;
use App\Http\Controllers\Client\ClientProductController;
use App\Http\Controllers\Client\ForgotPasswordController;
use App\Http\Controllers\ProfileController as AdminProfileController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Client\BarberController as ClientBarberController;
use App\Http\Controllers\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Client\AppointmentController as ClientAppointmentController;

Broadcast::routes(['middleware' => ['auth']]);

// ==== Auth ====
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postLogin'])->name('postLogin');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('postRegister');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');



Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.sendOtp');

Route::get('verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verifyForm');
Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');


// ==== Trang chủ ====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/barbers', [HomeController::class, 'getBarbers']);
Route::get('/api/products', [HomeController::class, 'getProducts']);

// ==== Chính sách bảo mật ====
Route::get('/chinh-sach-bao-mat', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');

// ==== Chính sách giao dịch ====
Route::get('/chinh-sach-giao-dich', [HomeController::class, 'tradingPolicy'])->name('trading.policy');

// ==== Chính sách vận chuyển ====
Route::get('/chinh-sach-van-chuyen', [HomeController::class, 'shippingPolicy'])->name('shipping.policy');

// ==== Chính sách bảo hành - đổi trả ====
Route::get('/chinh-sach-bao-hanh-doi-tra', [HomeController::class, 'warrantyReturnPolicy'])->name('warranty.return.policy');

// ==== Chat AI ====
Route::post('/chat-ai', [ChatController::class, 'chatAI'])->name('chat.ai');
Route::get('/chat-history', [ChatController::class, 'getChatHistory'])->name('chat.history');
Route::delete('/chat-history', [ChatController::class, 'clearChatHistory'])->name('chat.clear');
// == Liên hệ ==
Route::get('/lien-he', [HomeController::class, 'contact'])->name('contact');

// == Giỏ hàng ==
Route::get('/gio-hang', [CartController::class, 'show'])->name('cart.show');
Route::post('/gio-hang/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::put('/gio-hang/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/gio-hang/remove/{cartItem}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::put('/gio-hang/update-variant/{cartItem}', [CartController::class, 'updateVariant'])->name('cart.update.variant');

// == Mua ngay ==
Route::match(['get', 'post'], '/mua-ngay', [CartController::class, 'buyNow'])->name('cart.buyNow');
Route::get('/mua-ngay/checkout', [CartController::class, 'showBuyNowCheckout'])->name('cart.buyNow.checkout');

//checkout
Route::get('/thanh-toan', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/thanh-toan/process', [CartController::class, 'processCheckout'])->name('cart.checkout.process');
// Route::get('/dat-hang-thanh-cong', function () {
//     return view('client.order-success');
// })->name('order.success');
Route::patch('/orders/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('client.orders.cancel');

// ==== Đặt lịch ====
Route::get('/dat-lich', [ClientAppointmentController::class, 'index'])->name('dat-lich');
Route::post('/dat-lich', [ClientAppointmentController::class, 'store'])->name('dat-lich.store');
Route::post('/dat-lich/verify-otp', [ClientAppointmentController::class, 'verifyOtp'])->name('dat-lich.verifyOtp');
Route::get('/get-barbers-by-branch/{branch_id}', [ClientAppointmentController::class, 'getBarbersByBranch'])->name('getBarbersByBranch');
Route::get('/get-available-barbers-by-date/{branch_id}/{date}/{time}/{service_id}', [ClientAppointmentController::class, 'getAvailableBarbersByDate'])->name('getAvailableBarbersByDate');
Route::get('/confirm-booking/{token}', [ClientAppointmentController::class, 'confirmBooking'])->name('confirm.booking');
Route::get('/xac-nhan-dat-lich', [ClientAppointmentController::class, 'showBookingConfirmed'])->name('booking.confirmed');

// ==== Profile ====
Route::get('/cai-dat-tai-khoan', [ProfileController::class, 'index'])->name('cai-dat-tai-khoan');
Route::post('/store-errors', function (Request $request) {
    session()->flash('errors', $request->input('errors'));
    return response()->json(['success' => true]);
})->name('store.errors');

// == Lịch sử đặt lịch ==
Route::get('/lich-su-dat-lich', [ClientAppointmentController::class, 'appointmentHistory'])->name('client.appointmentHistory');
Route::get('/lich-su-dat-lich/{id}', [ClientAppointmentController::class, 'detailAppointmentHistory'])->name('client.detailAppointmentHistory');
Route::patch('lich-su-dat-lich/{appointment}/cancel', [ClientAppointmentController::class, 'cancel'])->name('client.appointments.cancel');
Route::get('/lich-su-dat-lich/huy/{id}', [ClientAppointmentController::class, 'showCancelledAppointment'])->name('client.cancelledAppointment.show');

// ==== Chi nhánh ====
Route::get('/chi-nhanh', [ClientBranchController::class, 'index'])->name('client.branch');
Route::get('/chi-nhanh/{id}', [ClientBranchController::class, 'detail'])->name('client.detailBranch');

// Đặt route danh sách
Route::get('/bai-viet', [ClientPostController::class, 'index'])->name('client.posts');
Route::get('/bai-viet-chi-tiet/{id}', [ClientPostController::class, 'detail'])->name('client.detailPost');

// Đánh giá
Route::post('/appointments/{appointment}/review', [ClientReviewController::class, 'submitReview'])->name('client.submitReview');

// == Sản phẩm ==
Route::get('/san-pham', [ClientProductController::class, 'index'])->name('client.product');
Route::get('/chi-tiet-san-pham/{id}', [ClientProductController::class, 'show'])->name('client.product.detail');

// == Thợ cắt tóc ==
Route::get('/tho-cat', [ClientBarberController::class, 'index'])->name('client.listBarber');
Route::get('/tho-cat/{id}', [ClientBarberController::class, 'show'])->name('client.detailBarber');

// == Đổi điểm ==
Route::get('/doi-diem', [PointController::class, 'redeemForm'])->name('client.redeem');
Route::post('/doi-diem', [PointController::class, 'redeem'])->name('client.redeem.store');

// == Lịch sử đơn hàng ==
Route::get('/lich-su-don-hang', [ClientOrderController::class, 'index'])->name('client.orderHistory');
Route::get('/chi-tiet-don-hang/{order}', [ClientOrderController::class, 'show'])->name('client.detailOrderHistory');


// == hoàn tiền ==
Route::get('hoan-tien', [WalletController::class, 'index'])->name('client.detailWallet');
Route::get('hoan-tien/create', [WalletController::class, 'create'])->name('client.wallet');
Route::post('hoan-tien', [WalletController::class, 'store'])->name('client.wallet.store');


// == Thanh toán ==
Route::match(['get', 'post'], '/payment/vnpay', [PaymentController::class, 'vnpayPayment'])->name('client.payment.vnpay');
Route::get('/payment/vnpay/callback', [PaymentController::class, 'vnpayCallback'])->name('client.payment.vnpay.callback');

Route::match(['get', 'post'], '/payment/vnpay/order', [PaymentController::class, 'vnpayOrderPayment'])->name('client.payment.vnpay.order');
Route::get('/payment/vnpay/order/callback', [PaymentController::class, 'vnpayOrderCallback'])->name('client.payment.vnpay.order.callback');

// Route cho VNPAY checkout callback
Route::get('/payment/vnpay/checkout/callback', [CartController::class, 'vnpayCheckoutCallback'])->name('client.payment.vnpay.checkout.callback');

Route::middleware(['auth', 'role'])->prefix('admin')->group(function () {
    Route::get('barber-schedules/branch/{branchId}', [BarberScheduleController::class, 'showBranch'])
        ->name('barber_schedules.showBranch');

    Route::middleware('branch.admin')->group(function () {
        Route::resource('barber_schedules', BarberScheduleController::class)
            ->except(['index', 'show']);
    });

    // Nếu bạn vẫn muốn index và show có thể xem được bình thường cho tất cả user đăng nhập
    Route::resource('barber_schedules', BarberScheduleController::class)
        ->only(['index', 'show']);
    // ==== Admin Dashboard ====
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Hiển thị giao diện Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==== Profile ====
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.update');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.password');


    // Hiển thị giao diện danh sách Thợ cắt tóc
    Route::resource('barbers', BarberController::class);
    Route::patch('/barbers/{id}/soft-delete', [BarberController::class, 'softDelete'])->name('barbers.softDelete');
    Route::post('admin/barbers/{id}/restore', [BarberController::class, 'restore'])->name('barbers.restore');

    // Hoàn tiền
    Route::resource('refunds', RefundRequestController::class);
    Route::patch('/refunds/{id}/soft-delete', [RefundRequestController::class, 'softDelete'])->name('refunds.softDelete');
    Route::post('/refunds/{id}/restore', [RefundRequestController::class, 'restore'])->name('refunds.restore');

    // Route::put('/refunds/{refund}', [RefundRequestController::class, 'update'])->name('refunds.update');

    // ==== Đơn hàng ====
    Route::resource('orders', OrderController::class)->names('admin.orders');
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('admin.orders.confirm');
    Route::put('/{order}/ship', [OrderController::class, 'ship'])->name('admin.orders.ship');
    Route::put('/{order}/complete', [OrderController::class, 'complete'])->name('admin.orders.complete');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('admin.orders.update'); // Sử dụng PUT cho update
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    // ==== Lịch sử điểm ====
    Route::get('/point_histories', [PointHistoryController::class, 'index'])->name('point_histories.index');
    Route::get('/point_histories/user/{id}', [PointHistoryController::class, 'userHistory'])->name('point_histories.user');

    // ==== Dịch vụ ====
    Route::resource('services', ServiceController::class);
    Route::patch('/services/{id}/soft-delete', [ServiceController::class, 'softDelete'])->name('services.softDelete');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::post('/services/{id}/restore', [ServiceController::class, 'restore'])->name('services.restore');

    // ==== settings ====
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings/save', [SettingController::class, 'saveSettings'])->name('client.settings.save');

    // ==== Bình luận ====
    Route::resource('reviews', ReviewController::class);
    Route::patch('/reviews/{id}/soft-delete', [ReviewController::class, 'softDelete'])->name('reviews.softDelete');
    Route::post('/reviews/{id}/restore', [ReviewController::class, 'restore'])->name('reviews.restore');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // ==== Ảnh khách hàng ====
    Route::resource('customer-images', CustomerImageController::class);

    // ==== Chatbot ====
Route::resource('chatbot', AdminChatController::class);
Route::delete('/chatbot/message/{id}', [AdminChatController::class, 'destroyMessage'])->name('chatbot.message.destroy');

    // ==== Đổi điểm voucher ====
    Route::resource('user_redeemed_vouchers', UserRedeemedVoucherController::class);
    Route::get('/user_redeemed_vouchers/{id}', [UserRedeemedVoucherController::class, 'show'])->name('admin.user_redeemed_vouchers.show');

    // ==== Đặt lịch ====
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{appointment}/completed', [AppointmentController::class, 'completed'])->name('appointments.completed');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/approve-cancel', [AppointmentController::class, 'approveCancel'])->name('appointments.approve-cancel');
    Route::post('/appointments/{appointment}/reject-cancel', [AppointmentController::class, 'rejectCancel'])->name('appointments.reject-cancel');
    Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no-show');
    Route::get('/appointments/cancelled/{cancelledAppointment}', [AppointmentController::class, 'showCancelled'])->name('appointments.show_cancelled');

    // ==== Thống kê lịch thợ ====
    Route::get('/barber-statistics', [BarberStatisticsController::class, 'index'])->name('barber_statistics.index');
    Route::get('/barber-statistics/{barber}', [BarberStatisticsController::class, 'show'])->name('barber_statistics.show');
    // Route::get('/barber-statistics/export', [BarberStatisticsController::class, 'export'])->name('barber_statistics.export');

    // ==== Bài viết ====
    Route::resource('posts', PostController::class);
    Route::patch('/posts/{id}/soft-delete', [PostController::class, 'softDelete'])->name('posts.softDelete');
    Route::post('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.forceDelete');

    // ==== Danh muc ====
    Route::resource('product_categories', ProductCategoryController::class);
    Route::patch('product_categories/{id}/soft-delete', [ProductCategoryController::class, 'softDelete'])->name('product_categories.softDelete');
    Route::post('product_categories/{id}/restore', [ProductCategoryController::class, 'restore'])->name('product_categories.restore');
    Route::delete('product_categories/{id}/force-delete', [ProductCategoryController::class, 'destroy'])->name('product_categories.destroy');
    // ==== Checkins ====
    Route::resource('checkins', CheckinController::class);

    // ==== Volums ====
    Route::resource('volumes', VolumeController::class)->names('admin.volumes');
    Route::post('volumes/{id}/restore', [VolumeController::class, 'restore'])->name('admin.volumes.restore');
    Route::delete('volumes/{id}/force-delete', [VolumeController::class, 'forceDelete'])->name('admin.volumes.forceDelete');

    // ==== Banner ====
    Route::resource('banners', BannerController::class);
    Route::patch('banners/{id}/soft-delete', [BannerController::class, 'softDelete'])->name('banners.softDelete');
    Route::post('banners/{id}/restore', [BannerController::class, 'restore'])->name('banners.restore');
    // Route::delete('banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // ==== Chi nhánh ====
    Route::resource('branches', BranchController::class);
    Route::patch('admin/branches/{id}/soft-delete', [BranchController::class, 'softDelete'])
        ->name('branches.softDelete');
    Route::post('admin/branches/{id}/restore', [BranchController::class, 'restore'])
        ->name('branches.restore');
    Route::delete('admin/branches/{id}/force-delete', [BranchController::class, 'forceDelete'])
        ->name('branches.forceDelete');

    // ==== Lịch trình ====
    Route::resource('barber_schedules', BarberScheduleController::class);
    Route::get('barber-schedules/branch/{branchId}', [BarberScheduleController::class, 'showBranch'])->name('barber_schedules.showBranch');
    Route::get('barber-schedules/create/{branchId}', [BarberScheduleController::class, 'create'])->name('barber_schedules.createForBranch');
    Route::post('barber-schedules', [BarberScheduleController::class, 'store'])->name('barber_schedules.store');
    Route::get('barber-schedules/holiday/edit/{id}', [BarberScheduleController::class, 'editHoliday'])->name('barber_schedules.editHoliday');
    Route::put('barber-schedules/holiday/update/{id}', [BarberScheduleController::class, 'updateHoliday'])->name('barber_schedules.updateHoliday');
    Route::delete('/barber-schedules/delete-holiday/{id}', [BarberScheduleController::class, 'deleteHoliday'])
        ->name('barber_schedules.deleteHoliday');


    // ==== Người dùng ====
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::resource('users', UserController::class);
    Route::delete('users/{user}/soft-delete', [UserController::class, 'softDelete'])->name('users.softDelete');
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');

    // ==== Mã giảm giá ====
    Route::resource('promotions', PromotionController::class);
    Route::delete('/{id}/soft-delete', [PromotionController::class, 'softDelete'])->name('promotions.softDelete');
    Route::put('/{id}/restore', [PromotionController::class, 'restore'])->name('promotions.restore');
    // ==== Sản phẩm ====
    Route::resource('products', ProductController::class)->names('admin.products');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('admin.products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('admin.products.forceDelete');
    Route::get('/products/search', [ProductController::class, 'search'])->name('admin.products.search');
    Route::post('admin/product-variants/{id}/restore', [ProductController::class, 'restoreVariant'])->name('admin.product-variants.restore');
    Route::post('admin/product-variants/{id}/soft-delete', [ProductController::class, 'softDeleteVariant'])->name('admin.product-variants.softDelete');
    Route::delete('admin/product-variants/{id}/hard-delete', [ProductController::class, 'hardDeleteVariant'])->name('admin.product-variants.hardDelete');
});

// ==== profile ====
Route::get('/profile', [ProfileController::class, 'index'])->name('client.profile');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('client.update');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('client.password');

// Form tạo nghỉ lễ
Route::get('/barber-schedules/holiday/create', [BarberScheduleController::class, 'createHoliday'])->name('barber_schedules.createHoliday');
// Thêm vào cuối file routes/web.php
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
