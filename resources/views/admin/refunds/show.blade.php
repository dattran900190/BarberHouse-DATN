@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Yêu cầu hoàn tiền')

@section('content')
    @php
        $isSoftDeleted = $refund->trashed();
        $statusMap = [
            'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
            'processing' => ['label' => 'Đang xử lý', 'class' => 'primary'],
            'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'success'],
            'rejected' => ['label' => 'Từ chối', 'class' => 'danger'],
        ];
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Yêu cầu hoàn tiền</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/refunds') }}">Danh sách hoàn tiền</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <!-- Card: Chi tiết Yêu cầu hoàn tiền -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Chi tiết Yêu cầu hoàn tiền</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <i class="fa fa-user me-2 text-muted"></i>
                    <strong>Người dùng:</strong> {{ $refund->user->name ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-receipt me-2 text-primary"></i>
                    <strong>Mã đơn hàng:</strong>
                    {{ $refund->order->order_code ?? ($refund->appointment->appointment_code ?? 'Không có') }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-dollar-sign me-2 text-success"></i>
                    <strong>Số tiền hoàn:</strong> {{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ
                </div>
                <div class="col-md-6">
                    <i class="fa fa-comment-alt me-2 text-info"></i>
                    <strong>Lý do yêu cầu:</strong> {{ $refund->reason }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-university me-2 text-muted"></i>
                    <strong>Ngân hàng:</strong> {{ $refund->bank_name }} - {{ $refund->bank_account_number }}
                    ({{ $refund->bank_account_name }})
                </div>
                <div class="col-md-6">
                    <i class="fa fa-info-circle me-2 text-warning"></i>
                    <strong>Trạng thái:</strong>
                    @php
                        $info = $statusMap[$refund->refund_status] ?? [
                            'label' => ucfirst($refund->refund_status),
                            'class' => 'secondary',
                        ];
                    @endphp
                    <span class="badge bg-{{ $info['class'] }}">
                        {{ $info['label'] }}
                    </span>
                    @if ($isSoftDeleted)
                        <span class="badge bg-danger ms-2">Đã xóa mềm</span>
                    @endif
                </div>
                @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                    <div class="col-md-6">
                        <i class="fa fa-ban me-2 text-danger"></i>
                        <strong>Lý do từ chối:</strong> {{ $refund->reject_reason }}
                    </div>
                @endif
                @if ($refund->refund_status === 'refunded' && $refund->proof_image)
                    <div class="col-md-6">
                        <i class="fa fa-image me-2 text-info"></i>
                        <strong>Hình ảnh minh chứng:</strong>
                        <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">
                            <img src="{{ Storage::url($refund->proof_image) }}" alt="Proof Image"
                                style="max-width: 200px; max-height: 200px;">
                        </a>
                    </div>
                @endif
                @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                    <div class="col-md-6">
                        <i class="fa fa-calendar-check me-2 text-success"></i>
                        <strong>Ngày hoàn tiền:</strong>
                        {{ \Carbon\Carbon::parse($refund->refunded_at)->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($refund->order && $refund->order->items->count())
        <!-- Card: Danh sách sản phẩm hoàn tiền -->
        <div class="card shadow-sm mb-4">
            <div class="card-header text-white d-flex align-items-center">
                <h4 class="card-title mb-0">Danh sách sản phẩm</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Dung tích</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refund->order->items as $item)
                            <tr>
                                <td>{{ $item->productVariant->product->name ?? 'Không có tên' }}</td>
                                <td>{{ $item->productVariant->volume->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price_at_time, 0, ',', '.') }} VNĐ</td>
                                <td>{{ number_format($item->total_price, 0, ',', '.') }} VNĐ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Card 3: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            @php
                $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
                $currentStatus = $refund->refund_status;
                $currentOrder = $statusOrder[$currentStatus];
            @endphp

            @if (!$isSoftDeleted && !in_array($currentStatus, ['refunded', 'rejected']))
                <form action="{{ route('refunds.update', $refund->id) }}" method="POST" enctype="multipart/form-data" id="update-refund-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="{{ request('page', 1) }}">

                    <div class="form-group mt-3">
                        <label for="refund_status" class="form-label">Cập nhật trạng thái hoàn tiền</label>
                        <select name="refund_status" id="refund_status" class="form-control" required
                            onchange="toggleRejectReason(this)">
                            @foreach ($statusOrder as $value => $order)
                                @php
                                    $isCurrent = $currentStatus === $value;
                                    $isAllowed =
                                        ($currentStatus === 'pending' && $value === 'processing') ||
                                        ($currentStatus === 'processing' &&
                                            in_array($value, ['refunded', 'rejected'])) ||
                                        $isCurrent;
                                @endphp
                                <option value="{{ $value }}" {{ $isCurrent ? 'selected' : '' }}
                                    {{ !$isAllowed ? 'disabled' : '' }}>
                                    {{ $statusMap[$value]['label'] ?? ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-3" id="reject_reason_container" style="display: none;">
                        <label for="reject_reason" class="form-label">Lý do từ chối</label>
                        <textarea name="reject_reason" id="reject_reason" class="form-control" placeholder="Nhập lý do từ chối" rows="4"></textarea>
                        @error('reject_reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-3" id="proof_image_container" style="display: none;">
                        <label for="proof_image" class="form-label">Hình ảnh minh chứng</label>
                        <input type="file" name="proof_image" id="proof_image" class="form-control">
                        @error('proof_image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-save me-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('refunds.index', ['page' => request('page', 1)]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </form>

                <script>
                    function toggleRejectReason(select) {
                        const rejectReasonContainer = document.getElementById('reject_reason_container');
                        const proofImageContainer = document.getElementById('proof_image_container');
                        rejectReasonContainer.style.display = select.value === 'rejected' ? 'block' : 'none';
                        proofImageContainer.style.display = select.value === 'refunded' ? 'block' : 'none';
                    }
                </script>
            @elseif (!$isSoftDeleted && in_array($currentStatus, ['refunded', 'rejected']))
                <div class="alert alert-secondary mt-4">
                    Yêu cầu đã {{ $currentStatus === 'refunded' ? 'hoàn tiền' : 'bị từ chối' }}.
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    @if ($currentStatus === 'rejected' || $currentStatus === 'refunded')
                        <button class="btn btn-sm btn-outline-danger soft-delete-btn"
                            data-id="{{ $refund->id }}"
                            data-page="{{ request('page', 1) }}">
                            <i class="fa fa-trash me-1"></i> Xóa mềm
                        </button>
                    @endif
                    <a href="{{ route('refunds.index', ['page' => request('page', 1)]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            @elseif ($isSoftDeleted)
                <div class="alert alert-warning mt-4">
                    Yêu cầu này đã bị xóa mềm. Không thể cập nhật trạng thái.
                </div>
                
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success restore-btn"
                        data-id="{{ $refund->id }}"
                        data-page="{{ request('page', 1) }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>
                    <a href="{{ route('refunds.index', ['page' => request('page', 1)]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('js')
    <script>
        // Hiển thị thông báo SweetAlert2 dựa trên session flash message
        @if (session('success'))
            Swal.fire({
                title: 'Thành công!',
                text: '{{ session('success') }}',
                icon: 'success',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                icon: 'error',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            });
        @endif

        // Xử lý xác nhận trước khi submit form cập nhật
        document.getElementById('update-refund-form')?.addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Cập nhật trạng thái hoàn tiền',
                text: 'Bạn có chắc muốn cập nhật trạng thái này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                width: '400px',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Xử lý nút Xóa mềm và Khôi phục
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false,
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');
                    const page = this.getAttribute('data-page');

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
                                input: result.value || ''
                            }) : undefined;

                            fetch(route.replace(':id', id) + (page ? `?page=${page}` : ''), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Phản hồi không hợp lệ từ máy chủ.');
                                    }
                                    return response.json();
                                })
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
                                        if (data.success) onSuccess();
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
            selector: '.soft-delete-btn',
            title: 'Xóa mềm yêu cầu hoàn tiền',
            text: 'Bạn có chắc muốn xóa mềm yêu cầu hoàn tiền này?',
            route: '{{ route('refunds.softDelete', ':id') }}',
            method: 'PATCH'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục yêu cầu hoàn tiền',
            text: 'Bạn có chắc muốn khôi phục yêu cầu hoàn tiền này?',
            route: '{{ route('refunds.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection