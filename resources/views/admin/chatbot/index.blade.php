@extends('layouts.AdminLayout')

@section('title', 'Quản lý Chatbot Logs')

@section('content')
    {{-- @if (session('success'))
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
    @endif --}}

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Chatbot</h3>
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
                <a href="{{ route('chatbot.index') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('chatbot.index') }}">Chatbot</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách Chatbot</div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('chatbot.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên khách hàng hoặc nội dung chat..."
                        value="{{ request('search') }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center align-middle">
                        <tr>
                            <th>STT</th>
                            <th>Khách hàng</th>

                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($chatLogs as $index => $log)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    @if($log->user)
                                        <span class="badge bg-info me-2">{{ $log->user->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Khách vãng lai</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionMenu{{ $log->latest_id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionMenu{{ $log->latest_id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('chatbot.show', $log->latest_id) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem chi tiết
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Chưa có log nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $chatLogs->links() }}
            </div>
        </div>
    </div>
@endsection

        {{-- <style>
        .custom-swal-popup {
            border-radius: 10px !important;
            font-family: 'Roboto', sans-serif !important;
        }

        .custom-swal-popup .swal2-title {
            color: #333 !important;
            font-weight: 600 !important;
            font-size: 18px !important;
        }

        .custom-swal-popup .swal2-content {
            color: #666 !important;
            font-size: 14px !important;
        }

        .custom-swal-popup .swal2-confirm {
            background-color: #7e84f3 !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 30px !important;
            font-weight: 500 !important;
            color: white !important;
        }

        .custom-swal-popup .swal2-confirm:hover {
            background-color: #6c75e8 !important;
        }

        .custom-swal-popup .swal2-cancel {
            background-color: #6c757d !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 30px !important;
            font-weight: 500 !important;
            color: white !important;
        }

        .custom-swal-popup .swal2-cancel:hover {
            background-color: #5a6268 !important;
        }
    </style> --}}

    @section('js')
        <script>
            // Hiển thị thông báo success bằng SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: 'Thành công!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                });
            @endif

            // Hiển thị thông báo error bằng SweetAlert
            @if (session('error'))
                Swal.fire({
                    title: 'Lỗi!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                });
            @endif

            // Xử lý nút xóa
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const logId = this.getAttribute('data-id');

                                    Swal.fire({
                    title: 'Xoá chat',
                    text: 'Bạn có chắc chắn muốn xoá chat này?',
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
                            // Tạo form để submit
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('chatbot.index') }}/' + logId;

                            // Thêm CSRF token
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            form.appendChild(csrfToken);

                            // Thêm method DELETE
                            const methodField = document.createElement('input');
                            methodField.type = 'hidden';
                            methodField.name = '_method';
                            methodField.value = 'DELETE';
                            form.appendChild(methodField);

                            // Submit form
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endsection
