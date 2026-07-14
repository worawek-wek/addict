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
            <span class="h5" style="color: white;">&nbsp;รายละเอียด คอมมิชชั่น รอบ วันที่ &nbsp;{{ date('d/m/Y', strtotime($list_data[0]->from_date)).' - '.date('d/m/Y', strtotime($list_data[0]->to_date)) }} &nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-5" style="padding: 1em 3em;">

        <div class="col-md-12" style="padding-right: unset !important;">
            <table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
                <thead class="border-top">
                    <tr class="table-info">
                        <th class="text-center" style="width: 10px;">#</th>
                        <th class="text-center">ชื่อพนักงาน</th>
                        <th class="text-center">โหมด</th>
                        <th class="text-center">ยอดขาย</th>
                        <th class="text-center">จำนวนรอบ</th>
                        <th class="text-center">Rank</th>
                        <th class="text-center">เรต/เกณฑ์</th>
                        <th class="text-center">คอมมิชชั่นที่ได้รับ</th>
                    </tr>
                </thead>
                <tbody id="commission-table-body">
                    @foreach($list_data as $i => $staff)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $staff->user->name ?? '-' }}{{ optional($staff->user)->nickname ? ' (' . $staff->user->nickname . ')' : '' }}</td>
                        <td class="text-center">{{ ($staff->mode ?? null) === 'rounds' ? 'จำนวนรอบ' : 'ยอดขาย %' }}</td>
                        <td class="text-end" style="padding-right:6%;">{{ number_format($staff->sales_received, 2) }}</td>
                        <td class="text-center">{{ $staff->accumulated_rounds ?? '-' }}</td>
                        <td class="text-center">{{ ($staff->rank_no ?? 0) > 0 ? 'Rank ' . $staff->rank_no : '-' }}</td>
                        <td class="text-center">{{ $staff->commission_rate }}</td>
                        <td class="text-end" style="padding-right:6%;">{{ number_format($staff->commission, 2) }} บาท</td>
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