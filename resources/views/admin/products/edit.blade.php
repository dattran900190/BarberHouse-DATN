@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Chỉnh sửa sản phẩm</h1>
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="product_category_id" class="form-label">Danh mục</label>
                <select name="product_category_id" id="product_category_id" class="form-control" required>
                    <option value="">Chọn danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->product_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('product_category_id')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" required>
                @error('price')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Tồn kho</label>
                <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                @error('stock')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" width="100" class="mt-2" alt="Ảnh chính">
                @endif
                @error('image')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <div class="mb-3">
                <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                <input type="file" name="additional_images[]" id="additional_images" class="form-control" accept="image/jpeg,image/png,image/jpg" multiple>
                @if ($product->images->isNotEmpty())
                    <div class="mt-2">
                        <p>Hình ảnh hiện tại:</p>
                        @foreach ($product->images as $image)
                            <div class="d-inline-block me-2 mb-2">
                                <img src="{{ asset('storage/' . $image->image_url) }}" width="100" alt="Ảnh bổ sung">
                                <div>
                                    <label><input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Xóa</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('additional_images.*')
                    <div class="text-danger">{{ $message }}</div>
                @endError
            </div>

            <h3>Biến thể sản phẩm</h3>
            <div id="variants">
                @foreach ($product->variants as $index => $variant)
                    <div class="variant mb-3 border p-3">
                        <div class="mb-3">
                            <label for="variants[{{ $index }}][volume_id]" class="form-label">Dung tích</label>
                            <select name="variants[{{ $index }}][volume_id]" class="form-control" required>
                                <option value="">Chọn dung tích</option>
                                @foreach ($volumes as $volume)
                                    <option value="{{ $volume->id }}" {{ $variant->volume_id == $volume->id ? 'selected' : '' }}>{{ $volume->name }}</option>
                                @endforeach
                            </select>
                            @error("variants.$index.volume_id")
                                <div class="text-danger">{{ $message }}</div>
                            @endError
                        </div>
                        <div class="mb-3">
                            <label for="variants[{{ $index }}][price]" class="form-label">Giá</label>
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ $variant->price }}" step="0.01" required>
                            @error("variants.$index.price")
                                <div class="text-danger">{{ $message }}</div>
                            @endError
                        </div>
                        <div class="mb-3">
                            <label for="variants[{{ $index }}][stock]" class="form-label">Tồn kho</label>
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control" value="{{ $variant->stock }}" required>
                            @error("variants.$index.stock")
                                <div class="text-danger">{{ $message }}</div>
                            @endError
                        </div>
                        <div class="mb-3">
                            <label for="variants[{{ $index }}][image]" class="form-label">Ảnh biến thể</label>
                            <input type="file" name="variants[{{ $index }}][image]" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            @if ($variant->image)
                                <img src="{{ asset('storage/' . $variant->image) }}" width="100" class="mt-2" alt="Ảnh biến thể">
                            @endif
                            @error("variants.$index.image")
                                <div class="text-danger">{{ $message }}</div>
                            @endError
                        </div>
                        <button type="button" class="btn btn-danger remove-variant">Xóa biến thể</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="add-variant">Thêm biến thể</button>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>

    <script>
        let variantIndex = {{ $product->variants->count() }};
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