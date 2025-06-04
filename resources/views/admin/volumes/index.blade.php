@extends('adminlte::page')

@section('title', 'Danh sách dung tích')

@section('content')
    <h1>Danh sách dung tích</h1>

    {{-- Hiển thị thông báo --}}
    @if (session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="color: red; margin-bottom: 10px;">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('admin.volumes.create') }}" style="margin-bottom: 10px; display: inline-block;">
        Thêm mới
    </a>

    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>ID</th>
                <th>Tên</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($volumes as $volume)
                <tr>
                    <td>{{ $volume->id }}</td>
                    <td>{{ $volume->name }}</td>
                    <td>
                        <a href="{{ route('admin.volumes.edit', $volume) }}">Sửa</a>
                        <form action="{{ route('admin.volumes.destroy', $volume) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
