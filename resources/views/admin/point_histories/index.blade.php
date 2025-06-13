@extends('adminlte::page')

@section('title', 'Lịch sử tích điểm')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="card-title mb-0">Lịch sử tích điểm</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('point_histories.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên người dùng..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th>Stt</th>
                        <th>Tên người dùng</th>
                        <th>Điểm</th>
                        <th>Loại</th>
                        <th>Mã đặt lịch/Khuyến mãi</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $index => $history)
                        <tr>
                            <td>{{ $loop->iteration + ($histories->currentPage() - 1) * $histories->perPage() }}</td>
                            <td>{{ $history->user->name ?? 'Không có' }}</td>
                            <td>{{ $history->points }}</td>
                            <td>
                                @if ($history->type === 'earned')
                                    <span class="badge bg-success">Tích điểm</span>
                                @elseif ($history->type === 'redeemed')
                                    <span class="badge bg-danger">Đổi điểm</span>
                                @else
                                    <span class="badge bg-secondary">Không xác định</span>
                                @endif
                            </td>
                            <td>
                                @if ($history->type === 'earned' && $history->appointment)
                                    Mã đặt lịch: {{ $history->appointment->appointment_code ?? 'Không rõ' }}
                                @elseif ($history->type === 'redeemed' && $history->promotion)
                                    Mã khuyễn mãi: {{ $history->promotion->code ?? 'Không rõ' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $history->created_at ? $history->created_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @endforeach

                    @if ($histories->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{ $histories->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .input-group input {
            border-right: 0;
        }

        .input-group .input-group-append {
            border-left: 0;
        }

        .input-group button {
            border-radius: 0;
        }
    </style>
@endsection
