<style>
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
</style>
<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;รายละเอียด คอมมิชชั่น รอบ วันที่ &nbsp;{{-- date('d/m/Y H:i', strtotime($list_data[0]->date_time)) --}} &nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-5" style="padding: 1em 3em;">

        <div class="col-md-12" style="padding-right: unset !important;">
            <table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
                <thead class="border-top">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">คำสั่งซื้อ</th>
                        <th class="text-center">สาขา</th>
                        <th class="text-center">พนักงานขาย</th>
                        <th class="text-center">ยอดรวมสุทธิ</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody id="commission-table-body">
                    @foreach($list_data as $i => $order)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer rounded-0 justify-content-center">
        <button
            class="btn btn-secondary add-new btn-primary me-2 ms-sm-0 waves-effect waves-light"
            type="button"
            onclick="printPdf()">
            <span>
                <i class="ti ti-file-upload me-0 me-sm-1"></i>
                <span class="d-none d-sm-inline-block">พิมพ์
                </span>
            </span>
        </button>
    </div>
</div>