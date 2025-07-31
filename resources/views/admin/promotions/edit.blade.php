@extends('layouts.AdminLayout')

@section('title', 'Sửa Mã Giảm Giá')

@section('content')

    <div class="page-header">
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
                <a href="{{ route('promotions.edit', $promotion) }}">Sửa mã giảm giá</a>
            </li>
        </ul>
    </div>


    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa mã giảm giá</div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('promotions.update', $promotion) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="row">
                    {{-- Mã --}}
                    <div class="col-md-6 mb-3">
                        <label for="code">Mã giảm giá</label>
                        <input type="text" name="code" class="form-control"
                            value="{{ old('code', $promotion->code) }}" placeholder="Nhập mã">
                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Loại giảm --}}
                    <div class="col-md-6 mb-3">
                        <label for="discount_type">Loại giảm giá</label>
                        <select name="discount_type" class="form-control">
                            <option value="fixed"
                                {{ old('discount_type', $promotion->discount_type) === 'fixed' ? 'selected' : '' }}>Cố định
                            </option>
                            <option value="percent"
                                {{ old('discount_type', $promotion->discount_type) === 'percent' ? 'selected' : '' }}>Phần
                                trăm</option>
                        </select>
                        @error('discount_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giá trị giảm --}}
                    <div class="col-md-6 mb-3">
                        <label for="discount_value">Giá trị giảm giá</label>
                        <div class="input-group">
                              <input type="text" name="discount_value" class="form-control" id="discount_value"
                                value="{{ old('discount_value', $promotion->discount_value) }}" placeholder="Nhập giá trị">
                            <span class="input-group-text" id="discount_unit">
                                {{ old('discount_type', $promotion->discount_type) === 'percent' ? '%' : 'VNĐ' }}
                            </span>
                        </div>
                        <small class="form-text text-muted">Nếu là phần trăm, tối đa 100%.</small><br>
                        @error('discount_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giảm tối đa --}}
                    <div class="col-md-6 mb-3">
                        <label for="max_discount_amount">Giảm giá tối đa</label>
                        <input type="number" step="0.01" name="max_discount_amount" class="form-control"
                            value="{{ old('max_discount_amount', $promotion->max_discount_amount) }}">
                        @error('max_discount_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Đơn hàng tối thiểu --}}
                    <div class="col-md-6 mb-3">
                        <label for="min_order_value">Giá trị đơn hàng tối thiểu</label>
                        <input type="number" step="0.01" name="min_order_value" class="form-control"
                            value="{{ old('min_order_value', $promotion->min_order_value) }}">
                        @error('min_order_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Số lượng --}}
                    <div class="col-md-6 mb-3">
                        <label for="quantity">Số lượng</label>
                        <input type="number" name="quantity" class="form-control"
                            value="{{ old('quantity', $promotion->quantity) }}">
                        @error('quantity')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Điểm yêu cầu --}}
                    <div class="col-md-6 mb-3">
                        <label for="required_points">Điểm yêu cầu</label>
                        <input type="number" name="required_points" class="form-control"
                            value="{{ old('required_points', $promotion->required_points) }}">
                        @error('required_points')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giới hạn --}}
                    <div class="col-md-6 mb-3">
                        <label for="usage_limit">Giới hạn sử dụng</label>
                        <input type="number" name="usage_limit" class="form-control"
                            value="{{ old('usage_limit', $promotion->usage_limit) }}">
                        @error('usage_limit')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Ngày bắt đầu/kết thúc --}}
                    <div class="col-md-6 mb-3">
                        <label for="start_date">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ old('start_date', $promotion->start_date?->format('Y-m-d')) }}">
                        @error('start_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_date">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ old('end_date', $promotion->end_date?->format('Y-m-d')) }}">
                        @error('end_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="col-md-12 mb-3">
                        <label for="description">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $promotion->description) }}</textarea>
                        @error('description')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- Nút --}}
                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-edit me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('promotions.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const discountType = document.querySelector('select[name="discount_type"]');
            const discountValue = document.getElementById('discount_value');
            const discountUnit = document.getElementById('discount_unit');

            function updateUnit() {
                if (discountType.value === 'percent') {
                    discountUnit.textContent = '%';
                    discountValue.setAttribute('max', '100');
                    discountValue.setAttribute('step', '0.01');
                } else {
                    discountUnit.textContent = 'VNĐ';
                    discountValue.removeAttribute('max');
                    discountValue.setAttribute('step', '1000');
                }
            }

            updateUnit();
            discountType.addEventListener('change', updateUnit);
        });
    </script>
@endsection
