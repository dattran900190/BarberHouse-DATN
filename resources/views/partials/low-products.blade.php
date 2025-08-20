<ul class="list-group list-group-flush">
    @forelse ($lowSellingProducts as $index => $item)
        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 product-item"
            data-name="{{ strtolower($item->product->name ?? 'không xác định') }}">
            <div class="d-flex align-items-center">
                <!-- Warning icon -->
                <div class="me-3">
                    @if (($item->total_sold ?? 0) == 0)
                        <span class="badge bg-danger rounded-circle p-2">
                            <i class="fas fa-exclamation"></i>
                        </span>
                    @else
                        <span class="badge bg-warning rounded-circle p-2">
                            <i class="fas fa-clock"></i>
                        </span>
                    @endif
                </div>

                <!-- Product info -->
                <div>
                    <strong class="d-block">{{ $item->product->name ?? 'Không xác định' }}</strong>
                    <small class="text-muted">
                        {{ number_format($item->price) }} VNĐ
                        @if ($item->product->category ?? false)
                            • {{ $item->product->category->name }}
                        @endif
                    </small>

                    <!-- Ngày tạo -->
                    <div class="mt-1">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Tạo: {{ $item->product?->created_at?->format('d/m/Y') ?? 'Không xác định' }}
                            {{ $item->product?->created_at?->format('d/m/Y') ?? 'Không xác định' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="text-end">
                @if (($item->total_sold ?? 0) == 0)
                    <span class="badge bg-danger rounded-pill fs-6 mb-1">
                        Chưa bán
                    </span>
                @else
                    <span class="badge bg-warning rounded-pill fs-6 mb-1">
                        {{ $item->total_sold }} sp
                    </span>
                @endif
            </div>
        </li>
    @empty
        <li class="list-group-item text-center text-muted border-0">
            <i class="fas fa-smile fa-2x mb-2 d-block text-success"></i>
            Tất cả sản phẩm đều bán tốt!
        </li>
    @endforelse
</ul>
