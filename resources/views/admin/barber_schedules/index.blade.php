@extends('layouts.AdminLayout')
@section('title', 'Danh sách lịch theo chi nhánh')

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Chi nhánh</h3>

        </div>
        <div class="card-body">
            <form action="{{ route('barber_schedules.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên chi nhánh..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên chi nhánh</th>
                        <th>Địa chỉ</th>
                        <th>Điện thoại</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->phone }}</td>
                            <td>
                                <a href="{{ route('barber_schedules.showBranch', $branch->id) }}"
                                    class="btn btn-info btn-sm">Xem</a>


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endsection
