@extends('layouts.AdminLayout')

@section('title', 'Thêm Mã Giảm Giá')

@section('content')

    <div class="page-header mb-4">
        <h3 class="fw-bold mb-3">Mã giảm giá</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/promotions') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/promotions') }}">Mã giảm giá</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('promotions.create') }}">Thêm mã giảm giá</a>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('promotions.store') }}">
                @csrf
                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">

                <div class="row g-3">
                    {{-- Mã giảm giá + loại --}}
                    <div class="col-md-6">
                        <label for="code" class="form-label">Mã giảm giá</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}"
                            placeholder="Nhập mã giảm giá">
                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="discount_type" class="form-label">Loại giảm giá</label>
                        <select name="discount_type" class="form-control">
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Phần trăm
                            </option>
                        </select>
                        @error('discount_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giá trị giảm giá + giới hạn --}}
                    <div class="col-md-6">
                        <label for="discount_value" class="form-label">Giá trị giảm giá</label>
                        <div class="input-group">
                            <input type="text" name="discount_value" class="form-control"
                                value="{{ old('discount_value') }}" placeholder="Nhập giá trị">
                            <span class="input-group-text"
                                id="discount_unit">{{ old('discount_type') == 'percent' ? '%' : 'VNĐ' }}</span>
                        </div>
                        <small class="form-text text-muted">Nếu là phần trăm, giá trị tối đa là 100%</small><br>
                        @error('discount_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="max_discount_amount" class="form-label">Giảm giá tối đa</label>
                        <input type="number" name="max_discount_amount" class="form-control"
                            value="{{ old('max_discount_amount') }}" placeholder="Nhập giá trị tối đa">
                        @error('max_discount_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giá trị đơn hàng tối thiểu + số lượng --}}
                    <div class="col-md-6">
                        <label for="min_order_value" class="form-label">Giá trị đơn hàng tối thiểu</label>
                        <input type="number" name="min_order_value" class="form-control"
                            value="{{ old('min_order_value') }}" placeholder="Nhập giá trị tối thiểu">
                        @error('min_order_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Số lượng</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}"
                            placeholder="Nhập số lượng">
                        @error('quantity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giới hạn sử dụng + điểm yêu cầu --}}
                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label">Giới hạn sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', 1) }}"
                            placeholder="Nhập giới hạn">
                        @error('usage_limit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="required_points" class="form-label">Điểm yêu cầu</label>
                        <input type="number" name="required_points" class="form-control"
                            value="{{ old('required_points', null) }}" placeholder="Điểm cần để đổi mã nếu không nhập điểm là voucher công khai">
                        @error('required_points')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Ngày bắt đầu + kết thúc --}}
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        @error('start_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        @error('end_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="col-md-12">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-plus"></i> <span class="ms-2">Thêm</span>
                    </button>

                    <a href="{{ route('promotions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('css')
    <style>
        textarea.form-control {
            resize: vertical;
            /* Cho phép thay đổi kích thước theo chiều dọc */
            max-height: 100px;
            /* Giới hạn chiều cao tối đa */
            min-height: 60px;
            /* Chiều cao tối thiểu */
        }
    </style>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const discountType = document.querySelector('select[name="discount_type"]');
            const discountValue = document.querySelector('input[name="discount_value"]');
            const discountUnit = document.getElementById('discount_unit');

            function updateDiscountDisplay() {
                if (discountType.value === 'percent') {
                    discountValue.setAttribute('max', '100');
                    discountValue.setAttribute('step', '0.01');
                    discountUnit.textContent = '%';
                } else {
                    discountValue.removeAttribute('max');
                    discountValue.setAttribute('step', '1000'); // hoặc step="1"
                    discountUnit.textContent = 'VNĐ';
                }
            }
            // Khi form load
            updateDiscountDisplay();

            // Khi thay đổi loại giảm giá
            discountType.addEventListener('change', updateDiscountDisplay);
        });
    </script>
@endsection
