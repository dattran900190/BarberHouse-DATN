<div class="dropdown">
    <button class="btn btn-sm btn-outline-secondary" type="button" id="actionMenu{{ $appointment->id }}"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $appointment->id }}">
        <!-- Hành động Xem -->
        <li>
            <a href="{{ $type == 'cancelled' ? route('appointments.show_cancelled', $appointment->id) : route('appointments.show', $appointment->id) }}"
                class="dropdown-item">
                <i class="fas fa-eye me-2"></i> Xem
            </a>
        </li>

        <!-- Hành động Sửa (không áp dụng cho completed và cancelled) -->
        @if ($type != 'completed' && $type != 'cancelled')
            <li>
                <a href="{{ route('appointments.edit', $appointment->id) }}" class="dropdown-item">
                    <i class="fas fa-edit me-2"></i> Sửa
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
        @endif

        <!-- Hành động tùy theo type -->
        @if ($type == 'pending')
            <li>
                <button type="button" class="dropdown-item text-success confirm-btn" data-id="{{ $appointment->id }}">
                    <i class="fas fa-check me-2"></i> Xác nhận
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item text-danger cancel-btn" data-id="{{ $appointment->id }}">
                    <i class="fas fa-times-circle me-2"></i> Hủy
                </button>
            </li>
        @elseif($type == 'confirmed')
        <li>
            <button type="button" class="dropdown-item text-info checkin-btn" data-id="{{ $appointment->id }}" data-bs-toggle="collapse" data-bs-target="#checkinForm{{ $appointment->id }}">
                <i class="fas fa-sign-in-alt me-2"></i> Check-in
            </button>
        </li>
            <li>
                <button type="button" class="dropdown-item text-primary no-show-btn" data-id="{{ $appointment->id }}">
                    <i class="fas fa-user-times me-2"></i> Không đến
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item text-danger cancel-btn" data-id="{{ $appointment->id }}">
                    <i class="fas fa-times-circle me-2"></i> Hủy
                </button>
            </li>
        @elseif($type == 'progress')
            <li>
                <button type="button" class="dropdown-item text-success complete-btn"
                    data-id="{{ $appointment->id }}">
                    <i class="fas fa-check-circle me-2"></i> Hoàn thành
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item text-danger cancel-btn" data-id="{{ $appointment->id }}">
                    <i class="fas fa-times-circle me-2"></i> Hủy
                </button>
            </li>
        @endif
    </ul>
</div>
