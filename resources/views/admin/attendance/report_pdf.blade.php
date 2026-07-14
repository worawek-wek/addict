<style>
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
    thead th { background-color: #f0f0f0; font-weight: bold; }
    td.name { text-align: left; }
</style>

<div style="text-align:center; font-size:16px; font-weight:bold; margin-bottom:8px;">
    รายงานการเข้างาน
</div>
<div style="font-size:11px; margin-bottom:8px;">
    วันที่ {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }} ,
    พิมพ์เมื่อ {{ date('d/m/Y H:i') }} น.
</div>

<table>
    <thead>
        <tr>
            <th style="width:30px;">#</th>
            <th>วันที่</th>
            <th>ชื่อพนักงาน</th>
            <th>ตำแหน่ง</th>
            <th>สาขา</th>
            <th>เวลาเข้า</th>
            <th>เวลาออก</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $i => $r)
            @php
                $status = ['working' => 'กำลังทำงาน', 'left' => 'ออกงานแล้ว', 'auto_ended' => 'เลิกงาน (ตี 3)'][$r->status] ?? '-';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($r->work_date)->format('d/m/Y') }}</td>
                <td class="name">{{ optional($r->staff)->name }}{{ optional($r->staff)->nickname ? ' (' . $r->staff->nickname . ')' : '' }}</td>
                <td>{{ optional(optional($r->staff)->position)->position_name }}</td>
                <td>{{ optional(optional($r->staff)->branch)->name }}</td>
                <td>{{ $r->check_in_at ? \Carbon\Carbon::parse($r->check_in_at)->format('H:i') : '-' }}</td>
                <td>{{ $r->check_out_at ? \Carbon\Carbon::parse($r->check_out_at)->format('H:i') : '-' }}</td>
                <td>{{ $status }}</td>
            </tr>
        @endforeach
        @if (count($records) === 0)
            <tr><td colspan="8">- ไม่มีข้อมูล -</td></tr>
        @endif
    </tbody>
</table>
