<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
        @include('layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
    <link rel="stylesheet" href="assets/vendor/libs/leaflet/leaflet.css" />
</head>
<style>
    .modalHeadDecor .modal-header {
        padding: 0;
    }

    .modalHeadDecor .modal-title {
        padding: 1.25rem 1.5rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -65px;
        content: '';
        width: 0;
        height: 0;
        border-top: 65px solid #54BAB9;
        border-right: 65px solid transparent;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layout/inc_sidemenu')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                @include('layout/inc_topmenu')

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="card mb-3">
                                    <div class="card-header border-bottom border-light">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-6">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-building-community text-main ti-md"></i>
                                                    ข้อมูลหอพัก
                                                </h4>
                                            </div>
                                            <!-- <div class="col-sm-6 text-end">
                                                <button type="button" class="btn btn-main waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#addModal"><i
                                                        class="ti-xs ti ti-plus me-2"></i>เพิ่มบัญชี</button>
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="card-body pt-4">
                                        <form>
                                            <div class="row g-3">
                                                <div class="col-sm-12">
                                                    <label for="" class="form-label">ชื่อหอพัก<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id=""
                                                        placeholder="กรอกชื่อหอพัก" value="{{ $apartment->name }}" required />
                                                </div>
                                                <div class="col-sm-12">
                                                    <label for="" class="form-label">ที่อยู่หอพัก<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id=""
                                                        placeholder="บ้านเลขที่/ หมู่/ ซอย/ ถนน" value="{{ $apartment->address }}" required />
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>เลือกจังหวัด</label>
                                                    <select name="ref_province_id" id="select2Basic" class="select2 form-select form-select-lg">
                                                        <option selected>เลือกจังหวัด</option>
                                                        @foreach ($province as $pro)
                                                            <option @if ($pro->id == $apartment->ref_province_id)
                                                                selected
                                                            @endif value="{{ $pro->id }}">{{ $pro->name_in_thai }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>เลือกอำเภอ</label>
                                                    <select name="ref_district_id" id="select2District" class="select2 form-select form-select-lg">
                                                        <option selected>เลือกอำเภอ</option>
                                                        @foreach ($district as $dis)
                                                            <option @if ($dis->id == $apartment->ref_district_id)
                                                                selected
                                                            @endif value="{{ $dis->id }}">{{ $dis->name_in_thai }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>เลือกตำบล</label>
                                                    <select name="ref_subdistrict_id" id="select2Subdistrict" class="select2 form-select form-select-lg">
                                                        <option selected>เลือกตำบล</option>
                                                        @foreach ($subdistrict as $sub_dis)
                                                            <option @if ($sub_dis->id == $apartment->ref_subdistrict_id)
                                                                selected
                                                            @endif value="{{ $sub_dis->id }}">{{ $sub_dis->name_in_thai }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label for="" class="form-label">รหัสไปรษณีย์</label>
                                                    <input type="text" class="form-control" id=""
                                                        placeholder="รหัสไปรษณีย์" value="{{ $apartment->zipcode }}" />
                                                </div>
                                                <div class="col-sm-12">
                                                    <label for="" class="form-label">ระบุตำแหน่งหอพักของคุณ</label>
                                                    <div class="leaflet-map" id="dragMap"></div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">เบอร์โทรติดต่อหอพัก<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="" placeholder=""
                                                    value="{{ $apartment->phone }}" required />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">อีเมลติดต่อหอพัก </label>
                                                    <input type="email" class="form-control" id="" placeholder="" value="{{ $apartment->email }}" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="" class="form-label">หอพักของคุณทำบิลทุกวันที่เท่าไหร่
                                                        ?</label>
                                                    <select class="form-select">
                                                        <option value="">วันที่ 1 ของทุกเดือน</option>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for=""
                                                        class="form-label">กำหนดวันที่สิ้นสุดการชำระเงิน</label>
                                                    <select class="form-select">
                                                        <option value="">วันที่ 1 ของทุกเดือน</option>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-12 text-center">
                                                    <button type="submit" class="btn btn-main">บันทึก</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                        @include('layout/inc_footer')
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>

    <!-- / Layout wrapper -->
         @include('layout/inc_js')
    <script src="assets/vendor/libs/leaflet/leaflet.js"></script>
    <script>
        const draggableMap = L.map('dragMap').setView([48.817152, 2.455], 12);
        const markerLocation = L.marker([48.817152, 2.455], {
            draggable: 'true'
        }).addTo(draggableMap);
        markerLocation.bindPopup("<b>ที่ตั้งหอพัก</b>").openPopup();
        L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
            maxZoom: 18
        }).addTo(draggableMap);
    </script>

</body>

</html>