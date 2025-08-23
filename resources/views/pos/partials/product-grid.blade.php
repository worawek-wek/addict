@forelse($products as $product)
  @php
    $totalRemain = $product->total_remain ?? ($product->latestStock->total_remain ?? 0);
    $inStock = $totalRemain > 0;
  @endphp

  <div class="col-md-3 mb-4">
    <div class="card h-100 border-0 shadow-sm {{ $inStock ? '' : 'opacity-50' }}">

      {{-- ใช้ Bootstrap Icon (อาหาร/เครื่องดื่ม) แทนรูปภาพ --}}
      <div class="d-flex justify-content-center align-items-center py-4" style="height:150px;">
        <i class="bi bi-cup-straw" style="font-size:3rem; color:#5e2a5f;"></i>
      </div>

      <div class="card-body text-center">
        <h6 class="card-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
        <p class="fw-bold text-primary mb-2">THB {{ number_format($product->price, 2) }}</p>
        <div class="small text-muted mb-2">Stock: {{ $totalRemain }}</div>

        @if($inStock)
          <form action="{{ route('pos.add', $product->id) }}" method="POST">
            @csrf
            <input type="hidden" name="q" value="{{ request('q') }}">
            <button type="submit" class="btn btn-dark w-100">Add</button>
          </form>
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
        No products found
        @if(request('q')) for "<strong>{{ request('q') }}</strong>" @endif
      </div>
    </div>
  </div>
@endforelse
