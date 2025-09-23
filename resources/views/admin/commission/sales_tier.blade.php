<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('admin/layout/inc_header')
    <title>ตั้งค่าคอมมิชชั่นพนักงานขาย - CRM</title>
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
                                        <h5 class="mb-0"><i class="ti ti-currency-dollar"></i> ตั้งค่าคอมมิชชั่นพนักงานขาย</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('sales_commission_tier.store') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">สาขา</label>
                                                    <select name="ref_branch_id" id="ref_branch_id" class="form-select select2-branch" required>
                                                        <option value="">-- เลือกสาขา --</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">ยอดขายขั้นต่ำ</label>
                                                    <input type="number" step="0.01" name="min_sales_amount" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">ยอดขายสูงสุด</label>
                                                    <input type="number" step="0.01" name="max_sales_amount" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">อัตราคอมมิชชั่น (%)</label>
                                                    <input type="number" step="0.01" name="commission_rate" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="mt-4 text-end">
                                                <a href="{{ route('commission.index') }}" class="btn btn-label-secondary">ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-main ms-2">บันทึก</button>
                                            </div>
                                        </form>
                                        <hr>
                                        <h5 class="mt-4 mb-2">รายการคอมมิชชั่นแบบขั้นบันได</h5>
                                        <div class="mb-3">
                                            <label class="form-label">กรองตามสาขา</label>
                                            <select id="filter_branch_id" class="form-select select2-branch" style="max-width:300px;">
                                                <option value="">-- แสดงทุกสาขา --</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="tier-table">
                                                <thead>
                                                    <tr class="table-info">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">สาขา</th>
                                                        <th class="text-center">ยอดขายขั้นต่ำ</th>
                                                        <th class="text-center">ยอดขายสูงสุด</th>
                                                        <th class="text-center">อัตราคอมมิชชั่น (%)</th>
                                                        <th class="text-center">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tier-table-body">
                                                    @foreach($tiers as $tier)
                                                    <tr data-branch="{{ $tier->ref_branch_id }}">
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">{{ optional($branchMap[$tier->ref_branch_id] ?? null)->name ?? '-' }}</td>
                                                        <td class="text-center">{{ number_format($tier->min_sales_amount,2) }}</td>
                                                        <td class="text-center">{{ number_format($tier->max_sales_amount,2) }}</td>
                                                        <td class="text-center">{{ number_format($tier->commission_rate,2) }}</td>
                                                        <td class="text-center">
                                                            <form action="{{ route('sales_commission_tier.destroy', $tier->id) }}" method="POST" style="display:inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @if($tiers->isEmpty())
                                                    <tr>
                                                        <td colspan="6" class="text-center">- ไม่มีข้อมูล -</td>
                                                    </tr>
                                                    @endif
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
            // กำหนด dropdownParent ให้แต่ละ select2 ใช้ container เฉพาะตัวเอง
            $('#ref_branch_id').select2({
                width: '100%',
                placeholder: '-- เลือกสาขา --',
                allowClear: true,
                dropdownParent: $('#ref_branch_id').closest('.col-md-6')
            });
            $('#filter_branch_id').select2({
                width: '100%',
                placeholder: '-- แสดงทุกสาขา --',
                allowClear: true,
                dropdownParent: $('#filter_branch_id').parent()
            });

            $('#filter_branch_id').on('change', function() {
                var branchId = $(this).val();
                $('#tier-table-body tr').each(function() {
                    if (!branchId || $(this).data('branch') == branchId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

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
