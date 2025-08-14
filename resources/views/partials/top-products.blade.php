<ul class="list-group list-group-flush">
    @forelse ($topProducts as $index => $item)
        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 product-item"
            data-name="{{ strtolower($item->productVariant->product->name ?? 'không xác định') }}">
            <div class="d-flex align-items-center">
                <!-- Ranking -->
                <div class="me-3">
                    @if ($index < 3)
                        <span class="badge bg-warning rounded-circle p-2">
                            <i class="fas fa-medal"></i>
                        </span>
                    @else
                        <span class="badge bg-light text-dark rounded-circle p-2">
                            {{ $index + 1 }}
                        </span>
                    @endif
                </div>

                <!-- Product info -->
                <div>
                    <strong class="d-block">{{ $item->productVariant->product->name ?? 'Không xác định' }}</strong>
                    <small class="text-muted">
                        {{ number_format($item->productVariant->price ?? 0) }} VNĐ
                        @if ($item->productVariant->product->category ?? false)
                            • {{ $item->productVariant->product->category->name }}
                        @endif
                    </small>

                    <!-- Progress bar cho tỷ lệ -->
                    @php
                        $totalSales = $topProducts->sum('total_sold');
                        $percentage = $totalSales > 0 ? round(($item->total_sold / $totalSales) * 100, 1) : 0;
                    @endphp
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted">{{ $percentage }}% tổng doanh số</small>
                </div>
            </div>

            <div class="text-end">
                <span class="badge bg-success rounded-pill fs-6 mb-1">
                    {{ $item->total_sold }} sp
                </span>
                <br>
                <small class="text-muted">
                    {{ number_format($item->total_sold * ($item->productVariant->price ?? 0)) }} VNĐ
                </small>

                <!-- Quick actions -->
                <div class="btn-group btn-group-sm mt-1">
                </div>
            </div>
        </li>
    @empty
        <li class="list-group-item text-center text-muted border-0">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            Không có sản phẩm nào được bán
        </li>
    @endforelse
</ul>
@if ($topProducts->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info">
            <small class="text-muted">
                Hiển thị {{ $topProducts->firstItem() ?? 0 }} - {{ $topProducts->lastItem() ?? 0 }}
                trong tổng {{ $topProducts->total() }} sản phẩm
            </small>
        </div>
    </div>
@endif
