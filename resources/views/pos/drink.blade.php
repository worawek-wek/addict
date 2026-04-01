{{-- หน้า POS --}}
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
</head>
<link rel="stylesheet" href="../../assets/vendor/libs/spinkit/spinkit.css" />

<div id="loadingOverlay" style="display: none;">
    <div class="col">
        <!-- Chase -->
        <div class="sk-chase sk-primary m-auto">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
        </div>
    </div>
</div>

<style>
  /* พื้นหลังทึบ */
  #loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(234, 244, 255, 0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* สปินเนอร์หมุน */
  .spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #28c76f;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
</style>
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

    .btn-check:checked + .btn-course {
        background-color: #a31ea3;   /* สีตอนเลือก */
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
    .qty-minus, .qty-plus {
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

    .btn-check:checked + .payment-card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
</style>

<body>
<!-- Layout wrapper -->
<div class="layout-content-navbar pt-3">

    <div class="">
        <div>
             <div class="d-flex align-items-center gap-2 mb-3 ms-5">


                <!-- button -->
                <a href="{{ url('pos/product') }}" class="btn btn-warning d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-shopping-cart"></i>
                    ขายสินค้า
                </a>
                <a href="{{ url('pos/room') }}" class="btn btn-info d-flex align-items-center justify-content-center gap-2">
                    <i class="fa fa-door-open"></i>
                    รวมห้อง
                </a>
                <a href="{{ url('admin/order-rooms') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-settings"></i>
                    หลังบ้าน
                </a>

            </div>
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
                    <form method="POST" id="insert_drink" action="{{ route('pos.checkout') }}">
                    {{-- <form id="insert_order"> --}}
                        @csrf
                        <input type="hidden" name="ref_room_id" value="{{ $room_id }}">
                        <div class="container-fluid">

                            <div class="row">


                                <div class="col-md-9">

                                    <div class="card">
                                        <div class="card-body">
                                        <div class="col-12 mt-2">
                                            <h4 class="label-pos ff-playfair p-2">ดื่ม</h4>
                                        </div>
                                        <div class="row" id="drinkGrid">
                                            {{-- @include('pos.partials.drink-grid', ['drinks' => $drinks]) --}}
                                            @forelse($drinks as $drink)
                                                @php
                                                    $totalRemain = \App\Models\DrinkStockReadyForSale::where('ref_drink_id', $drink->id)->sum('qty') ?? 0;
                                                    $inStock = $totalRemain > 0;
                                                @endphp
                                                                <input type="hidden"
                                                                        name="price_cus[{{ $drink->id }}]"
                                                                        value="{{ $drink->price }}"
                                                                        />
                                                                <input type="hidden"
                                                                        name="price_staff[{{ $drink->id }}]"
                                                                        value="{{ $drink->price_staff }}"
                                                                        />
                                                <div class="col-md-2 mb-4">
                                                    <div class="card border-0 shadow-sm {{ $inStock ? '' : 'opacity-50' }}">

                                                    {{-- ใช้ Bootstrap Icon (อาหาร/เครื่องดื่ม) แทนรูปภาพ --}}
                                                    <div class="d-flex justify-content-center align-items-center pt-2">
                                                        <i class="bi bi-cup-straw" style="font-size:3rem; color:#5e2a5f;"></i>
                                                    </div>

                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-truncate mb-1" title="{{ $drink->name }}">{{ $drink->name }}</h6>
                                                        <p class="fw-bold text-primary drink-price mb-1"
                                                                        data-price-customer="{{ $drink->price }}"
                                                                        data-price-staff="{{ $drink->price_staff }}">THB {{ number_format($drink->price, 2) }}</p>
                                                        <div class="small text-muted  mb-2">Stock: {{ $totalRemain }}</div>

                                                        @if($inStock)
                                                            <div class="input-group input-group-sm justify-content-center">
                                                                <button class="btn btn-outline-secondary qty-minus"
                                                                        type="button"
                                                                        data-max="{{ $totalRemain }}">−</button>

                                                                <input type="number"
                                                                        class="form-control text-center qty-input calculate"
                                                                        name="qty[{{ $drink->id }}]"
                                                                        @if(@$drink_id == $drink->id)
                                                                        value="1"
                                                                        @else
                                                                        value="0"
                                                                        @endif
                                                                        min="0"
                                                                        max="{{ $totalRemain }}"
                                                                        data-name="{{ $drink->name }}"
                                                                        data-price-customer="{{ $drink->price }}"
                                                                        data-price-staff="{{ $drink->price_staff }}"
                                                                        style="max-width:60px;"
                                                                        onchange="calculate()" />

                                                                <button class="btn btn-outline-secondary qty-plus"
                                                                        type="button"
                                                                        data-max="{{ $totalRemain }}">+</button>
                                                            </div>
                                                        @else
                                                            <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                                                        @endif
                                                    </div>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="col-12">
                                                    <div class="alert alert-light border d-flex align-items-center" role="alert">
                                                    <i class="bi bi-search me-2"></i>
                                                    <div>
                                                        No drinks found
                                                        @if(request('q')) for "<strong>{{ request('q') }}</strong>" @endif
                                                    </div>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Invoice</span>
                                        <span class="text-muted">#0001</span>
                                    </div>
                                    <div class="mb-3 px-4">
                                        <label class="form-label fw-bold">เลือกพนักงานขาย</label>
                                            <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                                            <input name="reception_name" type="text" id="reception" placeholder="แตะบัตรพนักงาน หรือ ป้อนรหัสพนักงาน" class="form-control me-2 reception-input" required/>
                                            <input name="reception_id" type="hidden" id="salesReceptionSelect">
                                            <input type="hidden" name="ref_position_id" value="1">
                                            </div>
                                    </div>
                                    <div class="mb-3 px-4">
                                        <label class="form-label fw-bold">เลือกพนักงานนวด</label>
                                            <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                                            <input name="staff_name" type="text" id="staff" placeholder="แตะบัตรพนักงานนวด หรือ ป้อนพนักงานนวด" class="form-control me-2 staff-input" required/>
                                            <input name="staff_id" type="hidden" id="salesStaffSelect">
                                            <input type="hidden" name="ref_position_id" value="1">
                                            </div>
                                    </div>
                                    <div class="mb-3 px-4">
                                        <label class="form-label fw-bold">ส่วนลด</label>
                                            <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                                            <input name="discount" type="text" placeholder="ส่วนลด" class="form-control me-2 calculate" id="discount-list" oninput="calculate()"/>
                                            </div>
                                    </div>
                                    <div class="row g-3 payment-methods px-4">

                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check calculate"
                                                name="payment_method"
                                                id="pay-cash"
                                                value="cash"
                                                required>

                                            <label class="card payment-card text-center p-3" for="pay-cash">
                                                <i class="bi bi-cash-coin fs-1 text-success"></i>
                                                <div class="mt-2 fw-bold">เงินสด</div>
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check calculate"
                                                name="payment_method"
                                                id="pay-credit"
                                                value="credit_card"
                                                required>

                                            <label class="card payment-card text-center p-3" for="pay-credit">
                                                <i class="bi bi-credit-card-2-front fs-1 text-primary"></i>
                                                <div class="mt-2 fw-bold">บัตรเครดิต</div>
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check calculate"
                                                name="payment_method"
                                                id="pay-alipay"
                                                value="alipay"
                                                required>

                                            <label class="card payment-card text-center p-3" for="pay-alipay">
                                                <i class="bi bi-phone fs-1 text-info"></i>
                                                <div class="mt-2 fw-bold">Alipay</div>
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check calculate"
                                                name="payment_method"
                                                id="pay-qr"
                                                value="qr_code"
                                                required>

                                            <label class="card payment-card text-center p-3" for="pay-qr">
                                                <i class="bi bi-qr-code-scan fs-1 text-dark"></i>
                                                <div class="mt-2 fw-bold">QR Code</div>
                                                <div class="small text-muted">PromptPay / WeChat / Alipay</div>
                                            </label>
                                        </div>

                                    </div>
                                        {{-- <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                                            @forelse($cart as $item)
                                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-cup-straw me-2" style="font-size: 1.6rem;"></i>
                                                        <div>
                                                            <div class="text-truncate" style="max-width: 180px" title="{{ $item['name'] }}">
                                                                {{ $item['name'] }}
                                                            </div>
                                                            <small class="text-muted">THB {{ number_format($item['price'], 2) }}</small>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2">

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}"
                                                                class="btn btn-sm btn-outline-dark">-</button>
                                                        </form>

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST" class="mx-1">
                                                            @csrf
                                                            <input type="number" name="qty"
                                                                class="form-control form-control-sm text-center qty-input"
                                                                value="{{ $item['qty'] }}" min="0" max="{{ $item['stock'] ?? 999 }}"
                                                                step="1" style="width:72px">
                                                        </form>

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}"
                                                                class="btn btn-sm btn-outline-dark">+</button>
                                                        </form>

                                                        <form action="{{ route('pos.remove', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted text-center mb-0">No items in cart</p>
                                            @endforelse
                                        </div> --}}
                                        <style>
                                            .drink-row {
                                                display: flex;
                                                justify-content: space-between;
                                                font-size: 14px;
                                                margin-bottom: 4px;
                                            }
                                            .drink-name {
                                                color: #555;
                                            }
                                            .drink-price {
                                                white-space: nowrap;
                                            }
                                            .summary-row {
                                                display: flex;
                                                justify-content: space-between;
                                                font-size: 14px;
                                                margin-bottom: 4px;
                                            }
                                            .summary-left {
                                                color: #555;
                                            }
                                            .summary-right {
                                                white-space: nowrap;
                                            }
                                        </style>
                                        <div class="card-footer">
                                            <h6 class="fw-bold mb-3">รายละเอียดการเลือก</h6>

                                            <div id="summary-room"></div>
                                            <div id="summary-course"></div>

                                            <hr class="my-2">

                                            <div id="summary-price"></div>

                                            <div class="mt-2">
                                                <div class="fw-bold mb-2">Options</div>
                                                <div id="summary-option-list"></div>
                                                <hr>
                                            </div>
                                            <div class="mt-2">
                                                <div class="fw-bold mb-2">รายการสินค้า</div>
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>สินค้า</th>
                                                            <th class="text-center">จำนวน</th>
                                                            <th class="text-end">รวม</th>
                                                            <th class="text-center">ลบ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="summary-drink-list">
                                                        <tr>
                                                            <td class="text-muted">ยังไม่มีสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="d-flex justify-content-between"><span>Subtotal</span><span>THB  <span id="subtotal">{{ number_format($subtotal, 2) }}</span></span></div>
                                            <div class="d-flex justify-content-between"><span>Discount</span><span>- THB  <span id="discount">{{ number_format($discount, 2) }}</span></span></div>
                                            {{-- <div class="d-flex justify-content-between"><span>Tax</span><span>THB  <span id="tax">{{ number_format($tax, 2) }}</span></span></div> --}}
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>THB <span id="total">{{ number_format($total, 2) }}</span></span></div>
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
                {{-- ================== MODAL #1 : เลือกห้อง + customer ================== --}}
                <div class="modal fade" id="checkoutRoomModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow rounded-4 p-3">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Choose Room & Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">เลือกพนักงานขาย</label>
                                    {{-- <select id="salesStaffSelect" class="form-select"></select> --}}
                                    <form id="form_staff">
                                        <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                                        <input name="user_code" type="text" placeholder="แสกนบัตรพนักงาน" class="form-control me-2"/>
                                        <input type="hidden" id="salesStaffSelect">
                                        <input type="hidden" name="ref_position_id" value="1">
                                        </div>
                                    </form>
                                </div>

                                <hr class="my-3">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Choose a room</label>
                                    <div class="store-pill mb-2">{{ $storefrontName ?? 'Cashier' }}</div>

                                    @foreach ($roomGroups ?? [] as $group)
                                        <div class="fw-bold mb-2">{{ $group['name'] }}</div>
                                        <div class="mb-3 d-flex flex-wrap gap-2">
                                            @foreach ($group['rooms'] as $room)
                                                <button type="button" class="room-chip btn btn-outline-secondary room-chip-disabled"
                                                    data-room-id="{{ $room['id'] }}" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Please select a sales staff first">{{ $room['label'] }}</button>
                                            @endforeach
                                        </div>
                                    @endforeach

                                    <input type="hidden" id="selectedRoomId">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Customers in this room</label>
                                    <select id="customerSelect" class="form-select" disabled>
                                        <option value="">-- กรุณาเลือก --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-dark w-100" id="nextToPaymentBtn" disabled>
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================== MODAL #Walkin ================== --}}
                <div class="modal fade" id="walkinModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow rounded-4 p-3">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">Walk-in Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">เลือกเบอร์สมาชิก (ไม่บังคับ)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-telephone fs-5 text-muted"></i>
                                        <select id="walkinPhoneSelect" class="form-select" style="width: 100%"></select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">เลือกพนักงาน (บังคับ)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge fs-5 text-muted"></i>

                                    <form id="form_user">
                                        <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                                        <input name="user_code" type="text" id="user" placeholder="แสกนบัตรพนักงาน" onclick="clearInput('user')" class="form-control me-2"/>
                                        <input type="hidden" id="walkinStaffSelect">
                                        <input type="hidden" name="ref_position_id" value="2">
                                        </div>
                                        {{-- <button type="submit" class="btn btn-primary mt-2" onclick="focusInput()">คลิ๊กที่นี่เมื่อแตะบัตรไม่ได้</button> --}}
                                    </form>
                                        {{-- <select id="walkinStaffSelect" class="form-select" style="width: 100%"></select> --}}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">เลือกเวลา (บังคับ)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock fs-5 text-muted"></i>
                                        <select id="walkinTimeSelect" class="form-select" style="width: 100%">
                                            <option value="" selected disabled>-- กรุณาเลือกเวลา --</option>
                                            <option value="40">40 นาที</option>
                                            <option value="60">60 นาที</option>
                                            <option value="90">90 นาที</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">เลือกสินค้าเสริม (ไม่บังคับ)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-plus-circle fs-5 text-muted"></i>
                                        <select id="walkinAddonSelect" class="form-select" style="width: 100%"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-dark w-100" id="walkinNextBtn" disabled>
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================== MODAL #2 : Payment Method ================== --}}
                <div class="modal fade" id="checkoutPaymentModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow rounded-4 p-3">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">วิธีการชำระเงิน</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('pos.checkout') }}">
                                @csrf
                                <input type="hidden" name="room_id" id="formRoomId">
                                <input type="hidden" name="order_id" id="formOrderId">
                                <input type="hidden" name="payment_method" id="paymentMethod">
                                <input type="hidden" name="customer_id" id="formCustomerId">
                                <input type="hidden" name="staff_id" id="formStaffId">
                                <input type="hidden" name="addon_id" id="formAddonId">
                                <input type="hidden" name="mama_id" id="formMamaId">
                                <input type="hidden" name="duration_minutes" id="formDuration">
                                <input type="hidden" name="total_price" id="formTotalPrice">

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <h6 class="fw-bold">สรุปรายการ</h6>
                                        <div id="paymentSummary" class="border-top pt-2">
                                            <p class="text-center text-muted py-3">กำลังโหลด...</p>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>ยอดรวมสุทธิ</span>
                                            <span id="paymentTotal">THB 0.00</span>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกวิธีการชำระเงิน</label>
                                        <div class="list-group">
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="cash">
                                                เงินสด (Cash)
                                            </label>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="promptpay">
                                                โอน/สแกน QR Code (PromptPay)
                                            </label>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="credit_card">
                                                บัตรเครดิต/เดบิต (Credit/Debit Card)
                                            </label>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="wechat">
                                                WeChat Pay
                                            </label>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="alipay">
                                                Alipay
                                            </label>
                                            <label class="list-group-item">
                                                <input class="form-check-input me-2" type="radio" name="payment_method_radio" value="ewallet">
                                                TrueMoney Wallet / LINE Pay (E-Wallet)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" class="btn btn-dark w-100" id="confirmBtn" disabled>
                                        Confirm Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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

</body>
</html>

{{-- ================== STYLES ================== --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .room-chip-disabled {
        pointer-events: none;
        opacity: 0.5;
    }
    .room-chip.active,
    .cash-btn.active,
    .other-btn.active { background-color: #5e2a5f; color: #fff; }
</style>
<script>


        $('#insert_drink').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            const iframe = document.getElementById('print-iframe');

            if (!this.checkValidity()) {
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }

            var formData = new FormData(this);

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
                        url: '/pos/drink-checkout',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {

                            if (response.status == true) {

                                Swal.fire({
                                    title: 'เพิ่มคำสั่งซื้อเรียบร้อยแล้ว',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {

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

                                    // รีเฟรชหน้าหลักทันที
                                    location.reload();

                                });

                            }

                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                            console.error(error);
                        }
                    });
                }
            });
        });
        $('#insert_order').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
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
            const min = parseInt(input.min || 0);
            let val = parseInt(input.value || 0);

            if (val > min) input.value = val - 1;
            input.dispatchEvent(new Event('change'));
        }
    });
    // calculate();
    document.querySelectorAll('.staff-input').forEach(input => {
        input.addEventListener('click', function () {
            this.value = '';
            this.focus();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // กัน submit form

                const userCode = this.value.trim();
                if (!userCode) return;

                fetch(`/pos/get-user?user_code=${encodeURIComponent(userCode)}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);

                    // if (data.success) {
                        this.value = data.name;
                        $('#salesStaffSelect').val(data.id);
                        this.blur(); // กันยิงซ้ำจากเครื่องสแกน
                    // } else {
                    //     alert(data.message || 'ไม่พบพนักงาน');
                    //     this.value = '';
                    // }
                })
                .catch(err => {
                    console.error(err);
                    alert('เกิดข้อผิดพลาด');
                });
            }
        });
    });
    document.querySelectorAll('.reception-input').forEach(input => {
        input.addEventListener('click', function () {
            this.value = '';
            this.focus();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // กัน submit form

                const userCode = this.value.trim();
                if (!userCode) return;

                fetch(`/pos/get-user?user_code=${encodeURIComponent(userCode)}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);

                    // if (data.success) {
                        this.value = data.name;
                        $('#salesReceptionSelect').val(data.id);
                        this.blur(); // กันยิงซ้ำจากเครื่องสแกน
                    // } else {
                    //     alert(data.message || 'ไม่พบพนักงาน');
                    //     this.value = '';
                    // }
                })
                .catch(err => {
                    console.error(err);
                    alert('เกิดข้อผิดพลาด');
                });
            }
        });
    });
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
    function summaryRow(label, value, boldLabel = false) {
        return `
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="${boldLabel ? 'fw-bold' : 'text-muted'}">${label}</span>
                <span class="fw-semibold text-end">${value}</span>
            </div>
        `;
    }
    function renderSummary() {

        let roomName = $('input[name="ref_room_type_id"]:checked')
            .closest('label')
            .find('.h6')
            .text();

        let courseName = $('input[name="ref_course_id"]:checked')
            .next('label')
            .text()
            .trim();

        $('#summary-room').html(
            summaryRow('รูปแบบห้อง', roomName, true)   // 👈 ตัวหนา
        );

        $('#summary-course').html(
            summaryRow('Time Period', courseName, true) // 👈 ตัวหนา
        );
    }
    function renderProductSummary() {

        let rows = [];

        $('.qty-input').each(function () {

            const input = this; // ✅ ประกาศให้ชัด
            const drinkId = input.name.match(/\[(.*?)\]/)[1];

            let qty = parseInt($(this).val()) || 0;
            if (qty <= 0) return;

            let card = $(this).closest('.card-body');
            let name = card.find('.card-title').text();

            // ราคา/ชิ้น
            let priceText = card.find('.fw-bold').text().replace(/[^\d.]/g, '');
            let price = Number(priceText);

            // ราคารวมต่อสินค้า
            let total = price * qty;

            rows.push(`
                <tr>
                    <td>${name}</td>
                    <td class="text-center">${qty}</td>
                    <td class="text-end">
                        ฿${total.toLocaleString('th-TH', { minimumFractionDigits: 2 })}
                    </td>
                    <td class="text-center">
                        <a href="javascript:;"
                            class="btn btn-xs btn-danger rounded-pill px-2 py-1"
                            onclick="removeItem(${drinkId})">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `);
        });

        $('#summary-drink-list').html(
            rows.length
                ? rows.join('')
                : `<tr>
                        <td colspan="4" class="text-center text-muted">
                            ยังไม่มีสินค้า
                        </td>
                </tr>`
        );
    }
    function removeItem(drinkId) {

        const input = document.querySelector(`input[name="qty[${drinkId}]"]`);

        if (input) {
            input.value = 0;
        }

        calculate();
    }
    // function setRoomCoursePrice() {

    //     let roomId = $('input[name="ref_room_type_id"]:checked').val();
    //     let courseId = $('input[name="ref_course_id"]:checked').val();

    //     let price = 0;

    //     if (roomId == 1 && courseId == 1) price = 1000;
    //     if (roomId == 1 && courseId == 2) price = 2000;
    //     if (roomId == 1 && courseId == 3) price = 1000;

    //     renderRoomCoursePrice(price);
    // }
    // function renderRoomCoursePrice(price) {
    //     $('#summary-price').html(
    //         summaryRow(
    //             'ราคา',
    //             `<span class="fw-bold fs-6">${Number(price).toLocaleString()} ฿</span>`
    //         )
    //     );
    // }
    function renderOptionSummary() {

        let rows = [];

        $('.addon-checkbox:checked').each(function () {

            let name = $(this).data('name');
            let price = Number($(this).data('price'));

            rows.push(`
                <div class="summary-row">
                    <span class="summary-left">${name}</span>
                    <span class="summary-right">฿${price.toLocaleString()}</span>
                </div>
            `);
        });

        $('#summary-option-list').html(
            rows.length
                ? rows.join('')
                : `<div class="text-muted">- ไม่มี Options -</div>`
        );
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

            const price = saleType === '1'
                ? parseFloat(input.dataset.priceStaff)
                : parseFloat(input.dataset.priceCustomer);

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
        document.getElementById('summary-drink-list').innerHTML = html;

        // subtotal
        let subtotalValue = subtotal;

        // รับค่าส่วนลด
        let discount = parseFloat($('#discount-list').val());
        if (isNaN(discount)) discount = 0;

        // คำนวณ total
        let total = subtotalValue - discount;
        if (total < 0) total = 0;

        // format แสดงผล
        const subtotalFormatted = subtotalValue.toLocaleString('th-TH', { minimumFractionDigits: 2 });
        const totalFormatted = total.toLocaleString('th-TH', { minimumFractionDigits: 2 });

        // render
        document.getElementById('discount').innerText = discount.toLocaleString('th-TH', { minimumFractionDigits: 2 });
        document.getElementById('subtotal').innerText = subtotalFormatted;
        document.getElementById('total').innerText = totalFormatted;

        // hidden input
        document.getElementById('total_value').value = total;
    }
