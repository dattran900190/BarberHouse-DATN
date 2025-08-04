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
                                                            <!-- Thêm vào <thead> -->
                                                            <tr>
                                                                <th scope="col">
                                                                    <input type="checkbox" id="check-all-cart">
                                                                </th>
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
                                                                    <td>
                                                                        <input type="checkbox" class="cart-item-checkbox"
                                                                            data-item-id="{{ $item->id }}"
                                                                            {{ $item->isProductAvailable() ? 'checked' : 'disabled' }}
                                                                            {{ !$item->isProductAvailable() ? 'style=opacity:0.5' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        @if ($item->isProductAvailable())
                                                                            <strong>{{ $item->productVariant->product->name }}</strong><br>
                                                                            @php
                                                                                $product =
                                                                                    $item->productVariant->product;
                                                                                $currentVariantId =
                                                                                    $item->product_variant_id;
                                                                                $variants =
                                                                                    $product->variants ?? collect();
                                                                            @endphp

                                                                            <form
                                                                                action="{{ route('cart.update.variant', $item->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <label
                                                                                    class="form-label text-muted small">Dung
                                                                                    tích:</label>
                                                                                <select name="product_variant_id"
                                                                                    class="form-select form-select-sm mt-1"
                                                                                    onchange="this.form.submit()">
                                                                                    @foreach ($variants as $variant)
                                                                                        <option value="{{ $variant->id }}"
                                                                                            {{ $variant->id == $currentVariantId ? 'selected' : '' }}>
                                                                                            {{ $variant->volume->name ?? ($variant->name ?? 'Không rõ') }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </form>
                                                                        @else
                                                                            <div class="text-muted">
                                                                                <strong>{{ $item->productVariant->product->name ?? 'Sản phẩm không xác định' }}</strong>
                                                                                <br>
                                                                                <small class="text-danger">
                                                                                    <i
                                                                                        class="fas fa-exclamation-triangle me-1"></i>
                                                                                    {{ $item->getProductStatusMessage() }}
                                                                                </small>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <img src="{{ $item->productVariant->image ? Storage::url($item->productVariant->image) : asset('images/no-image.png') }}"
                                                                            alt="{{ $item->productVariant->product->name ?? 'Sản phẩm' }}"
                                                                            class="img-fluid rounded-3 {{ !$item->isProductAvailable() ? 'opacity-50' : '' }}"
                                                                            style="width: 100px;">
                                                                    </td>
                                                                    <td>
                                                                        @if ($item->isProductAvailable())
                                                                            <div class="quantity d-flex align-items-center">
                                                                                <button type="button"
                                                                                    class="btn btn-outline-dark btn-sm quantity-minus"
                                                                                    data-item-id="{{ $item->id }}"
                                                                                    data-csrf="{{ csrf_token() }}">−</button>
                                                                                <input type="number"
                                                                                    class="form-control form-control-sm mx-2 quantity-input"
                                                                                    value="{{ $item->quantity }}"
                                                                                    min="1"
                                                                                    max="{{ $item->productVariant->stock }}"
                                                                                    data-item-id="{{ $item->id }}"
                                                                                    data-price="{{ $item->price }}"
                                                                                    style="width: 60px; text-align: center;" />
                                                                                <button type="button"
                                                                                    class="btn btn-outline-dark btn-sm quantity-plus"
                                                                                    data-item-id="{{ $item->id }}"
                                                                                    data-csrf="{{ csrf_token() }}">+</button>
                                                                            </div>
                                                                        @else
                                                                            <div class="text-muted">
                                                                                <small>Số lượng:
                                                                                    {{ $item->quantity }}</small>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="unit-price">
                                                                        {{ number_format($item->price, 0, ',', '.') }} VNĐ
                                                                    </td>
                                                                    <td class="subtotal">
                                                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                                                        VNĐ
                                                                    </td>

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
                                                    VNĐ</h5>
                                            </div>
                                            <hr class="my-4">
                                            <div class="d-flex justify-content-between mb-5">
                                                <h5 class="text-uppercase">Tổng tiền</h5>
                                                <h5 id="cart-total">
                                                    {{ number_format($cart->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}
                                                    VNĐ</h5>
                                            </div>
                                            <form id="checkout-form" action="{{ route('cart.checkout') }}" method="GET">
                                                @guest
                                                    <button type="button" class="btn btn-dark btn-block btn-lg"
                                                        id="btn-checkout-guest">Mua hàng</button>
                                                @else
                                                    <button type="submit" class="btn-outline-buy">Mua hàng</button>
                                                @endguest
                                            </form>

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
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.onload = function() {
            const formatVND = n => {
                return Number(n).toLocaleString('vi-VN', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }) + ' VNĐ';
            };

            const updateTotal = () => {
                const checkedBoxes = [...document.querySelectorAll('.cart-item-checkbox:checked')];
                let s = 0,
                    totalQuantity = 0;

                checkedBoxes.forEach(box => {
                    const id = box.dataset.itemId;
                    const input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                    const subtotal = document.querySelector(`#cart-item-${id} .subtotal`);
                    s += parseFloat(subtotal.textContent.replace(/[^0-9]/g, '')) || 0;
                    totalQuantity += parseInt(input.value) || 0;
                });

                document.getElementById('cart-subtotal').textContent = formatVND(s);
                document.getElementById('cart-total').textContent = formatVND(s);
                document.getElementById('item-count').textContent = `${totalQuantity} sản phẩm`;
                document.getElementById('item-count-side').textContent = totalQuantity;
            };

            const showMsg = (m, t = 'danger') => {
                let d = document.getElementById('error-message') || Object.assign(document.createElement('div'), {
                    id: 'error-message',
                    className: `alert alert-${t}`
                });
                d.textContent = m;
                document.querySelector('.p-5vh').prepend(d);
                setTimeout(() => d.remove(), 3000);
            };

            const ajax = (u, m, d, b, s) => {
                if (b) b.disabled = true;
                fetch(u, {
                        method: m,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(d)
                    })
                    .then(r => r.ok ? r.json() : r.text().then(t => {
                        throw Error(`HTTP ${r.status}: ${t}`);
                    }))
                    .then(data => {
                        s(data);
                        if (b) b.disabled = false;
                    })
                    .catch(e => {
                        console.error(e);
                        showMsg(`Lỗi ${m === 'PUT' ? 'cập nhật' : 'xóa'}.`);
                        if (b) b.disabled = false;
                    });
            };

            // Update quantity logic (unchanged)
            function updateQuantity(id, quantity, btn = null) {
                const input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                const subtotalEl = document.querySelector(`#cart-item-${id} .subtotal`);
                const priceEl = document.querySelector(`#cart-item-${id} .unit-price`);
                const maxStock = parseInt(input.getAttribute('max')) || Infinity;

                if (quantity > maxStock) {
                    quantity = maxStock;
                    input.value = quantity;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Vượt quá tồn kho',
                        text: `Chỉ còn ${maxStock} sản phẩm trong kho.`,
                    });
                }

                ajax(`/gio-hang/update/${id}`, 'PUT', {
                    quantity
                }, btn, d => {
                    if (d.success) {
                        input.dataset.price = d.unit_price;
                        priceEl.textContent = formatVND(d.unit_price);
                        subtotalEl.textContent = formatVND(d.subtotal);
                        updateTotal();
                        if (typeof d.cart_count !== 'undefined') {
                            document.getElementById('cartCount').textContent = d.cart_count;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cập nhật thất bại',
                            text: d.message || 'Có lỗi xảy ra.',
                        });
                        if (typeof d.available_stock !== 'undefined') {
                            input.value = d.available_stock;
                            updateQuantity(id, d.available_stock, btn);
                        } else {
                            input.value = 1;
                            updateQuantity(id, 1, btn);
                        }
                    }
                });
            }

            // Handle variant change
            document.querySelectorAll('form[action*="/gio-hang/update-variant/"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const url = this.action;
                    const formData = new FormData(this);
                    const itemId = url.split('/').pop();
                    const productVariantId = formData.get('product_variant_id');

                    ajax(url, 'PUT', {
                        product_variant_id: productVariantId
                    }, null, data => {
                        if (data.success) {
                            if (data.merged) {
                                // If merged, remove the old item and update the existing one
                                document.getElementById(`cart-item-${itemId}`).remove();
                                const existingItem = document.getElementById(
                                    `cart-item-${data.existing_item_id}`);
                                if (existingItem) {
                                    const input = existingItem.querySelector('.quantity-input');
                                    input.value = data.new_quantity;
                                    const subtotalEl = existingItem.querySelector('.subtotal');
                                    subtotalEl.textContent = formatVND(data.new_subtotal);
                                }
                            } else {
                                // Update the current item
                                const row = document.getElementById(`cart-item-${itemId}`);
                                row.querySelector('.unit-price').textContent = formatVND(data
                                    .unit_price);
                                row.querySelector('.subtotal').textContent = formatVND(data
                                    .subtotal);
                                row.querySelector('.quantity-input').dataset.price = data
                                    .unit_price;
                                row.querySelector('img').src = data.image_url ||
                                    '{{ asset('images/no-image.png') }}';
                            }
                            updateTotal();
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: data.message || 'Cập nhật dung tích thành công.',
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: data.message || 'Không thể cập nhật dung tích.',
                            });
                        }
                    });
                });
            });

            // Quantity plus/minus and input logic (unchanged)
            document.querySelectorAll('.quantity-plus').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.itemId;
                    const input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                    let value = parseInt(input.value) || 1;
                    const max = parseInt(input.getAttribute('max')) || Infinity;

                    if (value < max) {
                        input.value = ++value;
                        updateQuantity(id, value, btn);
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tối đa trong kho',
                            text: `Chỉ còn ${max} sản phẩm trong kho.`,
                        });
                    }
                });
            });

            document.querySelectorAll('.quantity-minus').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.itemId;
                    const input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                    let value = parseInt(input.value) || 1;
                    if (value > 1) {
                        input.value = --value;
                        updateQuantity(id, value, btn);
                    }
                });
            });

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('input', debounce(function() {
                    const id = this.dataset.itemId;
                    let value = parseInt(this.value) || 1;
                    const max = parseInt(this.getAttribute('max')) || Infinity;

                    if (value < 1) value = 1;
                    if (value > max) {
                        value = max;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Vượt quá tồn kho',
                            text: `Chỉ còn lại ${max} sản phẩm.`,
                        });
                    }

                    this.value = value;
                    updateQuantity(id, value);
                }, 500));
            });

            document.querySelectorAll('.cart-item-checkbox').forEach(box => {
                box.addEventListener('change', updateTotal);
            });

            const checkAllBox = document.getElementById('check-all-cart');
            if (checkAllBox) {
                checkAllBox.addEventListener('change', function() {
                    const checked = this.checked;
                    document.querySelectorAll('.cart-item-checkbox').forEach(box => box.checked = checked);
                    updateTotal();
                });
            }

            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const checkedBoxes = [...document.querySelectorAll('.cart-item-checkbox:checked')];
                    const items = checkedBoxes.map(box => {
                        const id = box.dataset.itemId;
                        const input = document.querySelector(`.quantity-input[data-item-id="${id}"]`);
                        return {
                            id,
                            quantity: input.value
                        };
                    });

                   if (items.length === 0) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa chọn sản phẩm',
                            text: 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    let hiddenInput = document.getElementById('checkout-items');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'checkout_items';
                        hiddenInput.id = 'checkout-items';
                        checkoutForm.appendChild(hiddenInput);
                    }
                    hiddenInput.value = JSON.stringify(items);
                });
            }

            updateTotal();

            const btnGuest = document.getElementById('btn-checkout-guest');
            if (btnGuest) {
                btnGuest.addEventListener('click', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bạn chưa đăng nhập!',
                        text: 'Vui lòng đăng nhập để thanh toán.',
                        showConfirmButton: true,
                        confirmButtonText: 'Đăng nhập'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                });
            }
        };
    </script>
    <script>
        $(document).on('submit', '.add-to-cart-form', function(e) {
            e.preventDefault();
            const $form = $(this);
            const url = $form.attr('action');
            const method = $form.attr('method');
            const formData = $form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#cartCount').text(response.cart_count);
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: response.message || 'Đã thêm sản phẩm vào giỏ hàng.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cảnh báo!',
                            text: response.message || 'Đã xảy ra lỗi!',
                        });
                    }
                },
                error: function(xhr) {
                    let message = 'Có lỗi xảy ra, vui lòng thử lại!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: message,
                    });
                }
            });
        });
    </script>
@endsection
