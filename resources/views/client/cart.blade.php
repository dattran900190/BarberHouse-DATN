@extends('layouts.ClientLayout')

@section('title-page')
    Giỏ hàng Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-custom">
            <div class="padding-5vh">
                <div class="flex-center">
                    <div class="col-12">
                        <div class="card-registration">
                            <div class="card-body no-padding">
                                <div class="flex-row no-gap">
                                    <div class="col-left-66">
                                        <div class="p-5vh">
                                            <div class="d-flex justify-content-between align-items-center mb-5">
                                                <h1 class="fw-bold mb-0">Giỏ hàng</h1>
                                                <h6 class="mb-0 text-muted" id="item-count">{{ $cart->items->count() }} sản
                                                    phẩm</h6>
                                            </div>
                                            <hr class="my-4">

                                            @if (session('success'))
                                                <div class="alert alert-success" id="success-message">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                            @if (session('error'))
                                                <div class="alert alert-danger" id="error-message">
                                                    {{ session('error') }}
                                                </div>
                                            @endif

                                            @if ($cart->items->isEmpty())
                                                <p id="empty-cart-message">Giỏ hàng trống.</p>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Tên sản phẩm</th>
                                                                <th scope="col">Hình ảnh</th>
                                                                <th scope="col">Số lượng</th>
                                                                <th scope="col">Đơn giá</th>
                                                                <th scope="col">Thành tiền</th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="cart-items">
                                                            @foreach ($cart->items as $item)
                                                                <tr id="cart-item-{{ $item->id }}">
                                                                    <td class="product-name">
                                                                        {{ $item->productVariant->product->name ?? 'N/A' }}
                                                                    </td>
                                                                    <td>
                                                                        <img src="{{ $item->productVariant->image ?? 'https://via.placeholder.com/100' }}"
                                                                            class="img-fluid rounded-3"
                                                                            alt="{{ $item->productVariant->product->name ?? 'Sản phẩm' }}"
                                                                            style="width: 80px;">
                                                                    </td>
                                                                    <td>
                                                                        <div class="quantity d-flex align-items-center">
                                                                            <button type="button"
                                                                                class="btn btn-outline-dark btn-sm quantity-minus"
                                                                                data-item-id="{{ $item->id }}"
                                                                                data-csrf="{{ csrf_token() }}">−</button>
                                                                            <input type="number"
                                                                                class="form-control form-control-sm mx-2 quantity-input"
                                                                                value="{{ $item->quantity }}" min="1"
                                                                                data-item-id="{{ $item->id }}"
                                                                                data-price="{{ $item->price }}"
                                                                                style="width: 60px; text-align: center;" />
                                                                            <button type="button"
                                                                                class="btn btn-outline-dark btn-sm quantity-plus"
                                                                                data-item-id="{{ $item->id }}"
                                                                                data-csrf="{{ csrf_token() }}">+</button>
                                                                        </div>
                                                                    </td>
                                                                    <td class="unit-price">
                                                                        {{ number_format($item->price, 0, ',', '.') }} ₫
                                                                    </td>
                                                                    <td class="subtotal">
                                                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                                        ₫</td>
                                                                    <td class="text-end">
                                                                        <form
                                                                            action="{{ route('cart.remove', $item->id) }}"
                                                                            method="POST" class="remove-form">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="text-muted btn btn-link"
                                                                                data-csrf="{{ csrf_token() }}">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif

                                            <div class="pt-5">
                                                <h6 class="mb-0"><a href="/" class="text-body"><i
                                                            class="fas fa-long-arrow-alt-left me-2"></i>Quay lại cửa
                                                        hàng</a></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 bg-body-tertiary">
                                        <div class="p-5">
                                            <h3 class="fw-bold mb-5 mt-2 pt-1">Tổng</h3>
                                            <hr class="my-4">

                                            <div class="d-flex justify-content-between mb-4">
                                                <h5 class="text-uppercase"><span
                                                        id="item-count-side">{{ $cart->items->count() }}</span> Sản phẩm
                                                </h5>
                                                <h5 id="cart-subtotal">
                                                    {{ number_format($cart->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}
                                                    ₫</h5>
                                            </div>

                                            <h5 class="text-uppercase mb-3">Phí ship</h5>
                                            <div class="mb-4 pb-2">
                                                <select class="form-select" id="shipping-method">
                                                    <option value="50000" selected>Standard-Delivery - 50.000 ₫</option>
                                                    <option value="100000">Express-Delivery - 100.000 ₫</option>
                                                    <option value="150000">Premium-Delivery - 150.000 ₫</option>
                                                </select>
                                            </div>

                                            <h5 class="text-uppercase mb-3">Mã khuyến mại</h5>
                                            <div class="mb-5">
                                                <div class="form-outline">
                                                    <input type="text" id="promo-code"
                                                        class="form-control form-control-lg" />
                                                    <label class="form-label" for="promo-code">Nhập mã khuyến mại</label>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="d-flex justify-content-between mb-5">
                                                <h5 class="text-uppercase">Tổng tiền</h5>
                                                <h5 id="cart-total">
                                                    {{ number_format($cart->items->sum(fn($item) => $item->price * $item->quantity) + 50000, 0, ',', '.') }}
                                                    ₫</h5>
                                            </div>

                                            <a href="">
                                                <button type="button" class="btn btn-dark btn-block btn-lg"
                                                    data-mdb-ripple-color="dark">
                                                    Xác nhận
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <style>
        .table th {
            white-space: nowrap;
        }

        #mainNav {
            background-color: #000;
        }

        .padding-5vh {
            padding: 5vh 0;
        }

        .flex-center {
            display: flex;
            justify-content: center;
        }

        .col-left-66 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        }

        .no-padding {
            padding: 0 !important;
        }

        .no-gap {
            gap: 0 !important;
        }

        .p-5vh {
            padding: 5vh;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection

@section('scripts')
    <script>
        addEventListener('DOMContentLoaded', () => {
            const formatVND = n => n.toLocaleString('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).replace('₫', '') + ' ₫';

            const updateTotal = () => {
                const s = [...document.querySelectorAll('.subtotal')].reduce((a, b) => a + (parseFloat(b
                    .textContent.replace(/[^0-9]/g, '')) || 0), 0);
                const f = parseInt(document.getElementById('shipping-method').value) || 5e4;
                const c = document.querySelectorAll('.subtotal').length;

                document.getElementById('cart-subtotal').textContent = formatVND(s);
                document.getElementById('cart-total').textContent = formatVND(s + f);

                const itemCountEl = document.getElementById('item-count');
                const itemCountSideEl = document.getElementById('item-count-side');

                if (itemCountEl) itemCountEl.textContent = `${c} sản phẩm`;
                if (itemCountSideEl) itemCountSideEl.textContent = c;
            };

            const showMsg = (m, t = 'danger') => {
                let d = document.getElementById('error-message') || Object.assign(document.createElement(
                    'div'), {
                    id: 'error-message',
                    className: `alert alert-${t}`
                });
                d.textContent = m;
                document.querySelector('.p-4').prepend(d);
                setTimeout(() => d.remove(), 3e3);
            };

            const ajax = (u, m, d, b, s) => {
                b && (b.disabled = true);
                fetch(u, {
                        method: m,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': b?.dataset.csrf || document.querySelector(
                                `[data-item-id="${u.split('/').pop()}"]`)?.dataset.csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(d)
                    })
                    .then(r => r.ok ? r.json() : r.text().then(t => {
                        throw Error(`HTTP ${r.status}: ${t}`);
                    }))
                    .then(s)
                    .catch(e => (console.error(e), showMsg(
                        `Lỗi ${m === 'PUT' ? 'cập nhật số lượng' : 'xóa sản phẩm'}.`), b && (b
                        .disabled = false)));
            };

            document.querySelectorAll('.quantity-minus, .quantity-plus, .quantity-input, .remove-form').forEach(
                el => {
                    el.addEventListener(el.classList.contains('remove-form') ? 'submit' : el.tagName ===
                        'INPUT' ? 'change' : 'click', e => {
                            if (el.classList.contains('remove-form')) {
                                e.preventDefault();
                                if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;
                                const id = el.action.split('/').pop();
                                ajax(el.action, 'DELETE', {}, el.querySelector('button'), d => {
                                    if (d.success) {
                                        document.getElementById(`cart-item-${id}`).remove();
                                        !document.querySelectorAll('.subtotal').length && (document
                                            .getElementById('cart-items').parentElement
                                            .innerHTML =
                                            '<p id="empty-cart-message">Giỏ hàng trống.</p>');
                                        updateTotal();
                                    } else showMsg(d.message || 'Xóa thất bại.');
                                    el.querySelector('button').disabled = false;
                                });
                            } else {
                                const id = el.dataset.itemId,
                                    input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                                let v = parseInt(input.value) || 1;
                                if (el.classList.contains('quantity-minus') && v > 1) v--;
                                else if (el.classList.contains('quantity-plus')) v++;
                                if (v !== parseInt(input.value)) {
                                    input.value = v;
                                    ajax(`/cart/update/${id}`, 'PUT', {
                                        quantity: v
                                    }, el.tagName === 'BUTTON' ? el : null, d => {
                                        if (d.success) {
                                            document.getElementById(`cart-item-${id}`)
                                                .querySelector('.subtotal').textContent = formatVND(
                                                    parseFloat(input.dataset.price) * v);
                                            updateTotal();
                                        } else showMsg(d.message || 'Cập nhật thất bại.');
                                        el.tagName === 'BUTTON' && (el.disabled = false);
                                    });
                                }
                            }
                        });
                });

            document.getElementById('shipping-method').addEventListener('change', updateTotal);
            document.getElementById('mainNav')?.addEventListener('scroll', () => document.getElementById('mainNav')
                .classList.toggle('scrolled', scrollY >= 100));
        });
    </script>
@endsection
