@extends('layouts.AdminLayout')

@section('title', 'Quản lý Cài đặt')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Cài đặt</h3>
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
                <a href="{{ url('admin/settings') }}">Cài đặt</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between4">
            <div class="card-title">Danh sách cài đặt chung</div>
        </div>

        <div class="card-body">
            <form action="{{ route('client.settings.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h5 class="fw-bold mb-3">Liên kết mạng xã hội</h5>
                <div id="social-links">
                    @foreach ($social_links as $index => $link)
                        <div class="setting-item" data-id="{{ $index }}">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label">Tên liên kết</label>
                                    <input type="text" class="form-control" name="social_links[{{ $index }}][key]"
                                        value="{{ $index }}" required> <!-- Sử dụng $index làm key -->
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">URL</label>
                                    <input type="url" class="form-control"
                                        name="social_links[{{ $index }}][value]" value="{{ $link }}"
                                        required> <!-- Sử dụng $link làm value -->
                                </div>

                                <div class="col-md-2 d-flex align-items-start justify-content-center pt-4">
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="removeSetting(this)">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addSocialLink()">Thêm liên
                    kết</button>

                <h5 class="fw-bold mb-3 mt-4">Hình ảnh</h5>
                <div id="images">
                    @foreach ($images as $index => $image)
                        <div class="setting-item" data-id="{{ $index }}">
                            <div class="row g-3 align-items-start">
                                <!-- Tên hình ảnh -->
                                <div class="col-md-5">
                                    <label class="form-label">Tên hình ảnh</label>
                                    <input type="text" class="form-control" name="images[{{ $index }}][key]"
                                        value="{{ $image['key'] }}" required>
                                </div>

                                <!-- Tệp ảnh + Preview -->
                                <div class="col-md-5">
                                    <label class="form-label">Tệp ảnh</label>
                                    <div class="d-flex flex-column flex-md-row align-items-start gap-3">
                                        <div class="flex-fill">
                                            <input type="file" class="form-control mb-2"
                                                name="images[{{ $index }}][value]" accept="image/*"
                                                onchange="previewImage(this, 'imagePreview{{ $index }}')">
                                            <input type="hidden" name="images[{{ $index }}][existing_value]"
                                                value="{{ $image['value'] }}">
                                        </div>
                                        <div>
                                            <img src="{{ $image['value'] ? asset('storage/' . $image['value']) : 'https://via.placeholder.com/200x100?text=No+Image' }}"
                                                id="imagePreview{{ $index }}" class="img-thumbnail shadow-sm"
                                                style="width: 200px; height: 100px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút xóa -->
                                <div class="col-md-2 d-flex align-items-start justify-content-center pt-4">
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="removeSetting(this)">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addImage()">Thêm hình
                    ảnh</button>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-outline-success me-2">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection

@section('js')
    <script>
        let socialLinkCounter = {{ $social_links->count() }};
        let imageCounter = {{ $images->count() }};

        function addSocialLink() {
            // Lấy chỉ số cao nhất từ các phần tử hiện có
            const existingItems = document.querySelectorAll('#social-links .setting-item');
            socialLinkCounter = existingItems.length > 0 ? Math.max(...Array.from(existingItems).map(item => parseInt(item
                .dataset.id))) + 1 : socialLinkCounter + 1;

            const container = document.getElementById('social-links');
            if (!container) {
                console.error('Container #social-links not found');
                return;
            }

            const newItem = document.createElement('div');
            newItem.className = 'setting-item';
            newItem.dataset.id = socialLinkCounter;
            newItem.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="form-label">Tên liên kết</label>
                        <input type="text" class="form-control" name="social_links[${socialLinkCounter}][key]" placeholder="Nhập tên liên kết" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">URL</label>
                        <input type="url" class="form-control" name="social_links[${socialLinkCounter}][value]" placeholder="Nhập URL" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-start justify-content-center pt-4">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="removeSetting(this)">Xóa</button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
        }

        function addImage() {
            imageCounter++;
            const container = document.getElementById('images');
            const newItem = document.createElement('div');
            newItem.className = 'setting-item';
            newItem.dataset.id = imageCounter;
            newItem.innerHTML = `
                <div class="row g-3 align-items-start">
                <div class="col-md-5">
                    <label class="form-label">Tên hình ảnh</label>
                    <input type="text" class="form-control" name="images[${imageCounter}][key]" placeholder="Nhập tên hình ảnh" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Tệp ảnh</label>
                    <div class="d-flex flex-column flex-md-row align-items-start gap-3">
                        <div class="flex-fill">
                            <input type="file" class="form-control mb-2" name="images[${imageCounter}][value]" accept="image/*" onchange="previewImage(this, 'imagePreview${imageCounter}')">
                        </div>
                        <div>
                            <img src="https://via.placeholder.com/200x100?text=New+Image"
                                id="imagePreview${imageCounter}"
                                class="img-thumbnail shadow-sm"
                                style="width: 170px; height: 100px; object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-start justify-content-center pt-4">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        onclick="removeSetting(this)">Xóa</button>
                </div>
            </div>
        `;
            container.appendChild(newItem);
        }

        function removeSetting(button) {
            if (confirm('Bạn có chắc muốn xóa cài đặt này không?')) {
                button.closest('.setting-item').remove();
            }
        }

        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
