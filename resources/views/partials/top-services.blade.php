<ul class="list-group list-group-flush">
    @php $totalUsage = $topServices->sum('usage_count'); @endphp
    @forelse ($topServices as $index => $item)
        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 service-item"
            data-name="{{ strtolower($item->service->name ?? 'không xác định') }}">
            <div class="d-flex align-items-center">
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
                <div>
                    <strong class="d-block">{{ $item->service->name ?? 'Không xác định' }}</strong>
                    <small class="text-muted">
                        {{ number_format($item->service->price ?? 0) }} VNĐ
                    </small>
                    @php
                        $percent = $totalUsage > 0 ? round(($item->usage_count / $totalUsage) * 100, 1) : 0;
                    @endphp
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                    </div>
                    <small class="text-muted">{{ $percent }}% tổng lượt sử dụng</small>
                </div>
            </div>
            <div class="text-end">
                <span class="badge bg-primary rounded-pill fs-6 mb-1">
                    {{ $item->usage_count }} lượt
                </span>
            </div>
        </li>
    @empty
        <li class="list-group-item text-center text-muted border-0">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            Không có dịch vụ nào
        </li>
    @endforelse
</ul>
