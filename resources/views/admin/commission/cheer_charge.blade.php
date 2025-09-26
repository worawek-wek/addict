<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('admin/layout/inc_header')
    <title>ตั้งค่าค่าเชียร์ - CRM</title>
</head>
<style>
    .table th {
        font-size: 15px;
        font-weight: bold;
    }

    .table td {
        padding-top: 14px;
        padding-bottom: 14px;
    }
</style>

<body>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin/layout/inc_sidemenu')
            <div class="layout-page">
                @include('admin/layout/inc_topmenu')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-10">
                                <div class="card mb-4">
                                    <div class="card-header bg-main text-white">
                                        <h5 class="mb-0"><i class="ti ti-settings"></i> ตั้งค่าค่าเชียร์</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('cheer_charge.store') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">สาขา</label>
                                                    @if(auth()->user()->ref_position_id == 0)
                                                    <select name="ref_branch_id" id="form-branch-select"
                                                        class="form-control select2-branch" required>
                                                        <option value="">เลือกสาขา</option>
                                                        @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    <input type="text" class="form-control"
                                                        value="{{ auth()->user()->branch->name ?? '-' }}" readonly>
                                                    <input type="hidden" name="ref_branch_id"
                                                        value="{{ auth()->user()->ref_branch_id }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">บริการเสริม (AddonOption)</label>
                                                    @if(auth()->user()->ref_position_id == 0)
                                                    <select name="addon_options_id" id="form-addon-select"
                                                        class="form-select" disabled required>
                                                        <option value="">-- เลือกบริการเสริม --</option>
                                                    </select>
                                                    @else
                                                    <select name="addon_options_id" id="form-addon-select"
                                                        class="form-select" required>
                                                        <option value="">-- เลือกบริการเสริม --</option>
                                                        @foreach($addonOptions as $opt)
                                                        @if($opt->branch == auth()->user()->ref_branch_id)
                                                        <option value="{{ $opt->id }}">{{ $opt->name }} ({{ number_format($opt->price, 2) }})</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">ประเภท</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="baht">จำนวนเงิน (บาท)</option>
                                                        <option value="percent">เปอร์เซ็นต์ (%)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">จำนวน</label>
                                                    <input type="number" step="0.01" name="amount" class="form-control"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="mt-4 text-end">
                                                <button type="submit" class="btn btn-main ms-2">บันทึก</button>
                                            </div>
                                        </form>
                                        <hr>
                                        <h5 class="mt-4 mb-2">รายการค่าเชียร์</h5>
                                        <div class="mb-3">
                                            <label class="form-label">สาขาที่กำลังใช้งาน</label>
                                            @if(auth()->user()->ref_position_id == 0)
                                            <select id="active-branch-select" class="form-control select2-branch"
                                                onchange="filterCheerTable()">
                                                <option value="">ทุกสาขา</option>
                                                @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                            @else
                                            <input type="text" class="form-control"
                                                value="{{ auth()->user()->branch->name ?? '-' }}" readonly>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="cheer-table">
                                                <thead>
                                                    <tr class="table-info">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">สาขา</th>
                                                        <th class="text-center">บริการเสริม</th>
                                                        <th class="text-center">ประเภท</th>
                                                        <th class="text-center">จำนวน</th>
                                                        <th class="text-center">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cheer-table-body">
                                                    @forelse($cheerCharges as $item)
                                                    <tr data-branch="{{ $item->ref_branch_id }}">
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">{{ $item->branch->name ?? '-' }}</td>
                                                        <td class="text-center">{{ $item->addonOption->name ?? '-' }}
                                                        </td>
                                                        <td class="text-center">{{ $item->type == 'percent' ?
                                                            'เปอร์เซ็นต์' : 'บาท' }}</td>
                                                        <td class="text-center">{{ number_format($item->amount, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <form
                                                                action="{{ route('cheer_charge.destroy', $item->id) }}"
                                                                method="POST" style="display:inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td class="text-center" colspan="6">- ไม่มีข้อมูล -</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-branch').select2({
                placeholder: 'เลือกสาขา',
                allowClear: true
            });
            // ฟังก์ชัน filter ตาราง cheer ตามสาขาที่เลือก
            window.filterCheerTable = function() {
                var branchId = $('#active-branch-select').val();
                $('#cheer-table-body tr').each(function() {
                    var rowBranch = $(this).data('branch');
                    if (!branchId || branchId == rowBranch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            };

            // --- AddonOption dropdown logic ---
            @if(auth()->user()->ref_position_id == 0)
            $('#form-branch-select').on('change', function() {
                var branchId = $(this).val();
                var $addonSelect = $('#form-addon-select');
                $addonSelect.prop('disabled', true);
                $addonSelect.html('<option value="">-- เลือกบริการเสริม --</option>');
                if (branchId) {
                    // AJAX ไป endpoint ที่ return AddonOption ของสาขานี้
                    $.get('/api/addon-options/' + branchId, function(data) {
                        if (Array.isArray(data)) {
                            data.forEach(function(opt) {
                                $addonSelect.append('<option value="'+opt.id+'">'+opt.name+' ('+parseFloat(opt.price).toFixed(2)+')</option>');
                            });
                            $addonSelect.prop('disabled', false);
                        }
                    });
                }
            });
            // ถ้า reload page ให้ dropdown disabled ถ้ายังไม่เลือกสาขา
            if (!$('#form-branch-select').val()) {
                $('#form-addon-select').prop('disabled', true);
            }
            @endif

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    html: @json(session('success')),
                    confirmButtonText: 'ปิด',
                    customClass: { confirmButton: 'btn btn-main' }
                });
            @elseif($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    html: @json(implode('<br>', $errors->all())),
                    confirmButtonText: 'ปิด',
                    customClass: { confirmButton: 'btn btn-main' }
                });
            @endif
        });
    </script>
</body>

</html>
