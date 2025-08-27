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
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        @include('admin/layout/inc_sidemenu')
        <div class="layout-page">
            @include('admin/layout/inc_topmenu')
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    <div class="row ">
                        <div class="col-sm-12">
                            <div class="card mb-3">
                                <div class="card-header border-bottom border-bottom">
                                    <div class="row g-3 justify-content-between">
                                        <div class="col-sm-12">
                                            <h4 class="mb-0">
                                                <i class="tf-icons ti ti-copy text-main ti-md me-2"></i>
                                                บุคลากร
                                            </h4>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text" id="basic-addon-search31">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input oninput='loadData("{{ $page_url }}/datatable")'
                                                           name="search" type="text" class="form-control p_search"
                                                           placeholder="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-label="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-describedby="basic-addon-search31" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="tab-content p-0">
                                        <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                                            <div class="row p-3">
                                                <div class="col-lg-2">
                                                    <div class="d-flex align-items-center mb-2 mb-md-0">
                                                        <label class="">Show</label>
                                                        <select onchange='loadData("{{ $page_url }}/datatable")'
                                                                name="limit" class="form-select ms-2 me-2 p_search" style="width:100px">
                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="15">15</option>
                                                            <option value="20">20</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <select onchange='loadData("{{ $page_url }}/datatable")'
                                                            name="ref_branch_id" id="select2Position2"
                                                            class="select2 form-select form-select-lg p_search"
                                                            data-allow-clear="true">
                                                        @if (Auth::user()->work_status == 3)
                                                            <option value="all">สาขา</option>
                                                        @endif
                                                        @foreach ($branch as $bra)
                                                            <option value="{{ $bra->id }}">{{ $bra->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <select onchange='loadData("{{ $page_url }}/datatable")'
                                                            name="ref_position_id" id="select2Position3"
                                                            class="select2 form-select form-select-lg p_search"
                                                            data-allow-clear="true">
                                                        <option value="all">ตำแหน่ง</option>
                                                        @foreach ($position as $pos)
                                                            <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5 flex text-end" style="padding-right: unset !important;">
                                                    <button style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                            class="btn btn-success buttons-collection btn-info waves-effect waves-light"
                                                            type="button"
                                                            data-bs-toggle="modal" data-bs-target="#addserviceModal">
                                                        <span><i class="ti ti-plus"></i> เพิ่มพนักงาน</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="card-body px-0 pt-0">
                                                <div class="tab-content p-0" id="pills-tabContent">
                                                    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel">
                                                        <div id="table-data">
                                                            {{-- ตารางอยู่ตรงนี้นะจ๊ะ --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @include('admin/layout/inc_footer')
                <div class="content-backdrop fade"></div>
            </div>
            </div>
        </div>

    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>


<div class="modal fade modalHeadDecor" id="addserviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header rounded-0">
                <h5 class="modal-title">เพิ่มพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="insert_user" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 p-4">
                        <div class="col-sm-6">
                            <label class="form-label">สาขา *</label><br>
                            @foreach ($branch as $bra)
                                <input class="form-check-input" type="radio" name="ref_branch_id"
                                       id="add-branch{{ $bra->id }}" value="{{ $bra->id }}"
                                       {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label me-4" for="add-branch{{ $bra->id }}">
                                    {{ $bra->name }}
                                </label>
                            @endforeach
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">บัตรพนักงาน *</label>
                            <input name="user_code" type="text" class="form-control" id="user_code" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ชื่อพนักงาน *</label>
                            <input name="name" type="text" class="form-control" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ชื่อเล่น *</label>
                            <input name="nickname" type="text" class="form-control" required />
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label">ตำแหน่ง</label>
                            <select name="ref_position_id" id="select2Position1"
                                    class="select2 form-select form-select-lg" data-allow-clear="true">
                                @foreach ($position as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- เพิ่มช่องสำหรับกรอกเงินเดือนตรงนี้ --}}
                        <div class="col-sm-6" id="salary-field" style="display: none;">
                            <label class="form-label">ราคานวด *</label>
                            <input name="salary" type="number" class="form-control" />
                        </div>

                        <div class="col-sm-10 mt-3">
                            <label>รูปภาพ</label>
                            <input type="file" name="image_name" class="form-control mb-2" id="paymentReceipt">
                            <div class="preview-container">
                                <img id="preview1" src="" style="display: none; width:30%">
                            </div>
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
        {{-- Content จะโหลดจาก AJAX --}}
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

    function delete_view(id, v, element) {
        $(element).prop('checked', v === 1 ? false : true);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการลบพนักงานหรือไม่?',
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
                            Swal.fire('ลบพนักงานเรียบร้อยแล้ว', '', 'success');
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

    $('#insert_user').on('submit', function (event) {
        event.preventDefault();
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }
        var formData = new FormData(this);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเพิ่มพนักงานหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => Swal.getConfirmButton().focus()
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
                            Swal.fire('เพิ่มพนักงานเรียบร้อยแล้ว', '', 'success');
                            $('#addserviceModal').modal('hide');
                            loadData(page);
                        }
                    },
                    error: function () {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                    }
                });
            }
        });
    });

    $('#user_code').on('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Preview Image
    function handleFileInput(fileInputId, previewId) {
        const fileInput = document.getElementById(fileInputId);
        const previewImage = document.getElementById(previewId);
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
            }
        });
    }
    handleFileInput('paymentReceipt', 'preview1');

    // Select2 fix
    $(document).ready(function () {
        $('#select2Position1').select2({ dropdownParent: $('#addserviceModal') });
        $('#select2Position2').select2({ dropdownParent: $('.card-body') });
        $('#select2Position3').select2({ dropdownParent: $('.card-body') });

        // Add change event listener for the position select
        $('#select2Position1').on('change', function () {
            toggleSalaryInput($(this).val());
        });

        // Initial check on page load
        toggleSalaryInput($('#select2Position1').val());
    });

    // Function to show/hide the salary field
    function toggleSalaryInput(positionId) {
        const salaryField = $('#salary-field');
        const salaryInput = $('input[name="salary"]');

        if (positionId === '2') {
            salaryField.show();
            salaryInput.prop('required', true); // Make the field required
        } else {
            salaryField.hide();
            salaryInput.prop('required', false);
            salaryInput.val(''); // Clear the value when hidden
        }
    }
</script>

</body>
</html>
