
 @extends('adminlte::page')

@section('title', 'Xác nhận mã Check-in')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Xác nhận mã Check-in</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('checkins.verify') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="code">Nhập mã Check-in</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                    maxlength="6" required placeholder="Nhập mã gồm 6 chữ số">

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success mt-3">
                                <i class="fas fa-check-circle"></i> Xác nhận
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('checkins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay về danh sách Check-in
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

