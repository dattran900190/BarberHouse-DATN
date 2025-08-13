@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Chatbot Log')

@section('content')
    <!-- Header + Breadcrumb -->
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
                <a href="{{ url('admin/chatbot') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('chatbot.index') }}">Chatbot</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <span>Chi tiết chatbot</span>
            </li>
        </ul>
    </div>

        <!-- Card: Thông tin chatbot -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết Chatbot</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-12">
                    <h4 class="fw-bold mb-3">Thông tin khách hàng</h4>

                    <p class="text-muted mb-2">
                        <i class="fa fa-user me-2 text-primary"></i><strong>Khách hàng:</strong>
                        @if($log->user)

                            <span class="fw-bold">{{ $log->user->name }}</span>
                        @else
                            <span class="badge bg-secondary">Khách vãng lai</span>
                        @endif
                    </p>

                    @if($log->user)
                    <p class="text-muted mb-2">
                        <i class="fa fa-envelope me-2 text-success"></i><strong>Email:</strong>
                        {{ $log->user->email }}
                    </p>
                    @endif


                </div>
            </div>
        </div>
    </div>

    <!-- Card: Lịch sử trò chuyện -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-comments me-2"></i>Lịch sử trò chuyện
            </h4>
        </div>
        <div class="card-body">
            @if($allLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light text-center align-middle">
                            <tr>
                                <th style="width: 40%">Tin nhắn khách hàng</th>
                                <th style="width: 50%">Phản hồi chatbot</th>
                                <th style="width: 10%">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allLogs as $chatLog)
                                <tr>
                                    <td>
                                        <div class="message-content">
                                            <div class="message-header mb-2">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <strong>Khách hàng</strong>
                                                <small class="text-muted float-end">{{ $chatLog->created_at->format('d/m/Y H:i:s') }}</small>
                                            </div>
                                            <div class="message-text">
                                                {{ Str::limit($chatLog->message, 150) }}
                                                @if(strlen($chatLog->message) > 150)
                                                    <span class="message-full" style="display: none;">{{ $chatLog->message }}</span>
                                                    <br><a href="#" class="text-primary show-more" data-target="message-{{ $chatLog->id }}">Xem thêm</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="reply-content">
                                            <div class="reply-header mb-2">
                                                <i class="fas fa-robot text-success me-2"></i>
                                                <strong>Chatbot</strong>
                                                <small class="text-muted float-end">{{ $chatLog->updated_at->format('d/m/Y H:i:s') }}</small>
                                            </div>
                                            <div class="reply-text">
                                                {{ Str::limit($chatLog->reply, 150) }}
                                                @if(strlen($chatLog->reply) > 150)
                                                    <span class="reply-full" style="display: none;">{{ $chatLog->reply }}</span>
                                                    <br><a href="#" class="text-success show-more" data-target="reply-{{ $chatLog->id }}">Xem thêm</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-message-btn" data-id="{{ $chatLog->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có lịch sử trò chuyện nào.
                </div>
            @endif
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">

                <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-id="{{ $log->id }}">
                    <i class="fas fa-trash me-2"></i> Xoá
                </button>

                <a href="{{ route('chatbot.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const logId = this.getAttribute('data-id');

                    Swal.fire({
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
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(route.replace(':id', logId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
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
                                            window.location.href = '{{ route('chatbot.show', $log->id) }}';
                                        }
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error'
                                    });
                                });
                        }
                    });
                });
            });
        }

        // Xử lý nút xóa từng tin nhắn
        document.querySelectorAll('.delete-message-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const messageId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Xóa tin nhắn',
                    text: 'Bạn có chắc chắn muốn xóa tin nhắn này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hiển thị loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => Swal.showLoading()
                        });

                        // Gửi request xóa
                        fetch(`/admin/chatbot/message/${messageId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                // Xóa row khỏi bảng
                                const row = button.closest('tr');
                                row.remove();

                                // Hiển thị thông báo thành công
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#7e84f3',
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Đã có lỗi xảy ra: ' + error.message,
                                icon: 'error',
                                confirmClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        });
                    }
                });
            });
        });

        // Xử lý nút xóa toàn bộ chat
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const logId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Xoá chat log',
                    text: 'Bạn có chắc muốn xoá chat log này?',
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

        // Xử lý chức năng "Xem thêm"
        document.querySelectorAll('.show-more').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-target');
                const targetElement = document.querySelector(`[data-target="${target}"]`);

                if (targetElement) {
                    const shortText = this.previousElementSibling;
                    const fullText = targetElement;

                    if (fullText.style.display === 'none') {
                        shortText.style.display = 'none';
                        fullText.style.display = 'inline';
                        this.textContent = 'Thu gọn';
                    } else {
                        shortText.style.display = 'inline';
                        fullText.style.display = 'none';
                        this.textContent = 'Xem thêm';
                    }
                }
            });
        });
    </script>
@endsection
