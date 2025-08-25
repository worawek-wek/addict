@extends('pos.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-md-8">

            <div class="row mb-3">
                <div class="col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" value="{{ request('q') }}" class="form-control"
                            placeholder="Search product name..." autocomplete="off">
                        <button id="clearSearch" class="btn btn-outline-secondary" type="button">Clear</button>
                    </div>
                </div>
            </div>

            <div class="row" id="productGrid">
                @include('pos.partials.product-grid', ['products' => $products])
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Invoice</span>
                    <span class="text-muted">#0001</span>
                </div>

                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
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
                                {{-- ปุ่มลด --}}
                                <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}"
                                        class="btn btn-sm btn-outline-dark">-</button>
                                </form>

                                {{-- ช่องจำนวน --}}
                                <form action="{{ route('pos.update', $item['id']) }}" method="POST" class="mx-1">
                                    @csrf
                                    <input type="number" name="qty"
                                        class="form-control form-control-sm text-center qty-input"
                                        value="{{ $item['qty'] }}" min="0" max="{{ $item['stock'] ?? 999 }}"
                                        step="1" style="width:72px">
                                </form>

                                {{-- ปุ่มเพิ่ม --}}
                                <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}"
                                        class="btn btn-sm btn-outline-dark">+</button>
                                </form>

                                {{-- ปุ่มลบ --}}
                                <form action="{{ route('pos.remove', $item['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No items in cart</p>
                    @endforelse
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>THB {{ number_format($subtotal, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><span>- THB {{ number_format($discount, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Tax</span><span>THB {{ number_format($tax, 2) }}</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>THB {{ number_format($total, 2) }}</span></div>

                    <button class="btn btn-dark w-100 mt-3" data-bs-toggle="modal" data-bs-target="#checkoutRoomModal">
                        Checkout
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

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
                    <select id="salesStaffSelect" class="form-select"></select>
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
                        <select id="walkinStaffSelect" class="form-select" style="width: 100%"></select>
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
                <h5 class="modal-title">Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('pos.checkout') }}">
                @csrf
                <input type="hidden" name="room_id" id="formRoomId">
                <input type="hidden" name="order_id" id="formOrderId">
                <input type="hidden" name="payment_method" id="paymentMethod">
                <input type="hidden" name="cash_amount" id="cashAmount">
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
                        <label class="form-label fw-bold">Cash</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="cash-btn btn btn-outline-secondary">THB 20.00</button>
                            <button type="button" class="cash-btn btn btn-outline-secondary">THB 50.00</button>
                            <button type="button" class="cash-btn btn btn-outline-secondary">THB 100.00</button>
                        </div>
                        <input type="number" id="cashInput" class="form-control text-center fw-bold"
                            placeholder="Enter cash amount">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Other</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary other-btn" data-method="qr">
                                <i class="bi bi-qr-code me-1"></i> QR Code
                            </button>
                            <button type="button" class="btn btn-outline-secondary other-btn" data-method="card">
                                <i class="bi bi-credit-card me-1"></i> Credit Card
                            </button>
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
        };

        const checkWalkinNextBtnStatus = () => {
            const isStaffSelected = $('#walkinStaffSelect').val();
            const isTimeSelected = walkinTimeSelect.value;
            walkinNextBtn.disabled = !(isStaffSelected && isTimeSelected);
        };

        // --- Initialize Sales Staff Select2 ---
        $('#salesStaffSelect').select2({
            dropdownParent: $("#checkoutRoomModal"),
            placeholder: '-- เลือกพนักงานขาย --',
            allowClear: true,
            ajax: {
                url: '{{ route("pos.api.searchSalesStaff") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            }
        }).on('select2:select', e => {
            tempMamaId = e.params.data.id;
            document.querySelectorAll('.room-chip').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('room-chip-disabled');
                const tooltip = bootstrap.Tooltip.getInstance(btn);
                if(tooltip) tooltip.disable();
            });
            checkNextBtnStatus();
        }).on('select2:clear', () => {
            tempMamaId = null;
        });

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

        $('#walkinStaffSelect').select2({
            dropdownParent: $("#walkinModal"),
            placeholder: '-- ค้นหาพนักงาน --',
            allowClear: true,
            ajax: {
                url: '{{ route("pos.api.searchStaff") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({
                    results: data.map(u => ({
                        id: u.id,
                        text: `${u.nickname ?? ''} | Salary: ${u.salary ?? 0}฿`
                    }))
                })
            }
        }).on('select2:select', checkWalkinNextBtnStatus)
          .on('select2:clear', checkWalkinNextBtnStatus);

        walkinTimeSelect.addEventListener('change', checkWalkinNextBtnStatus);

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
        const cashAmount = document.getElementById('cashAmount');
        const cashInput = document.getElementById('cashInput');
        document.querySelectorAll('.cash-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const val = btn.textContent.replace('THB','').trim();
                cashInput.value = val;
                paymentMethod.value = 'cash';
                cashAmount.value = val;
                confirmBtn.disabled = false;
            });
        });

        cashInput.addEventListener('input', () => {
            document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove('active'));
            if (cashInput.value) {
                paymentMethod.value = 'cash';
                cashAmount.value = cashInput.value;
                confirmBtn.disabled = false;
            } else {
                confirmBtn.disabled = true;
            }
        });

        document.querySelectorAll('.other-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                paymentMethod.value = btn.dataset.method;
                cashAmount.value = '';
                cashInput.value = '';
                confirmBtn.disabled = false;
            });
        });

        // --- Cart & Search Logic ---
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() { this.closest('form').submit(); });
        });

        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const productGrid = document.getElementById('productGrid');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`?q=${this.value}&ajax=true`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => { productGrid.innerHTML = html; });
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
    });
</script>

<script>
    @if(session('error'))
    Swal.fire({ icon:'error', title:'Error', text:@json(session('error')), confirmButtonColor:'#5e2a5f' })
    @endif
    @if(session('success'))
    Swal.fire({ icon:'success', title:'Success', text:@json(session('success')), confirmButtonColor:'#5e2a5f' })
    @endif
</script>
@endsection
