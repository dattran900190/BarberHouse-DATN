@extends('adminlte::page')

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

<div class="container">
    <h1>Thêm sản phẩm</h1>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Phần 1: Thông tin sản phẩm chính -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="product_category_id" class="form-label">Danh mục</label>
                    <select name="product_category_id" id="product_category_id" class="form-control" >
                        <option value="">Chọn danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('product_category_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" >
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" >
                    @error('price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Tồn kho</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock') }}" >
                    @error('stock')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                    <input type="file" name="additional_images[]" id="additional_images" class="form-control" accept="image/jpeg,image/png,image/jpg" multiple>
                    @error('additional_images.*')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Phần 2: Biến thể sản phẩm -->
            <div class="col-md-6">
                <h3>Biến thể sản phẩm</h3>
                <div id="variants">
                    {{-- Hiển thị biến thể cũ (nếu có), ngược lại show 1 biến thể mặc định --}}
                    @php
                        $oldVariants = old('variants', [
                            ['volume_id' => '', 'price' => '', 'stock' => '']
                        ]);
                    @endphp

                    @foreach ($oldVariants as $index => $variant)
                        <div class="variant mb-3 border p-3">
                            <div class="mb-3">
                                <label for="variants[{{ $index }}][volume_id]" class="form-label">Dung tích</label>
                                <select name="variants[{{ $index }}][volume_id]" class="form-control" >
                                    <option value="">Chọn dung tích</option>
                                    @foreach ($volumes as $volume)
                                        <option value="{{ $volume->id }}" {{ (string)($variant['volume_id'] ?? '') === (string)$volume->id ? 'selected' : '' }}>{{ $volume->name }}</option>
                                    @endforeach
                                </select>
                                @error('variants.' . $index . '.volume_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="variants[{{ $index }}][price]" class="form-label">Giá</label>
                                <input type="number" name="variants[{{ $index }}][price]" class="form-control" step="0.01" value="{{ $variant['price'] ?? '' }}">
                                @error('variants.' . $index . '.price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="variants[{{ $index }}][stock]" class="form-label">Tồn kho</label>
                                <input type="number" name="variants[{{ $index }}][stock]" class="form-control" value="{{ $variant['stock'] ?? '' }}">
                                @error('variants.' . $index . '.stock')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="variants[{{ $index }}][image]" class="form-label">Ảnh biến thể</label>
                                <input type="file" name="variants[{{ $index }}][image]" class="form-control" accept="image/jpeg,image/png,image/jpg">
                                @error('variants.' . $index . '.image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-danger remove-variant">Xóa biến thể</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-secondary mb-3" id="add-variant">Thêm biến thể</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Thêm mới</button>
    </form>
</div>

<script>
    let variantIndex = {{ count($oldVariants) }};

    document.getElementById('add-variant').addEventListener('click', function () {
        const variantDiv = document.createElement('div');
        variantDiv.classList.add('variant', 'mb-3', 'border', 'p-3');

        variantDiv.innerHTML = `
            <div class="mb-3">
                <label for="variants[${variantIndex}][volume_id]" class="form-label">Dung tích</label>
                <select name="variants[${variantIndex}][volume_id]" class="form-control" >
                    <option value="">Chọn dung tích</option>
                    @foreach ($volumes as $volume)
                        <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="variants[${variantIndex}][price]" class="form-label">Giá</label>
                <input type="number" name="variants[${variantIndex}][price]" class="form-control" step="0.01" >
            </div>
            <div class="mb-3">
                <label for="variants[${variantIndex}][stock]" class="form-label">Tồn kho</label>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control" >
            </div>
            <div class="mb-3">
                <label for="variants[${variantIndex}][image]" class="form-label">Ảnh biến thể</label>
                <input type="file" name="variants[${variantIndex}][image]" class="form-control" accept="image/jpeg,image/png,image/jpg">
            </div>
            <button type="button" class="btn btn-danger remove-variant">Xóa biến thể</button>
        `;
        document.getElementById('variants').appendChild(variantDiv);
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant').remove();
        }
    });
</script>
@endsection
