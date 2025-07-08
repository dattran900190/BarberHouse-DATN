@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Chi nhánh')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết Chi nhánh</h3>
        </div>

        <div class="card-body">
            <p><strong>Tên chi nhánh:</strong> {{ $branch->name }}</p>
            <p><strong>Địa chỉ:</strong> {{ $branch->address }}</p>
            <p><strong>Số điện thoại:</strong> {{ $branch->phone }}</p>

            @if ($branch->google_map_url)
                <p><strong>Google Map:</strong>
                    <a href="{{ $branch->google_map_url }}" target="_blank">Xem bản đồ</a>
                </p>
            @endif

            @if ($branch->content)
                <div class="mt-3">
                    <strong>Giới thiệu:</strong>
                    <div class="border rounded p-3 mt-1 bg-light">
                        {!! $branch->content !!}
                    </div>
                </div>
            @endif

            <p class="mt-3"><strong>Ngày tạo:</strong> {{ $branch->created_at->format('d/m/Y H:i') }}</p>

            <div class="mt-4">
                <a href="{{ route('branches.edit', ['branch' => $branch->id, 'page' => request('page', 1)]) }}"
                    class="btn btn-warning">Sửa</a>
                <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
            </div>
        </div>
    </div>

    {{-- Danh sách thợ cắt tóc --}}
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Danh sách Thợ Cắt Tóc tại chi nhánh</h3>
        </div>

        <div class="card-body">
            @if ($branch->barbers->isEmpty())
                <p>Hiện chưa có thợ cắt tóc nào thuộc chi nhánh này.</p>
            @else
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Họ tên</th>
                            <th>Trình độ</th>
                            <th>Đánh giá</th>
                            <th>Hồ sơ</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branch->barbers as $index => $barber)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $barber->name }}</td>
                                <td>{{ $barber->skill_level }}</td>
                                <td>{{ $barber->rating_avg }}</td>
                                <td>{{ Str::limit($barber->profile, 50) }}</td>
                                <td>
                                    @if ($barber->status === 'active')
                                        <span class="badge bg-success">Đang làm</span>
                                    @elseif ($barber->status === 'inactive')
                                        <span class="badge bg-warning">Tạm nghỉ</span>
                                    @else
                                        <span class="badge bg-secondary">Nghỉ việc</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('barbers.show', ['barber' => $barber->id, 'page' => request('page', 1)]) }}"
                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('barbers.edit', $barber->id) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
