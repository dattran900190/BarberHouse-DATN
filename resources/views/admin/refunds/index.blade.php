@extends('layouts.AdminLayout')

@section('title', 'Danh sách hoàn tiền')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    @php
        $currentRole = Auth::user()->role;
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Danh sách hoàn tiền</h3>
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
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Yêu cầu hoàn tiền</div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('refunds.index') }}" class="mb-3 d-flex gap-2 flex-wrap">
                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" class="form-control pe-5" placeholder="Tìm kiếm yêu cầu hoàn..."
                        value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <select name="filter" class="form-select"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>
            </form>

            @php
                $tabs = [
                    'pending' => 'Chờ duyệt',
                    'processing' => 'Đang xử lý',
                    'refunded' => 'Đã hoàn tiền',
                    'rejected' => 'Từ chối',
                ];
            @endphp

            <ul class="nav nav-tabs" id="refundTabs" role="tablist">
                @foreach ($tabs as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}" id="{{ $key }}-tab"
                            href="{{ route('refunds.index', ['status' => $key, 'search' => request('search'), 'filter' => request('filter')]) }}">
                            {{ $label }}
                            @if ($key == 'pending' && $pendingRefundCount > 0)
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

            <div class="tab-content">
                @foreach ($tabs as $key => $label)
                    <div class="tab-pane fade {{ $activeTab == $key ? 'show active' : '' }}" id="{{ $key }}">
                        @php
                            $filteredRefundsForTab = $refunds->where('refund_status', $key);
                        @endphp

                        @if ($search && $filteredRefundsForTab->isEmpty())
                            <div class="alert alert-warning mt-3">
                                Không tìm thấy yêu cầu nào khớp với "{{ $search }}" trong tab này.
                            </div>
                        @endif

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>STT</th>
                                        <th>Người dùng</th>
                                        <th>Mã giao dịch</th>
                                        <th>Số tiền hoàn</th>
                                        <th>Trạng thái</th>
                                        @if ($key === 'rejected')
                                            <th>Lý do từ chối</th>
                                        @endif
                                        @if ($key === 'refunded')
                                            <th>Hình ảnh minh chứng</th>
                                        @endif
                                        <th>Ngày tạo</th>
                                        <th>Tình trạng</th>
                                        @if ($currentRole == 'admin')
                                            <th>Hành động</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="refund-table-{{ $key }}">
                                    @forelse ($filteredRefundsForTab as $index => $refund)
                                        <tr data-refund-id="{{ $refund->id }}">
                                            <td>{{ $refunds->firstItem() + $index }}</td>
                                            <td>{{ $refund->user->name ?? 'N/A' }}</td>
                                            <td>{{ $refund->order->order_code ?? ($refund->appointment->appointment_code ?? 'Không có') }}
                                            </td>
                                            <td class="text-end">{{ number_format($refund->refund_amount, 0, ',', '.') }}
                                                VNĐ</td>
                                            <td class="text-center">
                                                @php
                                                    $map = [
                                                        'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
                                                        'processing' => ['label' => 'Đang xử lý', 'class' => 'primary'],
                                                        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'success'],
                                                        'rejected' => ['label' => 'Từ chối', 'class' => 'danger'],
                                                    ];
                                                    $info = $map[$refund->refund_status] ?? [
                                                        'label' => $refund->refund_status,
                                                        'class' => 'secondary',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $info['class'] }}">{{ $info['label'] }}</span>
                                            </td>
                                            @if ($key === 'rejected')
                                                <td>{{ $refund->reject_reason ?? 'N/A' }}</td>
                                            @endif
                                            @if ($key === 'refunded')
                                                <td class="text-center">
                                                    @if ($refund->proof_image)
                                                        <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">
                                                            <img src="{{ Storage::url($refund->proof_image) }}"
                                                                alt="Proof Image"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </a>
                                                    @else
                                                        <span>Không có</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                @if ($refund->trashed())
                                                    <span class="badge bg-danger">Đã xoá</span>
                                                @else
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @endif
                                            </td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('refunds.show', $refund->id) }}">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                            @if ($refund->trashed())
                                                                <li>
                                                                    <button class="dropdown-item text-success restore-btn"
                                                                        data-id="{{ $refund->id }}">
                                                                        <i class="fas fa-undo me-2"></i> Khôi phục
                                                                    </button>
                                                                </li>
                                                            @elseif (in_array($refund->refund_status, ['refunded', 'rejected']))
                                                                <li>
                                                                    <button
                                                                        class="dropdown-item text-danger soft-delete-btn"
                                                                        data-id="{{ $refund->id }}">
                                                                        <i class="fas fa-times me-2"></i> Xoá mềm
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            @php
                                                $colspan = $currentRole == 'admin' ? 8 : 7;
                                                if ($key === 'rejected') {
                                                    $colspan += 1;
                                                }
                                                if ($key === 'refunded') {
                                                    $colspan += 1;
                                                }
                                            @endphp
                                            <td colspan="{{ $colspan }}" class="text-center text-muted">Không có yêu
                                                cầu nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $refunds->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
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

                            fetch(route.replace(':id', id), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => {
                                    if (!response.ok) throw new Error(
                                        'Lỗi xử lý phía máy chủ.');
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
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: error.message,
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

        // Xoá mềm yêu cầu hoàn tiền
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm yêu cầu hoàn tiền',
            text: 'Bạn có chắc chắn muốn xoá mềm yêu cầu này?',
            route: '{{ route('refunds.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Khôi phục yêu cầu hoàn tiền
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục yêu cầu hoàn tiền',
            text: 'Bạn có chắc chắn muốn khôi phục yêu cầu này?',
            route: '{{ route('refunds.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection
