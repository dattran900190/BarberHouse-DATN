@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Chatbot Log')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chỉnh sửa Chatbot Log</h3>
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
                <a href="{{ route('chatbot.index') }}">Quản lý Chatbot</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('chatbot.show', $log->id) }}">Chi tiết</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <span>Chỉnh sửa</span>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Chỉnh sửa Chat Log #{{ $log->id }}</div>
            <div>
                <a href="{{ route('chatbot.show', $log->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Xem chi tiết
                </a>
                <a href="{{ route('chatbot.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('chatbot.update', $log->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">
                                <i class="fas fa-comment text-primary"></i> Tin nhắn từ khách hàng
                            </label>
                            <textarea
                                name="message"
                                id="message"
                                rows="6"
                                class="form-control @error('message') is-invalid @enderror"
                                placeholder="Nhập tin nhắn từ khách hàng..."
                                maxlength="1000"
                            >{{ old('message', $log->message) }}</textarea>
                            <div class="form-text">
                                <span id="message-count">0</span>/1000 ký tự
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reply" class="form-label fw-bold">
                                <i class="fas fa-robot text-success"></i> Phản hồi từ Chatbot
                            </label>
                            <textarea
                                name="reply"
                                id="reply"
                                rows="6"
                                class="form-control @error('reply') is-invalid @enderror"
                                placeholder="Nhập phản hồi từ chatbot..."
                                maxlength="2000"
                            >{{ old('reply', $log->reply) }}</textarea>
                            <div class="form-text">
                                <span id="reply-count">0</span>/2000 ký tự
                            </div>
                            @error('reply')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-info-circle"></i> Thông tin bổ sung
                                </h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <small class="text-muted">User ID:</small>
                                        <div class="fw-bold">
                                            @if($log->user)
                                                <span class="badge bg-info">{{ $log->user_id }}</span>
                                                <span class="ms-2">{{ $log->user->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Guest</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Ngày tạo:</small>
                                        <div class="fw-bold">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Lần cập nhật cuối:</small>
                                        <div class="fw-bold">{{ $log->updated_at->format('d/m/Y H:i:s') }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">ID Log:</small>
                                        <div class="fw-bold">#{{ $log->id }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('chatbot.show', $log->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </a>
                        <a href="{{ route('chatbot.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Đếm ký tự cho textarea
        function updateCharCount(textareaId, countId, maxLength) {
            const textarea = document.getElementById(textareaId);
            const count = document.getElementById(countId);

            textarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                count.textContent = currentLength;

                if (currentLength > maxLength * 0.9) {
                    count.style.color = 'orange';
                } else if (currentLength > maxLength * 0.8) {
                    count.style.color = 'blue';
                } else {
                    count.style.color = 'inherit';
                }

                if (currentLength >= maxLength) {
                    count.style.color = 'red';
                }
            });

            // Khởi tạo count ban đầu
            count.textContent = textarea.value.length;
        }

        // Khởi tạo đếm ký tự
        updateCharCount('message', 'message-count', 1000);
        updateCharCount('reply', 'reply-count', 2000);
    </script>
@endsection
