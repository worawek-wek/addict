<table class="datatables-basic table dataTable no-footer dtr-column" id="commission-table-view" aria-describedby="commission-table-view_info">
    <thead class="border-top">
        <tr class="table-info">
            <th class="text-center" style="width: 10px;">#</th>
            <th class="text-center">ชื่อพนักงาน</th>
            <th class="text-center">สาขา</th>
            <th class="text-center">โหมด</th>
            <th class="text-center">ยอดขายสะสม</th>
            <th class="text-center">จำนวนรอบ</th>
            <th class="text-center">Rank</th>
            <th class="text-center">เรต/เกณฑ์</th>
            <th class="text-center">คอมมิชชั่น</th>
        </tr>
    </thead>
    <tbody id="commission-table-body">
        @php $offset = (($list_data->currentPage() - 1) * $list_data->perPage()); @endphp
        @foreach($rows as $i => $row)
            @php
                $staff = $row['staff'];
                $c = $row['c'];
                $isRounds = $c['mode'] === 'rounds';
            @endphp
            <tr>
                <td class="text-center">{{ $offset + $i + 1 }}</td>
                <td class="text-center">{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                <td class="text-center">{{ optional($staff->branch)->name }}</td>
                <td class="text-center">
                    @if($isRounds)
                        <span class="badge bg-label-info">จำนวนรอบ</span>
                    @else
                        <span class="badge bg-label-primary">ยอดขาย %</span>
                    @endif
                </td>
                <td class="text-end" style="padding-right: 4%;">{{ number_format($c['accumulated_sales'], 2) }} บาท</td>
                <td class="text-center">{{ number_format($c['accumulated_rounds']) }}</td>
                <td class="text-center">
                    @if($c['rank_no'] > 0)
                        <span class="badge bg-label-success">Rank {{ $c['rank_no'] }}</span>
                    @else
                        <span class="badge bg-label-secondary">-</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($c['rank_no'] > 0)
                        @if($c['applied_payout_type'] === 'percent')
                            {{ rtrim(rtrim(number_format($c['applied_rate'], 2), '0'), '.') }}%
                        @elseif($c['applied_payout_type'] === 'fixed_per_round')
                            {{ number_format($c['applied_fixed_amount'] ?? 0, 2) }} /รอบ
                        @else
                            {{ number_format($c['applied_fixed_amount'] ?? 0, 2) }} คงที่
                        @endif
                        <div class="text-muted" style="font-size:11px;">
                            ตัดที่ {{ number_format($c['applied_min_threshold'], 2) }}{{ $isRounds ? ' รอบ' : '' }}
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-end" style="padding-right: 6%;">{{ number_format($c['commission_amount'], 2) }} บาท</td>
            </tr>
        @endforeach
    </tbody>
</table>
@include('admin/layout/pagination')
