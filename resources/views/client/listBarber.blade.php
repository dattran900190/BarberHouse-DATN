@extends('layouts.ClientLayout')

@section('title-page')
    Danh sách thợ Barber House
@endsection

@section('content')
<main class="container">
    <div class="list-barber">
        <h2>Top thợ cắt của Barber House</h2>

        <!-- Bộ lọc -->
        <form method="GET" action="{{ route('client.listBarber') }}" class="product-filters">
            <div class="filter-selects">
                <div class="filter-group">
                    <label for="branch_id">Chi nhánh:</label>
                    <select name="branch_id" id="branch_id" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="rating">Đánh giá:</label>
                    <select name="rating" id="rating" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>Từ 1 sao</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>Từ 2 sao</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>Từ 3 sao</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>Từ 4 sao</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>Từ 5 sao</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Danh sách thợ -->
        <div class="main-list-barber">
            @forelse ($barbers as $barber)
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="{{ route('client.detailBarber', $barber->id) }}">
                            <img src="{{ asset('storage/' . $barber->avatar) }}" alt="{{ $barber->name }}" />
                        </a>
                        <a href="{{ url('/dat-lich?barber_id=' . $barber->id) }}">
                            <button class="btn">Đặt lịch ngay</button>
                        </a>
                    </div>
                    <h5><a href="{{ route('client.detailBarber', $barber->id) }}">{{ $barber->name }}</a></h5>
                    <p>Chi nhánh: {{ $barber->branch->name ?? 'Không rõ' }}</p>
                    <p>⭐ {{ number_format($barber->rating_avg, 1) }} / 5</p>
                </div>
            @empty
                <p>Không có thợ nào phù hợp với bộ lọc.</p>
            @endforelse
        </div>

        <!-- Phân trang -->
        <div class="mt-3">
            {{ $barbers->withQueryString()->links() }}
        </div>
    </div>
</main>

<style>
    #mainNav {
        background-color: #000;
    }
</style>
@endsection
