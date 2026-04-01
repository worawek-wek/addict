{{-- Edit Order Product – styled like /pos/product --}}
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <title>แก้ไขคำสั่งซื้อ #{{ $order->order_number }}</title>
</head>

<style>
    .table th {
        font-size: 15px;
        font-weight: bold;
    }

    .table td {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .qty-input {
        font-weight: 600;
        border-left: 0;
        border-right: 0;
    }

    .qty-minus,
    .qty-plus {
        width: 32px;
    }

    .payment-card {
        cursor: pointer;
        border: 2px solid #eee;
        transition: all .2s ease;
        border-radius: 12px;
    }

    .payment-card:hover {
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    .btn-check:checked+.payment-card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }

    .label-pos {
        background-color: antiquewhite;
        border-radius: 2px;
    }

    /* product card */
    .product-card-btn {
        cursor: pointer;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        transition: all .2s;
        background: #fff;
    }

    .product-card-btn:hover {
        border-color: #5e2a5f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .product-card-btn.out-of-stock {
        opacity: .45;
        pointer-events: none;
    }

    .product-price {
        font-size: .85rem;
    }

    /* disabled payment cards */
    .payment-card.disabled {
        opacity: .45;
        pointer-events: none;
    }

    .payment-card.disabled:hover {
        border-color: #eee;
        transform: none;
    }

    .btn-check:disabled+.payment-card {
        border-color: #eee !important;
        background-color: #fff !important;
    }
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">

                    <div class="pt-3">
                        <div class="container-fluid">

                            {{-- Back + title bar --}}
                            <div class="d-flex align-items-center mb-3 gap-3">
                                <a href="{{ route('order-products.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-arrow-left"></i> กลับ
                                </a>
                                <h5 class="mb-0 label-pos ff-playfair p-2">
                                    <i class="ti ti-edit me-1"></i>
                                    แก้ไขคำสั่งซื้อ #{{ $order->order_number }}
                                </h5>
                                <span class="badge bg-secondary ms-2">
                                    {{ $order->branch->name ?? '-' }} &bull; ขายโดย:
                                    {{ $order->seller->nickname ?? '-' }}
                                </span>
                            </div>

                            <div class="row">
                                {{-- ===================== LEFT: Product Grid ===================== --}}
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="col-12 mt-2">
                                                <h4 class="label-pos ff-playfair p-2">สินค้า</h4>
                                            </div>
                                            <div class="row g-3" id="productGrid">
                                                @forelse ($products as $product)
                                                    @php
                                                        $inStock = $product->stock > 0;
                                                        $existingItem = $order->products->firstWhere(
                                                            'ref_product_id',
                                                            $product->id,
                                                        );
                                                        $initQty = $existingItem ? $existingItem->quantity : 0;
                                                    @endphp
                                                    {{-- hidden price inputs for calculate() --}}
                                                    <input type="hidden" name="price_cus[{{ $product->id }}]"
                                                        value="{{ $product->price ?? 0 }}">
                                                    <input type="hidden" name="price_staff[{{ $product->id }}]"
                                                        value="{{ $product->price_staff ?? 0 }}">

                                                    <div class="col-md-2 mb-4">
                                                        <div
                                                            class="product-card-btn {{ $inStock ? '' : 'out-of-stock' }} p-2 text-center">
                                                            <div
                                                                class="d-flex justify-content-center align-items-center pt-2">
                                                                <i class="bi bi-cup-straw"
                                                                    style="font-size:3rem; color:#5e2a5f;"></i>
                                                            </div>
                                                            <div class="card-body text-center p-1">
                                                                <h6 class="card-title text-truncate mb-1"
                                                                    title="{{ $product->name }}">{{ $product->name }}
                                                                </h6>
                                                                <p class="fw-bold text-primary product-price mb-1"
                                                                    data-price-customer="{{ $product->price ?? 0 }}"
                                                                    data-price-staff="{{ $product->price_staff ?? 0 }}">
                                                                    THB {{ number_format($product->price ?? 0, 2) }}
                                                                </p>
                                                                <div class="small text-muted mb-2">Stock:
                                                                    {{ $product->stock }}</div>

                                                                @if ($inStock)
                                                                    <div
                                                                        class="input-group input-group-sm justify-content-center">
                                                                        <button
                                                                            class="btn btn-outline-secondary qty-minus"
                                                                            type="button"
                                                                            data-max="{{ $product->stock }}">−</button>
                                                                        <input type="number"
                                                                            class="form-control text-center qty-input calculate"
                                                                            name="qty[{{ $product->id }}]"
                                                                            value="{{ $initQty }}" min="0"
                                                                            max="{{ $product->stock }}"
                                                                            data-name="{{ $product->name }}"
                                                                            data-price-customer="{{ $product->price ?? 0 }}"
                                                                            data-price-staff="{{ $product->price_staff ?? 0 }}"
                                                                            style="max-width:60px;"
                                                                            onchange="calculate()">
                                                                        <button
                                                                            class="btn btn-outline-secondary qty-plus"
                                                                            type="button"
                                                                            data-max="{{ $product->stock }}">+</button>
                                                                    </div>
                                                                @else
                                                                    <button class="btn btn-secondary w-100 btn-sm"
                                                                        disabled>Out of Stock</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12">
                                                        <div class="alert alert-light border d-flex align-items-center">
                                                            <i class="bi bi-search me-2"></i>
                                                            <div>ไม่มีสินค้า</div>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===================== RIGHT: Invoice Panel ===================== --}}
                                <div class="col-md-3">
                                    <div class="card shadow-sm border-0">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Invoice</span>
                                        </div>

                                        {{-- ผู้ซื้อ --}}
                                        <div class="mb-3 px-4 pt-3">
                                            <label class="form-label fw-bold">ผู้ซื้อ</label>
                                            <div class="d-flex gap-3">
                                                <div>
                                                    <input type="radio" class="btn-check sale-type calculate"
                                                        name="customer_type" id="sale-customer" value="2"
                                                        {{ ($order->customer_type ?? 2) == 2 ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-primary"
                                                        for="sale-customer">ลูกค้า</label>
                                                </div>
                                                <div>
                                                    <input type="radio" class="btn-check sale-type calculate"
                                                        name="customer_type" id="sale-staff" value="1"
                                                        {{ ($order->customer_type ?? 2) == 1 ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary"
                                                        for="sale-staff">พนักงาน</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">ส่วนลด (Discount)</label>
                                            <input id="discount-input" name="discount" type="number" placeholder="0.00"
                                                min="0" value="{{ $order->discount ?? 0 }}" class="form-control"
                                                oninput="calculate()">
                                        </div>
                                        {{-- Payment status: paid / not paid --}}
                                        <div class="px-4 mt-2 mb-3">
                                            <label class="form-label fw-bold">สถานะการชำระเงิน</label>
                                            <div class="row g-3 px-2">
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check" name="payment_status"
                                                        id="status-paid" value="1"
                                                        {{ ($order->payment_status == 1) ? 'checked' : '' }}>
                                                    <label class="card payment-card text-center p-3" for="status-paid">
                                                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                                        <div class="mt-2 fw-bold">ชำระแล้ว</div>
                                                    </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check" name="payment_status"
                                                        id="status-pending" value="0"
                                                        {{ ($order->payment_status != 1) ? 'checked' : '' }}>
                                                    <label class="card payment-card text-center p-3" for="status-pending">
                                                        <i class="bi bi-clock-history fs-1 text-warning"></i>
                                                        <div class="mt-2 fw-bold">ยังไม่ชำระ</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 payment-methods px-4">

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check payment-method" name="payment_method"
                                                    id="pay-cash" value="cash"
                                                    {{ $order->payment_method == 'cash' ? 'checked' : '' }}>
                                                <label class="card payment-card text-center p-3" for="pay-cash">
                                                    <i class="bi bi-cash-coin fs-1 text-success"></i>
                                                    <div class="mt-2 fw-bold">เงินสด</div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check payment-method" name="payment_method"
                                                    id="pay-credit" value="credit_card"
                                                    {{ $order->payment_method == 'credit_card' ? 'checked' : '' }}>
                                                <label class="card payment-card text-center p-3" for="pay-credit">
                                                    <i class="bi bi-credit-card-2-front fs-1 text-primary"></i>
                                                    <div class="mt-2 fw-bold">บัตรเครดิต</div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check payment-method" name="payment_method"
                                                    id="pay-alipay" value="alipay"
                                                    {{ $order->payment_method == 'alipay' ? 'checked' : '' }}>
                                                <label class="card payment-card text-center p-3" for="pay-alipay">
                                                    <i class="bi bi-phone fs-1 text-info"></i>
                                                    <div class="mt-2 fw-bold">Alipay</div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check payment-method" name="payment_method"
                                                    id="pay-qr" value="qr_code"
                                                    {{ $order->payment_method == 'qr_code' ? 'checked' : '' }}>
                                                <label class="card payment-card text-center p-3" for="pay-qr">
                                                    <i class="bi bi-qr-code-scan fs-1 text-dark"></i>
                                                    <div class="mt-2 fw-bold">QR Code</div>
                                                    <div class="small text-muted">PromptPay / WeChat / Alipay</div>
                                                </label>
                                            </div>

                                        </div>

                                        {{-- รายการสินค้า --}}
                                        <div class="px-2 mt-3">
                                            <div class="fw-bold mb-2">รายการสินค้า</div>
                                            <div class="small">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>สินค้า (#id)</th>
                                                            <th class="text-center">จำนวน</th>
                                                            <th class="text-end">ราคา/ชิ้น</th>
                                                            <th class="text-end">รวม</th>
                                                            <th class="text-center">ลบ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="invoiceItems">
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted">
                                                                ยังไม่มีสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card-footer bg-white">
                                            <div class="d-flex justify-content-between">
                                                <span>Subtotal</span>
                                                <span>THB <span id="subtotal">0.00</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between text-danger">
                                                <span>Discount</span>
                                                <span>- THB <span id="discount-display">0.00</span></span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total</span>
                                                <span>THB <span id="total">0.00</span></span>
                                            </div>

                                            <button type="button" class="btn btn-dark w-100 mt-3"
                                                onclick="saveChanges()">
                                                <i class="bi bi-floppy me-1"></i> บันทึกการเปลี่ยนแปลง
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('admin/layout/inc_footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    @include('admin/layout/inc_js')

    <style>
        .room-chip.active,
        .cash-btn.active,
        .other-btn.active {
            background-color: #5e2a5f;
            color: #fff;
        }
    </style>

    <script>
        window._orderId = {{ $order->id }};
        window._csrfToken = '{{ csrf_token() }}';

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('qty-plus')) {
                const input = e.target.previousElementSibling;
                const max = parseInt(input.max || 9999);
                let val = parseInt(input.value || 0);
                if (val < max) input.value = val + 1;
                input.dispatchEvent(new Event('change'));
            }
            if (e.target.classList.contains('qty-minus')) {
                const input = e.target.nextElementSibling;
                const min = parseInt(input.min || 0);
                let val = parseInt(input.value || 0);
                if (val > min) input.value = val - 1;
                input.dispatchEvent(new Event('change'));
            }
        });

        function updateCustomerTypeUI() {
            const saleType = document.querySelector('input[name="customer_type"]:checked')?.value || '2';

            // Update ลูกค้า / พนักงาน button highlight
            document.querySelectorAll('input[name="customer_type"]').forEach(radio => {
                const label = document.querySelector(`label[for="${radio.id}"]`);
                if (!label) return;
                if (radio.checked) {
                    label.classList.remove('btn-outline-secondary');
                    label.classList.add('btn-outline-primary');
                } else {
                    label.classList.remove('btn-outline-primary');
                    label.classList.add('btn-outline-secondary');
                }
            });

            // Update price shown on each product card
            document.querySelectorAll('.product-price').forEach(priceEl => {
                const price = saleType === '1' ?
                    Number(priceEl.dataset.priceStaff) :
                    Number(priceEl.dataset.priceCustomer);
                priceEl.textContent = 'THB ' + price.toLocaleString('th-TH', {
                    minimumFractionDigits: 2
                });
                priceEl.classList.toggle('text-success', saleType === '1');
                priceEl.classList.toggle('text-primary', saleType !== '1');
            });
        }

        document.querySelectorAll('.qty-input, input[name="customer_type"]').forEach(el => {
            el.addEventListener('change', function() {
                if (this.name === 'customer_type') {
                    updateCustomerTypeUI();
                }
                calculate();
            });
        });

        // ===== calculate (same logic as POS) =====
        function calculate() {
            let subtotal = 0;
            let html = '';
            let hasItem = false;

            const saleType = document.querySelector('input[name="customer_type"]:checked')?.value || '2';

            document.querySelectorAll('.qty-input').forEach(input => {
                const match = input.name.match(/\[(.*?)\]/);
                if (!match) return;
                const productId = match[1];
                const qty = parseInt(input.value) || 0;
                if (qty <= 0) return;

                hasItem = true;
                const name = input.dataset.name;
                const price = saleType === '1' ?
                    parseFloat(input.dataset.priceStaff) :
                    parseFloat(input.dataset.priceCustomer);
                const rowTotal = qty * price;
                subtotal += rowTotal;

                html += `
                <tr>
                    <td>${name} <span class="text-muted small">(#${productId})</span></td>
                    <td class="text-center">${qty}</td>
                    <td class="text-end">฿${price.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                    <td class="text-end">฿${rowTotal.toLocaleString('th-TH', { minimumFractionDigits: 2 })}</td>
                    <td class="text-center">
                        <a href="javascript:;" class="btn btn-xs btn-danger rounded-pill px-2 py-1"
                           onclick="removeItem(${productId})">
                            <i class="bi bi-trash3"></i>
                        </a>
                    </td>
                </tr>`;
            });

            if (!hasItem) {
                html = `<tr><td colspan="5" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>`;
            }

            document.getElementById('invoiceItems').innerHTML = html;
            const discount = parseFloat(document.getElementById('discount-input')?.value) || 0;
            const total = Math.max(0, subtotal - discount);
            document.getElementById('subtotal').innerText = subtotal.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });
            document.getElementById('discount-display').innerText = discount.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });
            document.getElementById('total').innerText = total.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });
        }

        function removeItem(productId) {
            const input = document.querySelector(`input[name="qty[${productId}]"]`);
            if (input) {
                input.value = 0;
            }
            calculate();
        }

        // ===== Toggle payment method cards based on payment status =====
        function togglePaymentMethod() {
            const status = document.querySelector('input[name="payment_status"]:checked')?.value;
            document.querySelectorAll('.payment-method').forEach(function(radio) {
                const card = radio.closest('.col-md-6')?.querySelector('.payment-card');
                if (status == 1) {
                    radio.disabled = false;
                    if (card) card.classList.remove('disabled');
                } else {
                    radio.disabled = true;
                    radio.checked = false;
                    if (card) card.classList.add('disabled');
                }
            });
        }

        document.querySelectorAll('input[name="payment_status"]').forEach(function(el) {
            el.addEventListener('change', togglePaymentMethod);
        });

        togglePaymentMethod();

        // ===== Init on page load =====
        updateCustomerTypeUI();
        calculate();
        function saveChanges() {
            const saleType = document.querySelector('input[name="customer_type"]:checked')?.value || '2';
            const paymentStatus = document.querySelector('input[name="payment_status"]:checked')?.value ?? '0';
            const paymentMethod = paymentStatus == 1
                ? (document.querySelector('input[name="payment_method"]:checked')?.value ?? null)
                : null;
            const discount = parseFloat(document.getElementById('discount-input')?.value) || 0;
            const items = [];

            document.querySelectorAll('.qty-input').forEach(input => {
                const match = input.name.match(/\[(.*?)\]/);
                if (!match) return;
                const qty = parseInt(input.value) || 0;
                if (qty <= 0) return;
                const price = saleType === '1' ?
                    parseFloat(input.dataset.priceStaff) :
                    parseFloat(input.dataset.priceCustomer);
                items.push({
                    product_id: match[1],
                    qty,
                    price,
                    customer_type: saleType
                });
            });

            if (items.length === 0) {
                return Swal.fire('แจ้งเตือน', 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ', 'warning');
            }

            Swal.fire({
                title: 'ยืนยันการบันทึก?',
                text: 'คุณต้องการบันทึกการเปลี่ยนแปลงคำสั่งซื้อนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                didOpen: () => Swal.getConfirmButton().focus()
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch(`/admin/order-products/edit/${window._orderId}/update`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': window._csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            items,
                            discount,
                            payment_status: paymentStatus,
                            payment_method: paymentMethod
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'บันทึกเรียบร้อยแล้ว',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => window.location.href = '{{ route('order-products.index') }}');
                        } else {
                            Swal.fire('ผิดพลาด!', data.message ?? 'เกิดข้อผิดพลาด', 'error');
                        }
                    })
                    .catch(() => Swal.fire('ผิดพลาด!', 'ไม่สามารถบันทึกข้อมูลได้', 'error'));
            });
        }

    </script>
