@extends('adminlte::page')

@section('title', 'Quản lý Checkin')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Check-ins</h3>
        </div>

        {{-- THÔNG BÁO --}}
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM CHECK-IN TRÀN TOÀN BỘ --}}
            <div class="mb-4">
                <form action="{{ route('checkins.store') }}" method="POST" class="w-100">
                    @csrf
                    <div class="form-group w-100 text-start">
                        <label for="code" class="fw-bold">Xác nhận mã Check-in</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror w-100"
                            maxlength="6" required placeholder="Nhập mã gồm 6 chữ số">

                        @error('code')
                            <span class="invalid-feedback d-block mt-1" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle"></i> Xác nhận
                        </button>
                    </div>
                </form>

            </div>

            {{-- BẢNG DỮ LIỆU --}}
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
                                @if ($checkin->is_checked_in)
                                    <span class="badge bg-success">Đã Check-in</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa Check-in</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $checkin->checkin_time ? \Carbon\Carbon::parse($checkin->checkin_time)->format('H:i d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">{{ $checkin->appointment->user->name ?? '-' }}</td>
                            <td>
                                {{ optional($checkin->appointment)->appointment_time ? \Carbon\Carbon::parse($checkin->appointment->appointment_time)->format('H:i d/m/Y') : '-' }}
                            </td>
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
