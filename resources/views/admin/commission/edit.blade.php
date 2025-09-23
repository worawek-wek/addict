<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('admin/layout/inc_header')
    <title>แก้ไขค่าคอมมิชชั่น - CRM</title>
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
                                        <h5 class="mb-0"><i class="ti ti-currency-dollar"></i> แก้ไขค่าคอมมิชชั่น</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('commission.update', $commission->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-3">
                                                <div class="col-md-6" id="addon-option-group" style="display:none;">
                                                    <!-- AddonOption dropdown removed, now merged into service_name -->
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">พนักงาน</label>
                                                    <select name="ref_user_id" id="ref_user_id" class="form-select"
                                                        required disabled>
                                                        <option value="">-- เลือกพนักงาน --</option>
                                                        @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            data-position="{{ $user->ref_position_id }}"
                                                            @if($commission->ref_user_id == $user->id) selected @endif>
                                                            {{ $user->name }} ({{ $user->nickname }})
                                                            @if($user->branch && $user->branch->name)
                                                            - {{ $user->branch->name }}
                                                            @endif
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">ตำแหน่ง</label>
                                                    <select id="ref_position_id" class="form-select" required disabled>
                                                        <option value="">-- เลือกตำแหน่ง --</option>
                                                        @foreach ($positions as $position)
                                                        <option value="{{ $position->id }}" @if($commission->
                                                            ref_position_id == $position->id) selected @endif>
                                                            {{ $position->position_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="ref_position_id"
                                                        id="hidden_ref_position_id"
                                                        value="{{ $commission->ref_position_id }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">บริการ</label>
                                                    <select name="service_name" id="service_name" class="form-select" required>
                                                        <option value="">-- เลือกบริการ --</option>
                                                        <option value="การบริการลูกค้า" {{ old('service_name', $commission->service_name) == 'การบริการลูกค้า' ? 'selected' : '' }}>การบริการลูกค้า</option>
                                                        <option value="บริการนวด" {{ old('service_name', $commission->service_name) == 'บริการนวด' ? 'selected' : '' }}>บริการนวด</option>
                                                        <option value="บริการทำความสะอาด" {{ old('service_name', $commission->service_name) == 'บริการทำความสะอาด' ? 'selected' : '' }}>บริการทำความสะอาด</option>
                                                        <option value="บริการซ่อมบำรุง" {{ old('service_name', $commission->service_name) == 'บริการซ่อมบำรุง' ? 'selected' : '' }}>บริการซ่อมบำรุง</option>
                                                        <option value="อื่น ๆ" {{ old('service_name', $commission->service_name) == 'อื่น ๆ' ? 'selected' : '' }}>อื่น ๆ</option>
                                                        @if($commission->ref_addon_options_id)
                                                            @php
                                                                $selectedAddon = $addonOptions->firstWhere('id', $commission->ref_addon_options_id);
                                                            @endphp
                                                            @if($selectedAddon)
                                                                <option value="addon_{{ $selectedAddon->id }}" selected>{{ $selectedAddon->name }} ({{ number_format($selectedAddon->price, 2) }})</option>
                                                            @endif
                                                        @endif
                                                    </select>
                                                    <input type="hidden" name="ref_addon_options_id" id="ref_addon_options_id" value="{{ old('ref_addon_options_id', $commission->ref_addon_options_id) }}">
                                                </div>
                                                <div class="col-md-6" id="duration-group">
                                                    <label class="form-label">ระยะเวลา</label>
                                                    <select name="service_duration" class="form-select">
                                                        <option value="">-- เลือก --</option>
                                                        <option value="40" @if(old('service_duration', $commission->
                                                            service_duration)=='40') selected @endif>40 นาที</option>
                                                        <option value="60" @if(old('service_duration', $commission->
                                                            service_duration)=='60') selected @endif>60 นาที</option>
                                                        <option value="90" @if(old('service_duration', $commission->
                                                            service_duration)=='90') selected @endif>90 นาที</option>
                                                        <option value="other" @if(old('service_duration', $commission->
                                                            service_duration)=='other') selected @endif>อื่น ๆ</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">เปอร์เซ็นต์คอมมิชชั่น (%)</label>
                                                    <input type="number" step="0.01" name="commission_percent"
                                                        class="form-control" min="0" max="100"
                                                        value="{{ old('commission_percent', $commission->commission_percent) }}"
                                                        >
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">จำนวนเงินคอมมิชชั่น</label>
                                                    <input type="number" step="0.01" name="commission_amount"
                                                        class="form-control"
                                                        value="{{ old('commission_amount', $commission->commission_amount) }}"
                                                        >
                                                </div>

                                            </div>
                                            <div class="mt-4 text-end">
                                                <a href="{{ route('commission.index') }}"
                                                    class="btn btn-label-secondary">ย้อนกลับ</a>
                                                <button type="submit" class="btn btn-main">บันทึก</button>
                                            </div>
                                        </form>
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
            // Init select2 for user dropdown
            var $userSelect = $('#ref_user_id');
            var $posSelect = $('#ref_position_id');
            $userSelect.select2({
                width: '100%',
                placeholder: '-- เลือกพนักงาน --',
                allowClear: true,
                dropdownParent: $userSelect.closest('.col-md-6')
            });
            // Prepare AddonOption data for JS
            var addonOptions = @json($addonOptions ?? []);
            // Enable search for service_name dropdown
            $('#service_name').select2({
                width: '100%',
                placeholder: '-- เลือกบริการ --',
                allowClear: true,
                dropdownParent: $('#service_name').closest('.col-md-6')
            });
            // Prepare users data for branch id lookup
            // disable service_name dropdown จนกว่าจะเลือกพนักงาน
            $('#service_name').prop('disabled', true);
            var $users = @json($users ?? []);
            function updatePosition() {
                var posId = $userSelect.find('option:selected').data('position');
                if (posId) {
                    $posSelect.val(posId).prop('disabled', true);
                } else {
                    $posSelect.val('').prop('disabled', false);
                }
                // sync hidden input ทุกครั้ง
                $('#hidden_ref_position_id').val($posSelect.val());
                // ปรับ dropdown service_name ตามตำแหน่ง
                var $serviceSelect = $('#service_name');
                $serviceSelect.empty();
                $serviceSelect.append('<option value="">-- เลือกบริการ --</option>');
                var selectedAddonId = $('#ref_addon_options_id').val();
                var selectedAddonOption = null;
                if (selectedAddonId) {
                    selectedAddonOption = addonOptions.find(function(opt){ return opt.id == selectedAddonId; });
                }
                if ($posSelect.val() == '1') {
                    $serviceSelect.append('<option value="การบริการลูกค้า">การบริการลูกค้า</option>');
                } else if ($posSelect.val() == '2') {
                    $serviceSelect.append('<option value="บริการนวด">บริการนวด</option>');
                    // Filter AddonOption by user's ref_branch_id
                    var selectedUserId = $userSelect.val();
                    var selectedUserBranchId = null;
                    if (selectedUserId) {
                        var selectedUser = $users.filter(function(u){ return u.id == selectedUserId; });
                        if (selectedUser.length > 0) {
                            selectedUserBranchId = selectedUser[0].ref_branch_id;
                        }
                    }
                    addonOptions.forEach(function(opt){
                        if (!selectedUserBranchId || opt.branch == selectedUserBranchId) {
                            var selected = (selectedAddonOption && opt.id == selectedAddonOption.id) ? 'selected' : '';
                            $serviceSelect.append('<option value="addon_'+opt.id+'" '+selected+'>'+opt.name+' ('+parseFloat(opt.price).toFixed(2)+')</option>');
                        }
                    });
                } else {
                    $serviceSelect.append('<option value="การบริการลูกค้า">การบริการลูกค้า</option>');
                    $serviceSelect.append('<option value="บริการนวด">บริการนวด</option>');
                    $serviceSelect.append('<option value="บริการทำความสะอาด">บริการทำความสะอาด</option>');
                    $serviceSelect.append('<option value="บริการซ่อมบำรุง">บริการซ่อมบำรุง</option>');
                    $serviceSelect.append('<option value="อื่น ๆ">อื่น ๆ</option>');
                }
                $serviceSelect.prop('disabled', false);
                // ถ้ามี addon ที่เลือกไว้ ให้ select2 เลือก option addon_xxx
                if (selectedAddonOption) {
                    $serviceSelect.val('addon_' + selectedAddonOption.id).trigger('change');
                }
                // ซ่อน/แสดงฟิลด์ระยะเวลา เฉพาะเมื่อเลือกบริการนวด
                if ($serviceSelect.val() == 'บริการนวด') {
                    $('#duration-group').show();
                } else {
                    $('#duration-group').hide();
                }
                // Set ref_addon_options_id hidden field
                setAddonOptionId();
            }
            function setAddonOptionId() {
                var val = $('#service_name').val();
                if (val && val.startsWith('addon_')) {
                    var addonId = val.replace('addon_', '');
                    $('#ref_addon_options_id').val(addonId);
                    // เซ็ต hidden service_name เป็นชื่อ Addon จริง
                    var addon = addonOptions.find(function(opt){ return opt.id == addonId; });
                    if (addon) {
                        // สร้าง hidden input ถ้ายังไม่มี
                        if ($('#hidden_service_name').length == 0) {
                            $('<input>').attr({type:'hidden',id:'hidden_service_name',name:'service_name'}).appendTo('form');
                        }
                        $('#hidden_service_name').val(addon.name);
                    }
                } else {
                    $('#ref_addon_options_id').val('');
                    // ลบ hidden service_name ถ้าไม่ใช่ addon
                    $('#hidden_service_name').remove();
                }
            }
            // trigger duration-group & addon-option-group show/hide when service_name changes
            $('#service_name').on('change', function() {
                var posVal = $('#ref_position_id').val();
                if ($(this).val() == 'บริการนวด') {
                    $('#duration-group').show();
                } else {
                    $('#duration-group').hide();
                }
                setAddonOptionId();
            });
            $userSelect.on('change', function(){
                // enable/disable service_name ตามการเลือกพนักงาน
                if ($userSelect.val()) {
                    $('#service_name').prop('disabled', false);
                } else {
                    $('#service_name').prop('disabled', true).val('').trigger('change');
                }
                updatePosition();
            });
            $posSelect.on('change', updatePosition);
            // ถ้า ref_addon_options_id มีค่า ให้แสดง addon option ใน dropdown ทันที
            var initialAddonId = $('#ref_addon_options_id').val();
            if (initialAddonId) {
                // ให้ตำแหน่งเป็น 2 (พนักงานนวด) เพื่อให้ dropdown แสดง addon
                $posSelect.val('2');
                $('#service_name').prop('disabled', false);
            } else {
                if (!$userSelect.val()) {
                    $('#service_name').prop('disabled', true);
                }
            }
            updatePosition();
            // Show SweetAlert2 popup if there is a message
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
