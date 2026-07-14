<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>ตั้งค่าบันได Rank คอมมิชชั่นมาม่า - CRM</title>
</head>
<style>
    .table th { font-size: 14px; font-weight: bold; }
    .rank-badge { font-weight: bold; }
</style>

<body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @unless($tableReady)
                            <div class="alert alert-warning">
                                <b>ยังไม่ได้สร้างตาราง <code>commission_ranks</code></b> — กรุณารัน migration หรือ raw SQL
                                (<code>database/sql/mama_rank_commission.sql</code>) ก่อน จึงจะบันทึกบันได Rank ได้
                            </div>
                        @endunless

                        <form action="{{ route('commission_ranks.save') }}" method="POST">
                            @csrf

                            <div class="card mb-3">
                                <div class="card-header bg-main text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="ti ti-stairs-up"></i> ตั้งค่าบันได Rank คอมมิชชั่นทีมมาม่า</h5>
                                    <a href="{{ url('admin/commission/view-sales') }}" class="btn btn-sm btn-white">
                                        <i class="ti ti-arrow-back-up"></i> กลับหน้ารายงาน
                                    </a>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" name="category" value="{{ $category }}">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-6">
                                            <label class="form-label">หมวดค่าคอมมิชชั่น</label>
                                            <select class="form-select"
                                                onchange="window.location='{{ url('admin/commission-ranks') }}?branch={{ $branchParam }}&category='+this.value">
                                                <option value="service" {{ $category === 'service' ? 'selected' : '' }}>นวด + สินค้า</option>
                                                <option value="drink" {{ $category === 'drink' ? 'selected' : '' }}>ดื่ม</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">สาขา</label>
                                            <select name="ref_branch_id" class="form-select"
                                                onchange="window.location='{{ url('admin/commission-ranks') }}?category={{ $category }}&branch='+this.value">
                                                <option value="" {{ $branchParam === '' ? 'selected' : '' }}>ค่ากลาง (ใช้ทุกสาขา)</option>
                                                @foreach($branches as $b)
                                                    <option value="{{ $b->id }}" {{ (string)$branchParam === (string)$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">บันได "ค่ากลาง" จะถูกใช้เมื่อสาขานั้นยังไม่ได้ตั้งค่าเอง</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $ladderTables = [
                                    'sales'  => ['title' => 'บันไดยอดขาย (Sales)', 'unit' => 'ยอดขายสะสมขั้นต่ำ (บาท)', 'icon' => 'ti-currency-dollar'],
                                    'rounds' => ['title' => 'บันไดจำนวนรอบ (Rounds)', 'unit' => 'จำนวนรอบสะสมขั้นต่ำ', 'icon' => 'ti-repeat'],
                                ];
                            @endphp

                            @foreach($ladderTables as $mode => $meta)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="ti {{ $meta['icon'] }}"></i> {{ $meta['title'] }}</h6>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr class="table-info">
                                                        <th class="text-center" style="width:80px;">Rank</th>
                                                        <th class="text-center">{{ $meta['unit'] }}</th>
                                                        <th class="text-center" style="width:200px;">วิธีจ่าย</th>
                                                        <th class="text-center" style="width:140px;">อัตรา % </th>
                                                        <th class="text-center" style="width:160px;">เงินคงที่ (บาท)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for($n = 1; $n <= 5; $n++)
                                                        @php
                                                            $row = $ladders[$mode][$n] ?? null;
                                                            // บันไดจำนวนรอบ ดีฟอลต์วิธีจ่าย = บาท/รอบ, บันไดยอดขาย = % ของยอดขาย
                                                            $defPayout = $mode === 'rounds' ? 'fixed_per_round' : 'percent';
                                                            $curPayout = $row->payout_type ?? $defPayout;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center align-middle">
                                                                <span class="badge bg-label-success rank-badge">Rank {{ $n }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    name="{{ $mode }}[{{ $n }}][min_threshold]"
                                                                    class="form-control"
                                                                    value="{{ $row->min_threshold ?? '' }}"
                                                                    placeholder="เว้นว่าง = ไม่ใช้ Rank นี้">
                                                            </td>
                                                            <td>
                                                                <select name="{{ $mode }}[{{ $n }}][payout_type]" class="form-select">
                                                                    <option value="percent" {{ $curPayout == 'percent' ? 'selected' : '' }}>% ของยอดขาย</option>
                                                                    <option value="fixed_per_round" {{ $curPayout == 'fixed_per_round' ? 'selected' : '' }}>บาท / รอบ</option>
                                                                    <option value="fixed" {{ $curPayout == 'fixed' ? 'selected' : '' }}>บาท (คงที่)</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    name="{{ $mode }}[{{ $n }}][rate]"
                                                                    class="form-control"
                                                                    value="{{ $row->rate ?? '' }}" placeholder="เช่น 1.5">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0"
                                                                    name="{{ $mode }}[{{ $n }}][fixed_amount]"
                                                                    class="form-control"
                                                                    value="{{ $row->fixed_amount ?? '' }}" placeholder="เช่น 200">
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="text-end mb-4">
                                <button type="submit" class="btn btn-main" {{ $tableReady ? '' : 'disabled' }}>
                                    <i class="ti ti-device-floppy"></i> บันทึกบันได Rank
                                </button>
                            </div>
                        </form>

                    </div>
                    @include('admin/layout/inc_footer')
                </div>
            </div>
        </div>
    </div>

    @include('admin/layout/inc_js')
    <script>
        $(document).ready(function() {
            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'สำเร็จ', html: @json(session('success')),
                    confirmButtonText: 'ปิด', customClass: { confirmButton: 'btn btn-main' } });
            @elseif($errors->any())
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', html: @json(implode('<br>', $errors->all())),
                    confirmButtonText: 'ปิด', customClass: { confirmButton: 'btn btn-main' } });
            @endif
        });
    </script>
</body>

</html>
