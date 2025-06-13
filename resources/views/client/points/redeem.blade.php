@extends('layouts.ClientLayout')

@section('title-page')
    Đổi điểm khuyến mãi
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container light-style flex-grow-1 container-p-y">
            <div class="card overflow-hidden">
                <h4 class="font-weight-bold text-center py-3 mb-4">
                    Đổi điểm khuyến mãi
                </h4>

                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group list-group-flush account-settings-links" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="tab-redeem" data-bs-toggle="list"
                                href="#redeem-tab" role="tab" aria-controls="redeem-tab">
                                Đổi điểm
                            </a>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="redeem-tab">
                                <div class="card-body">
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            {{ session('error') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form action="{{ route('client.points.redeem.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="promotion_id" class="form-label">Chọn mã giảm giá</label>
                                            <select name="promotion_id" id="promotion_id" class="form-control">
                                                @foreach ($promotions as $promotion)
                                                    <option value="{{ $promotion->id }}">
                                                        {{ $promotion->code }} ({{ $promotion->required_points }} điểm)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Đổi ngay</button>
                                    </form>
                                </div>
                            </div> {{-- tab-pane --}}
                        </div> {{-- tab-content --}}
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection
