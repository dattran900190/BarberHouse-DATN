@extends('layouts.ClientLayout')

@section('title-page')
    Gửi yêu cầu hoàn tiền
@endsection

@section('content')
    <main class="py-5">
        <div class="container">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="card-header border-0">
                    <h3 class="mb-0 fw-bold text-center">Yêu cầu hoàn tiền</h3>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form id="refund-form" action="{{ route('client.wallet.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label for="refundable_type" class="form-label">Loại yêu cầu</label>
                                <select name="refundable_type" id="refundable_type" class="form-select" required
                                    onchange="updateRefundableOptions()"
                                    {{ request()->has('refundable_type') ? 'disabled' : '' }}>
                                    <option value="">-- Chọn loại --</option>
                                    <option value="order" {{ request('refundable_type') == 'order' ? 'selected' : '' }}>Đơn
                                        hàng</option>
                                    <option value="appointment"
                                        {{ request('refundable_type') == 'appointment' ? 'selected' : '' }}>Đặt lịch
                                    </option>
                                </select>
                                @if (request()->has('refundable_type'))
                                    <input type="hidden" name="refundable_type" value="{{ request('refundable_type') }}">
                                @endif
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="refundable_id" class="form-label">Chọn đơn hàng/đặt lịch</label>
                                <select name="refundable_id" id="refundable_id" class="form-select" required
                                    {{ request()->has('refundable_id') ? 'disabled' : '' }}>
                                    <option value="">-- Chọn mục --</option>
                                    @if (request()->has('refundable_id') && request('refundable_type') == 'order')
                                        @php $order = $orders->firstWhere('id', request('refundable_id')); @endphp
                                        @if ($order)
                                            <option value="{{ $order->id }}" selected>
                                                {{ $order->order_code }} - {{ number_format($order->total_money, 0) }} VNĐ
                                            </option>
                                        @endif
                                    @elseif (request()->has('refundable_id') && request('refundable_type') == 'appointment')
                                        @php $appointment = $appointments->firstWhere('id', request('refundable_id')); @endphp
                                        @if ($appointment)
                                            <option value="{{ $appointment->id }}" selected>
                                                {{ $appointment->appointment_code }} -
                                                {{ number_format($appointment->total_amount, 0) }} VNĐ
                                            </option>
                                        @endif
                                    @endif
                                </select>
                                @if (request()->has('refundable_id'))
                                    <input type="hidden" name="refundable_id" value="{{ request('refundable_id') }}">
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Lý do hoàn tiền</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4">{{ old('reason') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label for="bank_account_name" class="form-label">Tên chủ tài khoản</label>
                                <input type="text" name="bank_account_name" id="bank_account_name" class="form-control"
                                    value="{{ old('bank_account_name') }}">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="bank_account_number" class="form-label">Số tài khoản</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"
                                    class="form-control" value="{{ old('bank_account_number') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Tên ngân hàng</label>
                            <select name="bank_name" id="bank_name" class="form-select">
                                <option value="">-- Chọn ngân hàng --</option>
                                @foreach (config('banks') as $bank)
                                    <option value="{{ $bank }}" {{ old('bank_name') == $bank ? 'selected' : '' }}>
                                        {{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn-outline-buy" style="padding: 5px 10px;">Gửi yêu cầu hoàn
                                tiền</button>
                        </div>
                    </form>

                    @if ($orders->isEmpty() && $appointments->isEmpty())
                        <div class="alert alert-info text-center">
                            Bạn không có đơn hàng hoặc đặt lịch nào đủ điều kiện hoàn tiền.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @php
        $ordersJs = $orders->map(
            fn($o) => [
                'id' => $o->id,
                'display' => $o->order_code . ' - ' . number_format($o->total_money, 0) . ' VNĐ',
            ],
        );
        $appointmentsJs = $appointments->map(
            fn($a) => [
                'id' => $a->id,
                'display' => $a->appointment_code . ' - ' . number_format($a->total_amount, 0) . ' VNĐ',
            ],
        );
    @endphp

    <script>
        const orders = @json($ordersJs);
        const appointments = @json($appointmentsJs);

        function updateRefundableOptions() {
            const type = document.getElementById('refundable_type').value;
            const select = document.getElementById('refundable_id');
            select.innerHTML = '<option value="">-- Chọn mục --</option>';

            const items = type === 'order' ? orders : appointments;
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.display;
                select.appendChild(option);
            });
        }

        @if (!request()->has('refundable_type'))
            updateRefundableOptions();
        @endif
    </script>

    <style>
        /* Ensure main content has proper spacing */
        main {
            min-height: calc(100vh - 100px);
            /* Adjust based on your header/footer height */
        }

        /* Card styling */
        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 1.5rem;
        }

        /* Form button styling */
        .btn-outline-buy {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-align: center;
            /* Căn giữa chữ trong nút */
            display: inline-block;
            /* Giữ nguyên dạng khối */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header h3 {
                font-size: 1.5rem;
            }

            .btn-outline-buy {
                width: 100%;
                padding: 0.75rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control,
            .form-select {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .card {
                margin-top: 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        /* SweetAlert2 custom styling */
        .custom-swal-popup {
            width: 90%;
            max-width: 500px;
        }

        @media (max-width: 576px) {
            .custom-swal-popup {
                width: 95%;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('refund-form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);

                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ.',
                        allowOutsideClick: false,
                        icon: 'info',
                        showConfirmButton: false,
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(async response => {
                            Swal.close();

                            if (response.ok) {
                                const data = await response.json();
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        icon: 'success'
                                    }).then(() => {
                                        window.location.href =
                                            '{{ route('client.detailWallet') }}';
                                    });
                                }
                            } else if (response.status === 422) {
                                const data = await response.json();
                                let errorMessages = '';

                                if (data.errors) {
                                    Object.values(data.errors).forEach(arr => {
                                        arr.forEach(msg => {
                                            errorMessages += msg + '<br>';
                                        });
                                    });
                                } else {
                                    errorMessages = data.message || 'Đã có lỗi xảy ra.';
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    },
                                    html: errorMessages,
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                const errorData = await response.json();
                                Swal.fire({
                                    icon: 'error',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    },
                                    title: 'Lỗi!',
                                    text: errorData.message || 'Đã có lỗi xảy ra.',
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                title: 'Lỗi!',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                text: 'Đã có lỗi xảy ra: ' + error.message,
                                icon: 'error'
                            });
                        });
                });
            }
        });
    </script>
@endsection
