@extends('pos.layout.app')

@section('content')
<div class="container-fluid">
  <div class="row">

    <!-- ================= LEFT : PRODUCTS ================= -->
    <div class="col-md-8">

      <!-- Search Bar -->
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

      <!-- Product Grid -->
      <div class="row" id="productGrid">
        @include('pos.partials.product-grid', ['products' => $products])
      </div>
    </div>

    <!-- ================= RIGHT : INVOICE ================= -->
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
        {{-- ========= เลือกห้อง ========= --}}
        <div class="mb-3">
          <label class="form-label fw-bold">Choose a room</label>
          <div class="store-pill mb-2">{{ $storefrontName ?? 'Cashier' }}</div>

          @foreach ($roomGroups ?? [] as $group)
            <div class="fw-bold mb-2">{{ $group['name'] }}</div>
            <div class="mb-3 d-flex flex-wrap gap-2">
              @foreach ($group['rooms'] as $room)
                <button type="button" class="room-chip btn btn-outline-secondary"
                        data-room-id="{{ $room['id'] }}">{{ $room['label'] }}</button>
              @endforeach
            </div>
          @endforeach

          <input type="hidden" id="selectedRoomId">
        </div>

        {{-- ========= Customers in room ========= --}}
        <div class="mb-3">
          <label class="form-label fw-bold">Customers in this room</label>
          <select id="customerSelect" class="form-select" disabled>
            <option value="">-- Select a room first --</option>
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

{{-- ================== MODAL #2 : เลือก Payment Method ================== --}}
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

        <div class="modal-body">
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
<style>
  .room-chip.active,
  .cash-btn.active,
  .other-btn.active {
    background-color: #5e2a5f;
    color: #fff;
  }
</style>

{{-- ================== SCRIPTS ================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ------- Auto submit qty input -------
  document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('blur', function() {
      const form = this.closest('form');
      if (form) form.submit();
    });
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        const form = this.closest('form');
        if (form) form.submit();
      }
    });
  });

  // ------- AJAX Live Search -------
  const input = document.getElementById('searchInput');
  const clearBtn = document.getElementById('clearSearch');
  const grid = document.getElementById('productGrid');
  const baseUrl = @json(route('pos.index'));
  let timer = null;

  function fetchProducts(q) {
    const url = new URL(baseUrl);
    if (q) url.searchParams.set('q', q);
    url.searchParams.set('ajax', '1');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(res => res.text())
      .then(html => { grid.innerHTML = html; })
      .catch(console.error);
  }

  if (input) {
    input.addEventListener('input', function () {
      clearTimeout(timer);
      const q = this.value.trim();
      timer = setTimeout(() => fetchProducts(q), 500);
    });
    input.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
  }
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (input) input.value = '';
      fetchProducts('');
    });
  }

  // ------- Room selection + Load customers -------
  const roomBtns = document.querySelectorAll('.room-chip');
  const roomIdInput = document.getElementById('selectedRoomId');
  const customerSelect = document.getElementById('customerSelect');
  const nextBtn = document.getElementById('nextToPaymentBtn');

  const formRoomId = document.getElementById('formRoomId');
  const formOrderId = document.getElementById('formOrderId');
  const confirmBtn = document.getElementById('confirmBtn');

  roomBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      roomBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      roomIdInput.value = btn.dataset.roomId;

      fetch(`/pos/room/${btn.dataset.roomId}/customers`)
        .then(res => res.json())
        .then(data => {
          customerSelect.innerHTML = '';
          if (data.length === 0) {
            customerSelect.innerHTML = '<option value="">No active customers</option>';
            customerSelect.disabled = true;
            nextBtn.disabled = true;
          } else {
            data.forEach(c => {
              customerSelect.innerHTML += `
                <option value="${c.order_id}">[Order #${c.order_id}] ${c.name}</option>`;
            });
            customerSelect.disabled = false;
            nextBtn.disabled = false;
          }
        });
    });
  });

  // Next → เปิด modal payment
  nextBtn.addEventListener('click', () => {
    formRoomId.value = roomIdInput.value;
    formOrderId.value = customerSelect.value;

    const roomModal = bootstrap.Modal.getInstance(document.getElementById('checkoutRoomModal'));
    roomModal.hide();
    new bootstrap.Modal(document.getElementById('checkoutPaymentModal')).show();
  });

  // ------- Payment selection -------
  const paymentMethod = document.getElementById('paymentMethod');
  const cashAmount = document.getElementById('cashAmount');
  const cashInput = document.getElementById('cashInput');
  const cashBtns = document.querySelectorAll('.cash-btn');
  const otherBtns = document.querySelectorAll('.other-btn');

  cashBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      cashBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const val = btn.textContent.replace('THB', '').trim();
      cashInput.value = val;
      paymentMethod.value = 'cash';
      cashAmount.value = val;
      confirmBtn.disabled = false;
    });
  });

  cashInput.addEventListener('input', () => {
    if (cashInput.value) {
      paymentMethod.value = 'cash';
      cashAmount.value = cashInput.value;
      confirmBtn.disabled = false;
    }
  });

  otherBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      otherBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      paymentMethod.value = btn.dataset.method;
      cashAmount.value = '';
      confirmBtn.disabled = false;
    });
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('error'))
Swal.fire({
  icon: 'error',
  title: 'Error',
  text: @json(session('error')),
  confirmButtonColor: '#5e2a5f'
})
@endif

@if(session('success'))
Swal.fire({
  icon: 'success',
  title: 'Success',
  text: @json(session('success')),
  confirmButtonColor: '#5e2a5f'
})
@endif
</script>
@endsection
