<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>รายงานการเข้างาน - CRM</title>
</head>
<style>
    .table th { font-size: 14px; font-weight: bold; }
</style>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="ti ti-report"></i> รายงานการเข้างาน</h5>
                                <a href="{{ url('admin/attendance') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-arrow-back-up"></i> กลับหน้ารายชื่อ
                                </a>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ url('admin/attendance/report') }}" class="row g-3 align-items-end mb-4">
                                    <div class="col-auto">
                                        <label class="form-label">ตั้งแต่วันที่</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">ถึงวันที่</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                                    </div>
                                    @if (auth()->id() === 1)
                                        <div class="col-auto">
                                            <label class="form-label">สาขา</label>
                                            <select name="ref_branch_id" class="form-select">
                                                <option value="">ทุกสาขา</option>
                                                @foreach ($branches as $b)
                                                    <option value="{{ $b->id }}" {{ (string) request('ref_branch_id') === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-main"><i class="ti ti-search"></i> ดูรายงาน</button>
                                        <a href="{{ url('admin/attendance/report/pdf') }}?{{ http_build_query(request()->only(['start_date','end_date','ref_branch_id'])) }}"
                                           target="_blank" class="btn btn-primary">
                                            <i class="ti ti-file-type-pdf"></i> พิมพ์ PDF
                                        </a>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="table-info">
                                                <th class="text-center" style="width:60px;">#</th>
                                                <th class="text-center">วันที่</th>
                                                <th>ชื่อพนักงาน</th>
                                                <th class="text-center">ตำแหน่ง</th>
                                                <th class="text-center">สาขา</th>
                                                <th class="text-center">เวลาเข้า</th>
                                                <th class="text-center">เวลาออก</th>
                                                <th class="text-center">สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($records as $i => $r)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($r->work_date)->format('d/m/Y') }}</td>
                                                    <td>{{ optional($r->staff)->name }}{{ optional($r->staff)->nickname ? ' (' . $r->staff->nickname . ')' : '' }}</td>
                                                    <td class="text-center">{{ optional(optional($r->staff)->position)->position_name }}</td>
                                                    <td class="text-center">{{ optional(optional($r->staff)->branch)->name }}</td>
                                                    <td class="text-center">{{ $r->check_in_at ? \Carbon\Carbon::parse($r->check_in_at)->format('H:i') : '-' }}</td>
                                                    <td class="text-center">{{ $r->check_out_at ? \Carbon\Carbon::parse($r->check_out_at)->format('H:i') : '-' }}</td>
                                                    <td class="text-center">
                                                        @switch($r->status)
                                                            @case('working') <span class="badge bg-label-success">กำลังทำงาน</span> @break
                                                            @case('left') <span class="badge bg-label-secondary">ออกงานแล้ว</span> @break
                                                            @case('auto_ended') <span class="badge bg-label-warning">เลิกงาน (ตี 3)</span> @break
                                                            @default <span class="badge bg-label-secondary">-</span>
                                                        @endswitch
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if (count($records) === 0)
                                                <tr><td colspan="8" class="text-center text-muted">- ไม่มีข้อมูล -</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('admin/layout/inc_footer')
                </div>
            </div>
        </div>
    </div>

    @include('admin/layout/inc_js')
</body>

</html>
