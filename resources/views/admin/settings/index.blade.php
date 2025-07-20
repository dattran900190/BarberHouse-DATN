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
            <form id="settings-form" action="{{ route('client.settings.save') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <h5 class="fw-bold mb-3">Liên kết mạng xã hội</h5>
                <div class="row g-3 mb-4">
                    @foreach (['youtube', 'facebook', 'instagram', 'tiktok'] as $key)
                        <div class="col-md-6">
                            <label class="form-label text-capitalize">{{ $key }}</label>
                            <input type="hidden" name="social_links[{{ $key }}][key]" value="{{ $key }}">
                            <input type="url" class="form-control" name="social_links[{{ $key }}][value]"
                                value="{{ data_get($social_links, $key, '') }}" placeholder="Nhập URL {{ $key }}">
                        </div>
                    @endforeach
                </div>

                <h5 class="fw-bold mb-3">Hình ảnh</h5>
                <div class="row g-4">
                    @foreach (['anh_dang_nhap' => 'Ảnh đăng nhập', 'anh_dang_ky' => 'Ảnh đăng ký', 'bang_gia' => 'Ảnh bảng giá'] as $key => $label)
                        <div class="col-md-4">
                            <label class="form-label">{{ $label }}</label>
                            <input type="hidden" name="images[{{ $key }}][key]" value="{{ $key }}">
                            <div class="mb-2">
                                <input type="file" class="form-control" name="images[{{ $key }}][value]"
                                    accept="image/*" onchange="previewImage(this,'preview-{{ $key }}')">
                                <input type="hidden" name="images[{{ $key }}][existing_value]"
                                    value="{{ data_get($images, $key . '.value', '') }}">
                            </div>
                            <img id="preview-{{ $key }}"
                                src="{{ data_get($images, $key . '.value') ? asset('storage/' . data_get($images, $key . '.value')) : 'https://via.placeholder.com/200x100?text=No+Image' }}"
                                class="img-thumbnail" style="width:300px; height:200px; object-fit:cover;">
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 ">
                    <button type="button" class="btn btn-outline-success submit-setting-btn">
                        Lưu thay đổi
                    </button>
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
        function previewImage(input, id) {
            if (!input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => document.getElementById(id).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    </script>

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
                        if (!result.isConfirmed) return;

                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => Swal.showLoading()
                        });

                        const form = document.getElementById('settings-form');
                        const fd = new FormData(form);

                        fetch(form.action, {
                                method,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: fd
                            })
                            .then(res => res.json())
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
                                    text: 'Đã có lỗi xảy ra: ' + error.message,
                                    icon: 'error',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            });
                    });
                });
            });
        }

        // Gọi hàm
        handleSwalAction({
            selector: '.submit-setting-btn',
            title: 'Thay đổi cài đặt',
            text: 'Bạn có chắc chắn muốn thay đổi cài đặt này?',
            route: '{{ route('client.settings.save') }}',
            method: 'POST'
        });
    </script>

@endsection
