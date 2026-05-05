<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
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
        @include('admin/layout/inc_sidemenu')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            @include('admin/layout/inc_topmenu')
            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row ">
                        <div class="col-sm-12">
                            <div class="card mb-3">
                                <div class="card-header border-bottom border-bottom">
                                    <div class="row g-3">
                                    {{-- <div class="row g-3 justify-content-between"> --}}
                                        <div class="col-sm-12">
                                            <h4 class="mb-0">
                                                <i class="tf-icons ti ti-copy text-main ti-md me-2"></i>
                                                รูปแบบห้อง
                                            </h4>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="select-branch">สาขา</label>
                                            <select name="ref_branch_id" id="select-branch" class="p_search"
                                                    onchange='loadData("{{ $page_url }}/datatable")' required>
                                                @if (Auth::user()->work_status == 3)
                                                    <option value="">ทั้งหมด</option>
                                                @endif
                                                @foreach ($branch as $bra)
                                                    <option value="{{ $bra->id }}">{{ $bra->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="col-sm-3">
                                            <label for="select-branch">ห้อง</label>
                                            <select name="ref_room_id" id="select-room" class="p_search"
                                                    onchange='loadData("{{ $page_url }}/datatable")' required>
                                                    <option value="">ทั้งหมด</option>
                                                @foreach ($room as $bra)
                                                    <option value="{{ $bra->id }}">{{ $bra->name }}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}

                                        <div class="col-sm-6">
                                            <label for="select-branch">ค้นหาคีเวิร์ดที่ต้องการ</label>
                                            <div class="row">
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text" id="basic-addon-search31">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input name="search" type="text"
                                                           class="form-control p_search"
                                                           placeholder="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-label="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-describedby="basic-addon-search31"
                                                           oninput='loadData("{{ $page_url }}/datatable")' />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <div class="tab-pane fade show active" id="navs-pills-top-home"
                                             role="tabpanel">
                                            <div class="row p-3">
                                                <div class="col-lg-4">
                                                    <div class="d-flex align-items-center mb-2 mb-md-0">
                                                        <label class="">Show</label>
                                                        <select onchange='loadData("{{ $page_url }}/datatable")'
                                                                name="limit" class="form-select ms-2 me-2 p_search"
                                                                style="width:100px">
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50" selected >50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 flex text-end"
                                                     style="padding-right: unset !important;">

                                                    <button
                                                        style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                        class="btn btn-success buttons-collection  btn-info waves-effect waves-light"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addserviceModal">
                                                        <span><i class="ti ti-plus"></i> เพิ่มรูปแบบห้อง</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body px-0 pt-0">
                                                <div id="table-data">
                                                    {{-- ตารางจะถูกโหลดด้วย ajax --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- / Content -->

                <!-- Footer -->
                @include('admin/layout/inc_footer')
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>

<!-- Modal Add Room -->
<div class="modal fade modalHeadDecor" id="addserviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header rounded-0">
                <h5 class="modal-title">&nbsp;เพิ่มรูปแบบห้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="insert_room_type" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 p-4">
                        {{-- <div class="col-sm-12">
                            <label class="form-label">ห้อง <span class="text-danger">*</span></label>
                            <select name="ref_room_id" id="select2Basic" class="">
                                <option selected disabled hidden value="">เลือกห้อง</option>
                                @foreach ($room as $ro)
                                    <option value="{{ $ro->id }}">{{ $ro->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div class="col-sm-6">
                            <label class="form-label">ชื่อรูปแบบห้อง *</label>
                            <input name="name" type="text" class="form-control" placeholder="ชื่อรูปแบบห้อง" required />
                        </div>

                        <div class="w-100"></div>
                        @foreach ($course as $course_item)
                            <div class="col-sm-4">
                                <label class="form-label">{{ $course_item->name }} *</label>
                                <input name="course[{{$course_item->id}}][price]" type="number" class="form-control"
                                    placeholder="{{ $course_item->name }}" required />
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label">ค่ามือ</label>
                                <input name="course[{{$course_item->id}}][commission]" type="number" class="form-control"
                                    placeholder="ค่ามือ" />
                            </div>

                            <div class="col-sm-4">
                                <label class="form-label">คูปอง</label>
                                <input name="course[{{$course_item->id}}][coupon]" type="number" class="form-control"
                                    placeholder="คูปอง" />
                            </div>
                        @endforeach

                        {{-- <div class="col-sm-4">
                            <label class="form-label">ราคา 60 นาที/บริการ *</label>
                            <input name="sixty_minutes" type="number" class="form-control"
                                   placeholder="ราคา 60 นาที/บริการ" required />
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">ค่ามือ</label>
                            <input name="commission_sixty_minutes" type="number" class="form-control"
                                   placeholder="ค่ามือ" />
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">คูปอง</label>
                            <input name="coupon_sixty_minutes" type="number" class="form-control"
                                   placeholder="คูปอง" />
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">ราคา 90 นาที/บริการ *</label>
                            <input name="ninety_minutes" type="number" class="form-control"
                                   placeholder="ราคา 90 นาที/บริการ" required />
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">ค่ามือ</label>
                            <input name="commission_ninety_minutes" type="number" class="form-control"
                                   placeholder="ค่ามือ" />
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">คูปอง</label>
                            <input name="coupon_ninety_minutes" type="number" class="form-control"
                                   placeholder="คูปอง" />
                        </div> --}}

                        <div class="col-sm-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="remark" class="form-control"></textarea>
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

<div class="modal fade modalHeadDecor" id="insurance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="view">
    </div>
</div>

@include('admin/layout/inc_js')

<script>
    var page = "{{ $page_url }}/datatable";
    var searchData = {};
    loadData(page);

    function loadData(pages) {
        $('.p_search').each(function () {
            var inputName = $(this).attr('name');
            var inputValue = $(this).val();
            searchData[inputName] = inputValue;
        });

        page = pages;
        $.ajax({
            type: "GET",
            url: pages,
            data: searchData,
            success: function (data) {
                $("#table-data").html(data);
            }
        });
    }

    function view(id) {
        $.ajax({
            type: "GET",
            url: "{{ $page_url }}/" + id,
            success: function (data) {
                $("#view").html(data);
            }
        });
    }

    function changeStatus(id, v, element) {
        $(element).prop('checked', v === 1 ? false : true);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเปลี่ยนสถานะหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => Swal.getConfirmButton().focus()
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ $page_url }}/change-status/' + id,
                    type: 'POST',
                    data: { ref_status_id: v, _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response == true) {
                            Swal.fire('เปลี่ยนสถานะเรียบร้อยแล้ว', '', 'success');
                            loadData(page);
                        }
                    },
                    error: function () {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    }
                });
            }
        });
    }

    function changeFrontStatus(id, v, element) {
        $(element).prop('checked', v === 1 ? false : true);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเปลี่ยนสถานะหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => Swal.getConfirmButton().focus()
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ $page_url }}/change-status/' + id,
                    type: 'POST',
                    data: { ref_front_status_id: v, _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response == true) {
                            Swal.fire('เปลี่ยนสถานะเรียบร้อยแล้ว', '', 'success');
                            loadData(page);
                        }
                    },
                    error: function () {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    }
                });
            }
        });
    }

    function updateSort(el) {
        // return alert(v);
        let id       = el.dataset.id;
        let oldSort  = el.dataset.old;   // ค่าเดิม
        let newSort  = el.value;          // ค่าใหม่
        if(newSort == ''){
            return loadData(page);
        }
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเปลี่ยนลำดับหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => Swal.getConfirmButton().focus()
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ $page_url }}/update-sort/' + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        old_sort: oldSort,
                        new_sort: newSort
                    },
                    success: function (response) {
                        if (response == true) {
                            Swal.fire('เปลี่ยนลำดับเรียบร้อยแล้ว', '', 'success');
                            loadData(page);
                        }
                    },
                    error: function () {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    }
                });
            }
        });
    }

    $('#insert_room_type').on('submit', function (event) {
        event.preventDefault();

        if (!this.checkValidity()) {
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }

        var formData = new FormData(this);

        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเพิ่มรูปแบบห้องหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => {
                Swal.getConfirmButton().focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ $page_url }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response == true) {
                            $('#insert_room_type')[0].reset();
                            Swal.fire('เพิ่มรูปแบบห้องเรียบร้อยแล้ว', '', 'success');
                            $('#addserviceModal').modal('hide');
                            loadData(page);
                        }
                    },
                    error: function (error) {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            }
        });
    });
    
    function Delete(id, v, element) {
        $(element).prop('checked', v === 1 ? false : true);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการลบรูปแบบห้องหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => Swal.getConfirmButton().focus()
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ $page_url }}/' + id,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if (response == true) {
                            Swal.fire('ลบรูปแบบห้องเรียบร้อยแล้ว', '', 'success');
                            loadData(page);
                        }
                    },
                    error: function () {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    }
                });
            }
        });
    }
    new TomSelect("#select-branch", {
                        create: false,
                        maxItems: 1,
                        allowEmptyOption: true,
                        sortField: { field: "text", direction: "asc" }
                    });
    new TomSelect("#select-room", {
                        create: false,
                        maxItems: 1,
                        allowEmptyOption: true,

                        openOnFocus: true,   // โฟกัสแล้วโชว์ทั้งหมด
                        preload: true,       // โหลด option ทั้งหมดตั้งแต่แรก

                        // ให้ทุก option แสดงเสมอ (ไม่โดนซ่อนโดย score)
                        score: function(search) {
                            if (!search) {
                                return function() {
                                    return 1;
                                };
                            }

                            // ถ้ามีการค้นหา ให้ใช้ scoring ปกติ
                            return this.getScoreFunction(search);
                        },

                        sortField: [] // ไม่เรียง
    });
    new TomSelect("#select2Basic", {
                        create: false,
                        maxItems: 1,
                        allowEmptyOption: true,
                        sortField: { field: "text", direction: "asc" }
                    });
</script>
</body>
</html>
