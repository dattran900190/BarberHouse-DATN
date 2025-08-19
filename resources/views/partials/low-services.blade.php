<ul class="list-group list-group-flush">
    @php $totalUsage = $lowUsageServices->sum('usage_count'); @endphp
    @forelse ($lowUsageServices as $index => $item)
        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 service-item"
            data-name="{{ strtolower($item->name ?? 'không xác định') }}">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    @if (($item->usage_count ?? 0) == 0)
                        <span class="badge bg-danger rounded-circle p-2">
                            <i class="fas fa-exclamation"></i>
                        </span>
                    @else
                        <span class="badge bg-warning rounded-circle p-2">
                            <i class="fas fa-turtle"></i>
                        </span>
                    @endif
                </div>
                <div>
                    <strong class="d-block">{{ $item->name ?? 'Không xác định' }}</strong>
                    <small class="text-muted">
                        {{ number_format($item->price ?? 0) }} VNĐ
                    </small>
                    @php
                        $percent = $totalUsage > 0 ? round(($item->usage_count / $totalUsage) * 100, 1) : 0;
                    @endphp
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-secondary" style="width: {{ $percent }}%"></div>
                    </div>
                    <small class="text-muted">{{ $percent }}% tổng lượt sử dụng</small>
                </div>
            </div>
            <div class="text-end">
                @if (($item->usage_count ?? 0) == 0)
                    <span class="badge bg-danger rounded-pill fs-6 mb-1">
                        Chưa có lượt
                    </span>
                @else
                    <span class="badge bg-warning rounded-pill fs-6 mb-1">
                        {{ $item->usage_count }} lượt
                    </span>
                @endif
            </div>
        </li>
    @empty
        <li class="list-group-item text-center text-muted border-0">
            <i class="fas fa-smile fa-2x mb-2 d-block text-success"></i>
            Tất cả dịch vụ đều được sử dụng!
        </li>
    @endforelse
</ul>
