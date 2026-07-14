@php
    $rankBadge = function ($no) {
        return $no > 0
            ? '<span class="badge bg-label-success">Rank ' . $no . '</span>'
            : '<span class="badge bg-label-secondary">-</span>';
    };
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="tile" style="background:#6f42c1;">
            <div class="l">จำนวนมาม่า</div>
            <div class="n">{{ number_format($summary['count']) }} คน</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="tile" style="background:#0d6efd;">
            <div class="l">รวมคอม นวด+สินค้า</div>
            <div class="n">{{ number_format($summary['service'], 2) }} ฿</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="tile" style="background:#20c997;">
            <div class="l">รวมคอม ดื่ม</div>
            <div class="n">{{ number_format($summary['drink'], 2) }} ฿</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="tile" style="background:#fd7e14;">
            <div class="l">รวมคอมที่ต้องจ่าย</div>
            <div class="n">{{ number_format($summary['total'], 2) }} ฿</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-2">
        <small class="text-muted">
            ช่วง {{ \Carbon\Carbon::parse($start)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($end)->format('d/m/Y H:i') }}
            ({{ $period === 'day' ? 'รายวัน' : 'รายเดือน' }})
        </small>
    </div>
    <div class="card-body px-0 pt-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0 align-middle">
                <thead>
                    <tr class="table-info text-center">
                        <th rowspan="2" style="width:50px;">#</th>
                        <th rowspan="2">ชื่อพนักงาน</th>
                        <th rowspan="2">สาขา</th>
                        <th colspan="4" style="background:#e7f1ff;">นวด + สินค้า</th>
                        <th colspan="4" style="background:#e6f9f2;">ดื่ม</th>
                        <th rowspan="2">รวมคอม</th>
                    </tr>
                    <tr class="table-info text-center">
                        <th style="background:#e7f1ff;">ยอดสะสม</th>
                        <th style="background:#e7f1ff;">รอบ</th>
                        <th style="background:#e7f1ff;">Rank</th>
                        <th style="background:#e7f1ff;">คอม</th>
                        <th style="background:#e6f9f2;">ยอดสะสม</th>
                        <th style="background:#e6f9f2;">รอบ</th>
                        <th style="background:#e6f9f2;">Rank</th>
                        <th style="background:#e6f9f2;">คอม</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        @php $s = $row['service']; $d = $row['drink']; $staff = $row['staff']; @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $staff->name }}{{ $staff->nickname ? ' (' . $staff->nickname . ')' : '' }}</td>
                            <td class="text-center">{{ optional($staff->branch)->name }}</td>

                            <td class="text-end">{{ number_format($s['accumulated_sales'], 2) }}</td>
                            <td class="text-center">{{ number_format($s['accumulated_rounds']) }}</td>
                            <td class="text-center">{!! $rankBadge($s['rank_no']) !!}</td>
                            <td class="text-end">{{ number_format($s['commission_amount'], 2) }}</td>

                            <td class="text-end">{{ number_format($d['accumulated_sales'], 2) }}</td>
                            <td class="text-center">{{ number_format($d['accumulated_rounds']) }}</td>
                            <td class="text-center">{!! $rankBadge($d['rank_no']) !!}</td>
                            <td class="text-end">{{ number_format($d['commission_amount'], 2) }}</td>

                            <td class="text-end fw-bold">{{ number_format($row['total'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if ($rows->isEmpty())
                        <tr><td colspan="12" class="text-center text-muted">- ไม่มีข้อมูล -</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="6" class="text-end">รวมทั้งหมด</td>
                        <td class="text-end">{{ number_format($summary['service'], 2) }}</td>
                        <td colspan="3"></td>
                        <td class="text-end">{{ number_format($summary['drink'], 2) }}</td>
                        <td class="text-end">{{ number_format($summary['total'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
