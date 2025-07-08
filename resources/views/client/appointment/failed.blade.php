@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán thất bại
@endsection

@section('content')
    <main class="container" style="padding: 10% 0;">
        <h1>Thanh toán thất bại!</h1>
        <p>Vui lòng thử lại hoặc liên hệ hỗ trợ.</p>
        <a href="{{ route('appointments.index') }}" class="btn btn-primary">Quay lại danh sách lịch hẹn</a>
    </main>
@endsection