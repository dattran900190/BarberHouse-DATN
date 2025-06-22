@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đặt lịch
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card order-detail shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-0">
                    <h3 class="mb-0 fw-bold">Chi tiết đặt lịch</h3>
                    <div>
                        <strong>Trạng thái:</strong> <span class="status-label status-processing">Đang chờ</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Mã đặt lịch:</strong> AP12345</p>
                            <p><strong>Thời gian:</strong> 05/31/2025 10:00 AM</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Dịch vụ:</strong> Cắt tóc nam</p>
                            <p><strong>Thợ:</strong> Nguyễn Văn A</p>
                            <p><strong>Chi nhánh:</strong> Quận 1</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold">Tổng tiền: $50.00</h5>
                    <div>
                        <a href="#" class="btn btn-outline-danger btn-sm me-2">Hủy đặt lịch</a>
                        <a href="{{ route('client.appointmentHistory') }}" class="btn btn-outline-secondary btn-sm">Quay
                            lại</a>
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
