@extends('adminlte::page')
@section('title', 'Sửa voucher đã đổi')

@section('content')
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">✏️ Sửa voucher đã đổi</h4>
        </div>
        <div class="card-body">
            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Lỗi!</strong> Vui lòng kiểm tra lại thông tin.<br><br>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user_redeemed_vouchers.update', $userRedeemedVoucher->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="user_id"><strong>👤 Người dùng:</strong></label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Chọn người dùng --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ $userRedeemedVoucher->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} (ID: {{ $user->id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="promotion_id"><strong>🏷️ Khuyến mãi:</strong></label>
                    <select name="promotion_id" id="promotion_id" class="form-control" required>
                        <option value="">-- Chọn khuyến mãi --</option>
                        @forelse($promotions as $promotion)
                            <option value="{{ $promotion->id }}"
                                {{ $userRedeemedVoucher->promotion_id == $promotion->id ? 'selected' : '' }}>
                                {{ $promotion->code }} (ID: {{ $promotion->id }})
                            </option>
                        @empty
                            <option disabled>Không có khuyến mãi nào</option>
                        @endforelse
                    </select>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="is_used" value="1" class="form-check-input" id="is_used"
                        {{ $userRedeemedVoucher->is_used ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_used">Đã dùng</label>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-warning font-weight-bold text-white">
                        <i class="fas fa-save mr-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('user_redeemed_vouchers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
