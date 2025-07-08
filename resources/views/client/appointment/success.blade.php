@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán thành công
@endsection

@section('content')
    <main class="container" style="padding: 10% 0;">
        <h1>Thanh toán thành công!</h1>
        <p>Cảm ơn bạn đã thanh toán. Lịch hẹn của bạn đã được xác nhận.</p>
        <a href="{{ route('appointments.index') }}" class="btn btn-primary">Quay lại danh sách lịch hẹn</a>
    </main>
@endsection