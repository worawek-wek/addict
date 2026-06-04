@forelse($products as $product)
    @php
        $totalRemain = \App\Models\StockReadyForSale::where('ref_product_id', $product->id)->sum('remain') ?? 0;
        $inStock = $totalRemain > 0;
    @endphp
    <div class="col">
        <div class="card border-0 shadow-sm pos-product-card {{ $inStock ? '' : 'opacity-50' }}">
            <div class="d-flex justify-content-center align-items-center py-4 pos-product-icon">
                <i class="bi bi-cup-straw" style="font-size:3rem; color:#5e2a5f;"></i>
            </div>
            <div class="card-body text-center">
                <h6 class="card-title text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                <p class="fw-bold text-primary product-price mb-2">THB {{ number_format($product->price, 2) }}</p>
                <div class="small text-muted mb-2 product-stock">Stock: {{ $totalRemain }}</div>

                @if($inStock)
                    <div class="input-group input-group-sm justify-content-center">
                        <button class="btn btn-outline-secondary qty-minus course-product-control"
                                type="button"
                                data-max="{{ $totalRemain }}"
                                disabled>-</button>

                        <input type="number"
                            class="form-control text-center qty-input calculate course-product-control"
                            name="qty[{{ $product->id }}]"
                            value="0"
                            min="0"
                            max="{{ $totalRemain }}"
                            style="max-width:60px;"
                            onchange="calculate()"
                            disabled>

                        <button class="btn btn-outline-secondary qty-plus course-product-control"
                                type="button"
                                data-max="{{ $totalRemain }}"
                                disabled>+</button>
                    </div>
                @else
                    <button class="btn btn-secondary w-100 btn-sm btn-out-of-stock" disabled>Out of Stock</button>
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
                @if (request('q'))
                    for "<strong>{{ request('q') }}</strong>"
                @endif
            </div>
        </div>
    </div>
@endforelse
