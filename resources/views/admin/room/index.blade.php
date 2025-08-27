<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
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
                                    <div class="row g-3 justify-content-between">
                                        <div class="col-sm-12">
                                            <h4 class="mb-0">
                                                <i class="tf-icons ti ti-copy text-main ti-md me-2"></i>
                                                ห้อง
                                            </h4>
                                        </div>
                                        <div class="col-sm-3">
                                            <select name="ref_branch_id" class="form-select p_search"
                                                    onchange='loadData("{{ $page_url }}/datatable")' required>
                                                @if (Auth::user()->work_status == 3)
                                                    <option value="">ทั้งหมด</option>
                                                @endif
                                                @foreach ($branch as $bra)
                                                    <option value="{{ $bra->id }}">{{ $bra->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-9">
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
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="15">15</option>
                                                            <option value="20">20</option>
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
                                                        <span><i class="ti ti-plus"></i> เพิ่มห้อง</span>
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
                <h5 class="modal-title">&nbsp;เพิ่มห้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="insert_user" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 p-4">
                        <div class="col-sm-12">
                            <label class="form-label">สาขา *</label><br>
                            @foreach ($branch as $bra)
                                <input class="form-check-input" type="radio" name="ref_branch_id"
                                       id="branch{{ $bra->id }}" value="{{ $bra->id }}"
                                       {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label me-4" for="branch{{ $bra->id }}">
                                    {{ $bra->name }}
                                </label>
                            @endforeach
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ชื่อห้อง *</label>
                            <input name="name" type="text" class="form-control" placeholder="ชื่อห้อง" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ราคา 40 นาที/บริการ *</label>
                            <input name="forty_minutes" type="number" step="0.01" class="form-control"
                                   placeholder="ราคา 40 นาที/บริการ" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ราคา 60 นาที/บริการ *</label>
                            <input name="sixty_minutes" type="number" step="0.01" class="form-control"
                                   placeholder="ราคา 60 นาที/บริการ" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ราคา 90 นาที/บริการ *</label>
                            <input name="ninety_minutes" type="number" step="0.01" class="form-control"
                                   placeholder="ราคา 90 นาที/บริการ" required />
                        </div>

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

    $('#insert_user').on('submit', function (event) {
        event.preventDefault();

        if (!this.checkValidity()) {
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }

        var formData = new FormData(this);

        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเพิ่มห้องหรือไม่?',
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
                            $('#insert_user')[0].reset();
                            Swal.fire('เพิ่มห้องเรียบร้อยแล้ว', '', 'success');
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
</script>
</body>
</html>
