@extends('adminlte::page')

@section('title', 'Chi tiết Check-in')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title">Chi tiết Check-in</h3>
            <a href="{{ route('checkins.index') }}" class="btn btn-secondary float-end">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Mã Check-in:</dt>
                <dd class="col-sm-9"><strong>{{ $checkin->qr_code_value }}</strong></dd>

                <dt class="col-sm-3">Trạng thái:</dt>
                <dd class="col-sm-9">
                    @if($checkin->is_checked_in)
                        <span class="badge bg-success">Đã Check-in</span>
                    @else
                        <span class="badge bg-warning text-dark">Chưa Check-in</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Thời gian Check-in:</dt>
                <dd class="col-sm-9">{{ $checkin->checkin_time ? $checkin->checkin_time->format('H:i d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Khách hàng:</dt>
                <dd class="col-sm-9">{{ optional($checkin->appointment->user)->name ?? '-' }}</dd>

                <dt class="col-sm-3">Thời gian hẹn:</dt>
                <dd class="col-sm-9">{{ optional($checkin->appointment)->appointment_time ? \Carbon\Carbon::parse($checkin->appointment->appointment_time)->format('H:i d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Ngày tạo:</dt>
                <dd class="col-sm-9">{{ $checkin->created_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>
@endsection
