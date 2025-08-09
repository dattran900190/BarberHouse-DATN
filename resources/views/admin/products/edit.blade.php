@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/products') }}">Quản lý sản phẩm</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/products') }}">Danh sách sản phẩm</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/products/' . $product->id . '/edit') }}">Chỉnh sửa sản phẩm</a>
            </li>
        </ul>
    </div>
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa sản phẩm</div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- Bỏ row/col, chỉ để 1 div bọc --}}
                <div>
                    {{-- Danh mục --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_category_id" class="form-label">Danh mục</label>
                            <select name="product_category_id" id="product_category_id" class="form-control w-100">
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('product_category_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Tên sản phẩm</label>
                            <input type="text" name="name" id="name" class="form-control w-100"
                                value="{{ old('name', $product->name) }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Mô tả --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea name="description" id="description" class="form-control w-100">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="long_description" class="form-label">Mô tả dài</label>
                            <textarea name="long_description" id="long_description" class="form-control w-100">{{ old('long_description', $product->long_description) }}</textarea>
                            @error('long_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- Giá đại diện --}}
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá đại diện</label>
                        <input type="number" name="price" id="price" class="form-control w-100"
                            value="{{ old('price', $product->price) }}" step="0.01">
                        @error('price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Ảnh chính sản phẩm --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Ảnh chính sản phẩm</label>
                        <input type="file" name="image" id="image" class="form-control w-100" accept="image/*">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" width="100" class="mt-2">
                        @endif
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Ảnh bổ sung --}}
                    <div class="mb-3">
                        <label for="additional_images" class="form-label">Ảnh bổ sung</label>
                        <input type="file" name="additional_images[]" id="additional_images" class="form-control w-100"
                            accept="image/*" multiple>
                        @if ($product->images->isNotEmpty())
                            <div class="mt-2">
                                <p>Hình ảnh hiện tại:</p>
                                @foreach ($product->images as $image)
                                    <div class="d-inline-block me-2 mb-2 text-center">
                                        <img src="{{ asset('storage/' . $image->image_url) }}" width="100">
                                        <div>
                                            <label><input type="checkbox" name="delete_images[]"
                                                    value="{{ $image->id }}"> Xóa</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('additional_images.*')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Gộp bảng biến thể --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Biến thể sản phẩm</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Dung tích</th>
                                    <th>Giá</th>
                                    <th>Tồn kho</th>
                                    <th>Ảnh</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allVariants = $product->variants()->withTrashed()->get();
                                    $trashedCount = $allVariants->where('deleted_at', '!=', null)->count();
                                @endphp

                                @foreach ($allVariants as $variant)
                                    <tr class="variant-row" data-variant-id="{{ $variant->id }}">
                                        <td>
                                            @if (isset($variant->id) && !$variant->trashed())
                                                <input type="hidden" name="variants[{{ $variant->id }}][id]"
                                                    value="{{ $variant->id }}">
                                            @endif
                                            @if ($variant->trashed())
                                                {{ optional($variant->volume)->name }}
                                            @else
                                                <select name="variants[{{ $variant->id }}][volume_id]"
                                                    class="form-control">
                                                    <option value="">Chọn dung tích</option>
                                                    @foreach ($volumes as $volume)
                                                        <option value="{{ $volume->id }}"
                                                            {{ $variant->volume_id == $volume->id ? 'selected' : '' }}>
                                                            {{ $volume->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            @error("variants.{$variant->id}.volume_id")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if ($variant->trashed())
                                                {{ $variant->price }}
                                            @else
                                                <input type="number" name="variants[{{ $variant->id }}][price]"
                                                    class="form-control" step="0.01" value="{{ $variant->price }}">
                                            @endif
                                            @error("variants.{$variant->id}.price")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if ($variant->trashed())
                                                {{ $variant->stock }}
                                            @else
                                                <input type="number" name="variants[{{ $variant->id }}][stock]"
                                                    class="form-control" value="{{ $variant->stock }}">
                                            @endif
                                            @error("variants.{$variant->id}.stock")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if ($variant->image)
                                                <img src="{{ asset('storage/' . $variant->image) }}" width="60"
                                                    class="mb-1"><br>
                                            @endif
                                            @if (!$variant->trashed())
                                                <input type="file" name="variants[{{ $variant->id }}][image]"
                                                    class="form-control" accept="image/*">
                                            @endif
                                            @error("variants.{$variant->id}.image")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if ($variant->trashed())
                                                <span class="badge bg-secondary">Đã ẩn</span>
                                            @else
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($variant->trashed())
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success btn-restore-variant"
                                                    data-variant-id="{{ $variant->id }}">Khôi phục</button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-hard-delete-variant"
                                                    data-variant-id="{{ $variant->id }}">Xóa cứng</button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger btn-soft-delete-variant"
                                                    data-variant-id="{{ $variant->id }}">Ẩn</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-variant">Thêm biến
                            thể</button>

                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Cập nhật</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let variantIndex = {{ count(old('variants', $product->variants)) }};

        document.getElementById('add-variant').addEventListener('click', function() {
            const tbody = document.querySelector('.table.table-bordered tbody');
            const newRow = document.createElement('tr');
            newRow.classList.add('variant');
            newRow.innerHTML = `
            <td>
                <select name="variants[${variantIndex}][volume_id]" class="form-control">
                    <option value="">Chọn dung tích</option>
                    @foreach ($volumes as $volume)
                        <option value="{{ $volume->id }}">{{ $volume->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][price]" class="form-control" step="0.01">
            </td>
            <td>
                <input type="number" name="variants[${variantIndex}][stock]" class="form-control">
            </td>
            <td>
                <input type="file" name="variants[${variantIndex}][image]" class="form-control" accept="image/*">
            </td>
            <td>
                <span class="badge bg-success">Đang hoạt động</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger remove-variant">Xóa</button>
            </td>
        `;
            tbody.appendChild(newRow);
            variantIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant')) {
                const row = e.target.closest('tr.variant');
                if (row) row.remove();
            }
        });

        document.getElementById('soft-delete-all').addEventListener('click', function() {
            document.querySelectorAll('input[type=checkbox][name^="delete_variants"]').forEach(cb => cb.checked =
                true);
        });
    </script>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Xóa mềm biến thể
        function addSoftDeleteListener(btn) {
            btn.addEventListener('click', function() {
                var variantId = this.getAttribute('data-variant-id');
                var row = this.closest('tr');
                Swal.fire({
                    title: 'Ẩn biến thể',
                    text: 'Bạn có chắc muốn ẩn biến thể này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ẩn',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('admin.product-variants.softDelete', ['id' => 'VARIANT_ID']) }}"
                                .replace('VARIANT_ID', variantId), {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                                    // Lấy giá trị hiển thị
                                    let volumeText = row.querySelector(
                                            'td:nth-child(1) select') ?
                                        row.querySelector('td:nth-child(1) select')
                                        .selectedOptions[0].textContent :
                                        row.querySelector('td:nth-child(1)').textContent;
                                    let priceValue = row.querySelector(
                                            'td:nth-child(2) input') ?
                                        row.querySelector('td:nth-child(2) input').value :
                                        row.querySelector('td:nth-child(2)').textContent;
                                    let stockValue = row.querySelector(
                                            'td:nth-child(3) input') ?
                                        row.querySelector('td:nth-child(3) input').value :
                                        row.querySelector('td:nth-child(3)').textContent;
                                    let imgHtml = row.querySelector('td:nth-child(4) img') ?
                                        row.querySelector('td:nth-child(4) img').outerHTML +
                                        '<br>' :
                                        '';
                                    // Xoá toàn bộ input/select/file, chỉ giữ lại text/ảnh
                                    row.querySelector('td:nth-child(1)').innerHTML = volumeText;
                                    row.querySelector('td:nth-child(2)').innerHTML = priceValue;
                                    row.querySelector('td:nth-child(3)').innerHTML = stockValue;
                                    row.querySelector('td:nth-child(4)').innerHTML = imgHtml;
                                    row.querySelector('td:nth-child(5)').innerHTML =
                                        '<span class="badge bg-secondary">Đã ẩn</span>';
                                    row.querySelector('td:nth-child(6)').innerHTML =
                                        '<button type="button" class="btn btn-sm btn-outline-success btn-restore-variant" data-variant-id="' +
                                        variantId + '">Khôi phục</button>' +
                                        '<button type="button" class="btn btn-sm btn-outline-danger btn-hard-delete-variant" data-variant-id="' +
                                        variantId + '">Xóa cứng</button>';

                                    // Thêm lại listener cho nút khôi phục và xóa cứng mới
                                    addRestoreListener(row.querySelector('.btn-restore-variant'));
                                    addHardDeleteListener(row.querySelector(
                                    '.btn-hard-delete-variant'));
                                } else {
                                    Swal.fire('Lỗi', data.message ||
                                        'Không thể ẩn biến thể cuối cùng!', 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Lỗi', 'Đã xảy ra lỗi khi ẩn biến thể.', 'error');
                            });
                    }
                });
            });
        }

        // Thêm listener cho các nút xóa mềm hiện có
        document.querySelectorAll('.btn-soft-delete-variant').forEach(addSoftDeleteListener);
        // Khôi phục biến thể
        function addRestoreListener(btn) {
            btn.addEventListener('click', function() {
                var variantId = this.getAttribute('data-variant-id');
                var row = this.closest('tr');
                Swal.fire({
                    title: 'Kích hoạt lại biến thể',
                    text: 'Bạn có chắc muốn kích hoạt lại biến thể này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Kích hoạt',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('admin.product-variants.restore', ['id' => 'VARIANT_ID']) }}"
                                .replace('VARIANT_ID', variantId), {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                                    // Cập nhật trạng thái dòng
                                    row.querySelector('td:nth-child(5)').innerHTML =
                                        '<span class="badge bg-success">Đang hoạt động</span>';
                                    row.querySelector('td:nth-child(6)').innerHTML =
                                        '<button type="button" class="btn btn-sm btn-outline-danger btn-soft-delete-variant" data-variant-id="' +
                                        variantId + '">Ẩn</button>';

                                    // Chuyển các trường về dạng input (cho phép sửa)
                                    // Dung tích
                                    let currentVolume = row.querySelector('td:nth-child(1)')
                                        .textContent.trim();
                                    let selectHtml =
                                        `<input type=\"hidden\" name=\"variants[${variantId}][id]\" value=\"${variantId}\">`;
                                    selectHtml +=
                                        `<select name=\"variants[${variantId}][volume_id]\" class=\"form-control\">`;
                                    selectHtml += `<option value=\"\">Chọn dung tích</option>`;
                                    @foreach ($volumes as $volume)
                                        selectHtml +=
                                            `<option value=\"{{ $volume->id }}\"${currentVolume === '{{ $volume->name }}' ? ' selected' : ''}>{{ $volume->name }}</option>`;
                                    @endforeach
                                    selectHtml += `</select>`;
                                    row.querySelector('td:nth-child(1)').innerHTML = selectHtml;

                                    // Giá
                                    let price = row.querySelector('td:nth-child(2)').textContent
                                        .trim();
                                    row.querySelector('td:nth-child(2)').innerHTML =
                                        `<input type=\"number\" name=\"variants[${variantId}][price]\" class=\"form-control\" step=\"0.01\" value=\"${price}\">`;

                                    // Tồn kho
                                    let stock = row.querySelector('td:nth-child(3)').textContent
                                        .trim();
                                    row.querySelector('td:nth-child(3)').innerHTML =
                                        `<input type=\"number\" name=\"variants[${variantId}][stock]\" class=\"form-control\" value=\"${stock}\">`;

                                    // Ảnh
                                    let imgHtml = row.querySelector('td:nth-child(4) img') ? row
                                        .querySelector('td:nth-child(4) img').outerHTML +
                                        '<br>' : '';
                                    imgHtml +=
                                        `<input type=\"file\" name=\"variants[${variantId}][image]\" class=\"form-control\" accept=\"image/*\">`;
                                    row.querySelector('td:nth-child(4)').innerHTML = imgHtml;

                                    // Thêm lại listener cho nút ẩn mới
                                    addSoftDeleteListener(row.querySelector(
                                    '.btn-soft-delete-variant'));
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi',
                                        text: data.message || 'Đã xảy ra lỗi khi kích hoạt lại biến thể.',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                        title: 'Lỗi',
                                        text: data.message || 'Đã xảy ra lỗi khi kích hoạt lại biến thể.',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                            });
                    }
                });
            });
        }

        // Thêm listener cho các nút khôi phục hiện có
        document.querySelectorAll('.btn-restore-variant').forEach(addRestoreListener);

        // Xóa cứng biến thể
        function addHardDeleteListener(btn) {
            btn.addEventListener('click', function() {
                var variantId = this.getAttribute('data-variant-id');
                var row = this.closest('tr');
                Swal.fire({
                    title: 'Xóa cứng biến thể',
                    text: 'Bạn có chắc muốn xóa cứng biến thể này? Hành động này không thể hoàn tác!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa cứng',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#d33',
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    width: '400px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('admin.product-variants.hardDelete', ['id' => 'VARIANT_ID']) }}"
                                .replace('VARIANT_ID', variantId), {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                                    // Xóa dòng khỏi bảng
                                    row.remove();
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi',
                                        text: data.message || 'Không thể xóa cứng biến thể!',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                                }
                            })
                            .catch(() => {
                                Swal.fire({
                                        title: 'Lỗi',
                                        text: data.message || 'Đã xảy ra lỗi khi xóa cứng biến thể.',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        width: '400px'
                                    });
                            
                            });
                    }
                });
            });
        }

        // Thêm listener cho các nút xóa cứng hiện có
        const hardDeleteButtons = document.querySelectorAll('.btn-hard-delete-variant');
        console.log('Found hard delete buttons:', hardDeleteButtons.length);
        console.log('SweetAlert2 available:', typeof Swal !== 'undefined');
        hardDeleteButtons.forEach(addHardDeleteListener);
    </script>
@endsection
