@extends('adminlte::page')

@section('title', 'Chi tiết Thợ Cắt Tóc')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title">Chi tiết Thợ Cắt Tóc</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Cột ảnh -->
                <div class="col-md-4 text-center">
                    @if ($barber->avatar)
                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar" class="img-fluid rounded"
                            style="max-height: 300px;">
                    @else
                        <p>Không có ảnh</p>
                    @endif
                </div>

                <!-- Cột thông tin -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <p>{{ $barber->name }}</p>
                    </div>

                    <div class="form-group">
                        <label>Trình độ</label>
                        <p>{{ $barber->skill_level }}</p>
                    </div>

                    <div class="form-group">
                        <label>Đánh giá trung bình</label>
                        <p>{{ $barber->rating_avg }}</p>
                    </div>

                    <div class="form-group">
                        <label>Hồ sơ</label>
                        <p>{{ $barber->profile }}</p>
                    </div>

                    <div class="form-group">
                        <label>Chi nhánh</label>
                        <p>{{ $barber->branch->name ?? 'Chưa có chi nhánh' }}</p>
                    </div>

                    <a href="{{ route('barbers.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
@endsection
