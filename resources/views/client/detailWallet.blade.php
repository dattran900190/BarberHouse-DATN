@extends('layouts.ClientLayout')

@section('title-page')
    Ví tài khoản
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="mb-0 fw-bold">Chi tiết ví</h3>
                </div>
                <div class="card-body">
                    <p><strong>Số dư hiện tại:</strong> 2.500.000 VNĐ</p>
                    <h4 class="fw-bold mb-3">Lịch sử giao dịch</h4>
                    <!-- Giao dịch 1: Hoàn tiền -->
                    <div class="transaction-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="transaction-type">Hoàn tiền (Mã đặt lịch: AP12346)</span><br>
                                <span class="transaction-date">Ngày hoàn tiền: 21/06/2025</span>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="text-success">+1.875.000 VNĐ</span>
                            </div>
                        </div>
                    </div>
                    <!-- Giao dịch 2: Rút tiền -->
                    <div class="transaction-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <span class="transaction-type">Rút tiền</span><br>
                                <span class="transaction-date">Ngày rút tiền: 20/06/2025</span>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="text-danger">-1.000.000 VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-outline-secondary btn-sm">Quay lại ví</a>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }

         .transaction-item { background-color: #f8f9fa; }
        .transaction-type { font-weight: bold; }
        .transaction-date { font-size: 0.9em; color: #6c757d; }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
