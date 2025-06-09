@extends('adminlte::page')

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
            <p><strong>Email:</strong> {{ $branch->email }}</p>
            <p><strong>Ngày tạo:</strong> {{ $branch->created_at->format('d/m/Y H:i') }}</p>

            <a href="{{ route('branches.edit', ['branch' => $branch->id, 'page' => request('page', 1)]) }}"
                class="btn btn-warning">Sửa</a>
            <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                lại</a>
        </div>
    </div>

    {{-- Danh sách thợ cắt tóc thuộc chi nhánh --}}
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Danh sách Thợ Cắt Tóc tại chi nhánh</h3>
        </div>
        <div class="card-body">
            @if ($branch->barbers->isEmpty())
                <p>Hiện chưa có thợ cắt tóc nào thuộc chi nhánh này.</p>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Họ tên</th>
                            <th>Trình độ</th>
                            <th>Đánh giá</th>
                            <th>Hồ sơ</th>
                            <th>Avatar</th>
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
                                    @if ($barber->avatar)
                                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                            style="height: 50px;">
                                    @else
                                        <span>Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('barbers.edit', $barber->id) }}"
                                        class="btn btn-sm btn-warning">Sửa</a>
                                    {{-- Thêm các hành động khác nếu cần --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
