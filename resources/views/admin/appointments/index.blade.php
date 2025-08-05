@extends('layouts.AdminLayout')

@section('title', 'Quản lý lịch hẹn')

@section('content')
    {{-- @if (session('success'))
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    @endif

    @if (session('error'))
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    @endif --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @php
        $statusMap = [
            'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
            'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
            'checked-in' => ['class' => 'info', 'text' => 'Đã check-in'],
            'progress' => ['class' => 'secondary', 'text' => 'Đang làm tóc'],
            'cancelled' => ['class' => 'danger', 'text' => 'Đã huỷ'],
            'confirmed' => ['class' => 'primary', 'text' => 'Đã xác nhận'],
        ];

        $paymentMap = [
            'paid' => ['class' => 'success', 'text' => 'Thanh toán thành công'],
            'unpaid' => ['class' => 'warning', 'text' => 'Chưa thanh toán'],
            'failed' => ['class' => 'danger', 'text' => 'Thanh toán thất bại'],
            'refunded' => ['class' => 'danger', 'text' => 'Hoàn trả thanh toán'],
        ];
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Đặt lịch cắt tóc</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments') }}">Danh sách đặt lịch</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">Danh sách đặt lịch</div>

            <form action="{{ route('checkins.store') }}" method="POST" class="d-flex align-items-start ms-auto">
                @csrf
                <div class="mt-4">
                    <input type="text" name="code" id="code"
                        class="form-control @error('code') is-invalid @enderror" maxlength="6" required
                        placeholder="Nhập mã check-in">
                    @error('code')
                        <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="mt-4 ms-1">
                    <button type="submit" class="btn btn-outline-success">
                        <i class="fas fa-check-circle"></i> Xác nhận
                    </button>
                </div>
            </form>
        </div>


        <div class="card-body">
            <form method="GET" action="{{ route('appointments.index') }}" id="searchForm" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên dịch vụ..."
                        class="form-control pe-5" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            @if ($search && trim($search) && $allAppointments->isEmpty())
                <div class="alert alert-warning">
                    Không tìm thấy lịch hẹn nào khớp với "{{ $search }}".
                </div>
            @endif

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="appointmentTabs" role="tablist">
                @php
                    $tabs = [
                        'pending' => 'Chưa xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'progress' => 'Đang làm tóc',
                        'completed' => 'Đã hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}" id="{{ $key }}-tab"
                            href="{{ route('appointments.index', ['status' => $key, 'search' => request('search')]) }}"
                            role="tab">
                            {{ $label }}
                            @if ($key == 'pending' && $pendingCount > 0)
                                <span class="position-relative">
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>


            <!-- Nội dung từng tab -->
            <div class="tab-content" id="appointmentTabsContent">
                <!-- Tab Chưa xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'pending' ? 'show active' : '' }}" id="pending"
                    role="tabpanel">
                    <div class="table-responsive">
                        @include('admin.appointments.partial._table', [
                            'appointments' => $pendingAppointments,
                            'statusMap' => $statusMap,
                            'paymentMap' => $paymentMap,
                            'type' => 'pending',
                        ])
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pendingAppointments->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'confirmed' ? 'show active' : '' }}" id="confirmed"
                    role="tabpanel">
                    <div class="table-responsive">
                        @include('admin.appointments.partial._table', [
                            'appointments' => $confirmedAppointments,
                            'statusMap' => $statusMap,
                            'paymentMap' => $paymentMap,
                            'type' => 'confirmed',
                        ])
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $confirmedAppointments->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                </div>

                <!-- Tab Đang làm tóc -->
                <div class="tab-pane fade {{ $activeTab == 'progress' ? 'show active' : '' }}" id="progress"
                    role="tabpanel">
                    <div class="table-responsive">
                        @include('admin.appointments.partial._table', [
                            'appointments' => $progressAppointments,
                            'statusMap' => $statusMap,
                            'paymentMap' => $paymentMap,
                            'type' => 'progress',
                        ])
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $progressAppointments->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hoàn thành -->
                <div class="tab-pane fade {{ $activeTab == 'completed' ? 'show active' : '' }}" id="completed"
                    role="tabpanel">
                    <div class="table-responsive">
                        @include('admin.appointments.partial._table', [
                            'appointments' => $completedAppointments,
                            'statusMap' => $statusMap,
                            'paymentMap' => $paymentMap,
                            'type' => 'completed',
                        ])
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $completedAppointments->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hủy -->
                <div class="tab-pane fade {{ $activeTab == 'cancelled' ? 'show active' : '' }}" id="cancelled"
                    role="tabpanel">
                    <div class="table-responsive">
                        @include('admin.appointments.partial._table', [
                            'appointments' => $cancelledAppointments,
                            'statusMap' => $statusMap,
                            'paymentMap' => $paymentMap,
                            'type' => 'cancelled',
                        ])
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $cancelledAppointments->appends(['search' => request('search'), 'status' => request('status')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        const paymentMap = {
            paid: {
                class: 'success',
                text: 'Thanh toán thành công'
            },
            unpaid: {
                class: 'warning',
                text: 'Chưa thanh toán'
            },
            failed: {
                class: 'danger',
                text: 'Thanh toán thất bại'
            },
            refunded: {
                class: 'danger',
                text: 'Hoàn trả thanh toán'
            }
        };

        const paymentMethodMap = {
            cash: 'Thanh toán tại tiệm',
            vnpay: 'Thanh toán VNPAY'
        };

        // 1. Lắng nghe sự kiện click các nút confirm-btn
        document.addEventListener('click', function(e) {
            const confirmBtn = e.target.closest('.confirm-btn');
            if (!confirmBtn) return;

            const id = confirmBtn.dataset.id;
            Swal.fire({
                title: 'Xác nhận lịch hẹn',
                text: 'Bạn có chắc chắn muốn xác nhận lịch hẹn này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ trong giây lát.',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'custom-swal-popup' // CSS
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`{{ route('appointments.confirm', ':id') }}`.replace(':id', id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            Swal.fire({
                                title: data.success ? 'Thành công!' : 'Thất bại!',
                                text: data.message,
                                customClass: {
                                    popup: 'custom-swal-popup' // CSS
                                },
                                icon: data.success ? 'success' : 'error'
                            }).then(() => {
                                if (data.success) location.reload();
                            });
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire('Lỗi', 'Không thể xác nhận lịch hẹn.', 'error');
                            console.error(err);
                        });
                }
            });
        });

        // 2. Lắng nghe sự kiện từ Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('appointments');

        channel.bind('App\\Events\\AppointmentCreated', function(data) {


            if (data.payment_method === 'vnpay') {
                data.payment_status = 'paid';
            }

            const paymentInfo = paymentMap[data.payment_status] || {
                class: 'secondary',
                text: data.payment_status || 'N/A'
            };

            const paymentMethod = paymentMethodMap[data.payment_method] || 'Không xác định';

            const additionalServices = data.additional_services && data.additional_services.length ?
                `<div class="mt-2">
            <strong class="text-muted">Dịch vụ thêm:</strong>
            <ul class="mb-0 mt-1 ps-3 text-muted">
                ${data.additional_services.map(service => `<li>${service}</li>`).join('')}
            </ul>
        </div>` :
                '';

            const actionDropdown = `
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary" type="button" id="actionMenu${data.id}"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu${data.id}">
                <li><a href="appointments/${data.id}" class="dropdown-item"><i class="fas fa-eye me-2"></i> Xem</a></li>
                <li><a href="appointments/${data.id}/edit" class="dropdown-item"><i class="fas fa-edit me-2"></i> Sửa</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><button type="button" class="dropdown-item text-success confirm-btn" data-id="${data.id}"><i class="fas fa-check me-2"></i> Xác nhận</button></li>
            </ul>
        </div>
    `;

            const row = `
        <tr>
            <td>Mới</td>
            <td>${data.appointment_code}</td>
            <td>${data.user_name}</td>
            <td>${data.phone}</td>
            <td>${data.barber_name}</td>
            <td>
                ${data.service_name}
                ${additionalServices}
            </td>
            <td>${paymentMethod}</td>
            <td>${data.appointment_time}</td>
            <td><span class="badge bg-warning">Chờ xác nhận</span></td>
            <td><span class="badge bg-${paymentInfo.class}">${paymentInfo.text}</span></td>
            <td class="text-center">${actionDropdown}</td>
        </tr>
    `;
            const tableBody = document.querySelector('#pending tbody');
            if (tableBody) {
                tableBody.insertAdjacentHTML('afterbegin', row);
            } else {
                console.warn('Không tìm thấy bảng #pending tbody!');
            }
        });
    </script>

    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false, // Thêm tùy chọn hiển thị input
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload() // Thay reload bằng callback linh hoạt
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const appointmentId = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    };

                    if (withInput) {
                        swalOptions.input = 'textarea';
                        swalOptions.inputPlaceholder = inputPlaceholder;
                        if (inputValidator) {
                            swalOptions.inputValidator = inputValidator;
                        }
                    }

                    Swal.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup' // CSS
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                cancellation_reason: result.value || 'Không có lý do',
                                current_tab: '{{ $activeTab }}'
                            }) : JSON.stringify({
                                current_tab: '{{ $activeTab }}'
                            });

                            fetch(route.replace(':id', appointmentId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) {
                                            if (data.redirect_url) {
                                                window.location.href = data.redirect_url;
                                            } else {
                                                onSuccess();
                                            }
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        handleSwalAction({
            selector: '.confirm-btn',
            title: 'Xác nhận lịch hẹn',
            text: 'Bạn có chắc chắn muốn xác nhận lịch hẹn này?',
            route: '{{ route('appointments.confirm', ':id') }}'
        });

        handleSwalAction({
            selector: '.complete-btn',
            title: 'Hoàn thành lịch hẹn',
            text: 'Bạn có chắc chắn muốn đánh dấu lịch hẹn này là HOÀN THÀNH?',
            route: '{{ route('appointments.completed', ':id') }}'
        });

        handleSwalAction({
            selector: '.no-show-btn',
            title: 'Đánh dấu lịch hẹn là No-show',
            text: 'Vui lòng nhập lý do (tùy chọn)',
            route: '{{ route('appointments.no-show', ':id') }}',
            withInput: true,
            inputPlaceholder: 'Nhập lý do no-show (tối đa 255 ký tự)...',
            inputValidator: (value) => {
                if (value && value.length > 255) return 'Lý do không được vượt quá 255 ký tự!';
            }
        });

        handleSwalAction({
            selector: '.cancel-btn',
            title: 'Huỷ lịch hẹn',
            text: 'Vui lòng nhập lý do (tùy chọn)',
            route: '{{ route('appointments.cancel', ':id') }}',
            withInput: true,
            inputPlaceholder: 'Nhập lý do cancel (tối đa 255 ký tự)...',
            inputValidator: (value) => {
                if (value && value.length > 255) return 'Lý do không được vượt quá 255 ký tự!';
            }
        });
    </script>

@endsection
