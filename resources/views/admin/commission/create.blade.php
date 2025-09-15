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
                                                    <label class="form-label">ตำแหน่ง</label>
                                                    <select id="ref_position_id" class="form-select" required>
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
                                                    <label class="form-label">ชื่อบริการ</label>
                                                    <input type="text" name="service_name" id="service_name" class="form-control" required>
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
                                                    <label class="form-label">จำนวนเงินคอมมิชชั่น</label>
                                                    <input type="number" step="0.01" name="commission_amount"
                                                        class="form-control" required>
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
            function updatePosition() {
                var posId = $userSelect.find('option:selected').data('position');
                if (posId) {
                    $posSelect.val(posId).prop('disabled', true);
                } else {
                    $posSelect.val('').prop('disabled', false);
                }
                // sync hidden input ทุกครั้ง
                $('#hidden_ref_position_id').val($posSelect.val());
                // ซ่อน/แสดงฟิลด์ระยะเวลา ถ้าตำแหน่งเป็น id 1
                if ($posSelect.val() == '1') {
                    $('#duration-group').hide();
                    // ตั้งชื่อบริการเป็น 'การบริการลูกค้า' และ readonly
                    $('#service_name').val('การบริการลูกค้า').prop('readonly', true);
                } else if ($posSelect.val() == '2') {
                    $('#duration-group').show();
                    // ตั้งชื่อบริการเป็น 'บริการนวด' และ readonly
                    $('#service_name').val('บริการนวด').prop('readonly', true);
                } else {
                    $('#duration-group').show();
                    // ให้กรอกชื่อบริการเอง
                    $('#service_name').val('').prop('readonly', false);
                }
            }
            $userSelect.on('change', updatePosition);
            $posSelect.on('change', updatePosition);
            // เรียกครั้งแรก (กรณีมีค่า default)
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
