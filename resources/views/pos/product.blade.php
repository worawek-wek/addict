{{-- หน้า POS --}}
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
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

    .modalHeadDecor .modal-header {
        padding: 0;
    }

    .modalHeadDecor .modal-title {
        padding: 1.25rem 1.5rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -65px;
        content: '';
        width: 0;
        height: 0;
        border-top: 65px solid #54BAB9;
        border-right: 65px solid transparent;
    }

    .btn-course {
        background-color: #ed2eed;
        color: white;
        border: 1px solid #ed2eed;
    }

    .btn-check:checked+.btn-course {
        background-color: #a31ea3;
        /* สีตอนเลือก */
        border-color: #a31ea3;
        color: #fff;
    }

    .btn-course:hover {
        background-color: #c91ec9 !important;
        border-color: #c91ec9 !important;
        color: #fff !important;
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

    /* hover ปกติ */
    .payment-card:hover {
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    /* checked ปกติ */
    .btn-check:checked+.payment-card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }

    /* ========================= */
    /* 🔒 disabled state */
    /* ========================= */
    .payment-card.disabled {
        opacity: 0.5;
        filter: grayscale(100%);
        cursor: not-allowed;
        pointer-events: none;
        /* กัน hover / click */
        transform: none !important;
    }

    /* กัน hover ตอน disabled */
    .payment-card.disabled:hover {
        border-color: #eee;
        transform: none;
    }

    /* กัน checked style ตอน disabled */
    .btn-check:disabled+.payment-card {
        border-color: #eee;
        background-color: #f8f9fa;
    }

    .pos-product-page {
        height: 100vh;
        font-size: 13px;
        overflow: hidden;
    }

    .pos-product-page>.pt-3 {
        height: 100vh;
        padding-top: .5rem !important;
        overflow: hidden;
    }

    .pos-product-page .container-fluid {
        padding-left: .5rem;
        padding-right: .5rem;
    }

    .pos-product-page .top-pos-actions {
        margin-bottom: .5rem !important;
        margin-left: 0 !important;
    }

    .pos-product-page .top-pos-actions .btn {
        padding: .35rem .55rem;
        font-size: 12px;
    }

    .pos-product-page .product-panel,
    .pos-product-page .pos-invoice-panel {
        height: calc(100vh - 58px);
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .pos-product-page .product-panel>.card-body {
        padding: .5rem;
    }

    .pos-product-page .label-pos {
        font-size: 1rem;
        margin-bottom: .5rem;
        padding: .35rem .5rem !important;
    }

    .pos-product-grid {
        --bs-gutter-x: .4rem;
        --bs-gutter-y: .4rem;
    }

    @media (min-width: 1200px) {
        .pos-product-grid>.col {
            flex: 0 0 12.5%;
            max-width: 12.5%;
        }
    }

    @media (min-width: 768px) and (max-width: 1199.98px) {
        .pos-product-grid>.col {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
    }

    .pos-product-card {
        min-height: 118px;
        border-radius: 6px;
    }

    .pos-product-icon {
        padding-top: .3rem !important;
    }

    .pos-product-icon i {
        font-size: 1.6rem !important;
    }

    .pos-product-card .card-body {
        padding: .35rem .4rem .45rem;
    }

    .pos-product-card .card-title {
        font-size: 12px;
        line-height: 1.2;
        margin-bottom: .15rem !important;
    }

    .pos-product-card .product-price {
        font-size: 11px;
        line-height: 1.2;
    }

    .pos-product-card .product-stock {
        font-size: 10px;
        margin-bottom: .25rem !important;
    }

    .pos-product-card .input-group {
        flex-wrap: nowrap;
    }

    .pos-product-card .qty-minus,
    .pos-product-card .qty-plus {
        width: 24px;
        height: 24px;
        padding: 0;
        line-height: 1;
    }

    .pos-product-card .qty-input {
        height: 24px;
        max-width: 38px !important;
        padding: .1rem;
        font-size: 11px;
    }

    .pos-product-card .btn-out-of-stock {
        padding: .2rem .35rem;
        font-size: 10px !important;
    }

    .pos-invoice-panel {
        font-size: 12px;
    }

    .pos-invoice-panel .card-header,
    .pos-invoice-panel .card-footer {
        padding: .5rem .75rem;
    }

    .pos-invoice-panel .px-4 {
        padding-left: .75rem !important;
        padding-right: .75rem !important;
    }

    .pos-invoice-panel .mb-3,
    .pos-invoice-panel .mt-3,
    .pos-invoice-panel .mt-4 {
        margin-top: .5rem !important;
        margin-bottom: .5rem !important;
    }

    .pos-invoice-panel .form-label {
        font-size: 12px;
        margin-bottom: .25rem;
    }

    .pos-invoice-panel .form-control,
    .pos-invoice-panel .btn {
        font-size: 12px;
        padding: .35rem .5rem;
    }

    .pos-invoice-panel .row.g-3 {
        --bs-gutter-x: .4rem;
        --bs-gutter-y: .4rem;
    }

    .pos-invoice-panel .payment-card {
        border-radius: 8px;
        padding: .45rem !important;
    }

    .pos-invoice-panel .payment-card i {
        font-size: 1.35rem !important;
    }

    .pos-invoice-panel .payment-card .mt-2 {
        margin-top: .2rem !important;
        font-size: 11px;
        line-height: 1.15;
    }

    .pos-invoice-panel .table th,
    .pos-invoice-panel .table td {
        font-size: 11px;
        padding: .25rem;
    }

    .pos-invoice-panel hr {
        margin: .5rem 0;
    }
</style>

<body class="pos-product-page">
    <!-- Layout wrapper -->
    <div class="pt-3">
        <div>
            <div>
                <div class="container-fluid">

                    <style>
                        .timer-box {
                            text-align: center;
                            color: white;
                            border-radius: 6px;
                            padding: 4px 0;
                            font-family: monospace;
                        }

                        .label-pos {
                            background-color: antiquewhite;
                            border-radius: 2px;
                        }
                    </style>
                    <form method="POST" id="insert_product" action="{{ route('pos.checkout') }}">
                        {{-- <form id="insert_order"> --}}
                        @csrf
                        <input type="hidden" name="type" value="2">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="d-flex align-items-center gap-2 mb-3 ms-3 top-pos-actions">


                                    <!-- button -->
                                    <a href="{{ url('pos/drink') }}"
                                        class="btn btn-danger d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa fa-wine-glass"></i>
                                        ขายดื่ม
                                    </a>
                                    <a href="{{ url('pos/room') }}"
                                        class="btn btn-info d-flex align-items-center justify-content-center gap-2">
                                        <i class="fa fa-door-open"></i>
                                        รวมห้อง
                                    </a>
                                    <a href="{{ url('admin/order-rooms') }}"
                                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                                        <i class="ti ti-settings"></i>
                                        หลังบ้าน
                                    </a>

                                </div>
                                <div class="col-md-9">
                                    <div class="card product-panel">
                                        <div class="card-body">
                                            <div class="col-12 mt-2">
                                                <h4 class="label-pos ff-playfair p-2">สินค้า</h4>
                                            </div>
                                            <div class="row row-cols-4 row-cols-md-6 row-cols-xl-8 pos-product-grid"
                                                id="productGrid">
                                                @include('pos.partials.product-grid', [
                                                    'products' => $products,
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm border-0 pos-invoice-panel">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Invoice</span>
                                            {{-- <span class="text-muted">#0001</span> --}}
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">ผู้ซื้อ</label>
                                            <div class="d-flex gap-3">

                                                <div>
                                                    <input type="radio" class="btn-check sale-type calculate"
                                                        name="customer_type" id="sale-customer" value="2">
                                                    <label class="btn btn-outline-secondary" for="sale-customer">
                                                        ลูกค้า
                                                    </label>
                                                </div>

                                                <div>
                                                    <input type="radio" class="btn-check sale-type calculate"
                                                        name="customer_type" id="sale-staff" value="1" checked>
                                                    <label class="btn btn-outline-primary" for="sale-staff">
                                                        พนักงาน
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">ผู้ซื้อ</label>
                                            {{-- <select id="salesStaffSelect" class="form-select"></select> --}}
                                            {{-- <form id="form_staff"> --}}
                                            <div
                                                class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                <input name="reception_name" type="text" id="reception"
                                                    placeholder="สแกนบัตรหรือกรอกรหัสผู้ซื้อ"
                                                    class="form-control me-2 reception-input" required />
                                                <input name="reception_id" type="hidden" id="salesReceptionSelect">
                                            </div>
                                            {{-- </form> --}}
                                        </div>
                                        <div class="px-4 mt-4">
                                            <label class="form-label fw-bold">สถานะการชำระเงิน</label>

                                            <div class="row g-3 px-2">

                                                <!-- ชำระแล้ว -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check" name="payment_status"
                                                        id="status-paid" value="1">

                                                    <label class="card payment-card text-center p-3" for="status-paid">
                                                        <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                                        <div class="mt-2 fw-bold">ชำระแล้ว</div>
                                                    </label>
                                                </div>

                                                <!-- ค้างชำระ -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check" name="payment_status"
                                                        id="status-pending" value="0" checked>

                                                    <label class="card payment-card text-center p-3"
                                                        for="status-pending">
                                                        <i class="bi bi-clock-history fs-1 text-warning"></i>
                                                        <div class="mt-2 fw-bold">ยังไม่ชำระ</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-4 mt-4">
                                            <label class="form-label fw-bold">ช่องทางชำระเงิน</label>

                                            <div class="row g-3 payment-methods">

                                                <!-- เงินสด -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check payment-method"
                                                        name="payment_method" id="pay-cash" value="cash">
                                                    <label class="card payment-card text-center p-3" for="pay-cash">
                                                        <i class="bi bi-cash-coin fs-1 text-success"></i>
                                                        <div class="mt-2 fw-bold">เงินสด</div>
                                                    </label>
                                                </div>

                                                {{-- <div class="col-md-6">
                                            <input type="radio" class="btn-check calculate"
                                                name="payment_method"
                                                id="pay-cash"
                                                value="cash"
                                                required>

                                            <label class="card payment-card text-center p-3" for="pay-cash">
                                                <i class="bi bi-cash-coin fs-1 text-success"></i>
                                                <div class="mt-2 fw-bold">เงินสด</div>
                                            </label>
                                        </div> --}}

                                                <!-- บัตรเครดิต -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check payment-method"
                                                        name="payment_method" id="pay-credit" value="credit_card">
                                                    <label class="card payment-card text-center p-3" for="pay-credit">
                                                        <i class="bi bi-credit-card-2-front fs-1 text-primary"></i>
                                                        <div class="mt-2 fw-bold">บัตรเครดิต</div>
                                                    </label>
                                                </div>
                                                <!-- Alipay -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check payment-method"
                                                        name="payment_method" id="pay-alipay" value="alipay">

                                                    <label class="card payment-card text-center p-3" for="pay-alipay">
                                                        <i class="bi bi-phone fs-1 text-info"></i>
                                                        <div class="mt-2 fw-bold">Alipay / WeChat</div>
                                                    </label>
                                                </div>
                                                <!-- QR -->
                                                <div class="col-md-6">
                                                    <input type="radio" class="btn-check payment-method"
                                                        name="payment_method" id="pay-qr" value="qr_code">
                                                    <label class="card payment-card text-center p-3" for="pay-qr">
                                                        <i class="bi bi-qr-code-scan fs-1"></i>
                                                        <div class="mt-2 fw-bold">QR Code</div>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="px-4 mt-3">

                                            <div class="fw-bold mb-2">รายการสินค้า</div>
                                            <div class="small">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>สินค้า</th>
                                                            <th class="text-center">จำนวน</th>
                                                            <th class="text-end">รวม</th>
                                                            <th class="text-center">ลบ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="invoiceItems">
                                                        <tr>
                                                            <td class="text-muted">ยังไม่มีสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="d-flex justify-content-between"><span>Subtotal</span><span>THB
                                                    <span
                                                        id="subtotal">{{ number_format($subtotal, 2) }}</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between"><span>Discount</span><span>-
                                                    THB <span
                                                        id="discount">{{ number_format($discount, 2) }}</span></span>
                                            </div>
                                            {{-- <div class="d-flex justify-content-between"><span>Tax</span><span>THB  <span id="tax">{{ number_format($tax, 2) }}</span></span></div> --}}
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total</span><span>THB <span
                                                        id="total">{{ number_format($total, 2) }}</span></span>
                                            </div>
                                            <input type="hidden" name="total_price" id="total_value">

                                            <button type="submit" class="btn btn-dark w-100 mt-3">
                                                Checkout
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                    {{-- <script>
    document.addEventListener('click', function (e) {

        // ➕ เพิ่มจำนวน
        if (e.target.classList.contains('qty-plus')) {
            const input = e.target.previousElementSibling;
            const max = parseInt(input.max || 9999);
            let val = parseInt(input.value || 1);
            if (val < max) input.value = val + 1;
            input.dispatchEvent(new Event('change'));
        }

        // ➖ ลดจำนวน
        if (e.target.classList.contains('qty-minus')) {
            const input = e.target.nextElementSibling;
            let val = parseInt(input.value || 1);
            if (val > 1) input.value = val - 1;
            input.dispatchEvent(new Event('change'));
        }
    });
</script> --}}
                    <!-- Footer -->
                    @include('admin/layout/inc_footer')

                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>

                </div>

                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
    <iframe id="print-iframe" style="display: none;"></iframe>


    @include('admin/layout/inc_js')

    {{-- ================== STYLES ================== --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .room-chip-disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .room-chip.active,
        .cash-btn.active,
        .other-btn.active {
            background-color: #5e2a5f;
            color: #fff;
        }
    </style>
    <script>
        document.querySelectorAll('.qty-input, input[name="customer_type"]').forEach(el => {
            el.addEventListener('change', function() {

                // ===== customer_type เปลี่ยน =====
                if (this.name === 'customer_type') {

                    const saleType =
                        document.querySelector('input[name="customer_type"]:checked')?.value || '2';

                    // 🔥 สลับ class ปุ่ม
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

                    console.log(111)
                    // (ของเดิม) อัปเดตราคาบนการ์ดสินค้า

                    document.querySelectorAll('.product-price').forEach(priceEl => {
                        console.log(priceEl);

                        const price = saleType === '1' ?
                            Number(priceEl.dataset.priceStaff) :
                            Number(priceEl.dataset.priceCustomer);

                        priceEl.textContent =
                            'THB ' + price.toLocaleString('th-TH', {
                                minimumFractionDigits: 2
                            });
                        // optional: เปลี่ยนสี
                        priceEl.classList.toggle('text-success', saleType === '1');
                        priceEl.classList.toggle('text-primary', saleType !== '1');
                    });
                }

                // ===== ของเดิม =====
                calculate();
            });
        });

        function togglePaymentMethod() {

            const status = $('input[name="payment_status"]:checked').val();

            $('.payment-method').each(function() {

                const card = $(this).closest('.payment-card');

                if (status == 1) {
                    $(this)
                        .prop('disabled', false)
                        .prop('required', true);

                    card.removeClass('disabled');

                } else {
                    $(this)
                        .prop('disabled', true)
                        .prop('required', false)
                        .prop('checked', false);

                    card.addClass('disabled');
                }
            });
        }

        // bind event
        document.querySelectorAll('input[name="payment_status"]').forEach(el => {
            el.addEventListener('change', togglePaymentMethod);
        });

        // init ตอนโหลด
        togglePaymentMethod();

        $('#insert_product').on('submit', function(event) {
            event.preventDefault();

            const iframe = document.getElementById('print-iframe');

            if (!this.checkValidity()) {
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }

            var formData = new FormData(this);

            var paymentStatus = formData.get('payment_status');

            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่มคำสั่งซื้อหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                didOpen: () => {
                    Swal.getConfirmButton().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/pos/checkout',
                        type: 'POST',
                        data: formData,
                        contentType: false, // ✅ ต้องมี
                        processData: false, // ✅ ต้องมี
                        success: function(response) {
                            if (response.status == true) {
                                Swal.fire({
                                    title: 'เพิ่มคำสั่งซื้อเรียบร้อยแล้ว',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    if (paymentStatus == '0') {
                                        location.reload();
                                    } else {
                                        const newWindow = window.open('', '_blank');
                                        newWindow.document.write(`
                                            <html>
                                            <head>
                                                <title>Print</title>
                                            </head>
                                            <body>
                                                ${response.data}
                                                <script>
                                                    window.onload = function () {
                                                        window.print();
                                                    };
                                                    window.onafterprint = function () {
                                                        window.close();
                                                    };
                                                <\/script>
                                            </body>
                                            </html>
                                        `);
                                        newWindow.document.close();

                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire('เกิดข้อผิดพลาด', response.message || '', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', error.responseJSON?.message || '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                }
            });
        });

        $('#insert_order').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if (!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            return alert(456)
            $.ajax({
                url: '/pos/insert-order', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                type: 'GET',
                data: $(this).serialize(),
                success: function(response) {

                },
                error: function(error) {

                }
            });
        });
        document.addEventListener('click', function(e) {

            // ➕ เพิ่มจำนวน
            if (e.target.classList.contains('qty-plus')) {
                const input = e.target.previousElementSibling;
                const max = parseInt(input.max || 9999);
                let val = parseInt(input.value || 1);
                if (val < max) input.value = val + 1;
                input.dispatchEvent(new Event('change'));
            }

            // ➖ ลดจำนวน
            if (e.target.classList.contains('qty-minus')) {
                const input = e.target.nextElementSibling;
                const min = parseInt(input.min || 0);
                let val = parseInt(input.value || 0);

                if (val > min) input.value = val - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
        calculate();

        function collectCalculatePayload() {
            const payload = {};

            document.querySelectorAll('.calculate').forEach(el => {
                if (!el.name) return;

                let name = el.name;

                // 🧠 qty[123] → payload.qty[123] = value
                if (name.startsWith('qty[')) {
                    const match = name.match(/\[(\d+)\]/);
                    if (!match) return;

                    const id = match[1];
                    const value = parseInt(el.value, 10) || 0;

                    if (value > 0) {
                        if (!payload.qty) payload.qty = {};
                        payload.qty[id] = value;
                    }
                    return;
                }

                // 🔧 แปลง ref_option_id[] → ref_option_id
                const isArray = name.endsWith('[]');
                if (isArray) {
                    name = name.replace('[]', '');
                }

                // 🟢 radio
                if (el.type === 'radio') {
                    if (el.checked) {
                        payload[name] = el.value;
                    }
                }

                // 🟢 checkbox (หลายค่า)
                else if (el.type === 'checkbox') {
                    if (el.checked) {
                        if (!Array.isArray(payload[name])) {
                            payload[name] = [];
                        }
                        payload[name].push(el.value);
                    }
                }

                // 🟢 text / number / hidden
                else {
                    const value = el.value.trim();
                    if (value === '') return;

                    payload[name] = isNaN(value) ? value : Number(value);
                }
            });

            return payload;
        }

        function calculate() {

            let subtotal = 0;
            let html = '';
            let hasItem = false;

            const saleType = document.querySelector('input[name="customer_type"]:checked')?.value || '2';

            document.querySelectorAll('.qty-input').forEach(input => {

                const productId = input.name.match(/\[(.*?)\]/)[1];
                const qty = parseInt(input.value) || 0;
                if (qty <= 0) return;

                hasItem = true;

                const name = input.dataset.name;

                const price = saleType === '1' ?
                    parseFloat(input.dataset.priceStaff) :
                    parseFloat(input.dataset.priceCustomer);

                const total = qty * price;
                subtotal += total;

                html += `
                    <tr>
                        <td>${name}</td>
                        <td class="text-center">${qty}</td>
                        <td class="text-end">
                            ฿${total.toLocaleString('th-TH', { minimumFractionDigits: 2 })}
                        </td>
                        <td class="text-center">
                            <a href="javascript:;"
                                class="btn btn-xs btn-danger rounded-pill px-2 py-1"
                                onclick="removeItem(${productId})">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
            `;
            });

            if (!hasItem) {
                html = `
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        ยังไม่มีสินค้า
                    </td>
                </tr>
            `;
            }

            // render item list
            document.getElementById('invoiceItems').innerHTML = html;

            // render price
            const formatted = subtotal.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            });

            document.getElementById('subtotal').innerText = formatted;
            document.getElementById('total').innerText = formatted;

            // hidden input (เอาไว้ submit)
            document.getElementById('total_value').value = subtotal;
        }

        function removeItem(productId) {

            const input = document.querySelector(`input[name="qty[${productId}]"]`);

            if (input) {
                input.value = 0;
            }

            calculate();
        }
    </script>

    {{-- ================== SCRIPTS ================== --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bindEmployeeLookup = (input, hiddenId, errorText) => {
                if (!input || input.dataset.lookupBound === '1') return;

                input.dataset.lookupBound = '1';

                const hiddenInput = input.closest('.d-flex')?.querySelector(`#${hiddenId}`) || document
                    .getElementById(hiddenId);

                const lookupEmployee = async () => {
                    const userCode = input.value.trim();
                    if (!userCode) return;

                    if (hiddenInput?.value && input.dataset.selectedName === userCode) {
                        return;
                    }

                    const refPositionId = input.dataset.refPositionId || '';

                    try {
                        const response = await fetch(
                            `/pos/get-user?user_code=${encodeURIComponent(userCode)}&ref_position_id=${encodeURIComponent(refPositionId)}&context=product`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            input.value = '';
                            input.dataset.selectedName = '';
                            if (hiddenInput) hiddenInput.value = '';
                            Swal.fire('แจ้งเตือน', data.message || 'ไม่พบพนักงาน', 'warning');
                            return;
                        }

                        input.value = data.name;
                        input.dataset.selectedName = data.name;
                        if (hiddenInput) hiddenInput.value = data.id;
                        input.blur(); // กันยิงซ้ำจากเครื่องสแกน
                    } catch (err) {
                        console.error(err);
                        Swal.fire('เกิดข้อผิดพลาด', errorText, 'error');
                    }
                };

                input.addEventListener('click', function() {
                    this.value = '';
                    this.dataset.selectedName = '';
                    if (hiddenInput) hiddenInput.value = '';
                    this.focus();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        e.preventDefault();
                        lookupEmployee();
                    }
                });

                input.addEventListener('change', lookupEmployee);
            };

            document.querySelectorAll('.staff-input').forEach(input => {
                bindEmployeeLookup(input, 'salesStaffSelect', 'ไม่สามารถค้นหาพนักงานได้');
            });

            document.querySelectorAll('.reception-input').forEach(input => {
                bindEmployeeLookup(input, 'salesReceptionSelect', 'ไม่สามารถค้นหาผู้ซื้อได้');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                tooltipTriggerEl))
        });

        document.addEventListener('DOMContentLoaded', () => {
            // --- Element Selections ---
            const salesStaffSelect = document.getElementById('salesStaffSelect');
            const customerSelect = document.getElementById('customerSelect');
            const roomIdInput = document.getElementById('selectedRoomId');
            const nextBtn = document.getElementById('nextToPaymentBtn');

            const formRoomId = document.getElementById('formRoomId');
            const formOrderId = document.getElementById('formOrderId');
            const formCustomerId = document.getElementById('formCustomerId');
            const formStaffId = document.getElementById('formStaffId');
            const formAddonId = document.getElementById('formAddonId');
            const formMamaId = document.getElementById('formMamaId');
            const formDuration = document.getElementById('formDuration');
            const formTotalPrice = document.getElementById('formTotalPrice');

            const walkinNextBtn = document.getElementById('walkinNextBtn');
            const walkinTimeSelect = document.getElementById('walkinTimeSelect');

            const confirmBtn = document.getElementById('confirmBtn');
            const roomModalEl = document.getElementById('checkoutRoomModal');
            const walkinModalEl = document.getElementById('walkinModal');
            const paymentModalEl = document.getElementById('checkoutPaymentModal');

            if (
                !salesStaffSelect ||
                !customerSelect ||
                !roomIdInput ||
                !nextBtn ||
                !walkinNextBtn ||
                !walkinTimeSelect ||
                !roomModalEl ||
                !walkinModalEl ||
                !paymentModalEl
            ) {
                return;
            }

            // --- Temporary State Variables ---
            let tempRoomId = null;
            let tempMamaId = null;

            // --- Validation Functions ---
            const checkNextBtnStatus = () => {
                const isStaffSelected = salesStaffSelect.value !== '';
                const isCustomerSelected = customerSelect.value !== '';
                nextBtn.disabled = !(isStaffSelected && isCustomerSelected);
                // alert(isStaffSelected);
            };

            const checkWalkinNextBtnStatus = () => {
                const isStaffSelected = document.getElementById('walkinStaffSelect').value;
                const isTimeSelected = walkinTimeSelect.value;
                walkinNextBtn.disabled = !(isStaffSelected && isTimeSelected);
            };

            // --- Initialize Sales Staff Select2 ---
            // $('#salesStaffSelect').select2({
            //     dropdownParent: $("#checkoutRoomModal"),
            //     placeholder: '-- เลือกพนักงานขาย --',
            //     allowClear: true,
            //     ajax: {
            //         url: '{{ route('pos.api.searchSalesStaff') }}',
            //         dataType: 'json',
            //         delay: 250,
            //         data: params => ({ q: params.term }),
            //         processResults: data => ({ results: data })
            //     }
            // }).on('select2:select', e => {
            //     tempMamaId = e.params.data.id;
            //     document.querySelectorAll('.room-chip').forEach(btn => {
            //         btn.disabled = false;
            //         btn.classList.remove('room-chip-disabled');
            //         const tooltip = bootstrap.Tooltip.getInstance(btn);
            //         if(tooltip) tooltip.disable();
            //     });
            //     checkNextBtnStatus();
            // }).on('select2:clear', () => {
            //     tempMamaId = null;
            // });

            // --- Room Selection ---
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('room-chip') && !e.target.disabled) {
                    document.querySelectorAll('.room-chip').forEach(b => b.classList.remove('active'));
                    e.target.classList.add('active');
                    tempRoomId = e.target.dataset.roomId;
                    roomIdInput.value = tempRoomId;

                    checkNextBtnStatus();

                    fetch(`/pos/room/${tempRoomId}/customers`)
                        .then(res => res.json())
                        .then(data => {
                            customerSelect.innerHTML =
                                '<option value="" disabled selected>-- กรุณาเลือก --</option>';
                            customerSelect.innerHTML +=
                                '<option value="walkin">+ Walk-in Customer</option>';
                            data.forEach(c => {
                                customerSelect.innerHTML +=
                                    `<option value="${c.order_id}" data-customer-id="${c.customer_id}">[Order #${c.order_id}] ${c.name}</option>`;
                            });
                            customerSelect.disabled = false;
                        });
                }
            });

            customerSelect.addEventListener('change', checkNextBtnStatus);

            // --- Flow Control: Normal Checkout ---
            nextBtn.addEventListener('click', () => {
                if (customerSelect.value === 'walkin') return;

                const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                formRoomId.value = roomIdInput.value;
                formOrderId.value = customerSelect.value;
                formCustomerId.value = selectedOption.dataset.customerId || null;
                formStaffId.value = salesStaffSelect.value;
                formMamaId.value = salesStaffSelect.value;
                formDuration.value = '';

                const roomModal = bootstrap.Modal.getInstance(roomModalEl);
                roomModal.hide();
                roomModalEl.addEventListener('hidden.bs.modal', () => {
                    const paymentModal = new bootstrap.Modal(paymentModalEl);
                    paymentModal.show();
                }, {
                    once: true
                });
            });

            // --- Flow Control: Walk-in ---
            customerSelect.addEventListener('change', (e) => {
                if (e.target.value === 'walkin') {
                    const roomModal = bootstrap.Modal.getInstance(roomModalEl);
                    roomModal.hide();
                    roomModalEl.addEventListener('hidden.bs.modal', () => {
                        const walkinModal = new bootstrap.Modal(walkinModalEl, {
                            focus: false
                        });
                        walkinModal.show();
                    }, {
                        once: true
                    });
                }
            });

            walkinNextBtn.addEventListener('click', () => {
                formRoomId.value = tempRoomId;
                formOrderId.value = 'walkin';
                formCustomerId.value = $('#walkinPhoneSelect').val() || null;
                formStaffId.value = $('#walkinStaffSelect').val() || null;
                formMamaId.value = tempMamaId;
                formAddonId.value = $('#walkinAddonSelect').val() || null;
                formDuration.value = walkinTimeSelect.value;

                const walkinModal = bootstrap.Modal.getInstance(walkinModalEl);
                walkinModal.hide();
                walkinModalEl.addEventListener('hidden.bs.modal', () => {
                    const paymentModal = new bootstrap.Modal(paymentModalEl);
                    paymentModal.show();
                }, {
                    once: true
                });
            });

            // --- Walk-in Modal Select2 Initializers & Event Listeners ---
            $('#walkinPhoneSelect').select2({
                dropdownParent: $("#walkinModal"),
                placeholder: '-- ค้นหาเบอร์โทร --',
                allowClear: true,
                ajax: {
                    url: '/pos/api/search-users',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(u => ({
                            id: u.id,
                            text: `${u.phone} - ${u.name}`
                        }))
                    })
                }
            });

            // $('#walkinStaffSelect').select2({
            //     dropdownParent: $("#walkinModal"),
            //     placeholder: '-- ค้นหาพนักงาน --',
            //     allowClear: true,
            //     ajax: {
            //         url: '{{ route('pos.api.searchStaff') }}',
            //         dataType: 'json',
            //         delay: 250,
            //         data: params => ({ q: params.term }),
            //         processResults: data => ({
            //             results: data.map(u => ({
            //                 id: u.id,
            //                 text: `${u.user_code ? '['+u.user_code+'] ' : ''}${u.nickname ?? ''} | Salary: ${u.salary ?? 0}฿`
            //             }))
            //         })
            //     }
            // }).on('select2:select', checkWalkinNextBtnStatus)
            //   .on('select2:clear', checkWalkinNextBtnStatus);

            walkinTimeSelect.addEventListener('change', checkWalkinNextBtnStatus);
            document.getElementById('walkinStaffSelect').addEventListener('input', checkWalkinNextBtnStatus);

            $('#walkinAddonSelect').select2({
                dropdownParent: $("#walkinModal"),
                placeholder: '-- ค้นหาสินค้าเสริม --',
                allowClear: true,
                ajax: {
                    url: '/pos/api/search-addons',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.map(a => ({
                            id: a.id,
                            text: `${a.name} | ${a.price}฿`
                        }))
                    })
                }
            });

            // --- Payment Logic ---
            const paymentMethod = document.getElementById('paymentMethod');
            // const confirmBtn = document.getElementById('confirmBtn'); // Already declared above
            // เมื่อเลือกวิธีการชำระเงิน ให้เซ็ตค่าและ enable ปุ่ม
            document.querySelectorAll('input[name="payment_method_radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    paymentMethod.value = this.value;
                    confirmBtn.disabled = false;
                });
            });

            // --- Cart & Search Logic ---
            // document.querySelectorAll('.qty-input').forEach(input => {
            //     input.addEventListener('change', function() { this.closest('form').submit(); });
            // });

            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearch');
            const productGrid = document.getElementById('productGrid');
            let searchTimeout;
            if (searchInput && clearSearchBtn && productGrid) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        fetch(`?q=${this.value}&ajax=true`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                productGrid.innerHTML = html;
                            });
                    }, 500);
                });
                clearSearchBtn.addEventListener('click', () => {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                });
            }

            // --- Modal Event Listeners (Cleanup & API Call) ---
            roomModalEl.addEventListener('show.bs.modal', () => {
                tempRoomId = null;
                tempMamaId = null;
            });

            paymentModalEl.addEventListener('show.bs.modal', () => {
                const summaryContainer = document.getElementById('paymentSummary');
                const totalContainer = document.getElementById('paymentTotal');

                summaryContainer.innerHTML = '<p class="text-center text-muted py-3">กำลังโหลด...</p>';
                totalContainer.textContent = 'THB 0.00';

                const addonId = formAddonId.value;
                const roomId = formRoomId.value;
                const duration = formDuration.value;
                const staffId = formStaffId.value;
                const csrfToken = '{{ csrf_token() }}';

                fetch('{{ route('pos.api.calculateSummary') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            addon_id: addonId,
                            room_id: roomId,
                            duration_minutes: duration,
                            staff_id: staffId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        summaryContainer.innerHTML = '';
                        if (data.items && data.items.length > 0) {
                            data.items.forEach(item => {
                                const itemDiv = document.createElement('div');
                                itemDiv.className =
                                    'd-flex justify-content-between text-muted small py-1';
                                itemDiv.innerHTML = `
                            <span>${item.name} <small>(${item.details})</small></span>
                            <span>${parseFloat(item.total).toFixed(2)}</span>
                        `;
                                summaryContainer.appendChild(itemDiv);
                            });
                        } else {
                            summaryContainer.innerHTML =
                                '<p class="text-center text-muted py-3">ไม่มีรายการ</p>';
                        }
                        totalContainer.textContent = `THB ${parseFloat(data.total).toFixed(2)}`;
                        formTotalPrice.value = data.total;
                    })
                    .catch(error => {
                        summaryContainer.innerHTML =
                            '<p class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>';
                        console.error('Error fetching summary:', error);
                    });
            });

            roomModalEl.addEventListener('hidden.bs.modal', () => {
                $('#salesStaffSelect').val(null).trigger('change');

                document.querySelectorAll('.room-chip').forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('room-chip-disabled');
                    btn.classList.remove('active');
                    const tooltip = bootstrap.Tooltip.getInstance(btn);
                    if (tooltip) tooltip.enable();
                });

                customerSelect.innerHTML = '<option value="" disabled selected>-- กรุณาเลือก --</option>';
                customerSelect.disabled = true;

                roomIdInput.value = '';

                nextBtn.disabled = true;
            });

            walkinModalEl.addEventListener('hidden.bs.modal', () => {
                $('#walkinPhoneSelect').val(null).trigger('change');
                $('#walkinStaffSelect').val(null).trigger('change');
                $('#walkinAddonSelect').val(null).trigger('change');
                walkinTimeSelect.value = '';
                walkinNextBtn.disabled = true;
            });

            paymentModalEl.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove(
                    'active'));
                cashInput.value = '';
                paymentMethod.value = '';
                cashAmount.value = '';
                confirmBtn.disabled = true;
            });

            $('#form_staff').on('submit', function(event) {
                event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
                if (!this.checkValidity()) {
                    // ถ้าฟอร์มไม่ถูกต้อง
                    this.reportValidity();
                    return console.log('ฟอร์มไม่ถูกต้อง');
                }
                $.ajax({
                    url: '/pos/get-user', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function(response) {
                        tempMamaId = response.id;
                        document.querySelectorAll('.room-chip').forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('room-chip-disabled');
                            const tooltip = bootstrap.Tooltip.getInstance(btn);
                            if (tooltip) tooltip.disable();
                        });
                        checkNextBtnStatus();
                        document.getElementById("staff").value = response.name;
                        document.getElementById("salesStaffSelect").value = response.id;
                        document.getElementById('staff').blur();

                    },
                    error: function(error) {
                        document.getElementById("staff").value = "";
                        document.getElementById("salesStaffSelect").value = "";
                        Swal.fire('แจ้งเตือน', error.responseJSON?.message || 'ไม่พบพนักงาน',
                            'warning');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            });
            $('#form_user').on('submit', function(event) {
                event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
                if (!this.checkValidity()) {
                    // ถ้าฟอร์มไม่ถูกต้อง
                    this.reportValidity();
                    return console.log('ฟอร์มไม่ถูกต้อง');
                }
                $.ajax({
                    url: '/pos/get-user', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function(response) {
                        document.getElementById("user").value = response.name;
                        document.getElementById("walkinStaffSelect").value = response.id;
                        document.getElementById('user').blur();
                        checkWalkinNextBtnStatus();

                    },
                    error: function(error) {
                        document.getElementById("user").value = "";
                        document.getElementById("walkinStaffSelect").value = "";
                        Swal.fire('แจ้งเตือน', error.responseJSON?.message || 'ไม่พบพนักงาน',
                            'warning');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            });
        });

        function clearInput(id) {
            document.getElementById(id).value = '';
        }

        function clearStaffInput(id) {
            document.getElementById(id).value = '';
            tempMamaId = null;
        }
    </script>

    <script>
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
                confirmButtonColor: '#5e2a5f'
            })
        @endif
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                confirmButtonColor: '#5e2a5f'
            })
        @endif
    </script>
</body>

</html>
