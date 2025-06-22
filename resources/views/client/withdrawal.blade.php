@extends('layouts.ClientLayout')

@section('title-page')
    Rút tiền
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="mb-0 fw-bold">Rút tiền </h3>
                </div>
                <div class="card-body">
                    <p><strong>Số dư hiện tại:</strong> 2.500.000 VNĐ</p>
                    <form>
                        <div class="mb-3">
                            <label for="withdrawalAmount" class="form-label">Số tiền muốn rút (VNĐ)</label>
                            <input type="number" class="form-control" id="withdrawalAmount" placeholder="Nhập số tiền"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="bankAccountName" class="form-label">Tên tài khoản ngân hàng</label>
                            <input type="text" class="form-control" id="bankAccountName" value="Nguyễn Văn A" required>
                        </div>
                        <div class="mb-3">
                            <label for="bankAccountNumber" class="form-label">Số tài khoản ngân hàng</label>
                            <input type="text" class="form-control" id="bankAccountNumber" value="123456789" required>
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Tên ngân hàng</label>
                            <input type="text" class="form-control" id="bankName" value="Ngân hàng Mẫu" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận rút tiền</button>
                    </form>
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

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
