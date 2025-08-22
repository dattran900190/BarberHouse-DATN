@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đặt lịch
@endsection

@section('content')
    @php
        $time = \Carbon\Carbon::parse($appointment->appointment_time);
        $formattedTime = $time->format('d/m/Y - H:i');
        $period = $time->format('H') < 12 ? 'Sáng' : 'Chiều tối';

        $statusLabels = [
            '' => 'Tất cả',
            'completed' => 'Đã hoàn thành',
            'pending' => 'Đang chờ',
            'progress' => 'Đang làm tóc',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
        ];
    @endphp
    <main style="padding: 10%">
        <div class="modal-body">
            <div class="card mb-0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-sm-flex">
                                <div class="flex-grow-1">
                                    {{-- Logo công ty --}}
                                    <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}" class="card-logo card-logo-dark"
                                        alt="logo tối" height="56">

                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">

                                    <h6><span class="text-muted fw-normal">Email:</span> {{ $appointment->email }}
                                    </h6>

                                    <h6 class="mb-0"><span class="text-muted fw-normal">Điện thoại:</span>
                                        {{ $appointment->phone }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Mã đặt lịch</p>
                                    <h5 class="fs-15 mb-0">{{ $appointment->appointment_code ?? 'Không xác định' }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Ngày đặt</p>
                                    <h5 class="fs-15 mb-0">{{ $appointment->created_at->format('d/m/Y') }} <small
                                            class="text-muted">{{ $appointment->created_at->format('h:ia') }}</small></h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Trạng thái</p>
                                    <span class="status-label status-{{ $appointment->status }}">
                                        {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Tổng cộng</p>
                                    <h5 class="fs-15 mb-0">{{ number_format($appointment->total_amount, 0, ',', '.') }} VNĐ
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="row g-3">
                                <div class="col-6">
                                    <h6 class="text-muted text-uppercase fw-semibold fs-15 mb-3">Thông tin chi tiết</h6>
                                    <p class="text-muted mb-1">Chi nhánh: <span class="fw-medium">
                                            {{ $appointment->branch->name ?? 'Không xác định' }}
                                        </span></p>
                                    <p class="text-muted mb-1">Điện thoại: <span class="fw-medium">
                                            {{ $appointment->phone }}
                                        </span></p>
                                    <p class="text-muted mb-1">Thợ: <span class="fw-medium">
                                            {{ $appointment->barber->name ?? 'Không xác định' }}
                                        </span></p>
                                    @if ($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                                        <p class="text-muted mb-1">Lý do huỷ: <span
                                                class="fw-medium">{{ $appointment->cancellation_reason }}</span></p>
                                    @endif

                                    @php
                                    $time = \Carbon\Carbon::parse($appointment->appointment_time);
                                    $formattedTime = $time->format('d/m/Y - H:i');
                                    $period = $time->format('H') < 12 ? 'Sáng' : 'Chiều tối';
                                @endphp
                                <p class="text-muted mb-1">Thời gian lịch hẹn:
                                    <span class="fw-medium">
                                        {{ $formattedTime }} {{ $period }}
                                    </span>
                                </p>
                                    <p class="text-muted mb-1">Dịch vụ: <span class="fw-medium">
                                            {{ $appointment->service->name ?? 'Không xác định' }}
                                        </span></p>
                                    @if ($additionalServices->isNotEmpty())
                                        <p class="text-muted mb-1">Dịch vụ bổ sung: <span class="fw-medium">
                                                <ul class="m-2 mt-1 ps-3">
                                                    @foreach ($additionalServices as $service)
                                                        <li>
                                                            {{ $service->name ?? 'Không xác định' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </span></p>
                                    @endif

                                </div>

                                <div class="col-6">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Chi tiết thanh toán</h6>
                                    <p class="text-muted mb-1">Phương thức thanh toán: <span
                                            class="fw-medium">{{ $appointment->payment_method === 'cash' ? 'Thanh toán tại tiệm' : 'Thanh toán vnpay' }}</span>
                                    </p>

                                    <p class="text-muted">Tổng cộng: <span
                                            class="fw-medium">{{ number_format($appointment->total_amount, 0, ',', '.') }}
                                            VNĐ</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="alert alert-light">
                                    <p class="mb-0"><span class="fw-semibold">GHI CHÚ:</span>
                                        {{ $appointment->notes ?? 'Không có ghi chú' }}</p>
                                </div>
                            </div>
                            <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                @if (
                                    !($appointment instanceof \App\Models\CancelledAppointment) &&
                                        $appointment->status != 'completed' &&
                                        $appointment->status != 'cancelled')
                                    <button type="button" class="btn-outline-show cancel-btn"
                                    data-swal-toggle="modal" data-id="{{ $appointment->id }}" style="padding: 6px 10px;">Hủy đặt
                                    lịch</button>
                                @endif
                                @if ($appointment->status === 'completed' && !$appointment->review)
                                            <button class="btn-outline-show review-btn" style="padding: 6px 10px;" data-id="{{ $appointment->id }}">
                                                Đánh giá
                                            </button>
                                        @endif
                                <a href="{{ route('client.appointmentHistory') }}"
                                    class="btn-outline-show" style="padding: 6px 10px;">Quay
                                    lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
        .swal2-textarea {
            width: 85%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
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
                                    window.location.href = '{{ route('client.appointmentHistory') }}';
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
@endsection
