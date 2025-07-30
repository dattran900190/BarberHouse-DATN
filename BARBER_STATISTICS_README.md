# Tính năng Thống kê thợ

## 🎯 Mục đích
Tạo hệ thống thống kê thợ để quản lý các ngày nghỉ, đổi ca làm, nghỉ lễ theo chi nhánh, giúp người quản lý nắm rõ lịch của thợ và tự tính lương bên ngoài hệ thống.

## 📊 Các loại thống kê

### 1. **Ngày nghỉ (off)**
- Thợ nghỉ cá nhân
- Có thể trừ lương tùy theo chính sách

### 2. **Nghỉ lễ (holiday)**
- Nghỉ lễ, tết
- **Hưởng nguyên lương, không trừ lương**

### 3. **Đổi ca làm (custom)**
- Thợ làm việc với lịch tùy chỉnh
- Có thời gian bắt đầu và kết thúc cụ thể

## 🏗️ Cấu trúc hệ thống

### **Controller**: `BarberStatisticsController`
- `index()`: Trang danh sách thống kê tổng quan
- `show($barber)`: Chi tiết thống kê từng thợ
- `export()`: Xuất báo cáo Excel

### **Routes**:
```php
Route::get('/barber-statistics', [BarberStatisticsController::class, 'index'])->name('barber_statistics.index');
Route::get('/barber-statistics/{barber}', [BarberStatisticsController::class, 'show'])->name('barber_statistics.show');
Route::get('/barber-statistics/export', [BarberStatisticsController::class, 'export'])->name('barber_statistics.export');
```

### **Views**:
- `resources/views/admin/barber_statistics/index.blade.php`
- `resources/views/admin/barber_statistics/show.blade.php`

## 📈 Tính năng chính

### **1. Thống kê tổng quan**
- Tổng số thợ
- Tổng ngày nghỉ
- Tổng nghỉ lễ
- Tổng đổi ca
- Tổng ngày làm việc

### **2. Bộ lọc**
- Theo tháng/năm
- Theo chi nhánh (admin có thể xem tất cả, admin_branch chỉ xem chi nhánh của mình)

### **3. Thống kê chi tiết theo thợ**
- Danh sách thợ với thống kê từng loại
- Link đến trang chi tiết từng thợ

### **4. Chi tiết từng thợ**
- Thông tin cá nhân thợ
- Thống kê theo tuần
- Chi tiết lịch theo ngày
- Tóm tắt tổng quan

### **5. Xuất báo cáo**
- Xuất Excel với đầy đủ thông tin
- Tên file: `thong_ke_tho_{thang}_{nam}.csv`

## 🔐 Phân quyền

### **Admin (Super Admin)**
- Xem thống kê tất cả chi nhánh
- Lọc theo chi nhánh
- Xuất báo cáo toàn hệ thống

### **Admin Branch**
- Chỉ xem thống kê chi nhánh của mình
- Không thể lọc theo chi nhánh khác
- Xuất báo cáo chi nhánh

## 📋 Dữ liệu thống kê

### **Theo loại lịch**:
- `status = 'off'`: Ngày nghỉ
- `status = 'holiday'`: Nghỉ lễ  
- `status = 'custom'`: Làm việc/đổi ca

### **Thống kê theo tuần**:
- Tuần 1, 2, 3, 4, 5 (tùy theo tháng)
- Thời gian từ ngày đến ngày
- Số lượng từng loại trong tuần

### **Chi tiết theo ngày**:
- Ngày cụ thể
- Thứ trong tuần
- Trạng thái (Nghỉ/Nghỉ lễ/Làm việc)
- Thời gian làm việc (nếu có)
- Ghi chú

## 💡 Lưu ý quan trọng

### **1. Tách biệt với tính lương**
- Hệ thống thống kê này **KHÔNG liên quan** đến tính lương trong code
- Chỉ cung cấp dữ liệu để quản lý tự tính lương bên ngoài

### **2. Nghỉ lễ hưởng nguyên lương**
- Các ngày `status = 'holiday'` được đánh dấu rõ ràng
- Quản lý có thể dễ dàng phân biệt để không trừ lương

### **3. Dữ liệu thực tế**
- Thống kê dựa trên dữ liệu lịch thực tế trong `barber_schedules`
- Không có dữ liệu giả hay mẫu

## 🚀 Cách sử dụng

### **1. Truy cập**
- Menu: Quản lý chi nhánh → Thống kê thợ
- URL: `/admin/barber-statistics`

### **2. Lọc dữ liệu**
- Chọn tháng/năm cần xem
- Chọn chi nhánh (nếu là admin)
- Click "Lọc" để cập nhật

### **3. Xem chi tiết**
- Click "Chi tiết" bên cạnh tên thợ
- Xem thống kê theo tuần và ngày

### **4. Xuất báo cáo**
- Click "Xuất Excel" để tải file CSV
- File chứa đầy đủ thông tin thống kê

## 🔄 Cập nhật tương lai

### **Có thể mở rộng**:
- Thêm biểu đồ thống kê
- Thống kê theo quý/năm
- So sánh giữa các tháng
- Thống kê theo nhóm thợ
- Báo cáo tự động gửi email 