</script>

{{-- ================== SCRIPTS ================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
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
        //         url: '{{ route("pos.api.searchSalesStaff") }}',
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
                        customerSelect.innerHTML = '<option value="" disabled selected>-- กรุณาเลือก --</option>';
                        customerSelect.innerHTML += '<option value="walkin">+ Walk-in Customer</option>';
                        data.forEach(c => {
                            customerSelect.innerHTML += `<option value="${c.order_id}" data-customer-id="${c.customer_id}">[Order #${c.order_id}] ${c.name}</option>`;
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
            }, { once: true });
        });

        // --- Flow Control: Walk-in ---
        customerSelect.addEventListener('change', (e) => {
            if (e.target.value === 'walkin') {
                const roomModal = bootstrap.Modal.getInstance(roomModalEl);
                roomModal.hide();
                roomModalEl.addEventListener('hidden.bs.modal', () => {
                    const walkinModal = new bootstrap.Modal(walkinModalEl, { focus: false });
                    walkinModal.show();
                }, { once: true });
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
            }, { once: true });
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
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(u => ({ id: u.id, text: `${u.phone} - ${u.name}` }))
                })
            }
        });

        // $('#walkinStaffSelect').select2({
        //     dropdownParent: $("#walkinModal"),
        //     placeholder: '-- ค้นหาพนักงาน --',
        //     allowClear: true,
        //     ajax: {
        //         url: '{{ route("pos.api.searchStaff") }}',
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
                data: params => ({ q: params.term }),
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
        const drinkGrid = document.getElementById('drinkGrid');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`?q=${this.value}&ajax=true`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => { drinkGrid.innerHTML = html; });
            }, 500);
        });
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        });

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

            fetch('{{ route("pos.api.calculateSummary") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
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
                        itemDiv.className = 'd-flex justify-content-between text-muted small py-1';
                        itemDiv.innerHTML = `
                            <span>${item.name} <small>(${item.details})</small></span>
                            <span>${parseFloat(item.total).toFixed(2)}</span>
                        `;
                        summaryContainer.appendChild(itemDiv);
                    });
                } else {
                    summaryContainer.innerHTML = '<p class="text-center text-muted py-3">ไม่มีรายการ</p>';
                }
                totalContainer.textContent = `THB ${parseFloat(data.total).toFixed(2)}`;
                formTotalPrice.value = data.total;
            })
            .catch(error => {
                summaryContainer.innerHTML = '<p class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>';
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
                if(tooltip) tooltip.enable();
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
            document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove('active'));
            cashInput.value = '';
            paymentMethod.value = '';
            cashAmount.value = '';
            confirmBtn.disabled = true;
        });

        $('#form_staff').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
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
                        if(tooltip) tooltip.disable();
                    });
                    checkNextBtnStatus();
                    document.getElementById("staff").value = response.name;
                    document.getElementById("salesStaffSelect").value = response.id;
                    document.getElementById('staff').blur();

                },
                error: function(error) {
                    document.getElementById("staff").value = "";
                    document.getElementById("salesStaffSelect").value = "";
                    Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    console.error('เกิดข้อผิดพลาด:', error);
                }
            });
        });
        $('#form_user').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
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
                    document.getElementById("staff").value = "";
                    document.getElementById("walkinStaffSelect").value = "";
                    Swal.fire('เกิดข้อผิดพลาด', '', 'error');
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
    @if(session('error'))
    Swal.fire({ icon:'error', title:'Error', text:@json(session('error')), confirmButtonColor:'#5e2a5f' })
    @endif
    @if(session('success'))
    Swal.fire({ icon:'success', title:'Success', text:@json(session('success')), confirmButtonColor:'#5e2a5f' })
    @endif
</script>
