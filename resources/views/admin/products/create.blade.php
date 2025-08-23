@extends('layouts.AdminLayout')

@section('title', 'Quản lý Sản phẩm')

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
        <h3 class="fw-bold mb-3 text-uppercase">Sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/dashboard') }}">Danh sách sản phẩm</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/products/create') }}">Thêm sản phẩm</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm sản phẩm</div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Danh mục --}}
                    <div class="mb-3 col-md-6">
                        <label for="product_category_id" class="form-label">Danh mục</label>
                        <select name="product_category_id" id="product_category_id" class="form-control w-100">
                            <option value="">Chọn danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('product_category_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên sản phẩm --}}
                    <div class="mb-3 col-md-6">
                        <label for="name" class="form-label">Tên sản phẩm</label>
                        <input type="text" name="name" id="name" class="form-control w-100"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Mô tả --}}
                    <div class="mb-3 col-md-6">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea name="description" id="description" class="form-control w-100">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mô tả dài --}}
                    <div class="mb-3 col-md-6">
                        <label for="long_description" class="form-label">Mô tả dài</label>
                        <textarea name="long_description" id="long_description" class="form-control w-100">{{ old('long_description') }}</textarea>
                        @error('long_description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Giá đại diện --}}
                <div class="mb-3">
                    <label for="price" class="form-label">Giá đại diện</label>
                    <input type="number" name="price" id="price" class="form-control w-100" step="0.01"
                        value="{{ old('price') }}">
                    @error('price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh chính --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                    <input type="file" name="image" id="image" class="form-control w-100"
                        accept="image/jpeg,image/png,image/jpg">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh bổ sung --}}
                <div class="mb-3">
                    <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                    <input type="file" name="additional_images[]" id="additional_images" class="form-control w-100"
                        multiple accept="image/jpeg,image/png,image/jpg">
                    @error('additional_images')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Biến thể --}}
                <h3>Biến thể sản phẩm</h3>
                <div id="variants">
                    @php
                        $oldVariants = old('variants', [['volume_id' => '', 'price' => '', 'stock' => '']]);
                    @endphp
                    @foreach ($oldVariants as $index => $variant)
                        <div class="variant mb-3 border p-3">
                            <div class="row align-items-end">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Dung tích</label>
                                    <select name="variants[{{ $index }}][volume_id]" class="form-control w-100">
                                        <option value="">Chọn dung tích</option>
                                        @foreach ($volumes as $volume)
                                            <option value="{{ $volume->id }}"
                                                {{ (string) ($variant['volume_id'] ?? '') === (string) $volume->id ? 'selected' : '' }}>
                                                {{ $volume->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("variants.{$index}.volume_id")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">Giá</label>
                                    <input type="number" name="variants[{{ $index }}][price]"
                                        class="form-control w-100" step="0.01" value="{{ $variant['price'] }}">
                                    @error("variants.{$index}.price")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">Tồn kho</label>
                                    <input type="number" name="variants[{{ $index }}][stock]"
                                        class="form-control w-100" value="{{ $variant['stock'] }}">
                                    @error("variants.{$index}.stock")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Ảnh biến thể</label>
                                    <input type="file" name="variants[{{ $index }}][image]"
                                        class="form-control w-100" accept="image/jpeg,image/png,image/jpg">
                                    @error("variants.{$index}.image")
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa biến
                                        thể</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-outline-success" id="add-variant">Thêm biến thể</button>

                <br><br>
                <button type="submit" class="btn btn-sm btn-outline-success">Thêm mới</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
            </form>
        </div>
    </div>

    {{-- Template ẩn --}}
    <template id="variant-template">
        <div class="variant mb-3 border p-3">
            <div class="row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Dung tích</label>
                    <select class="form-control volume-select">
                        <option value="">Chọn dung tích</option>
                        @foreach ($volumes as $volume)
                            <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Giá</label>
                    <input type="number" class="form-control price-input" step="0.01">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Tồn kho</label>
                    <input type="number" class="form-control stock-input">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Ảnh biến thể</label>
                    <input type="file" class="form-control image-input" accept="image/jpeg,image/png,image/jpg">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa biến thể</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Script --}}
    <script>
        let variantIndex = {{ count($oldVariants) }};

        document.getElementById('add-variant').addEventListener('click', function() {
            const template = document.getElementById('variant-template');
            const clone = template.content.cloneNode(true);

            // Gán lại name động
            clone.querySelector('.volume-select').name = `variants[${variantIndex}][volume_id]`;
            clone.querySelector('.price-input').name = `variants[${variantIndex}][price]`;
            clone.querySelector('.stock-input').name = `variants[${variantIndex}][stock]`;
            clone.querySelector('.image-input').name = `variants[${variantIndex}][image]`;

            document.getElementById('variants').appendChild(clone);
            variantIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                const variants = document.querySelectorAll('#variants .variant');
                if (variants.length > 1) {
                    e.target.closest('.variant').remove();
                } else {
                    showToast('Phải có ít nhất 1 biến thể!');
                }
            }
        });

        // Thêm hàm showToast sử dụng Bootstrap Toast
        function showToast(message) {
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.position = 'fixed';
                toastContainer.style.top = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-bg-danger border-0 show';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.minWidth = '200px';
            toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
            toastContainer.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }, 2000);
        }
    </script>
@endsection
