<style>
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
    thead th { background-color: #f0f0f0; font-weight: bold; }
    td.num { text-align: right; }
</style>

<div style="text-align:center; font-size:16px; font-weight:bold; margin-bottom:10px;">
    รายงานค่าคอม (ดื่ม)
</div>
<div style="font-size:11px; margin-bottom:8px;">
    วันที่ {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }} , พิมพ์เมื่อ {{ date('d/m/Y H:i') }} น.
</div>

<table>
    <thead>
        <tr>
            <th style="width: 24px;">#</th>
            <th>ชื่อพนักงาน</th>
            <th>สาขา</th>
            <th>โหมด</th>
            <th>ยอดขายดื่มสะสม</th>
            <th>จำนวนรอบ</th>
            <th>Rank</th>
            <th>เรต/เกณฑ์</th>
            <th>คอมมิชชั่น</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            @php $staff = $row['staff']; $c = $row['c']; $isRounds = $c['mode'] === 'rounds'; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                <td>{{ optional($staff->branch)->name }}</td>
                <td>{{ $isRounds ? 'จำนวนรอบ' : 'ยอดขาย %' }}</td>
                <td class="num">{{ number_format($c['accumulated_sales'], 2) }}</td>
                <td>{{ number_format($c['accumulated_rounds']) }}</td>
                <td>{{ $c['rank_no'] > 0 ? 'Rank '.$c['rank_no'] : '-' }}</td>
                <td>
                    @if($c['rank_no'] > 0)
                        @if($c['applied_payout_type'] === 'percent')
                            {{ rtrim(rtrim(number_format($c['applied_rate'], 2), '0'), '.') }}%
                        @elseif($c['applied_payout_type'] === 'fixed_per_round')
                            {{ number_format($c['applied_fixed_amount'] ?? 0, 2) }}/รอบ
                        @else
                            {{ number_format($c['applied_fixed_amount'] ?? 0, 2) }} คงที่
                        @endif
                        ({{ number_format($c['applied_min_threshold'], 2) }}{{ $isRounds ? ' รอบ' : '' }})
                    @else
                        -
                    @endif
                </td>
                <td class="num">{{ number_format($c['commission_amount'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
