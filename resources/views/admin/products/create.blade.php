@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Thêm sản phẩm</h1>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="product_category_id" class="form-label">Danh mục</label>
                <select name="product_category_id" id="product_category_id" class="form-control" required>
                    <option value="">Chọn danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('product_category_id')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" required>
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Tồn kho</label>
                <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock') }}" required>
                @error('stock')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                <input type="file" name="additional_images[]" id="additional_images" class="form-control" accept="image/jpeg,image/png,image/jpg" multiple>
                @error('additional_images.*')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <h3>Biến thể sản phẩm</h3>
            <div id="variants">
                <div class="variant mb-3 border p-3">
                    <div class="mb-3">
                        <label for="variants[0][volume_id]" class="form-label">Dung tích</label>
                        <select name="variants[0][volume_id]" class="form-control" required>
                            <option value="">Chọn dung tích</option>
                            @foreach ($volumes as $volume)
                                <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                            @endforeach
                        </select>
                        @error('variants.0.volume_id')
                            <div class="text-danger">{{ $message }}</div>
                        @endError
                    </div>
                    <div class="mb-3">
                        <label for="variants[0][price]" class="form-label">Giá</label>
                        <input type="number" name="variants[0][price]" class="form-control" step="0.01" required>
                        @error('variants.0.price')
                            <div class="text-danger">{{ $message }}</div>
                        @endError
                    </div>
                    <div class="mb-3">
                        <label for="variants[0][stock]" class="form-label">Tồn kho</label>
                        <input type="number" name="variants[0][stock]" class="form-control" required>
                        @error('variants.0.stock')
                            <div class="text-danger">{{ $message }}</div>
                        @endError
                    </div>
                    <div class="mb-3">
                        <label for="variants[0][image]" class="form-label">Ảnh biến thể</label>
                        <input type="file" name="variants[0][image]" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        @error('variants.0.image')
                            <div class="text-danger">{{ $message }}</div>
                        @endError
                    </div>
                    <button type="button" class="btn btn-danger remove-variant">Xóa biến thể</button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-variant">Thêm biến thể</button>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </form>
    </div>

    <script>
        let variantIndex = 1;
        document.getElementById('add-variant').addEventListener('click', function () {
            const variantDiv = document.createElement('div');
            variantDiv.classList.add('variant', 'mb-3', 'border', 'p-3');
            variantDiv.innerHTML = `
                <div class="mb-3">
                    <label for="variants[${variantIndex}][volume_id]" class="form-label">Dung tích</label>
                    <select name="variants[${variantIndex}][volume_id]" class="form-control" required>
                        <option value="">Chọn dung tích</option>
                        @foreach ($volumes as $volume)
                            <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="variants[${variantIndex}][price]" class="form-label">Giá</label>
                    <input type="number" name="variants[${variantIndex}][price]" class="form-control" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="variants[${variantIndex}][stock]" class="form-label">Tồn kho</label>
                    <input type="number" name="variants[${variantIndex}][stock]" class="form-control" required>
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