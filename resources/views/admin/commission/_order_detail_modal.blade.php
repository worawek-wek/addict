<div>
    <h5>รายละเอียด Order #{{ $order->order_number }}</h5>
    <table class="table table-bordered mb-3">
        <tr>
            <th>วันที่จอง</th>
            <td>{{ $order->booking_date }}</td>
        </tr>
        <tr>
            <th>ลูกค้า</th>
            <td>{{ $order->customer ? $order->customer->name : '-' }}</td>
        </tr>
        <tr>
            <th>สาขา</th>
            <td>{{ $order->branch ? $order->branch->name : '-' }}</td>
        </tr>
        <tr>
            <th>พนักงานนวด</th>
            <td>{{ $order->user ? $order->user->name : '-' }}</td>
        </tr>
        <tr>
            <th>ยอดขาย</th>
            <td>{{ number_format($order->total_price, 2) }} บาท</td>
        </tr>
    </table>
    <h6 class="mt-3">Addon Options</h6>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>ชื่อ Addon</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->addons as $addon)
            <tr>
                <td>{{ $addon->option ? $addon->option->name : '-' }}</td>
                <td>{{ $addon->option ? $addon->option->price : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="2" class="text-center">ไม่มีข้อมูล</td></tr>
            @endforelse
        </tbody>
    </table>
    <h6 class="mt-3">Products</h6>
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->products as $product)
            <tr>
                <td>{{ $product->product ? $product->product->name : '-' }}</td>
                <td>{{ $product->quantity ?? '-' }}</td>
                <td>{{ number_format($product->price ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center">ไม่มีข้อมูล</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
