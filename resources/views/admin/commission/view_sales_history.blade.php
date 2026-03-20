<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5">&nbsp;รายละเอียด คอมมิชชั่น รอบ วันที่ {{ $list_data[0]->from_date.' - '.$list_data[0]->to_date }} &nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">

        <div class="col-md-12" style="padding-right: unset !important;">
            <table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
                <thead class="border-top">
                    <tr class="table-info">
                        <th class="text-center" style="width: 10px;">#</th>
                        <th class="text-center">ชื่อพนักงาน</th>
                        <th class="text-center">จำนวนเงินคอมมิชชั่น</th>
                        <th class="text-center">สาขา</th>
                        <th class="text-center">ชื่อตำแหน่ง</th>
                        {{-- <th class="text-center">ค่าเชียร์</th> --}}
                        {{-- <th class="text-center">ดู Order</th> --}}
                    </tr>
                </thead>
                <tbody id="commission-table-body">
                    @foreach($list_data as $i => $staff)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                        <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                        <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                        <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>