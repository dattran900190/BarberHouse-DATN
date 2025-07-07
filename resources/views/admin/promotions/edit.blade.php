@extends('layouts.AdminLayout')

@section('title', 'Sửa Mã Giảm Giá')

@section('content')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Sửa Mã Giảm Giá</h3>
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('promotions.update', $promotion) }}">
                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Mã giảm giá</label>
                            <input type="text" name="code" class="form-control"
                                value="{{ old('code', $promotion->code) }}" placeholder="Nhập mã giảm giá">
                            @error('code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="discount_type">Loại giảm giá</label>
                            <select name="discount_type" class="form-control">
                                <option value="fixed"
                                    {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>
                                    Cố định</option>
                                <option value="percent"
                                    {{ old('discount_type', $promotion->discount_type) == 'percent' ? 'selected' : '' }}>
                                    Phần trăm</option>
                            </select>
                            
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="discount_value">Giá trị giảm giá</label>
                            <div class="input-group">
                                <input type="number" name="discount_value" id="discount_value"
                                    class="form-control"
                                    value="{{ old('discount_value', $promotion->discount_value ?? '') }}"
                                    placeholder="Nhập giá trị">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="discount_unit">
                                        {{ old('discount_type', $promotion->discount_type ?? 'fixed') === 'percent' ? '%' : 'VND' }}
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Nếu là giảm theo phần trăm, giá trị tối đa là 100%.
                            </small>
                            @error('discount_value')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_discount_amount">Giảm giá tối đa</label>
                            <input type="number" step="0.01" name="max_discount_amount" class="form-control"
                                value="{{ old('max_discount_amount', $promotion->max_discount_amount) }}"
                                placeholder="Nhập giá trị tối đa (nếu có)">
                            @error('max_discount_amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_order_value">Giá trị đơn hàng tối thiểu</label>
                            <input type="number" step="0.01" name="min_order_value" class="form-control"
                                value="{{ old('min_order_value', $promotion->min_order_value) }}"
                                placeholder="Nhập giá trị tối thiểu (nếu có)">
                            @error('min_order_value')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" min="1" max="999"
                                value="{{ old('quantity', $promotion->quantity) }}" placeholder="Nhập số lượng">
                            @error('quantity')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="required_points">Điểm yêu cầu</label>
                            <input type="number" name="required_points" class="form-control"
                                value="{{ old('required_points', $promotion->required_points) }}"
                                placeholder="Nhập số điểm (nếu có)">
                            @error('required_points')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usage_limit">Giới hạn sử dụng </label>
                            <input type="number" name="usage_limit" class="form-control"
                                value="{{ old('usage_limit', $promotion->usage_limit) }}"
                                placeholder="Nhập giới hạn sử dụng">
                            @error('usage_limit')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Ngày bắt đầu </label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', $promotion->start_date->format('Y-m-d')) }}">
                            @error('start_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">Ngày kết thúc </label>
                            <input type="date" name="end_date" class="form-control"
                                value="{{ old('end_date', $promotion->end_date->format('Y-m-d')) }}">
                            @error('end_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả">{{ old('description', $promotion->description) }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                id="is_active" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Kích hoạt mã giảm giá</label>

                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Hủy
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
            const discountValue = document.querySelector('input[name="discount_value"]');
            const discountUnit = document.getElementById('discount_unit');

            function updateDiscountDisplay() {
                if (discountType.value === 'percent') {
                    discountValue.setAttribute('max', '100');
                    discountValue.setAttribute('step', '0.01');
                    discountValue.removeAttribute('min');
                    discountUnit.textContent = '%';
                } else {
                    discountValue.removeAttribute('max');
                    discountValue.setAttribute('step', '1'); // Cho phép nhập lẻ như 12
                    discountValue.setAttribute('min', '0');
                    discountUnit.textContent = 'VND';
                }
            }
            // Khi form load
            updateDiscountDisplay();

            // Khi thay đổi loại giảm giá
            discountType.addEventListener('change', updateDiscountDisplay);
        });
    </script>
@endsection
