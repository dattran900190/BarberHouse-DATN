@extends('adminlte::page')
@section('title', 'Gán voucher cho người dùng')

@section('content')
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">🎁 Gán voucher cho người dùng</h4>
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <div class="card-body">
            <form action="{{ route('user_redeemed_vouchers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="user_id"><strong>👤 Người dùng:</strong></label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="promotion_id"><strong>🏷️ Khuyến mãi:</strong></label>
                    <select name="promotion_id" id="promotion_id" class="form-control" required>
                        <option value="">-- Chọn khuyến mãi --</option>
                        @foreach($promotions as $promotion)
                            <option value="{{ $promotion->id }}">{{ $promotion->code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_used" value="1" class="form-check-input" id="is_used"
                        {{ old('is_used') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_used">Đã dùng</label>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fas fa-plus-circle mr-1"></i> Gán voucher
                    </button>
                    <a href="{{ route('user_redeemed_vouchers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
