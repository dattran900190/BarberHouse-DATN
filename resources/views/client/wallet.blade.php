@extends('layouts.ClientLayout')

@section('title-page')
    Gửi yêu cầu hoàn tiền
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="card-header border-0">
                    <h3 class="mb-0 fw-bold">Yêu cầu hoàn tiền</h3>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    {{-- Form gửi yêu cầu hoàn tiền --}}
                    <form action="{{ route('client.wallet.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="order_id" class="form-label">Chọn đơn hàng</label>
                            <select name="order_id" id="order_id" class="form-control" required>
                                <option value="">-- Chọn đơn hàng --</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}">
                                        {{ $order->order_code }} - {{ number_format($order->total_money, 0) }} VNĐ
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Lý do hoàn tiền</label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_name" class="form-label">Tên chủ tài khoản</label>
                            <input type="text" name="bank_account_name" id="bank_account_name" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_number" class="form-label">Số tài khoản</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Tên ngân hàng</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success">Gửi yêu cầu hoàn tiền</button>
                    </form>

                    @if ($orders->isEmpty())
                        <div class="alert alert-info">
                            Bạn không có đơn hàng nào đủ điều kiện hoàn tiền.
                        </div>
                    @endif
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
