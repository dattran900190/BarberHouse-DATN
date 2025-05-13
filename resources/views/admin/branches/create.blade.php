@extends('adminlte::page')

@section('title', 'Thêm Chi nhánh mới')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title mb-0">Thêm Chi nhánh</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.branches.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên chi nhánh</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Thêm</button>
                <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
