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
                                                    <select name="ref_user_id" id="ref_user_id" class="form-select" required disabled>
                                                        <option value="">-- เลือกพนักงาน --</option>
                                                        @forelse ($users as $user)
                                                            <option value="{{ $user->id }}" data-position="{{ $user->ref_position_id }}" @if($commission->ref_user_id == $user->id) selected @endif>
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
                                                    <select id="ref_position_id" class="form-select" required disabled>
                                                        <option value="">-- เลือกตำแหน่ง --</option>
                                                        @foreach ($positions as $position)
                                                        <option value="{{ $position->id }}" @if($commission->user && $commission->user->position && $commission->user->position->id == $position->id) selected @endif>
                                                            {{ $position->position_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="ref_position_id" id="hidden_ref_position_id" value="{{ $commission->user && $commission->user->position ? $commission->user->position->id : '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">บริการ</label>
                                                    <input type="text" class="form-control" value="{{ $commission->service_name }}" readonly disabled>
                                                    <input type="hidden" name="service_name" value="{{ $commission->service_name }}">
                                                    <input type="hidden" name="ref_addon_options_id" id="ref_addon_options_id" value="{{ old('ref_addon_options_id', $commission->ref_addon_options_id) }}">
                                                </div>
                                                @php use Illuminate\Support\Str; @endphp
                                                @if(Str::contains($commission->service_name, 'นวด'))
                                                <div class="col-md-6" id="duration-group">
                                                    <label class="form-label">ระยะเวลา</label>
                                                    <select name="service_duration" class="form-select" disabled>
                                                        <option value="">-- เลือก --</option>
                                                        <option value="40" @if(old('service_duration', $commission->service_duration)=='40') selected @endif>40 นาที</option>
                                                        <option value="60" @if(old('service_duration', $commission->service_duration)=='60') selected @endif>60 นาที</option>
                                                        <option value="90" @if(old('service_duration', $commission->service_duration)=='90') selected @endif>90 นาที</option>
                                                        <option value="other" @if(old('service_duration', $commission->service_duration)=='other') selected @endif>อื่น ๆ</option>
                                                    </select>
                                                    <input type="hidden" name="service_duration" value="{{ old('service_duration', $commission->service_duration) }}">
                                                </div>
                                                @endif
                                                <div class="col-md-6">
                                                    <label class="form-label">เปอร์เซ็นต์คอมมิชชั่น (%)</label>
                                                    <input type="number" step="0.01" name="commission_percent" class="form-control" min="0" max="100" value="{{ old('commission_percent', $commission->commission_percent) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">จำนวนเงินคอมมิชชั่น</label>
                                                    <input type="number" step="0.01" name="commission_amount" class="form-control" value="{{ old('commission_amount', $commission->commission_amount) }}">
                                                </div>
                                            </div>
                                            <div class="mt-4 text-end">
                                                <a href="{{ route('commission.index') }}" class="btn btn-label-secondary">ย้อนกลับ</a>
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
            // Init select2 for user dropdown (still used for staff)
            var $userSelect = $('#ref_user_id');
            $userSelect.select2({
                width: '100%',
                placeholder: '-- เลือกพนักงาน --',
                allowClear: true,
                dropdownParent: $userSelect.closest('.col-md-6')
            });
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
