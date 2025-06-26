@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử đặt lịch
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card order-history mt-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-0 m-3">
                    <h3 class="mb-0 fw-bold">Lịch sử đặt lịch của tôi</h3>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('status', 'Tất cả') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => ''])) }}">Tất cả</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => 'completed'])) }}">Đã hoàn thành</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => 'pending'])) }}">Đang chờ</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => 'confirmed'])) }}">Đã xác nhận</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}">Đã hủy</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => 'pending_cancellation'])) }}">Chờ hủy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form class="d-flex mb-4" method="GET" action="{{ route('client.appointmentHistory') }}">
                        <input type="text" class="form-control me-2" name="search" placeholder="Tìm kiếm đặt lịch" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                    </form>
                    @forelse ($appointments as $appointment)
                        <div class="order-item mb-3 p-3 rounded-3">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <span class="fw-bold">Mã đặt lịch: {{ $appointment->appointment_code }}</span>
                                    <br>
                                    <span class="text-dark">Dịch vụ: {{ $appointment->service?->name ?? 'N/A' }}</span>
                                    <br>
                                    <span class="text-muted">Thợ: {{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</span>
                                    <br>
                                    <span class="text-muted">Chi nhánh: {{ $appointment->branch?->name ?? 'N/A' }}</span>
                                    <br>
                                    @php
                                        $time = \Carbon\Carbon::parse($appointment->appointment_time);
                                        $formattedTime = $time->format('d/m/Y - H:i');
                                        $period = $time->format('H') < 12 ? 'Sáng' : 'Chiều tối';
                                    @endphp
                                    <span class="text-muted">Thời gian: {{ $formattedTime }} {{ $period }}</span>
                                    <br>
                                    <span class="text-muted">Tổng tiền: {{ number_format($appointment->total_amount) }}đ</span>
                                    @if ($appointment->status == 'cancelled' && $appointment->cancellation_reason)
                                        <br>
                                        <span class="text-muted">Lý do hủy: {{ $appointment->cancellation_reason }}</span>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    @if ($appointment->status == 'pending')
                                        <span class="status-label status-processing">Đang chờ</span>
                                    @elseif ($appointment->status == 'confirmed')
                                        <span class="status-label status-confirmed">Đã xác nhận</span>
                                    @elseif ($appointment->status == 'cancelled')
                                        <span class="status-label status-canceled">Đã hủy</span>
                                    @elseif ($appointment->status == 'completed')
                                        <span class="status-label status-completed">Đã hoàn thành</span>
                                    @elseif ($appointment->status == 'pending_cancellation')
                                        <span class="status-label status-warning">Chờ hủy</span>
                                    @endif
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('client.detailAppointmentHistory', $appointment->id) }}">
                                            Xem chi tiết
                                        </a>
                                        @if (in_array($appointment->status, ['pending', 'confirmed']))
                                            {{-- <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#cancelModal{{ $appointment->id }}">Hủy</button> --}}
                                                <button type="button" class="btn btn-danger btn-sm" data-swal-toggle="modal" data-id="{{ $appointment->id }}">Hủy</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                           
                        </div>
                    @empty
                        <div class="text-center text-muted p-3">Bạn chưa có lịch hẹn nào.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3" >
            {{ $appointments->links() }}
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
        .status-label {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 14px;
            
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/client.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-swal-toggle="modal"]').forEach(button => {
                button.addEventListener('click', () => {
                    const appointmentId = button.getAttribute('data-id');
                    console.log('Button clicked for appointment ID:', appointmentId); // Debug
                    Swal.fire({
                        title: 'Hủy lịch hẹn',
                        html: `
                            <p>Bạn có chắc chắn muốn hủy lịch hẹn không?</p>
                            <div class="form-group">
                                <label for="swal-cancellation_reason">Lý do hủy <span class="text-danger">*</span></label>
                                <textarea id="swal-cancellation_reason" class="form-control" style="box-shadow: none" rows="4" required placeholder="Vui lòng nhập lý do hủy..."></textarea>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Gửi yêu cầu hủy',
                        cancelButtonText: 'Đóng',
                        focusConfirm: false,
                        preConfirm: () => {
                            const reason = document.getElementById('swal-cancellation_reason').value;
                            if (!reason) {
                                Swal.showValidationMessage('Vui lòng nhập lý do hủy');
                                return false;
                            }
                            return { reason: reason };
                        },
                        allowOutsideClick: false,
                        didOpen: () => {
                            const form = Swal.getHtmlContainer().querySelector('form');
                            if (form) form.addEventListener('submit', (e) => e.preventDefault());
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `{{ route('client.appointments.cancel', ':id') }}`.replace(':id', appointmentId);
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            const method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'PATCH';
                            const reason = document.createElement('input');
                            reason.type = 'hidden';
                            reason.name = 'cancellation_reason';
                            reason.value = result.value.reason;
                            form.appendChild(csrf);
                            form.appendChild(method);
                            form.appendChild(reason);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <script>
    const swiper = new Swiper(".mySwiper", {
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });
</script>
@endsection