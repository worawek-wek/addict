<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('admin/layout/inc_header')
    <title>ตั้งค่าเริ่มต้นพนักงานนวด - CRM</title>
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
                                        <h5 class="mb-0"><i class="ti ti-settings"></i> ตั้งค่าเริ่มต้นพนักงานนวด</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <span class="fw-bold">สาขา:</span>
                                            @if(auth()->user()->ref_position_id == 0)
                                                <form id="branch-form" method="GET" action="">
                                                    <select name="branch_id" id="branch-select" class="form-control select2-branch" style="width: 250px; display: inline-block;">
                                                        <option value="">ทุกสาขา</option>
                                                        @foreach($branches as $branch)
                                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @else
                                                <span class="text-main">
                                                    {{ auth()->user()->branch->name ?? '-' }}
                                                </span>
                                            @endif
                                        </div>
                                        <form action="{{ route('massage_default_setting.store') }}" method="POST">
                                            @csrf
                                            @if(auth()->user()->ref_position_id == 0)
                                                <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                                            @endif
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">บริการ</label>
                                                    <select name="service_name" id="service_name" class="form-select" required>
                                                        <option value="">-- เลือกบริการ --</option>
                                                        <option value="บริการนวด">บริการนวด</option>
                                                        @foreach($addonOptions ?? [] as $opt)
                                                            <option value="addon_{{ $opt->id }}">{{ $opt->name }} ({{ number_format($opt->price,2) }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6" id="duration-group" style="display:none;">
                                                    <label class="form-label">ระยะเวลา</label>
                                                    <select name="service_duration" class="form-select">
                                                        <option value="">-- เลือก --</option>
                                                        <option value="40">40 นาที</option>
                                                        <option value="60">60 นาที</option>
                                                        <option value="90">90 นาที</option>
                                                        <option value="other">อื่น ๆ</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">จำนวนเงิน (บาท)</label>
                                                    <input type="number" step="0.01" name="commission_amount" id="commission_amount" class="form-control" min="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">เปอร์เซ็นต์ (%)</label>
                                                    <input type="number" step="0.01" name="commission_percent" id="commission_percent" class="form-control" min="0" max="100">
                                                </div>
                                            </div>
                                            <div class="mt-4 text-end">
                                                <a href="{{ route('commission.index') }}" class="btn btn-label-secondary">ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-main ms-2" id="submit-btn">บันทึก</button>
                                            </div>
                                        </form>
                                        <hr>
                                        <h5 class="mt-4 mb-2">รายการค่าตั้งค่าเริ่มต้นพนักงานนวด</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="massage-default-table">
                                                <thead>
                                                    <tr class="table-info">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">ชื่อพนักงาน</th>
                                                        <th class="text-center">ค่าตั้งค่าเริ่มต้น</th>
                                                        <th class="text-center">จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($defaultSettings as $item)
                                                    <tr data-branch="{{ $item->ref_branch_id }}">
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            @if($item->addon_options_id)
                                                                {{ optional($addonOptions->where('id', $item->addon_options_id)->first())->name ?? '-' }}
                                                            @else
                                                                {{ $item->service_name ?? '-' }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(!is_null($item->commission_amount))
                                                                {{ number_format($item->commission_amount, 2) }} <span class="text-muted">บาท</span>
                                                            @elseif(!is_null($item->commission_percent))
                                                                {{ number_format($item->commission_percent, 2) }} <span class="text-muted">%</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <form action="{{ route('commission.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">- ไม่มีข้อมูล -</td>
                                                        <td class="text-center">-</td>
                                                        <td class="text-center">-</td>
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
            var $serviceSelect = $('#service_name');
            $serviceSelect.select2({
                width: '100%',
                placeholder: '-- เลือกบริการ --',
                allowClear: true,
                dropdownParent: $serviceSelect.closest('.col-md-6')
            });

            // Branch dropdown (เฉพาะ ref_position_id == 0)
            $('.select2-branch').select2({
                placeholder: 'เลือกสาขา',
                allowClear: true
            });
            // Branch dropdown: reload page with branch_id param
            $('#branch-select').on('change', function() {
                $('#branch-form').submit();
            });

            // แสดง/ซ่อนระยะเวลาเมื่อเลือกบริการนวด
            $serviceSelect.on('change', function() {
                if ($(this).val() == 'บริการนวด') {
                    $('#duration-group').show();
                } else {
                    $('#duration-group').hide();
                }
            });
            // sync ครั้งแรก
            if ($serviceSelect.val() == 'บริการนวด') {
                $('#duration-group').show();
            } else {
                $('#duration-group').hide();
            }

            // Validate at least one of amount or percent is filled
            $('#submit-btn').on('click', function(e) {
                var amount = $('#commission_amount').val();
                var percent = $('#commission_percent').val();
                if (!amount && !percent) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'กรุณากรอกจำนวนเงินหรือเปอร์เซ็นต์อย่างน้อย 1 ช่อง',
                        confirmButtonText: 'ปิด',
                        customClass: { confirmButton: 'btn btn-main' }
                    });
                }
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
