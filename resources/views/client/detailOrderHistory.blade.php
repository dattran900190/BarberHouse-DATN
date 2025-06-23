@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đơn hàng
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card order-detail shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-0">
                    <h3 class="mb-0 fw-bold">Chi tiết đơn hàng</h3>
                    <div>
                        <strong>Trạng thái:</strong> <span class="status-label status-processing">Đang xử lý</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> OD649344</p>
                            <p><strong>Ngày đặt hàng:</strong> 05/31/2025</p>
                        </div>

                    </div>
                    <h5 class="fw-bold">Danh sách sản phẩm</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sáp VIP</td>
                                <td>2</td>
                                <td>$161.57</td>
                                <td>$323.13</td>
                            </tr>
                            <tr>
                                <td>Dầu gội kokomi</td>
                                <td>2</td>
                                <td>$161.57</td>
                                <td>$323.13</td>
                            </tr>
                            <!-- Thêm các sản phẩm khác nếu có -->
                        </tbody>
                    </table>
                    <h5 class="fw-bold mt-4">Thông tin giao hàng</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Địa chỉ giao hàng:</strong> 123 Đường ABC, Quận XYZ, TP. HCM</p>
                            <p><strong>Phương thức giao hàng:</strong> Giao hàng tiêu chuẩn</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày dự kiến giao hàng:</strong> 06/05/2025</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold">Tổng tiền: $323.13</h5>
                    <div>
                        <a href="#" class="btn btn-outline-danger btn-sm me-2">Hủy đơn hàng</a>
                        <a href="{{ route('client.orderHistory') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
