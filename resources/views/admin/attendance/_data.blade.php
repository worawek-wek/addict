@php
    // ป้ายสถานะ
    $statusBadge = function ($att) {
        if (!$att) return '<span class="badge bg-label-secondary">ยังไม่เข้างาน</span>';
        return [
            'working' => '<span class="badge bg-label-success">กำลังทำงาน</span>',
            'left' => '<span class="badge bg-label-secondary">ออกงานแล้ว</span>',
            'auto_ended' => '<span class="badge bg-label-warning">เลิกงาน (ตี 3)</span>',
        ][$att->status] ?? '<span class="badge bg-label-secondary">-</span>';
    };
    $fmt = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('H:i') : '-';
@endphp

<ul class="nav nav-pills flex-wrap mb-3">
    @foreach ($byPosition as $posName => $staffs)
        @php
            $working = $staffs->filter(fn($u) => optional($attendance->get($u->id))->status === 'working')->count();
        @endphp
        <li class="nav-item mb-1">
            <span class="nav-link att-tab {{ $loop->first ? 'active' : '' }}" data-key="{{ $loop->index }}">
                {{ $posName }}
                <span class="badge rounded-pill bg-success ms-1">{{ $working }}</span>
                <span class="text-muted">/ {{ count($staffs) }}</span>
            </span>
        </li>
    @endforeach
</ul>

@foreach ($byPosition as $posName => $staffs)
    <div class="att-pane {{ $loop->first ? 'active' : '' }}" data-key="{{ $loop->index }}">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="table-info">
                        <th class="text-center" style="width:60px;">#</th>
                        <th>ชื่อพนักงาน</th>
                        <th class="text-center">สาขา</th>
                        <th class="text-center">เวลาเข้า</th>
                        <th class="text-center">เวลาออก</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffs as $i => $u)
                        @php $att = $attendance->get($u->id); @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $u->name }}{{ $u->nickname ? ' (' . $u->nickname . ')' : '' }}</td>
                            <td class="text-center">{{ optional($u->branch)->name }}</td>
                            <td class="text-center">{{ $fmt(optional($att)->check_in_at) }}</td>
                            <td class="text-center">{{ $fmt(optional($att)->check_out_at) }}</td>
                            <td class="text-center">{!! $statusBadge($att) !!}</td>
                        </tr>
                    @endforeach
                    @if (count($staffs) === 0)
                        <tr><td colspan="6" class="text-center text-muted">- ไม่มีพนักงาน -</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endforeach

@if ($byPosition->isEmpty())
    <div class="text-center text-muted py-5">- ไม่มีข้อมูลพนักงาน -</div>
@endif
