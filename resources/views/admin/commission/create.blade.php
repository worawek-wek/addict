<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @include('admin/layout/inc_header')
    <title>เพิ่มค่าคอมมิชชั่น - CRM</title>
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
                                        <h5 class="mb-0"><i class="ti ti-currency-dollar"></i> เพิ่มค่าคอมมิชชั่น</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('commission.store') }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">พนักงาน</label>
                                                    <select name="ref_user_id" id="ref_user_id" class="form-select" required>
                                                        <option value="">-- เลือกพนักงาน --</option>
                                                        @forelse ($users as $user)
                                                            <option value="{{ $user->id }}" data-position="{{ $user->ref_position_id }}">
                                                                {{ $user->name }} ({{ $user->nickname }})
                                                                @if($user->branch && $user->branch->name)
                                                                    - {{ $user->branch->name }}
                                                                @endif
                                                            </option>
                                                        @empty
                                                            <option value="">*** ไม่มีข้อมูลพนักงานในระบบ ***</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="addon-option-group" style="display:none;"></div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">ตำแหน่ง</label>
                                                    <select id="ref_position_id" class="form-select" required disabled>
                                                        <option value="">-- เลือกตำแหน่ง --</option>
                                                        @foreach ($positions as $position)
                                                        <option value="{{ $position->id }}">
                                                            {{ $position->position_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="ref_position_id" id="hidden_ref_position_id" value="">
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const userSelect = document.getElementById('ref_user_id');
                                                            const posSelect = document.getElementById('ref_position_id');
                                                            const hiddenPos = document.getElementById('hidden_ref_position_id');
                                                            function syncHiddenPosition() {
                                                                hiddenPos.value = posSelect.value;
                                                            }
                                                            userSelect.addEventListener('change', function() {
                                                                const selected = userSelect.options[userSelect.selectedIndex];
                                                                const posId = selected.getAttribute('data-position');
                                                                if (posId) {
                                                                    posSelect.value = posId;
                                                                    posSelect.disabled = true;
                                                                    hiddenPos.value = posId; // set hidden to user's position
                                                                } else {
                                                                    posSelect.value = '';
                                                                    posSelect.disabled = false;
                                                                    hiddenPos.value = posSelect.value;
                                                                }
                                                            });
                                                            posSelect.addEventListener('change', function() {
                                                                hiddenPos.value = posSelect.value;
                                                            });
                                                            // sync on load
                                                            hiddenPos.value = posSelect.value;
                                                        });
                                                    </script>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">บริการ</label>
                                                    <select name="service_name" id="service_name" class="form-select" required>
                                                        <option value="">-- เลือกบริการ --</option>
                                                        <option value="การบริการลูกค้า">การบริการลูกค้า</option>
                                                        <option value="บริการนวด">บริการนวด</option>

                                                    </select>
                                                    <input type="hidden" name="ref_addon_options_id" id="ref_addon_options_id" value="">
                                                </div>
                                                <div class="col-md-6" id="duration-group">
                                                    <label class="form-label">ระยะเวลา</label>
                                                    {{-- <div class="form-text text-muted mb-1">* ถ้าเป็นการนวดให้เลือกเวลา</div> --}}
                                                    <select name="service_duration" class="form-select">
                                                        <option value="">-- เลือก --</option>
                                                        <option value="40">40 นาที</option>
                                                        <option value="60">60 นาที</option>
                                                        <option value="90">90 นาที</option>
                                                        <option value="other">อื่น ๆ</option>
                                                    </select>
                                                </div>
                                                 <div class="col-md-6">
                                                    <label class="form-label">เปอร์เซ็นต์คอมมิชชั่น (%)</label>
                                                    <input type="number" step="0.01" name="commission_percent" class="form-control" min="0" max="100" >
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">จำนวนเงินคอมมิชชั่น</label>
                                                    <input type="number" step="0.01" name="commission_amount" class="form-control" >
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
            // ตำแหน่ง auto-lock ตาม user (รองรับทั้ง select2 และ native)
            // disable service_name dropdown จนกว่าจะเลือกพนักงาน
            $('#service_name').prop('disabled', true);
            // Prepare AddonOption data for JS
            var addonOptions = @json($addonOptions);

            // Enable search for service_name dropdown
            $('#service_name').select2({
                width: '100%',
                placeholder: '-- เลือกบริการ --',
                allowClear: true,
                dropdownParent: $('#service_name').closest('.col-md-6')
            });

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
                            $serviceSelect.append('<option value="addon_'+opt.id+'">'+opt.name+' ('+parseFloat(opt.price).toFixed(2)+')</option>');
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
                // Set ref_addon_options_id hidden field
                setAddonOptionId();
                // Always trigger change event to update duration-group visibility
                $serviceSelect.trigger('change');
            }

            function setAddonOptionId() {
                var val = $('#service_name').val();
                if (val && val.startsWith('addon_')) {
                    var addonId = val.replace('addon_', '');
                    $('#ref_addon_options_id').val(addonId);
                    // เซ็ต hidden service_name เป็นชื่อ Addon จริง
                    var addon = addonOptions.find(function(opt){ return opt.id == parseInt(addonId); });
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

            // Prepare users data for branch id lookup
            var $users = @json($users);

            // trigger duration-group & addon-option-group show/hide when service_name changes
            $('#service_name').on('change', function() {
                var posVal = $('#ref_position_id').val();
                if ($(this).val() == 'บริการนวด') {
                    $('#duration-group').show();
                    if (posVal == '2') {
                        $('#addon-option-group').show();
                    } else {
                        $('#addon-option-group').hide();
                    }
                } else {
                    $('#duration-group').hide();
                    $('#addon-option-group').hide();
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
            // เรียกครั้งแรก (กรณีมีค่า default)
            if (!$userSelect.val()) {
                $('#service_name').prop('disabled', true);
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
