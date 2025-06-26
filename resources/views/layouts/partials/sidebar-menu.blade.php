 <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    @foreach (config('adminlte.menu') as $menu)
        @if (is_array($menu) && isset($menu['text']) && !isset($menu['type']))
            <li class="nav-item">
                <a href="{{ url($menu['url']) }}" class="nav-link {{ request()->is($menu['url'] . '*') ? 'active' : '' }}">
                    <i class="{{ $menu['icon'] }}"></i>
                    <p>
                        {{ $menu['text'] }}
                        @if ($menu['text'] === 'Quản lý đặt lịch' && isset($pendingCount) && $pendingCount > 0)
                            <span class="badge badge-danger pending-appointment-count">{{ $pendingCount }}</span>
                        @elseif ($menu['text'] === 'Quản lý đặt lịch')
                            <span class="badge badge-danger pending-appointment-count" style="display: none;">0</span>
                        @endif
                    </p>
                </a>
            </li>
        @elseif (is_string($menu))
            <li class="nav-header">{{ $menu }}</li>
        @endif
    @endforeach
</ul>