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
                    @php
                        $statusLabels = [
                            '' => 'Tất cả',
                            'completed' => 'Đã hoàn thành',
                            'pending' => 'Đang chờ',
                            'progress' => 'Đang làm tóc',
                            'confirmed' => 'Đã xác nhận',
                            'cancelled' => 'Đã hủy',
                        ];

                        $currentStatus = request('status', '');
                        $currentStatusLabel = $statusLabels[$currentStatus] ?? $currentStatus;
                    @endphp

                    <div class="dropdown">
                        <button class="btn-outline-show dropdown-toggle" style="padding: 5px 10px" type="button"
                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $currentStatusLabel }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            @foreach ($statusLabels as $key => $label)
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('client.appointmentHistory', array_merge(request()->except('status'), ['status' => $key])) }}">
                                        {{ $label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('client.appointmentHistory') }}" id="searchForm" class="mb-3">
                        <div class="position-relative">
                            <input type="text" class="form-control me-2" name="search" placeholder="Tìm kiếm đặt lịch"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                    @forelse ($appointments as $appointment)
                        <div class="order-item mb-3 p-3 rounded-3">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-md-4">
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
                                    <span class="text-muted">Tổng tiền:
                                        {{ number_format($appointment->total_amount) }}đ</span>
                                    @if ($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                                        <br>
                                        <span class="text-muted">Lý do hủy: {{ $appointment->cancellation_reason }}</span>
                                    @endif
                                </div>

                                <div class="col-md-2 text-center">
                                    @if ($appointment->status == 'pending')
                                        <span class="status-label status-processing">Đang chờ</span>
                                    @elseif ($appointment->status == 'confirmed')
                                        <span class="status-label status-confirmed">Đã xác nhận</span>
                                    @elseif ($appointment->status == 'progress')
                                        <span class="status-label status-info">Đang làm tóc</span>
                                    @elseif ($appointment->status == 'cancelled')
                                        <span class="status-label status-cancelled">
                                            {{ $appointment->cancellation_type == 'no-show' ? 'Không đến' : 'Đã hủy' }}
                                        </span>
                                    @elseif ($appointment->status == 'completed')
                                        <span class="status-label status-completed">Đã hoàn thành</span>
                                    @endif
                                </div>

                                <div class="col-md-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">

                                        @if ($appointment instanceof \App\Models\CancelledAppointment)
                                            <a href="{{ route('client.cancelledAppointment.show', $appointment->id) }}"
                                                class="btn-outline-show">
                                                Xem chi tiết
                                            </a>
                                        @else
                                            <a href="{{ route('client.detailAppointmentHistory', $appointment->id) }}"
                                                class="btn-outline-show">
                                                Xem chi tiết
                                            </a>
                                        @endif

                                        @if ($appointment->status === 'completed' && !$appointment->review)
                                            <button class="btn-outline-show review-btn" data-id="{{ $appointment->id }}">
                                                Đánh giá
                                            </button>
                                        @endif
                                        @if (in_array($appointment->status, ['pending', 'confirmed']) 
                                        && !$appointment->whereIn('status', ['pending', 'processing'])->exists())
                                            <button type="button" class="btn-outline-show cancel-btn"
                                                data-swal-toggle="modal" data-id="{{ $appointment->id }}">Hủy đặt
                                                lịch</button>
                                        @endif
                                        @if (
                                            $appointment->status != 'cancelled' &&
                                                $appointment->status != 'completed' &&
                                                $appointment->payment_status == 'paid' &&
                                                $appointment->status != 'progress' &&
                                                !$appointment->refundRequests()->whereIn('refund_status', ['pending', 'processing'])->exists() &&
                                                !$appointment->refundRequests()->where('refund_status', 'rejected')->exists())
                                            <a href="{{ route('client.wallet', ['refundable_type' => 'appointment', 'refundable_id' => $appointment->id]) }}"
                                                class="btn-outline-show refund-btn"
                                                data-appointment-id="{{ $appointment->id }}">Yêu cầu hoàn tiền</a>
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
        <div class="d-flex justify-content-center mt-3" style="color: #000;">
            {{ $appointments->links() }}
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }

        .status-label {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 14px;
        }

        .swal2-textarea {
            width: 85%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }

        @media (max-width: 768px) {
            main {
                padding: 80px 10px 10px 10px !important;
            }
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
    {{-- <script src="{{ asset('js/client.js') }}"></script> --}}
    <script>
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn hành vi mặc định
                const appointmentId = this.getAttribute('data-id');

                // Cửa sổ từ chối với textarea
                Swal.fire({
                    title: 'Hủy lịch hẹn',
                    text: 'Vui lòng nhập lý do hủy',
                    input: 'textarea',
                    inputPlaceholder: 'Nhập lý do hủy (tối thiểu 5 ký tự)...',
                    inputAttributes: {
                        'rows': 4,
                        'required': true
                    },
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Hủy lịch',
                    cancelButtonText: 'Đóng',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Lý do hủy không được để trống!';
                        }
                        if (value.length < 5) {
                            return 'Lý do hủy phải có ít nhất 5 ký tự!';
                        }
                        if (value.length > 500) {
                            return 'Lý do hủy không được vượt quá 500 ký tự!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cửa sổ loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Gửi yêu cầu AJAX
                        fetch('{{ route('client.appointments.cancel', ':id') }}'.replace(':id',
                                appointmentId), {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    cancellation_reason: result.value
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(
                                        `HTTP error! Status: ${response.status}`
                                    );
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Đóng cửa sổ loading
                                Swal.close();

                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                // Đóng cửa sổ loading
                                Swal.close();
                                console.error('Lỗi AJAX:', error);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã có lỗi xảy ra: ' + error
                                        .message,
                                    icon: 'error',
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            });
                    }
                });
            });
        });
    </script>
    <script>
        document.querySelectorAll('.review-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.id;

                Swal.fire({
                    title: 'Đánh giá dịch vụ',
                    html: `
                    <div class="mb-2">Chọn số sao:</div>
                    <div id="star-rating" class="mb-3" style="font-size: 28px;">
                        ${[1, 2, 3, 4, 5].map(i =>
                            `<i class="fa fa-star star" data-value="${i}" style="cursor:pointer;color:#ccc;margin:0 2px;"></i>`
                        ).join('')}
                    </div>
                    <div id="feedback-text" class="mb-3" style="font-weight: 500; min-height: 24px;"></div>
                    <textarea id="reviewComment" class="swal2-textarea" placeholder="Nhận xét của bạn (tuỳ chọn)"></textarea>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Gửi đánh giá',
                    cancelButtonText: 'Huỷ',
                    focusConfirm: false,
                    preConfirm: () => {
                        const selected = document.querySelectorAll(
                            '#star-rating .star.selected');
                        if (selected.length === 0) {
                            Swal.showValidationMessage('Vui lòng chọn số sao!');
                            return false;
                        }
                        return {
                            rating: selected.length,
                            comment: document.getElementById('reviewComment').value
                        };
                    },
                    didOpen: () => {
                        const stars = document.querySelectorAll('#star-rating .star');
                        const feedback = document.getElementById('feedback-text');

                        stars.forEach((star, index) => {
                            star.addEventListener('click', () => {
                                // Highlight sao
                                stars.forEach((s, i) => {
                                    s.classList.toggle('selected', i <=
                                        index);
                                    s.style.color = i <= index ?
                                        '#f1c40f' : '#ccc';
                                });

                                // Gợi ý trạng thái cảm xúc
                                const rating = index + 1;
                                let text = '';
                                switch (rating) {
                                    case 1:
                                        text = 'Rất không hài lòng';
                                        break;
                                    case 2:
                                        text = 'Không hài lòng';
                                        break;
                                    case 3:
                                        text = 'Bình thường';
                                        break;
                                    case 4:
                                        text = 'Hài lòng';
                                        break;
                                    case 5:
                                        text = 'Rất hài lòng';
                                        break;
                                }
                                feedback.textContent = text;
                                feedback.style.color = rating <= 2 ? 'red' : (
                                    rating == 3 ? '#666' : 'green');
                            });
                        });
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        fetch("{{ route('client.submitReview', ['appointment' => '__ID__']) }}"
                                .replace('__ID__', appointmentId), {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    },
                                    body: JSON.stringify({
                                        rating: result.value.rating,
                                        comment: result.value.comment
                                    })
                                })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Gửi đánh giá thất bại');
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Đóng cửa sổ loading
                                Swal.close();

                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                // Đóng cửa sổ loading
                                Swal.close();
                                console.error('Lỗi AJAX:', error);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã có lỗi xảy ra: ' + error
                                        .message,
                                    icon: 'error',
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            });
                    }
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
