@extends('adminlte::page')

@section('title', 'Quản lý Checkin')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Check-ins</h3>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>STT</th>
                        <th>Mã Check-in</th>
                        <th>Trạng thái</th>
                        <th>Thời gian Check-in</th>
                        <th>Khách hàng</th>
                        <th>Thời gian hẹn</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse ($checkins as $index => $checkin)
                        <tr>
                            <td class="text-center">{{ $checkins->firstItem() + $index }}</td>
                            <td class="text-center"><strong>{{ $checkin->qr_code_value }}</strong></td>
                            <td class="text-center">
                                @if($checkin->is_checked_in)
                                    <span class="badge bg-success">Đã Check-in</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa Check-in</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $checkin->checkin_time ? $checkin->checkin_time->format('H:i d/m/Y') : '-' }}
                            </td>
                                                        <td>
                                {{ optional($checkin->appointment->user)->name ?? '-' }}
                                @php
                                    $type = optional($checkin->appointment->user)->type;
                                @endphp
                                @if ($type === 'vip')
                                    <span class="badge bg-danger ms-2">VIP</span>
                                @else
                                    <span class="badge bg-secondary ms-2">Thường</span>
                                @endif
                            </td>

                            <td>{{ optional($checkin->appointment)->appointment_time ? \Carbon\Carbon::parse($checkin->appointment->appointment_time)->format('H:i d/m/Y') : '-' }}</td>
                            <td class="text-center">{{ $checkin->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('checkins.show', $checkin->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có dữ liệu check-in nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $checkins->links() }}
            </div>
        </div>
    </div>
@endsection
