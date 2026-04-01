<style>
    .payment-wrapper {
        display: grid;
        gap: 10px;
    }

    .payment-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .payment-card input {
        display: none;
    }

    .payment-card .card-content {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
    }

    .payment-card:hover {
        background: #f8f9fa;
    }

    .payment-card input:checked+.card-content {
        font-weight: bold;
        color: #7066e0;
    }

    .payment-card:has(input:checked) {
        border: 2px solid #7066e0;
        background: #eef5ff;
    }
</style>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="text-center">#</th>
            <th class="text-center">คำสั่งซื้อ</th>
            <th class="text-center">สาขา</th>
            <th class="text-center">พนักงานขาย</th>
            <th class="text-center">ยอดรวมสุทธิ</th>
            <th class="text-center">ช่องทางชำระเงิน</th>
            <th class="text-center">จัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orderProducts as $order)
            <tr>
                <td class="text-center">
                    {{ $loop->iteration + ($orderProducts->currentPage() - 1) * $orderProducts->perPage() }}</td>
                <td class="text-center">{{ $order->order_number ?? '-' }}</td>
                <td class="text-center">{{ $order->branch->name ?? '-' }}</td>
                <td class="text-center">{{ $order->seller->nickname ?? '-' }}</td>
                <td class="text-center">{{ $order->total_price }}</td>
                <td class="text-center">
                    @if ($order->payment_status == 3)
                        <span class="badge bg-danger">ยกเลิกคำสั่งซื้อ</span>
                    @elseif($order->payment_status == 0)
                        <span class="badge bg-warning">ยังไม่ชำระเงิน</span>
                    @else
                        <span class="badge bg-success">ชำระเงินแล้ว</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="dropdown">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button"
                            id="actionDropdown{{ $order->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            จัดการ
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionDropdown{{ $order->id }}">
                            <li><a class="dropdown-item" href="#"
                                    onclick="view({{ $order->id }}); return false;">ดู</a></li>
                            @if ($order->payment_status != 3)
                                <li><a class="dropdown-item text-primary" href="#"
                                        onclick="printReceipt({{ $order->id }}); return false;">ปริ้นใบเสร็จ</a>
                                </li>
                            @endif
                            @if ($order->payment_status == 0)
                                <li><a class="dropdown-item text-success" href="#"
                                        onclick="confirmOrder({{ $order->id }}); return false;">ยืนยันชำระเงิน</a>
                                </li>
                                <li><a class="dropdown-item text-warning" href="#"
                                        onclick="editOrder({{ $order->id }}); return false;">แก้ไขคำสั่งซื้อ</a>
                                </li>
                                <li><a class="dropdown-item text-danger" href="#"
                                        onclick="cancelOrder({{ $order->id }}); return false;">ยกเลิกคำสั่งซื้อ</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        @if ($orderProducts->isEmpty())
            <tr>
                <td colspan="10" class="text-center">ไม่มีข้อมูล</td>
            </tr>
        @endif

    </tbody>
</table>

<script>
    if("{{$check}}" == 0){
        $('#ButtonSummaryReport').prop('disabled', true)
    }

    function confirmOrder(orderId) {
        Swal.fire({
            title: 'เลือกช่องทางการชำระเงิน',
            icon: 'question',
            html: `
            <div class="payment-wrapper">
                <label class="payment-card">
                    <input type="radio" name="payment_channel" value="cash" checked>
                    <div class="card-content">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>เงินสด</span>
                    </div>
                </label>

                <label class="payment-card">
                    <input type="radio" name="payment_channel" value="credit_card">
                    <div class="card-content">
                        <i class="fas fa-university"></i>
                        <span>บัตรเครดิต</span>
                    </div>
                </label>

                <label class="payment-card">
                    <input type="radio" name="payment_channel" value="alipay">
                    <div class="card-content">
                        <i class="fas fa-credit-card"></i>
                        <span>Alipay</span>
                    </div>
                </label>

                <label class="payment-card">
                    <input type="radio" name="payment_channel" value="qr_code">
                    <div class="card-content">
                        <i class="fas fa-qrcode"></i>
                        <span>QR Code</span>
                    </div>
                </label>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'ยืนยันการชำระเงิน',
            cancelButtonText: 'ยกเลิก',
            focusConfirm: false,
            preConfirm: () => {
                const selected = document.querySelector('input[name="payment_channel"]:checked');
                if (!selected) {
                    Swal.showValidationMessage('กรุณาเลือกช่องทางการชำระเงิน');
                    return false;
                }
                return selected.value;
            }
        }).then((result) => {
            if (result.isConfirmed) {

                fetch(`/admin/order-products/${orderId}/confirm-payment`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status_id: 1,
                            payment_channel: result.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', 'ชำระเงินเรียบร้อย', 'success')
                                .then(() => {
                                    loadData(page);
                                    printReceipt(orderId);
                                });
                        }
                    });
            }
        });
    }

    function cancelOrder(orderId) {
        Swal.fire({
            title: 'ยืนยันการยกเลิกคำสั่งซื้อ?',
            text: 'คุณต้องการยกเลิกคำสั่งซื้อนี้หรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ยกเลิกคำสั่งซื้อ',
            cancelButtonText: 'ไม่ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/order-products/${orderId}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status_id: 3,
                            ref_status_id: 4
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('สำเร็จ!', 'ยกเลิกคำสั่งซื้อเรียบร้อย', 'success');
                            loadData(page)
                        } else {
                            Swal.fire('ผิดพลาด!', data.message || 'ไม่สามารถยกเลิกคำสั่งซื้อได้', 'error');
                        }
                    });
            }
        });
    }


    const editOrder = (orderId) => {
        window.location.href = `/admin/order-products/edit/${orderId}`;
    }

    function printReceipt(orderId) {
        // remove any previous iframe
        const old = document.getElementById('slip-print-frame');
        if (old) old.remove();

        const iframe = document.createElement('iframe');
        iframe.id  = 'slip-print-frame';
        iframe.src = `/admin/order-products/${orderId}/slip`;
        iframe.style.cssText = 'position:fixed;top:0;left:0;width:0;height:0;border:none;visibility:hidden;';
        document.body.appendChild(iframe);

        iframe.onload = function () {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }
</script>
</table>

{{-- Pagination --}}
<div class="mt-3">
    {!! $orderProducts->links('vendor.pagination.custom') !!}
</div>
