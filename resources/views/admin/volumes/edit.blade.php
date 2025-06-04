@extends('adminlte::page')

@section('title', 'Chỉnh sửa dung tích')

@section('content')
    <h1>Chỉnh sửa dung tích</h1>

    {{-- Hiển thị thông báo lỗi --}}
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.volumes.update', $volume) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Tên dung tích:</label>
        <input type="text" name="name" id="name" value="{{ old('name', $volume->name) }}" required>
        <br><br>

        <button type="submit">Cập nhật</button>
        <a href="{{ route('admin.volumes.index') }}">Quay lại</a>
    </form>
@endsection
