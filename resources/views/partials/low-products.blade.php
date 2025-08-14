<ul class="list-group list-group-flush">
    @forelse ($lowSellingProducts as $index => $item)
        @php
            $sortBy = request('sort_by', 'total_sold');
            $createdAt = optional($item->product)->created_at
                ? \Carbon\Carbon::parse($item->product->created_at)
                : null;

            switch ($sortBy) {
                case 'price':
                    $maxValue = $lowSellingProducts->max('price') ?: 1;
                    $currentValue = $item->price ?? 0;
                    $metricLabel = 'so với giá cao nhất';
                    $percentage = round(($currentValue / $maxValue) * 100, 1);
                    break;

                case 'total_sold':
                    $maxValue = $lowSellingProducts->max('total_sold') ?: 1;
                    $currentValue = $item->total_sold ?? 0;
                    $metricLabel = 'so với cao nhất';
                    $percentage = round(($currentValue / $maxValue) * 100, 1);
                    break;

                default:
                    // Nếu sắp xếp theo ngày tạo hoặc tên thì không hiển thị %
                    $percentage = null;
                    $metricLabel = null;
            }
        @endphp

        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 product-item"
            data-name="{{ strtolower($item->product->name ?? 'không xác định') }}">
            <div class="d-flex align-items-center">
                <!-- Warning icon for low selling -->
                <div class="me-3">
                    <span class="badge bg-danger rounded-circle p-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                </div>

                <!-- Product info -->
                <div>
                    <strong class="d-block">{{ $item->product->name ?? 'Không xác định' }}</strong>
                    <small class="text-muted">
                        {{ number_format($item->price ?? 0) }} VNĐ
                        @if ($item->product->category ?? false)
                            • {{ $item->product->category->name }}
                        @endif
                    </small>

                    {{-- Chỉ hiển thị progress nếu có % --}}
                    @if (!is_null($percentage))
                        <div class="progress mt-1" style="height: 4px;">
                            <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                        </div>
                        <small class="text-muted">{{ $percentage }}% {{ $metricLabel }}</small>
                    @endif

                    {{-- Hiển thị ngày tạo khi lọc theo ngày tạo --}}
                    @if ($sortBy === 'created_at' && $createdAt)
                        <div class="mt-1">
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                Tạo: {{ $createdAt->format('d/m/Y') }} ({{ $createdAt->diffForHumans() }})
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-end">
                <span class="badge bg-warning rounded-pill fs-6 mb-1">
                    {{ $item->total_sold }} sp
                </span>
                <br>
                <small class="text-muted">
                    {{ number_format($item->total_sold * ($item->price ?? 0)) }} VNĐ
                </small>
            </div>
        </li>
    @empty
        <li class="list-group-item text-center text-muted border-0">
            <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
            Tất cả sản phẩm đều bán tốt!
        </li>
    @endforelse
</ul>

@if ($lowSellingProducts->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info">
            <small class="text-muted">
                Hiển thị {{ $lowSellingProducts->firstItem() ?? 0 }} - {{ $lowSellingProducts->lastItem() ?? 0 }}
                trong tổng {{ $lowSellingProducts->total() }} sản phẩm
            </small>
        </div>
    </div>
@endif
