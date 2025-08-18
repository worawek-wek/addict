<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>ลูกค้า - CRM</title>
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
                                                    ลูกค้า
                                                </h4>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                                        <input oninput='loadData("{{ $page_url }}/datatable")'
                                                            name="search" type="text" class="form-control p_search"
                                                            placeholder="ค้นหา..." />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
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
                                            <div class="col-md-8 text-end">
                                                <button class="btn btn-warning me-2">
                                                    <i class="ti ti-upload"></i> ดาวน์โหลด Excel
                                                </button>
                                                <button class="btn btn-main" data-bs-toggle="modal"
                                                    data-bs-target="#addserviceModal">
                                                    <i class="ti ti-plus"></i> เพิ่มลูกค้า
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body px-0 pt-0">
                                            <div id="table-data"><!-- ตารางโหลดด้วย AJAX --></div>
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

    {{-- Modal: Add Customer --}}
    <div class="modal fade modalHeadDecor" id="addserviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">เพิ่มลูกค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="insert_user" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 p-4">
                            <div class="col-sm-12">
                                <label class="form-label">สาขา</label><span class="text-danger">*</span><br>
                                @foreach ($branches as $branch)
                                    <input class="form-check-input" type="radio" name="ref_branch_id"
                                        value="{{ $branch->id }}">
                                    <label class="form-check-label me-4">{{ $branch->name }}</label>
                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">ชื่อลูกค้า</label><span class="text-danger">*</span>
                                <input name="name" type="text" class="form-control" required />
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">เบอร์โทร</label>
                                <input name="phone" type="text" class="form-control" />
                            </div>
                            <div class="col-sm-12">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea name="remark" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-main">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Customer --}}
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลลูกค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="edit_customer_form">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <div class="modal-body row g-3">
                        {{-- <div class="col-md-6">
                            <label class="form-label">ชื่อ</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div> --}}
                        <div class="col-md-6">
                            <label class="form-label">ชื่อจริง</label>
                            <input type="text" name="first_name" id="edit_first_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล</label>
                            <input type="text" name="last_name" id="edit_last_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เลขบัตรประชาชน</label>
                            <input type="text" name="id_card" id="edit_id_card" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">สาขา</label>
                            <select name="ref_branch_id" id="edit_branch" class="form-select">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin/layout/inc_js')
    <script>
        var page = "{{ $page_url }}/datatable";
        var searchData = {};
        loadData(page);

        function loadData(pages) {
            $('.p_search').each(function() {
                searchData[$(this).attr('name')] = $(this).val();
            });
            $.get(pages, searchData, function(data) {
                $("#table-data").html(data);
            });
        }

        function editCustomer(id) {
            $.ajax({
                url: "{{ $page_url }}/" + id,
                type: "GET",
                success: function(data) {
                    $('#customer_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_first_name').val(data.first_name);
                    $('#edit_last_name').val(data.last_name);
                    $('#edit_phone').val(data.phone);
                    $('#edit_id_card').val(data.id_card);
                    $('#edit_branch').val(data.ref_branch_id);
                },
                error: function(err) {
                    Swal.fire('ไม่สามารถโหลดข้อมูลลูกค้าได้', '', 'error');
                }
            });
        }

        // ✅ Insert
        $('#insert_user').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.post({
                url: '{{ $page_url }}', // POST /admin/customer
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res === true) {
                        $('#insert_user')[0].reset();
                        $('#addserviceModal').modal('hide');
                        Swal.fire('เพิ่มลูกค้าสำเร็จ', '', 'success');
                        loadData(page);
                    }
                }
            });
        });

        // ✅ Update
        $('#edit_customer_form').on('submit', function(e) {
            e.preventDefault();
            var id = $('#customer_id').val();
            $.ajax({
                url: `{{ $page_url }}/${id}`, // PUT /admin/customer/{id}
                method: 'PUT',
                data: $(this).serialize(),
                success: function(res) {
                    if (res === true) {
                        $('#editCustomerModal').modal('hide');
                        Swal.fire('แก้ไขเรียบร้อย', '', 'success');
                        loadData(page);
                    }
                }
            });
        });

        function lockCustomer(id) {
            Swal.fire({
                title: 'ยืนยันระงับบัญชี?',
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`{{ $page_url }}/${id}/lock`, {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('ระงับแล้ว', '', 'success');
                            loadData(page);
                        }
                    });
                }
            });
        }

        function unlockCustomer(id) {
            Swal.fire({
                title: 'ยืนยันเปิดใช้งาน?',
                icon: 'info',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`{{ $page_url }}/${id}/unlock`, {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('เปิดใช้งานแล้ว', '', 'success');
                            loadData(page);
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>
