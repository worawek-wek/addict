<div class="modal-header rounded-0">
    <span class="modal-title">
        <span class="h5" style="color: white;">ห้อง {{ $invoice->room_for_rent->room->name }}</span>
        <span class="ms-2">
            {{-- {{ date('m/Y', strtotime($invoice->month.' '.$invoice->year)) }} --}}
            @php
            $monthNames = [
                            '1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน',
                            '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม',
                            '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                        ];
                        echo $monthNames[$invoice->month].' '.$invoice->year;
            @endphp

        </span>
        <span class="ms-2" style="border: 1px solid #69c2c1;padding: 7px 12px;border-radius: 5px;font-size: smaller;">{{ $invoice->invoice_number }}</span>
    </span>
    <span class="badge bg-label-{{ $invoice->status->color }} m-auto" text-capitalized="" style="font-size: unset;" >
        <span class="ti-md ti {{ $invoice->status->icon }} me-2"></span>{{ $invoice->status->name }}
    </span>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="mb-3" style="border: 1px solid #dbdade;padding: 15px 2px;">
        <div class="d-flex">
            <div class="flex-grow-1 ms-3">
            <b class="text-black">รายละเอียดหัวบิล</b> <br>
                {{ $invoice->room_for_rent->renter->prefix.' '.$invoice->room_for_rent->renter->name.' '.$invoice->room_for_rent->renter->surname }} <br>
                เลขประจำตัวผู้เสียภาษี {{ $invoice->room_for_rent->renter->id_card_number }} <br>
                โทร {{ $invoice->room_for_rent->renter->phone }}
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>รายการ</th>
                <th>จำนวนเงิน (บาท)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ค่าเช่าห้อง (Room rate) {{ $invoice->room_for_rent->room->name }} เดือน {{ $invoice->month.'/'.$invoice->year }}</td>
                <td class="text-end">{{ number_format($invoice->room_for_rent->room->rent) }}</td>
            </tr>
            <tr>
                <td>ค่าน้ำ (Water rate) เดือน {{ $invoice->month.'/'.$invoice->year }} ( 180 - {{ $invoice->water_unit }} = {{ $invoice->water_unit-180 }} ยูนิต)</td>
                <td class="text-end">{{ $invoice->water_amount }}</td>
            </tr>
            <tr>
                <td>ค่าไฟฟ้า (Electrical rate) เดือน {{ $invoice->month.'/'.$invoice->year }} ( 7194 - {{ $invoice->electricity_unit }} = 98 ยูนิต)</td>
                <td class="text-end">{{ $invoice->electricity_amount }}</td>
            </tr>
            @foreach ($expenses as $expen)
            <tr>
                @if ($expen->status == 1)
                    <td>{{ $expen->title }}</td>
                    <td class="text-end">{{ number_format($expen->amount) }}</td>
                @else
                    <td style="color: red">{{ $expen->title }}</td>
                    <td class="text-end" style="color: red">- {{ number_format($expen->amount) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>รวม</th>
                <th class="text-end mb-0 fw-bold">
                    {{ number_format($invoice->room_for_rent->room->rent + $invoice->water_amount+$invoice->electricity_amount+($expenses[0]->amount-$expenses[1]->amount)) }}
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="modal-footer rounded-0 justify-content-start">
    <button type="button" class="btn btn-label-primary waves-effect"><span
            class="ti-md ti ti-printer me-2"></span>พิมพ์ใบแจ้งหนี้</button>
            
    {{-- <button type="button" class="btn btn-label-secondary waves-effect"><span
        class="ti-md ti ti-pencil"></span></button> --}}
    @if ($invoice->ref_status_id == 1)
        <button type="button" class="btn btn-label-{{ $invoice->status->color }} waves-effect ms-auto" onclick="changeStatusBill({{ $invoice->id }},2,'คอนเฟิร์มบิล')">
            <span class="ti-md ti {{ $invoice->status->icon }} me-2"></span>คอนเฟิร์มบิล
        </button>
    @elseif($invoice->ref_status_id == 2)
        {{-- <button type="button" class="btn btn-label-{{ $invoice->status->color }} waves-effect ms-auto" onclick="changeStatusBill({{ $invoice->id }},5,'ชำระเงิน')">
            <span class="ti-md ti ti-report-money me-2"></span>ชำระเงิน
        </button> --}}
    @endif
</div>