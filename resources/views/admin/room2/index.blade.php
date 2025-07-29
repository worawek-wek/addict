<!doctype html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
</head>
<style>
    .new_box .col-md-6 {
        padding: 5px 12px;
    }
    .table th {
        font-size: 15px;
        font-weight: bold;
        border: 1px solid black
    }
    .table td {
        padding-top: 14px;
        padding-bottom: 14px;
    }
    .modalHeadDecor .modal-header {
        padding: 0;
    }

    .modalHeadDecor .modal-title {
        padding: 1.25rem 0.5rem 1.25rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -64px;
        content: '';
        width: 0;
        height: 0;
        border-top: 67px solid #54BAB9;
        border-right: 65px solid transparent;
    }

    #pills-tablayout button {
        background: transparent;
    }

    #pills-tablayout button.active {
        color: #54BAB9 !important;
    }

    .select-floor {
        width: 100px;
    }

    .box {
        display: none;
    }

    @media screen and (min-width:1024px) {
        .col-lg5 {
            width: calc(100%/5);
        }
    }


    @media screen and (max-width:767px) {
        .select-floor {
            width: 100%;
        }
    }
</style>

<link rel="stylesheet" href="assets/vendor/libs/select2/select2.css" />
<link rel="stylesheet" href="assets/vendor/libs/bootstrap-select/bootstrap-select.css" />

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
                                    <div class="card-header">
                                        <div class="row g-3 justify-content-between">
                                            <div class="col-sm-12">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-sitemap text-main ti-xl me-2"></i>
                                                    ผังห้อง
                                                </h4>
                                            </div>
                                        <div class="col-xl-12 col-lg-5 col-12">
                                            <div class="card bg-light-danger">
                                            <div class="d-flex align-items-end row">
                                                <div class="col-7" style="line-height: 2.2;">
                                                <div class="card-body text-nowrap">
                                                    <h5 class="text-white card-title mb-0">มีห้องที่ค้างชำระ</h5>
                                                    <p class="text-white mb-2">จำนวน</p>
                                                    <div style="display: flex;align-items: center;gap: 10px;">
                                                        <h4 class="text-white mr-2 my-auto">{{ $summary['all_overdue'] }} ห้อง</h4>
                                                        <a href="dashboard/overdue" class="btn bg-label-warning text-black">รายละเอียด</a>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-5 text-center text-sm-left">
                                                <div class="card-body pb-0 px-0 px-md-4 pt-2" align="right">
                                                    <img
                                                    src="../../assets/img/illustrations/girl-with-laptop.png"
                                                    height="155"
                                                    alt="view sales" />
                                                </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-border-shadow-primary">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-2 pb-1">
                                                            <div class="avatar me-2" style="cursor: unset;">
                                                            <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-chart-pie-2 ti-md"></i></span>
                                                            </div>
                                                            <h4 class="ms-1 mb-0"><span id="percent">{{ $summary['percent'] }}</span> %</h4>
                                                        </div>
                                                        <p class="mb-1">อัตราการเข้าพัก</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-border-shadow-warning">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-2 pb-1">
                                                            <div class="avatar me-2" style="cursor: unset;">
                                                            <span class="avatar-initial rounded bg-label-info"><i class="ti ti-calendar-time ti-md"></i></span>
                                                            </div>
                                                            <h4 class="ms-1 mb-0"><span id="all_booking_room">{{ $summary['all_booking_room'] }}</span> ห้อง</h4>
                                                        </div>
                                                        <p class="mb-1">ห้องจอง</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-border-shadow-danger">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-2 pb-1">
                                                            <div class="avatar me-2" onclick="location.href='/dashboard/overdue';">
                                                            <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-report-money ti-md"></i></span>
                                                            </div>
                                                            <h4 class="ms-1 mb-0">{{ $summary['all_overdue'] }} ห้อง</h4>
                                                        </div>
                                                        <p class="mb-1">ค้างชำระ</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-3">
                                                <div class="card card-border-shadow-info">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-2 pb-1">
                                                            <div class="avatar me-2" style="cursor: unset;">
                                                            <span class="avatar-initial rounded bg-label-success"><i class="ti ti-door ti-md"></i></span>
                                                            </div>
                                                            <h4 class="ms-1 mb-0"><span id="vacant_room">{{ $summary['vacant_room'] }}</span> ห้อง</h4>
                                                        </div>
                                                        <p class="mb-1">ห้องว่าง</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                    <div class="row border-bottom border-top border-light p-3">
                                        <div class="row mt-3">
                                            <div class="col-md-3" style="padding-right: unset !important;">
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                                                    <input
                                                        name="search"
                                                        type="text"
                                                        class="form-control p_search"
                                                        placeholder="Search..."
                                                        aria-label="Search..."
                                                        oninput="loadData('{{$page_url}}/datatable')"
                                                        aria-describedby="basic-addon-search31" />
                                                  </div>
                                            </div>
                                            <div class="col-md-2 select_off" style="padding-right: unset !important;">
                                            <select onchange='loadData("{{$page_url}}/datatable")' name="building" id="selectpickerBuilding" class="select2 form-select form-select-lg p_search" data-style="btn-default">
                                                    <option value="all">ทุกตึก</option>
                                                    @foreach ($buildings as $b)
                                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                    @endforeach
                                            </select>
                                            </div>
                                            <!-- Group -->
                                            <div class="col-md-2 select_off" style="padding-right: unset !important;">
                                            <select onchange='loadData("{{$page_url}}/datatable")' name="floor" id="selectpickerFloor" class="select2 form-select form-select-lg p_search" data-style="btn-default">
                                                    <option value="all">ทุกชั้น</option>
                                                    @foreach ($floors as $f)
                                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                    @endforeach
                                            </select>
                                            </div>
                                            <div class="col-md-5 text-end" style="padding-right: unset !important;">
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;"
                                                        class="btn btn-success buttons-collection btn-warning waves-effect waves-light me-2"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false">
                                                <span>
                                                <i class="ti ti-upload"></i> ดาวน์โหลด Excel</span>
                                                </button>
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-info buttons-collection btn-info"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false" data-bs-toggle="modal" data-bs-target="#insertRenter">
                                                    <span><i class="ti ti-plus"></i> จองห้อง</span>
                                                </button>
                                                <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-success buttons-collection btn-success"
                                                        tabindex="0" aria-controls="DataTables_Table_0"
                                                        type="button" aria-haspopup="dialog"
                                                        aria-expanded="false" data-bs-toggle="modal" data-bs-target="#roomRentalContract">
                                                    <span><i class="ti ti-plus"></i> ทำสัญญาเช่า</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 mt-4">
                                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                                <label class="">Show</label>
                                                <select onchange='loadData("{{$page_url}}/datatable")' name="limit" class="form-select ms-2 me-2 p_search" style="width:100px">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <ul class="nav nav-pills" id="pills-tablayout" role="tablist">
                                                    <li class="nav-item me-1" role="presentation">
                                                        <button type="button" onclick="ch_div('pills-home')"
                                                            class="btn btn-icon btn-sm btn-label-secondary waves-effect"
                                                            id="pills-home-tab" data-bs-toggle="pill"
                                                            data-bs-target="#pills-home" type="button" role="tab"
                                                            aria-controls="pills-home" aria-selected="true">
                                                            <span class="ti ti-layout-grid ti-md"></span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button" onclick="ch_div('pills-profile')"
                                                            class="btn btn-icon btn-sm btn-label-secondary waves-effect active"
                                                            data-bs-toggle="pill" data-bs-target="#pills-profile"
                                                            type="button" role="tab" aria-controls="pills-profile"
                                                            aria-selected="false">
                                                            <span class="ti ti-list ti-md"></span>
                                                        </button>
                                                    </li>
                                                </ul>
                                                {{-- <ul class="nav nav-pills" id="pills-tablayout" role="tablist">
                                                    <li class="nav-item me-1" role="presentation">
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-label-secondary waves-effect active"
                                                            id="pills-home-tab" data-bs-toggle="pill"
                                                            data-bs-target="#pills-home" type="button" role="tab"
                                                            aria-controls="pills-home" aria-selected="true">
                                                            <span class="ti ti-layout-grid ti-md"></span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-label-secondary waves-effect"
                                                            data-bs-toggle="pill" data-bs-target="#pills-profile"
                                                            type="button" role="tab" aria-controls="pills-profile"
                                                            aria-selected="false">
                                                            <span class="ti ti-list ti-md"></span>
                                                        </button>
                                                    </li>
                                                </ul> --}}
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="tab-content p-0" id="pills-tabContent">
                                        </div>
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
    <!--set rent Modal -->
    <div class="modal fade modalHeadDecor" id="insertRenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">เพิ่มการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <form id="insert_renter">
                        @csrf
                <div class="modal-body">
                    <div class="m-2" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                        <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);;">
                            <i class="tf-icons ti ti-user text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                            ข้อมูลส่วนตัว
                        </h5>
                        <div class="row g-3 p-4 pt-1">
                            <div class="col-sm-2">
                                <label for="exampleFormControlSelect1" class="form-label">คำนำหน้า</label>
                                <select name="prefix" class="form-select" id="exampleFormControlSelect1"
                                    aria-label="Default select example">
                                    <option value="บริษัท" selected>บริษัท</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="นาง">นาง</option>
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <label for="exampleFormControlInput1" class="form-label">ชื่อจริง</label>
                                <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" />
                            </div>
                            <div class="col-sm-5">
                                <label for="exampleFormControlInput2" class="form-label">นามสกุล</label>
                                <input type="text" name="surname" class="form-control" id="exampleFormControlInput2" placeholder="" />
                            </div>
                            <div class="col-sm-6">
                                <label for="exampleFormControlInput3" class="form-label">เบอร์โทรศัพท์ (ตัวอย่าง. 0815578945)</label>
                                <input type="text" name="phone" class="form-control" id="exampleFormControlInput3" placeholder="" />
                            </div>
                            <div class="col-sm-6">
                                <label for="exampleFormControlInput4" class="form-label">หมายเลขบัตรประชาชน</label>
                                <input type="text" name="id_card_number" class="form-control" id="exampleFormControlInput4" placeholder="" />
                            </div>
                            <div class="col-sm-12">
                                <label for="exampleFormControlInput5" class="form-label">ที่อยู่ตามสำเนาทะเบียนบ้าน</label>
                                <input type="text" name="address" class="form-control" id="exampleFormControlInput5" placeholder="เลขที่ ซอย ถนน อาคาร ห้องเลขที่ หรือหมู่บ้าน" />
                            </div>
                            <div class="col-sm-4">
                                <label>เลือกจังหวัด</label>
                                <select name="ref_province_id" id="select2Basic" class="select2 form-select form-select-lg" required>
                                    <option selected disabled hidden value="">เลือกจังหวัด</option>
                                    @foreach ($province as $pro)
                                        <option value="{{ $pro->id }}">{{ $pro->name_in_thai }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>เลือกอำเภอ</label>
                                <select name="ref_district_id" id="select2District" class="select2 form-select form-select-lg" required>
                                    <option selected disabled hidden value="">เลือกอำเภอ</option>
                                    @foreach ($district as $dis)
                                        <option value="{{ $dis->id }}">{{ $dis->name_in_thai }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>เลือกตำบล</label>
                                <select name="ref_subdistrict_id" id="select2Subdistrict" class="select2 form-select form-select-lg" required>
                                    <option selected disabled hidden value="">เลือกตำบล</option>
                                    @foreach ($subdistrict as $sub_dis)
                                        <option value="{{ $sub_dis->id }}">{{ $sub_dis->name_in_thai }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="exampleFormControlInput9" class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" name="zipcode" class="form-control" id="exampleFormControlInput9" placeholder="รหัสไปรษณีย์" />
                            </div>
                            <div class="col-sm-12">
                                <label for="bs-datepicker-format" class="form-label">วันเดือนปีเกิดผู้จอง</label>
                                <input type="text" name="birthdate" value="" class="form-control" id="bs-datepicker-format" placeholder="วัน/เดือน/ปี" required/>
                            </div>
                            <div class="col-sm-6">
                                <label for="bs-datepicker-format2" class="form-label">วันที่จอง</label>
                                <input type="text" name="booking_date" value="" class="form-control" id="bs-datepicker-format2" placeholder="วัน/เดือน/ปี" required/>
                            </div>
                            <div class="col-sm-6">
                                <label for="exampleFormControlInput12" class="form-label">ช่องทางการจอง</label>
                                <input type="text" name="booking_channel" class="form-control" id="exampleFormControlInput12" placeholder="ช่องทางการจอง" value="จองโดยตรงกับที่พัก" />
                            </div>
                        </div>
                    </div>

                    <div class="m-2 mt-4" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                        <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);">
                            <i class="tf-icons ti ti-browser-plus text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                            รายการจองห้อง
                        </h5>
                        <div class="row g-3 p-4 pt-1">
                            <div class="col-sm-6">
                                <label for="exampleFormControlInput13" class="form-label">วันที่เข้าพัก</label>
                                <input type="text" name="date_stay" class="form-control" id="exampleFormControlInput13" placeholder="วัน/เดือน/ปี" value="" required/>
                            </div>
                            <div class="col-sm-7">
                                <label for="exampleFormControlInput14" class="form-label">เลือกห้อง</label>
                                <div class="accordion stick-top accordion-bordered" id="courseContent">
                                    @foreach ($buildings as $build)
                                    <!-- ตึกคุณแบม -->
                                    <div class="accordion-item
                                        @if ($buildings[0]->id == $build->id)
                                            active
                                        @endif
                                    mb-0">
                                        <div class="accordion-header" id="headingOne{{ $build->id }}">
                                            <button type="button" class="accordion-button bg-lighter rounded-0" data-bs-toggle="collapse" data-bs-target="#chapterOne{{ $build->id }}" aria-expanded="true" aria-controls="chapterOne{{ $build->id }}">
                                                <span class="d-flex flex-column">
                                                    <span style="font-size: medium;font-weight: 430">{{ $build->name }}</span>
                                                </span>
                                            </button>
                                        </div>
                                        <div id="chapterOne{{ $build->id }}" class="accordion-collapse collapse
                                            @if ($buildings[0]->id == $build->id)
                                                show
                                            @endif
                                            " data-bs-parent="#courseContent">
                                            <div class="accordion-body py-3 border-top">
                                                <div class="form-check align-items-center mb-3">
                                                    <div class="accordion stick-top accordion-bordered" id="courseContent2">
                                                        <!-- ชั้นมาทำอะไรที่นี่ -->
                                                    @foreach ($build->floor as $floor)
                                                        <div class="accordion-item 
                                                            @if ($buildings[0]->id == $build->id && $build->floor[0]->id == $floor->id)
                                                                active
                                                            @endif
                                                            mb-0">
                                                            <div class="accordion-header" id="headingOne{{ $build->id }}2">
                                                                <button type="button" class="accordion-button bg-lighter rounded-0" data-bs-toggle="collapse" data-bs-target="#chapterOne{{ $floor->id }}1" aria-expanded="true" aria-controls="chapterOne{{ $floor->id }}1">
                                                                    <span class="d-flex flex-column">
                                                                        <span class="me-2" style="font-size: medium;font-weight: 430">{{ $floor->name }}</span>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                            <div id="chapterOne{{ $floor->id }}1" class="accordion-collapse collapse
                                                                @if ($buildings[0]->id == $build->id && $build->floor[0]->id == $floor->id)
                                                                show
                                                                @endif"
                                                                data-bs-parent="#courseContent2">
                                                                <div class="accordion-body py-3 border-top">

                                                                @foreach ($floor->room as $room)

                                                                    <div class="form-check d-flex align-items-center mb-1">
                                                                        <input name="buildings[{{ $build->id }}][{{ $floor->id }}][]" value="{{ $room->id }}" class="form-check-input room-selected" type="checkbox" id="{{ $room->id }}" onchange="room_selected()"
                                                                        @if (in_array($room->id, @$room_check))
                                                                            disabled
                                                                        @endif
                                                                        />
                                                                        <label for="{{ $room->id }}" class="form-check-label ms-3">
                                                                            <span class="mb-0 h6">{{ $room->name }} 
                                                                                @if (in_array($room->id, @$room_check))
                                                                                    (ติดจอง)
                                                                                @endif
                                                                            </span>
                                                                        </label>
                                                                    </div>

                                                                @endforeach
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="col-sm-5">
                                <div class="accordion-body py-3 mt-3">
                                    <div class="ms-4" id="room-selected">
                                        @include('room/selected')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="m-2 mt-4" style="border: 1px solid #dbdbdb;border-radius: 5px;">
                        <h5 class="border-bottom p-2" style="background-color: rgb(255, 248, 237);">
                            <i class="tf-icons ti ti-device-ipad-dollar text-main" style="font-size: 25px;vertical-align: baseline;"></i>
                            รายละเอียดการชำระเงิน
                        </h5>
                        <div class="row g-3 p-4 pt-1">
                            <div class="col-sm-12">
                                <label for="exampleFormControlInput30" class="form-label">ค่ามัดจำ (บาท)</label>
                                <input type="text" name="deposit" class="form-control" id="exampleFormControlInput30" placeholder="" />
                            </div>
                            <div class="col-sm-12">
                                <div>
                                    <label for="exampleFormControlInput31" class="form-label">วิธีการชำระเงิน</label>
                                </div>
                                <div class="ms-3">
                                <input
                                    name="payment_method"
                                    class="form-check-input"
                                    type="radio"
                                    value="1"
                                    id="defaultRadio1"
                                    checked />
                                <label class="form-check-label" for="defaultRadio1">&nbsp; เงินสด </label>
                                <input
                                    name="payment_method"
                                    class="form-check-input ms-2"
                                    type="radio"
                                    value="2"
                                    id="defaultRadio2" />
                                <label class="form-check-label" for="defaultRadio2">&nbsp; โอนเงิน </label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label for="exampleFormControlInput33" class="form-label">วันที่รับชำระเงิน</label>
                                <input type="text" name="payment_received_date" class="form-control" id="exampleFormControlInput33" placeholder="วัน/เดือน/ปี" value="" required/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-main">บันทึก</button>
                </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade modalHeadDecor" id="roomRentalContract" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">ทำสัญญาเช่าหลายห้อง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="insert_contract">
                    @csrf
                    <div class="modal-body">
                        <div class="p-2">
                            <label class="h5 mb-1">เลือกข้อมูลจากผู้เช่า</label>
                                <select name="ref_renter_id" id="select2Renter" class="select2 form-select form-select-lg" onchange="get_room_rental_contract(this.value)" required>
                                    <option selected disabled hidden value="no">เลือกข้อมูลจากผู้เช่า</option>
                                    @foreach ($renter as $rent)
                                        <option {{$rent->contracts_id}} value="{{ $rent->id }}">{{ $rent->prefix.' '.$rent->name.' '.$rent->surname }}</option>
                                    @endforeach
                                </select>
                                
                        </div>
                        <div id="room-rental-contract">

                        </div>
                    </div>
                    <div class="modal-footer rounded-0 justify-content-center">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-main">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade modalHeadDecor" id="insurance" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="view">
            
        </div>
    </div>

    {{-- ///////////////////////////////////////////////////////////// --}}
    @include('room/add-renter')
    {{-- ///////////////////////////////////////////////////////////// --}}


    <!-- / Layout wrapper -->
    @include('layout/inc_js')
    <script src="assets/vendor/libs/select2/select2.js"></script>
    <script src="assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>
    <script src="assets/js/forms-selects.js"></script>
    <script>
        
        var page = "{{$page_url}}/datatable";
        var searchData = {};
        loadData(page);
        
        var ch = "pills-profile";
        function ch_div(id_ch){
            ch = id_ch;
        }

        function view(id){
            $.ajax({
                type: "GET",
                url: "{{$page_url}}/"+id,
                success: function(data) {
                    $("#view").html(data);
                    $('#select2District').select2('destroy');
                }
            });
        }
        function get_room_rental_contract(id){
            $.ajax({
                type: "GET",
                url: "{{ $page_url }}/get-room-rental-contract/"+id,
                success: function(data) {
                    $("#room-rental-contract").html(data);
                }
            });
        }
        function loadData(pages){
            
            $('.p_search').each(function() {
                var inputName = $(this).attr('name'); // ดึงชื่อ attribute 'name' ของ input
                var inputValue = $(this).val(); // ดึงค่า value ของ input
                
                searchData[inputName] = inputValue; // เก็บข้อมูลลงในออบเจ็กต์ searchData
            });

            // alert(page);
            page = pages;
            $.ajax({
                type: "GET",
                url: pages,
                data: searchData,
                success: function(data) {
                    $("#pills-tabContent").html(data);
                    $('#'+ch).addClass('active');
                    
                    // $("#table-data").html(data);
                }
            });
            // alert(page);
        }
        function summary(){
            // alert(page);
            $.ajax({
                type: "GET",
                url: 'room/summary',
                success: function(data) {
                    // $("#pills-tabContent").html(data);
                    $('#all_booking_room').html(data.all_booking_room);
                    $('#vacant_room').html(data.vacant_room);
                    // $('#').html(data.);
                    $('#percent').html(data.percent);
                    
                    // $("#table-data").html(data);
                }
            });
            // alert(page);
        }
        function room_selected() {
             $.ajax({
                url: '/room/selected', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                type: 'get',
                data: $('#insert_renter .room-selected:checked').serialize(),
                success: function(response) {
                        $("#room-selected").html(response);
                        // Swal.fire('เพิ่มการจองเรียบร้อยแล้ว', '', 'success');
                        // $('#addserviceModal').modal('hide');
                        // setTimeout(function() {
                        //   location.reload();
                        // }, 4000);
                },
                error: function(error) {
                    Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    console.error('เกิดข้อผิดพลาด:', error);
                }
            });
        };
        $('#select2District').select2();
        $('#select2Subdistrict').select2();
        
        $('#insert_renter').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            if($('#check_selected').val() == 0){
                return Swal.fire('! โปรดเลือกห้องเช่า', '', 'warning');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่ม การจอง หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                showDenyButton: false,
                didOpen: () => {
                    // โฟกัสที่ปุ่ม confirm
                    Swal.getConfirmButton().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/room', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if(response == true){
                                $('#insertRenter').modal('hide');
                                $('#insert_renter')[0].reset();
                                loadData(page);
                                summary();
                                Swal.fire('เพิ่มการจองเรียบร้อยแล้ว', '', 'success');
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('ยกเลิกการดำเนินการ', '', 'info');
                }
            });
        });
        
        $('#insert_contract').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่ม สัญญา หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                showDenyButton: false,
                didOpen: () => {
                    // โฟกัสที่ปุ่ม confirm
                    Swal.getConfirmButton().focus();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/room/insert_contract', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if(response == true){
                                $('#roomRentalContract').modal('hide');
                                $('#insert_contract')[0].reset();
                                loadData(page);
                                summary();
                                Swal.fire('เพิ่มสัญญาเรียบร้อยแล้ว', '', 'success').then((result) => {
                                    location.reload();
                                });
                                // $('#room-rental-contract').html('');
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                } else if (result.isDismissed) {
                    // Swal.fire('ยกเลิกการดำเนินการ', '', 'info');
                }
            });
        });
        
        $(document).ready(function() {
            $('#select2Basic').change(function() {
                var provinceId = $(this).val();
                
                // เคลียร์ dropdown สำหรับอำเภอและตำบล
                $('#select2District').empty().append('<option selected disabled hidden value="">เลือกอำเภอ</option>');
                $('#select2Subdistrict').empty().append('<option selected disabled hidden value="">เลือกตำบล</option>');

                if (provinceId) {
                    $.ajax({
                        url: '/get-districts/' + provinceId,
                        type: 'GET',
                        success: function(data) {
                            data.forEach(function(district) {
                                $('#select2District').append('<option value="' + district.id + '">' + district.name_in_thai + '</option>');
                            });
                        }
                    });
                }
            });

            $('#select2District').change(function() {
                var districtId = $(this).val();
                
                // เคลียร์ dropdown สำหรับตำบล
                $('#select2Subdistrict').empty().append('<option selected disabled hidden value="">เลือกตำบล</option>');

                if (districtId) {
                    $.ajax({
                        url: '/get-subdistricts/' + districtId,
                        type: 'GET',
                        success: function(data) {
                            data.forEach(function(subdistrict) {
                                $('#select2Subdistrict').append('<option value="' + subdistrict.id + '">' + subdistrict.name_in_thai + '</option>');
                            });
                        }
                    });
                }
            });

            $('#selectpickerBuilding').change(function() {
                var building = $(this).val();
                
                // เคลียร์ dropdown สำหรับตำบล
                $('#selectpickerFloor').empty().append('<option value="all">ทุกชั้น</option>');

                if (building) {
                    $.ajax({
                        url: '/get-floors/' + building,
                        type: 'GET',
                        success: function(data) {
                            data.forEach(function(floor) {
                                $('#selectpickerFloor').append('<option value="' + floor.id + '">' + floor.name + '</option>');
                            });
                        }
                    });
                }
            });
        });
        
        $('#bs-datepicker-format').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
        $('#bs-datepicker-format2').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
        $('#exampleFormControlInput13').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });
        $('#exampleFormControlInput33').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true,      // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true  // ไฮไลต์วันที่ปัจจุบัน
        });

    </script>
    
    {{-- <script>
                                                
        var modalElement = document.getElementById('roomRentalContract');
        var insuranceModal = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('hidden.bs.modal', function () {
            // console.log("Modal is hidden");
            document.querySelector('.select_off').style.display = 'block';
        });
        modalElement.addEventListener('shown.bs.modal', function () {
            // console.log("Modal is hidden");
            document.querySelector('.select_off').style.display = 'none';
        });
        </script> --}}
</body>

</html>
