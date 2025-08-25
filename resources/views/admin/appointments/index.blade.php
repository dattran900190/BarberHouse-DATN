@extends('layouts.AdminLayout')

@section('title', 'Quản lý lịch hẹn')

@section('content')
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

            <a href="{{ route('appointments.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm lịch hẹn</span>
            </a>
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
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'custom-swal-popup'
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
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
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
                                                window.location.href = data
                                                    .redirect_url;
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
            text: 'Bạn có chắc chắn muốn đánh dấu lịch hẹn này là hoàn thành?',
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
                if (!value) {
                    return 'Lý do hủy không được để trống!';
                }
                if (value.length < 5) {
                    return 'Lý do hủy phải có ít nhất 5 ký tự!';
                }
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
                if (!value) {
                    return 'Lý do hủy không được để trống!';
                }
                if (value.length < 5) {
                    return 'Lý do hủy phải có ít nhất 5 ký tự!';
                }
                if (value && value.length > 255) return 'Lý do không được vượt quá 255 ký tự!';
            }
        });

        // Xử lý check-in riêng biệt
        document.querySelectorAll('.checkin-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const appointmentId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Check-in lịch hẹn',
                    text: 'Vui lòng nhập mã check-in',
                    icon: 'question',
                    input: 'number',
                    inputPlaceholder: 'Nhập mã check-in 6 chữ số',
                    inputValidator: (value) => {
                        if (!value || value === '') {
                            return 'Vui lòng nhập mã check-in!';
                        }
                        if (value && value.length !== 6) {
                            return 'Mã check-in phải có 6 chữ số!';
                        }
                        return null;
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Check-in',
                    cancelButtonText: 'Hủy',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const code = result.value;

                        // Bước 1: Kiểm tra mã check-in
                        Swal.fire({
                            title: 'Đang kiểm tra mã...',
                            text: 'Vui lòng chờ trong giây lát.',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`{{ route('checkin.check', ':id') }}`.replace(':id',
                                appointmentId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    code: code
                                })
                            })
                            .then(res => res.json())
                            .then(checkData => {
                                if (checkData.valid) {

                                    fetch(`{{ route('appointments.checkin', ':id') }}`.replace(
                                            ':id', appointmentId), {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                code: code,
                                                current_tab: '{{ $activeTab }}'
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            Swal.close();
                                            if (data.success) {
                                                Swal.fire({
                                                    title: 'Thành công!',
                                                    text: data.message,
                                                    icon: 'success',
                                                    customClass: {
                                                        popup: 'custom-swal-popup'
                                                    }
                                                }).then(() => {
                                                    if (data.redirect_url) {
                                                        window.location.href = data
                                                            .redirect_url;
                                                    } else {
                                                        location.reload();
                                                    }
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: 'Thất bại!',
                                                    text: data.message ||
                                                        'Không thể thực hiện check-in.',
                                                    icon: 'error',
                                                    customClass: {
                                                        popup: 'custom-swal-popup'
                                                    }
                                                });
                                            }
                                        })
                                        .catch(err => {
                                            Swal.close();
                                            Swal.fire({
                                                title: 'Lỗi!',
                                                text: 'Không thể thực hiện check-in. Vui lòng thử lại.',
                                                icon: 'error',
                                                customClass: {
                                                    popup: 'custom-swal-popup'
                                                }
                                            });
                                            console.error(err);
                                        });
                                } else {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Mã không hợp lệ!',
                                        text: checkData.message ||
                                            'Mã check-in không đúng hoặc không khớp với lịch hẹn này.',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.close();
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Không thể kiểm tra mã check-in. Vui lòng thử lại.',
                                    icon: 'error',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                                console.error(err);
                            });
                    }
                });
            });
        });
    </script>

@endsection
