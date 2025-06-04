@extends('adminlte::page')

@section('title', 'Quản lý dung tích')

@section('content')
    <h1>Thêm dung tích mới</h1>

    {{-- Hiển thị lỗi nếu có --}}
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.volumes.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="name">Tên dung tích:</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   style="padding: 5px; width: 300px;" placeholder="Nhập tên dung tích">
        </div>

        <button type="submit" style="padding: 5px 10px;">Lưu</button>
        <a href="{{ route('admin.volumes.index') }}" style="margin-left: 10px;">Quay lại</a>
    </form>
@endsection
