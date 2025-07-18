@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Sản phẩm</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">
            <a href="{{ url('admin/dashboard') }}">Danh sách sản phẩm</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/products/' . $product->id .'/edit') }}">Chỉnh sửa sản phẩm</a>
        </li>
    </ul>
</div>
<div class="container-fluid px-4">
    <h1 class="mb-4">Chỉnh sửa sản phẩm</h1>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            {{-- Cột trái: Thông tin sản phẩm --}}
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="product_category_id" class="form-label">Danh mục</label>
                    <select name="product_category_id" id="product_category_id" class="form-control">
                        <option value="">Chọn danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('product_category_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}">
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="long_description" class="form-label">Mô tả dài</label>
                    <textarea name="long_description" id="long_description" class="form-control">{{ old('long_description', $product->long_description) }}</textarea>
                    @error('long_description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá đại diện</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01">
                    @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="stock" class="form-label">Tồn kho</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}">
                    @error('stock') <div class="text-danger">{{ $message }}</div> @enderror
                </div> --}}

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" width="100" class="mt-2">
                    @endif
                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                    <input type="file" name="additional_images[]" id="additional_images" class="form-control" accept="image/*" multiple>
                    @if ($product->images->isNotEmpty())
                        <div class="mt-2">
                            <p>Hình ảnh hiện tại:</p>
                            @foreach ($product->images as $image)
                                <div class="d-inline-block me-2 mb-2 text-center">
                                    <img src="{{ asset('storage/' . $image->image_url) }}" width="100">
                                    <div>
                                        <label><input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Xóa</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('additional_images.*') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Cột phải: Biến thể sản phẩm --}}
            <div class="col-md-4">
                <h5 class="mb-3">Biến thể sản phẩm</h5>
                <div id="variants">
                    @php
                        $oldVariants = old('variants', []);
                        $variantsToShow = count($oldVariants) ? $oldVariants : $product->variants->toArray();
                        $volumeOptions = $volumes;
                    @endphp

                    @foreach ($variantsToShow as $index => $variant)
                        <div class="variant border p-3 mb-3">
                            @if (isset($variant['id']) || (!is_array($variant) && isset($variant->id)))
                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ is_array($variant) ? ($variant['id'] ?? '') : $variant->id }}">
                                <div class="mb-2">
                                    <label><input type="checkbox" name="delete_variants[]" value="{{ is_array($variant) ? ($variant['id'] ?? '') : $variant->id }}"> Xóa biến thể</label>
                                </div>
                            @endif

                            <div class="mb-2">
                                <label class="form-label">Dung tích</label>
                                <select name="variants[{{ $index }}][volume_id]" class="form-control">
                                    <option value="">Chọn dung tích</option>
                                    @foreach ($volumeOptions as $volume)
                                        <option value="{{ $volume->id }}" {{ (is_array($variant) ? ($variant['volume_id'] ?? '') : ($variant['volume_id'] ?? '')) == $volume->id ? 'selected' : '' }}>{{ $volume->name }}</option>
                                    @endforeach
                                </select>
                                @error("variants.$index.volume_id") <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Giá</label>
                                <input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ is_array($variant) ? ($variant['price'] ?? '') : $variant['price'] }}" step="0.01">
                                @error("variants.$index.price") <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" name="variants[{{ $index }}][stock]" class="form-control" value="{{ is_array($variant) ? ($variant['stock'] ?? '') : $variant['stock'] }}">
                                @error("variants.$index.stock") <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Ảnh biến thể</label>
                                <input type="file" name="variants[{{ $index }}][image]" class="form-control" accept="image/*">
                                @if (!is_array($variant) && $variant->image)
                                    <img src="{{ asset('storage/' . $variant->image) }}" width="100" class="mt-2">
                                @endif
                                @error("variants.$index.image") <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa khỏi giao diện</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-success" id="add-variant">Thêm biến thể</button>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-sm btn-outline-primary">Cập nhật</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
        </div>
      
    </form>
</div>

<script>
    let variantIndex = {{ count(old('variants', $product->variants)) }};

    document.getElementById('add-variant').addEventListener('click', function () {
        const variantDiv = document.createElement('div');
        variantDiv.classList.add('variant', 'border', 'p-3', 'mb-3');

        variantDiv.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Dung tích</label>
                <select name="variants[${variantIndex}][volume_id]" class="form-control">
                    <option value="">Chọn dung tích</option>
                    @foreach ($volumes as $volume)
                        <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Giá</label>
                <input type="number" name="variants[${variantIndex}][price]" class="form-control" step="0.01">
            </div>
            <div class="mb-2">
                <label class="form-label">Tồn kho</label>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Ảnh biến thể</label>
                <input type="file" name="variants[${variantIndex}][image]" class="form-control" accept="image/*">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger btn-sm remove-variant">Xóa khỏi giao diện</button>
        `;
        document.getElementById('variants').appendChild(variantDiv);
        variantIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            const variantsContainer = document.getElementById('variants');
            const visibleVariants = Array.from(variantsContainer.querySelectorAll('.variant')).filter(v => v.style.display !== 'none');
            if (visibleVariants.length <= 1) {
                alert('Phải có ít nhất 1 biến thể!');
                return;
            }
            const variantDiv = e.target.closest('.variant');
            // Tìm checkbox xóa biến thể trong variantDiv
            const checkbox = variantDiv.querySelector('input[type="checkbox"][name^="delete_variants"]');
            if (checkbox) {
                if (checkbox.checked) {
                    variantDiv.style.display = 'none'; // Ẩn khỏi giao diện nếu đã tick checkbox
                } else {
                    alert('Bạn phải tick vào ô "Xóa biến thể" trước!');
                }
            } else {
                // Biến thể mới thì xóa khỏi DOM luôn
                variantDiv.remove();
            }
        }
    });
</script>
@endsection