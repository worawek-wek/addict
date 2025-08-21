@extends('pos.layout.app')

@section('content')
<div class="container-fluid">
  <div class="row">

    <!-- Left: Products -->
    <div class="col-md-8">
      <div class="row">
        @foreach($products as $product)
          @php $inStock = $product->total_remain > 0; @endphp

          <div class="col-md-3 mb-4">
            <div class="card h-100 border-0 shadow-sm {{ $inStock ? '' : 'opacity-50' }}">
              <img src="{{ $product->image ?? 'https://via.placeholder.com/150' }}"
                   class="card-img-top"
                   style="height:150px; object-fit:contain;">
              <div class="card-body text-center">
                <h6 class="card-title">{{ $product->name }}</h6>
                <p class="fw-bold text-primary">THB {{ number_format($product->price, 2) }}</p>

                @if($inStock)
                  <form action="{{ route('pos.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark w-100">Add</button>
                  </form>
                @else
                  <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                @endif
              </div>
            </div>
          </div>

        @endforeach
      </div>
    </div>

    <!-- Right: Invoice -->
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
                <img src="{{ $item['image'] }}" width="40" class="me-2">
                <div>
                  <div>{{ $item['name'] }}</div>
                  <small class="text-muted">THB {{ number_format($item['price'], 2) }}</small>
                </div>
              </div>

              {{-- ปุ่ม - / + / ลบ --}}
              <div class="d-flex align-items-center">
                <form action="{{ route('pos.update', $item['id']) }}" method="POST" class="d-flex align-items-center me-2">
                  @csrf
                  <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}" class="btn btn-sm btn-outline-dark">-</button>
                  <span class="mx-2">{{ $item['qty'] }}</span>
                  <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}" class="btn btn-sm btn-outline-dark">+</button>
                </form>

                <form action="{{ route('pos.remove', $item['id']) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          @empty
            <p class="text-muted text-center">No items in cart</p>
          @endforelse
        </div>

        <div class="card-footer bg-white">
          <div class="d-flex justify-content-between"><span>Subtotal</span><span>THB {{ number_format($subtotal,2) }}</span></div>
          <div class="d-flex justify-content-between"><span>Discount</span><span>- THB {{ number_format($discount,2) }}</span></div>
          <div class="d-flex justify-content-between"><span>Tax</span><span>THB {{ number_format($tax,2) }}</span></div>
          <hr>
          <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>THB {{ number_format($total,2) }}</span></div>
          <button class="btn btn-dark w-100 mt-3">Checkout</button>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