</body>

</html>
@if (false)

    <body>
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('admin/layout/inc_sidemenu')
                <div class="layout-page">
                    @include('admin/layout/inc_topmenu')
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">

                            {{-- Header --}}
                            <div class="d-flex align-items-center mb-4 gap-3">
                                <a href="{{ route('order-products.index') }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-arrow-left"></i> กลับ
                                </a>
                                <h4 class="mb-0">
                                    <i class="ti ti-edit text-main ti-md me-2"></i>
                                    แก้ไขคำสั่งซื้อ #{{ $order->order_number }}
                                </h4>
                                <span class="badge bg-secondary ms-2">
                                    {{ $order->branch->name ?? '-' }} &bull; พนักงานขาย:
                                    {{ $order->seller->nickname ?? '-' }}
                                </span>
                            </div>

                            <div class="row g-4">
                                {{-- LEFT: Product grid --}}
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-grid me-2"></i>สินค้า</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3" id="productGrid">
                                                @forelse ($products as $product)
                                                    @php $inStock = $product->stock > 0; @endphp
                                                    <div class="col-md-3 col-6">
                                                        <div class="card product-card text-center p-3 {{ $inStock ? '' : 'out-of-stock' }}"
                                                            onclick="addProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price ?? 0 }}, {{ $product->price_staff ?? 0 }}, {{ $product->stock }})">
                                                            <i class="bi bi-cup-straw"
                                                                style="font-size:2.5rem; color:#5e2a5f;"></i>
                                                            <div class="fw-bold mt-2 text-truncate"
                                                                title="{{ $product->name }}">{{ $product->name }}
                                                            </div>
                                                            <div class="text-primary fw-bold">
                                                                ฿{{ number_format($product->price, 2) }}</div>
                                                            <div class="small text-muted">Stock: {{ $product->stock }}
                                                            </div>
                                                            @if (!$inStock)
                                                                <span class="badge bg-secondary mt-1">หมด</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-12 text-center text-muted">ไม่มีสินค้า</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- RIGHT: Order summary / edit --}}
                                <div class="col-md-4">
                                    <div class="card sidebar-panel">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>รายการสินค้าในออเดอร์
                                            </h5>
                                        </div>
                                        <div class="card-body p-2">

                                            {{-- Customer type --}}
                                            <div class="mb-3 px-2">
                                                <label class="form-label fw-bold">ผู้ซื้อ</label>
                                                <div class="d-flex gap-3">
                                                    <div>
                                                        <input type="radio" class="btn-check" name="customer_type"
                                                            id="sale-customer" value="2"
                                                            {{ ($order->customer_type ?? 2) == 2 ? 'checked' : '' }}
                                                            onchange="recalcPrices()">
                                                        <label class="btn btn-outline-primary btn-sm"
                                                            for="sale-customer">ลูกค้า</label>
                                                    </div>
                                                    <div>
                                                        <input type="radio" class="btn-check" name="customer_type"
                                                            id="sale-staff" value="1"
                                                            {{ ($order->customer_type ?? 2) == 1 ? 'checked' : '' }}
                                                            onchange="recalcPrices()">
                                                        <label class="btn btn-outline-secondary btn-sm"
                                                            for="sale-staff">พนักงาน</label>
                                                    </div>
                                                </div>

                                            </div>



                                            <div id="order-items-wrapper">
                                                <table class="table table-sm" id="order-items-table">
                                                    <thead>
                                                        <tr>
                                                            <th>สินค้า</th>
                                                            <th style="width:90px">จำนวน</th>
                                                            <th style="width:70px">ราคา</th>
                                                            <th style="width:36px"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="order-items-body">
                                                        {{-- Existing items --}}
                                                        @foreach ($order->products as $item)
                                                            <tr class="order-item-row"
                                                                data-product-id="{{ $item->ref_product_id }}"
                                                                data-price-customer="{{ $item->product->price ?? $item->price }}"
                                                                data-price-staff="{{ $item->product->price_staff ?? $item->price }}">
                                                                <td class="small">{{ $item->product->name ?? '-' }}
                                                                </td>
                                                                <td>
                                                                    <div class="input-group input-group-sm">
                                                                        <button
                                                                            class="btn btn-outline-secondary qty-minus px-1"
                                                                            type="button"
                                                                            onclick="changeQty(this, -1)">−</button>
                                                                        <input type="number"
                                                                            class="form-control text-center qty-input"
                                                                            value="{{ $item->quantity }}"
                                                                            min="1" onchange="updateTotal()">
                                                                        <button
                                                                            class="btn btn-outline-secondary qty-plus px-1"
                                                                            type="button"
                                                                            onclick="changeQty(this, 1)">+</button>
                                                                    </div>
                                                                </td>
                                                                <td class="row-price small text-end">
                                                                    {{ number_format($item->price * $item->quantity, 2) }}
                                                                </td>
                                                                <td>
                                                                    <button
                                                                        class="btn btn-sm btn-outline-danger px-1 py-0"
                                                                        onclick="removeRow(this)">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @if ($order->products->isEmpty())
                                                    <p class="text-center text-muted small" id="empty-msg">
                                                        ยังไม่มีสินค้า
                                                        — คลิกที่สินค้าด้านซ้ายเพื่อเพิ่ม</p>
                                                @endif
                                            </div>

                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between fw-bold px-2">
                                                <span>ยอดรวม</span>
                                                <span
                                                    id="grand-total">฿{{ number_format($order->total_price, 2) }}</span>
                                            </div>

                                            <div class="d-grid mt-3 px-2">
                                                <button class="btn btn-success" onclick="saveChanges()">
                                                    <i class="bi bi-floppy me-1"></i> บันทึกการเปลี่ยนแปลง
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('admin/layout/inc_footer')
                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>


        <script>
            const orderId = {{ $order->id }};
            const csrfToken = '{{ csrf_token() }}';
            const productData = @json($products->keyBy('id'));

            function getCustomerType() {
                return document.querySelector('input[name="customer_type"]:checked')?.value ?? '2';
            }

            function addProduct(id, name, priceCustomer, priceStaff, stock) {
                const type = getCustomerType();
                const price = type == 1 ? priceStaff : priceCustomer;
                const tbody = document.getElementById('order-items-body');

                const existing = tbody.querySelector(`tr[data-product-id="${id}"]`);
                if (existing) {
                    const qtyInput = existing.querySelector('.qty-input');
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    updateTotal();
                    return;
                }

                document.getElementById('empty-msg')?.remove();

                const tr = document.createElement('tr');
                tr.className = 'order-item-row';
                tr.dataset.productId = id;
                tr.dataset.priceCustomer = priceCustomer;
                tr.dataset.priceStaff = priceStaff;
                tr.innerHTML = `
            <td class="small">${name}</td>
            <td>
                <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary qty-minus px-1" type="button" onclick="changeQty(this, -1)">−</button>
                    <input type="number" class="form-control text-center qty-input" value="1" min="1" onchange="updateTotal()">
                    <button class="btn btn-outline-secondary qty-plus px-1" type="button" onclick="changeQty(this, 1)">+</button>
                </div>
            </td>
            <td class="row-price small text-end">${price.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-outline-danger px-1 py-0" onclick="removeRow(this)">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
                tbody.appendChild(tr);
                updateTotal();
            }

            function changeQty(btn, delta) {
                const input = btn.closest('.input-group').querySelector('.qty-input');
                const newVal = Math.max(1, parseInt(input.value) + delta);
                input.value = newVal;
                updateTotal();
            }

            function removeRow(btn) {
                btn.closest('tr').remove();
                updateTotal();
                if (!document.querySelector('#order-items-body tr')) {
                    const p = document.createElement('p');
                    p.id = 'empty-msg';
                    p.className = 'text-center text-muted small';
                    p.textContent = 'ยังไม่มีสินค้า — คลิกที่สินค้าด้านซ้ายเพื่อเพิ่ม';
                    document.getElementById('order-items-wrapper').appendChild(p);
                }
            }

            function recalcPrices() {
                const type = getCustomerType();
                document.querySelectorAll('#order-items-body tr.order-item-row').forEach(tr => {
                    const price = type == 1 ? parseFloat(tr.dataset.priceStaff) : parseFloat(tr.dataset.priceCustomer);
                    const qty = parseInt(tr.querySelector('.qty-input').value);
                    tr.querySelector('.row-price').textContent = (price * qty).toFixed(2);
                });
                updateTotal();
            }

            function updateTotal() {
                const type = getCustomerType();
                let total = 0;
                document.querySelectorAll('#order-items-body tr.order-item-row').forEach(tr => {
                    const price = type == 1 ? parseFloat(tr.dataset.priceStaff) : parseFloat(tr.dataset.priceCustomer);
                    const qty = parseInt(tr.querySelector('.qty-input').value);
                    tr.querySelector('.row-price').textContent = (price * qty).toFixed(2);
                    total += price * qty;
                });
                document.getElementById('grand-total').textContent = '฿' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,
                    ',');
            }

            function saveChanges() {
                const type = getCustomerType();
                const rows = document.querySelectorAll('#order-items-body tr.order-item-row');
                const items = [];
                rows.forEach(tr => {
                    items.push({
                        product_id: tr.dataset.productId,
                        qty: tr.querySelector('.qty-input').value,
                        customer_type: type
                    });
                });

                fetch(`/admin/order-products/edit/${orderId}/update`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            items: items
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', 'บันทึกการเปลี่ยนแปลงเรียบร้อย', 'success')
                                .then(() => window.location.href = '{{ route('order-products.index') }}');
                        } else {
                            Swal.fire('ผิดพลาด!', data.message ?? 'เกิดข้อผิดพลาด', 'error');
                        }
                    })
                    .catch(() => Swal.fire('ผิดพลาด!', 'ไม่สามารถบันทึกข้อมูลได้', 'error'));
            }

            updateTotal();
        </script>
    </body>

    </html>
@endif
