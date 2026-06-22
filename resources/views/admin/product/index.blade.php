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

    .product-image-uploader {
        max-width: 220px;
        margin: 0 auto;
    }

    .product-image-preview {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 6px;
        background: #f3f4f6;
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
                                                    สินค้า
                                                </h4>
                                            </div>
                                            <div class="col-sm-3">
                                                <select name="ref_branch_id" class="form-select p_search"
                                                    onchange='loadData("{{ $page_url }}/datatable")' required>
                                                    @if (Auth::id() == 1)
                                                        <option value="">ทั้งหมด</option>
                                                    @endif
                                                    @foreach ($branch as $bra)
                                                        <option value="{{ $bra->id }}"
                                                            @if (Auth::user()->ref_branch_id == $bra->id) selected @endif>
                                                            {{ $bra->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text" id="basic-addon-search31"><i
                                                                class="ti ti-search"></i></span>
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
                                                                <option value="20">20</option>
                                                                <option value="50">50</option>
                                                                <option value="100" selected>100</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 flex text-end"
                                                        style="padding-right: unset !important;">

                                                        <button
                                                            style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                            class="btn btn-warning buttons-collection waves-effect waves-light"
                                                            tabindex="0" aria-controls="DataTables_Table_0"
                                                            type="button" aria-haspopup="dialog" aria-expanded="false"
                                                            data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                                            <span><i class="ti ti-package-export"></i> เบิกสินค้า</span>
                                                        </button>
                                                        <button
                                                            style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                            class="btn buttons-collection  btn-info waves-effect waves-light"
                                                            tabindex="0" aria-controls="DataTables_Table_0"
                                                            type="button" aria-haspopup="dialog" aria-expanded="false"
                                                            data-bs-toggle="modal" data-bs-target="#addserviceModal">
                                                            <span><i class="ti ti-plus"></i> เพิ่มสินค้า</span>
                                                        </button>
                                                        <button
                                                            style="padding-right: 14px;padding-left: 14px;margin-right: 0px;"
                                                            class="btn buttons-collection btn-primary waves-effect waves-light"
                                                            type="button" data-bs-toggle="modal"
                                                            data-bs-target="#manageProductTypeModal"
                                                            onclick="loadProductTypes()">
                                                            <span><i class="ti ti-settings"></i> ประเภทสินค้า</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body px-0 pt-0">
                                                    <div class="tab-content p-0" id="pills-tabContent">
                                                        <div class="tab-pane fade show active" id="pills-profile"
                                                            role="tabpanel" aria-labelledby="pills-profile-tab"
                                                            tabindex="0">

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

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!--add service  Modal -->

    <div class="modal fade modalHeadDecor" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">&nbsp;เบิกสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="withdraw_product" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 p-4">

                            <div class="col-sm-12">
                                <div class="product-image-uploader">
                                    <div class="border-2 border-dashed shadow-sm rounded-md p-3">
                                        <div class="position-relative cursor-pointer mx-auto">
                                            <img class="product-image-preview imagePreview" alt="Product image preview"
                                                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='220' height='160' viewBox='0 0 220 160'%3E%3Crect width='220' height='160' fill='%23f3f4f6'/%3E%3Cpath d='M79 102l22-25 17 19 11-13 25 30H66z' fill='%23cbd5e1'/%3E%3Ccircle cx='141' cy='53' r='13' fill='%23cbd5e1'/%3E%3C/svg%3E">
                                        </div>
                                        <div class="mx-auto cursor-pointer position-relative mt-3">
                                            <button type="button" class="btn btn-primary w-100">รูปภาพ</button>
                                            <input type="file" class="w-100 h-100 top-0 start-0 position-absolute opacity-0"
                                                name="image_name" accept="image/*" onchange="imgChange(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">

                                <label class="form-label">สาขา</label>
                                <span class="text-danger">*</span><br>

                                @foreach ($branch as $bra)
                                    <input class="form-check-input" type="radio" name="ref_branch_id"
                                        id="withdraw_branch{{ $bra->id }}" value="{{ $bra->id }}"
                                        onchange="filterWithdrawProduct(this.value)"
                                        {{ Auth::user()->ref_branch_id == $bra->id ? 'checked' : '' }}>

                                    <label class="form-check-label me-4" for="withdraw_branch{{ $bra->id }}">

                                        {{ $bra->name }}

                                    </label>
                                @endforeach

                            </div>

                            <div class="col-sm-6">

                                <label>เลือกสินค้า</label>

                                <select name="ref_product_id" id="select2Product">

                                    <option value="">
                                        เลือกสินค้า
                                    </option>

                                    @foreach ($product as $pos)
                                        <option value="{{ $pos->id }}" data-branch="{{ $pos->ref_branch_id }}">

                                            {{ $pos->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="col-sm-4">
                                <label>เลือก Lot</label>
                                <select name="ref_lot_id" id="select2Stock" class="">
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">จำนวนที่เบิก</label><span
                                    class="text-danger">
                                    *</span>
                                <input name="qty" type="number" class="form-control" id="stock_qty"
                                    placeholder="จำนวนที่เบิก" required />
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
    <div class="modal fade modalHeadDecor" id="addserviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel1">&nbsp;เพิ่มสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="insert_user" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 p-4">
                            <div class="col-sm-12">

                                <label class="form-label">สาขา</label>
                                <span class="text-danger">*</span><br>

                                @foreach ($branch as $bra)
                                    <input class="form-check-input branch-radio-insert" type="radio"
                                        name="ref_branch_id" id="insert_branch{{ $bra->id }}"
                                        value="{{ $bra->id }}" onchange="filterProductType(this.value)"
                                        {{ Auth::user()->ref_branch_id == $bra->id ? 'checked' : '' }}>

                                    <label class="form-check-label me-4" for="insert_branch{{ $bra->id }}">

                                        {{ $bra->name }}

                                    </label>
                                @endforeach

                            </div>

                            <div class="col-sm-6">

                                <label class="form-label">ประเภทสินค้า</label>

                                <span class="text-danger">*</span>

                                <select name="producttype" id="producttype" class="form-control">

                                    <option value="">
                                        ---เลือกประเภทสินค้า---
                                    </option>

                                    @foreach ($producttype as $item)
                                        <option value="{{ $item->id }}"
                                            data-branch="{{ $item->ref_branch_id }}">

                                            {{ $item->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">ชื่อสินค้า</label><span class="text-danger">
                                    *</span>
                                <input name="name" type="text" class="form-control" placeholder="ชื่อสินค้า"
                                    required />
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">ราคาขาย(ลูกค้า)</label><span
                                    class="text-danger">
                                    *</span>
                                <input name="price" type="text" class="form-control" placeholder="ราคาขาย"
                                    required />
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">ราคาขาย(พนักงาน)</label><span
                                    class="text-danger">
                                    *</span>
                                <input name="price_staff" type="text" class="form-control" placeholder="ราคาขาย"
                                    required />
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label d-block">ขายพร้อมคอร์ส</label>
                                <label class="switch switch-primary mb-0">
                                    <input type="checkbox" name="sell_with_course" value="1"
                                        class="switch-input">
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">Minimum Stock
                                    แจ้งเตือนที่ต้องการซื้อ</label><span class="text-danger"> *</span>
                                <input name="minimum" type="number" class="form-control"
                                    placeholder="Minimum Stock แจ้งเตือนที่ต้องการซื้อ" required />
                            </div>
                            <script>
                                //// ทำ input เงินเดือน เริ่ม
                                function formatSalary() {
                                    const input = document.getElementById('salary');
                                    let value = input.value.replace(/,/g, ''); // ลบเครื่องหมายจุลภาค
                                    if (!isNaN(value) && value !== '') {
                                        input.value = Number(value).toLocaleString(); // แปลงเป็นรูปแบบ number_format
                                    } else {
                                        input.value = ''; // ถ้าไม่ใช่ตัวเลขให้ลบค่าที่ป้อน
                                    }
                                }
                            </script>
                            <div class="col-sm-12">
                                <label for="" class="form-label">หมายเหตุ</label>
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

    <div class="modal fade modalHeadDecor" id="manageProductTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title" id="exampleModalLabelType">&nbsp;จัดการประเภทสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">

                    <form id="formAddProductType" class="mb-4 d-flex gap-2">

                        <div class="row g-3 p-4">
                            <div class="col-sm-12">
                                <label class="form-label">สาขา *</label><br>
                                @foreach ($branch as $bra)
                                    <input class="form-check-input product_type_branch_id" type="radio"
                                        name="ref_branch_id" id="add-branch{{ $bra->id }}"
                                        value="{{ $bra->id }}" {{ $loop->first ? 'checked' : '' }}
                                        @if (Auth::user()->ref_branch_id == $bra->id) checked @endif>
                                    <label class="form-check-label me-4" for="add-branch{{ $bra->id }}">
                                        {{ $bra->name }}
                                    </label>
                                @endforeach
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label">ชื่อประเภทสินค้า *</label><br>
                                <input type="text" id="new_type_name" class="form-control"
                                    placeholder="กรอกชื่อประเภทสินค้าใหม่ที่นี่..." required>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success text-nowrap"><i class="ti ti-plus"></i>
                                    เพิ่ม</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%" class="text-center">ลำดับ</th>
                                    <th>ชื่อประเภทสินค้า</th>
                                    <th>สาขา</th>
                                    <th width="25%" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="productTypeTableBody">
                                <tr>
                                    <td colspan="3" class="text-center">กำลังโหลดข้อมูล...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer rounded-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary"
                        data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>
    </div>
    <!--set rent Modal -->

    <!-- / Layout wrapper -->
    @include('admin/layout/inc_js')
    <script>
        let select2Product = null;
        let select2Stock = null;

        function imgChange(input) {
            const file = input.files && input.files[0];
            const form = input.closest('form');
            const preview = form ? form.querySelector('.imagePreview') : null;

            if (!file || !preview) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                input.value = '';
                Swal.fire('เกิดข้อผิดพลาด', 'กรุณาเลือกไฟล์รูปภาพเท่านั้น', 'error');
                return;
            }

            preview.src = URL.createObjectURL(file);
        }

        function ajaxErrorMessage(error) {
            if (error.responseJSON?.message) {
                return error.responseJSON.message;
            }

            if (error.responseJSON?.errors) {
                return Object.values(error.responseJSON.errors).flat().join('<br>');
            }

            if (error.responseText) {
                return error.responseText;
            }

            return 'ไม่พบรายละเอียดข้อผิดพลาด';
        }

        select2Product = new TomSelect("#select2Product", {
            create: false,
            maxItems: 1,
            allowEmptyOption: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        select2Stock = new TomSelect("#select2Stock", {
            create: false,
            maxItems: 1,
            allowEmptyOption: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        $('#select2Product').on('change', function() {
            const product_id = $(this).val();
            if (product_id) {

                document.getElementById('loadingOverlay').style.display = 'flex';

                if (select2Stock) {
                    select2Stock.destroy();
                }
                $('#select2Stock').html('<option selected disabled hidden value="">เลือก Lot</option>');

                $.ajax({
                    url: 'admin/card_stock_report/get-stock/' + product_id,
                    type: 'GET',
                    success: function(data) {
                        data.forEach(function(stock) {
                            $('#select2Stock').append(
                                `<option value="${stock.id}">${stock.label}</option>`
                            );
                        });

                        select2Stock = new TomSelect("#select2Stock", {
                            create: false,
                            maxItems: 1,
                            allowEmptyOption: true,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            }
                        });
                        document.getElementById('loadingOverlay').style.display = 'none';
                    },
                    error: function(error) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            }
        });

        $('#select2Stock').on('change', function() {
            var stock_id = $(this).val();

            if (stock_id) {

                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'admin/card_stock_report/get-stock-by-id/' + stock_id,
                    type: 'GET',
                    success: function(data) {

                        $('#stock_qty').val(data.remain).attr('max', data.remain);;

                        document.getElementById('loadingOverlay').style.display = 'none';
                    },
                    error: function(error) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            }
        });
        //////////////////////////////////////////////////////////////////////////////////////////////
        var page = "{{ $page_url }}/datatable";
        var searchData = {};
        loadData(page);

        function loadData(pages) {

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
                    $("#table-data").html(data);
                }
            });
            // alert(page);
        }

        function view(id) {
            $.ajax({
                type: "GET",
                url: "{{ $page_url }}/" + id,
                success: function(data) {
                    $("#view").html(data);
                }
            });
        }
        //////////////////////////////////////////////////////////////////////////////////////////////
        function Delete(id, v, element) {
            $(element).prop('checked', v === 1 ? false : true);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการลบสินค้าหรือไม่?',
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
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response == true) {
                                Swal.fire('ลบสินค้าเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                            }
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        }
                    });
                }
            });
        }
        //////////////////////////////////////////////////////////////////////////////////////////////
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
                        data: {
                            ref_status_id: v,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response == true) {
                                Swal.fire('เปลี่ยนสถานะเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                            }
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        }
                    });
                }
            });
        }
        //////////////////////////////////////////////////////////////////////////////////////////////
        function changeSellWithCourse(id, v, element) {
            $(element).prop('checked', v === 1 ? false : true);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเปลี่ยนการขายพร้อมคอร์สหรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                didOpen: () => Swal.getConfirmButton().focus()
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ $page_url }}/change-sell-with-course/' + id,
                        type: 'POST',
                        data: {
                            sell_with_course: v,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response == true) {
                                Swal.fire('เปลี่ยนการขายพร้อมคอร์สเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                            }
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        }
                    });
                }
            });
        }
        //////////////////////////////////////////////////////////////////////////////////////////////
        $('#withdraw_product').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ

            if (!this.checkValidity()) {
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }

            var formData = new FormData(this);

            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเบิกสินค้าหรือไม่?',
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
                        url: '/admin/product/withdraw-product',
                        type: 'POST',
                        data: formData,
                        contentType: false, // ✅ ต้องมี
                        processData: false, // ✅ ต้องมี
                        success: function(response) {
                            if (response == true) {
                                $('#withdraw_product')[0].reset();
                                $('#withdrawModal').modal('hide');
                                Swal.fire('เบิกสินค้าเรียบร้อยแล้ว', '', 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                                loadData(page);
                            }
                        },
                        error: function(error) {
                            Swal.fire('เกิดข้อผิดพลาด', error.responseJSON?.message || '', 'error');
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                }
            });
        });

        //////////////////////////////////////////////////////////////////////////////////////////////
        $('#insert_user').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ

            if (!this.checkValidity()) {
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }

            var formData = new FormData(this);

            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการเพิ่มสินค้าหรือไม่?',
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
                        contentType: false, // ✅ ต้องมี
                        processData: false, // ✅ ต้องมี
                        success: function(response) {
                            if (response == true) {
                                $('#insert_user')[0].reset();
                                Swal.fire('เพิ่มสินค้าเรียบร้อยแล้ว', '', 'success');
                                $('#addserviceModal').modal('hide');
                                loadData(page);
                            }
                        },
                        error: function(error) {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด',
                                html: ajaxErrorMessage(error),
                                icon: 'error',
                                width: 800
                            });
                            console.error('เกิดข้อผิดพลาด:', error);
                        }
                    });
                }
            });
        });
        //////////////////////////////////////////////////////////////////////////////////////////////
        function updateSort(el) {
            // return alert(v);
            let id = el.dataset.id;
            let oldSort = el.dataset.old; // ค่าเดิม
            let newSort = el.value; // ค่าใหม่
            if (newSort == '') {
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
                        success: function(response) {
                            if (response == true) {
                                Swal.fire('เปลี่ยนลำดับเรียบร้อยแล้ว', '', 'success');
                                loadData(page);
                            }
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        }
                    });
                }
            });
        }
        //////////////////////////////////////////////////////////////////////////////////////////////
        // window.onload = function() {
        //     $('#addserviceModal').modal('show');
        // };
        $('#bs-datepicker-format').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
            autoclose: true, // ปิด datepicker เมื่อเลือกวันที่
            todayHighlight: true // ไฮไลต์วันที่ปัจจุบัน
        });
        $('#select2Position1').select2();
        // $('#select2Product').select2({ dropdownParent: $('.card-body') });
        // $('#select2Stock').select2({ dropdownParent: $('.card-body') });

        // ==========================================
        // ระบบจัดการประเภทสินค้า (Product Type CRUD)
        // ==========================================

        function loadProductTypes() {
            $('#productTypeTableBody').html(
                '<tr><td colspan="3" class="text-center"><i class="ti ti-loader ti-spin"></i> กำลังโหลด...</td></tr>');

            $.ajax({
                url: '/admin/product-type/get-all',
                type: 'GET',
                success: function(response) {
                    let html = '';
                    if (response.length > 0) {
                        response.forEach((item, index) => {
                            html += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.name}</td>
                                <td>${item.branch.name}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-warning waves-effect" onclick="editProductType(${item.id}, '${item.name}')" title="แก้ไข">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-danger waves-effect" onclick="deleteProductType(${item.id})" title="ลบ">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        });
                    } else {
                        html =
                            '<tr><td colspan="3" class="text-center text-muted">ยังไม่มีข้อมูลประเภทสินค้า</td></tr>';
                    }
                    $('#productTypeTableBody').html(html);
                },
                error: function(error) {
                    $('#productTypeTableBody').html(
                        '<tr><td colspan="3" class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>'
                        );
                }
            });
        }

        $('#formAddProductType').on('submit', function(event) {
            event.preventDefault();
            let typeName = $('#new_type_name').val();
            let ref_branch_id = $('.product_type_branch_id:checked').val();

            $.ajax({
                url: '/admin/product-type/store',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: typeName,
                    ref_branch_id: ref_branch_id
                },
                success: function(response) {
                    if (response.status == true || response == true) {
                        $('#new_type_name').val('');
                        Swal.fire({
                            title: 'เพิ่มสำเร็จ',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadProductTypes();
                    }
                },
                error: function(error) {
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเพิ่มข้อมูลได้', 'error');
                }
            });
        });

        function editProductType(id, oldName) {
            Swal.fire({
                title: 'แก้ไขประเภทสินค้า',
                input: 'text',
                inputValue: oldName,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณากรอกชื่อประเภทสินค้า!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value !== oldName) {
                    $.ajax({
                        url: '/admin/product-type/update/' + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: result.value
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'แก้ไขสำเร็จ',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadProductTypes();
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถแก้ไขข้อมูลได้', 'error');
                        }
                    });
                }
            });
        }

        function deleteProductType(id) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "หากลบแล้วจะไม่สามารถกู้คืนได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/product-type/delete/' + id,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'ลบข้อมูลแล้ว',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadProductTypes(); // โหลดตารางใหม่
                        },
                        error: function() {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้ (อาจมีสินค้าผูกอยู่)',
                                'error');
                        }
                    });
                }
            });
        }
        // เลือกสาขาแล้วให้แสดง ประภทสินค้า สาขา นั้น ๆ

        const allWithdrawProducts = [];

        $('#select2Product option').each(function() {

            allWithdrawProducts.push({
                value: $(this).val(),
                text: $(this).text(),
                branch: $(this).data('branch')
            });

        });

        const allProducts = @json($product);

        function filterWithdrawProduct(branchId) {

            select2Product.clear();

            select2Product.clearOptions();

            allProducts.forEach(product => {

                if (product.ref_branch_id == branchId) {

                    select2Product.addOption({
                        value: product.id,
                        text: product.name
                    });

                }

            });

            select2Product.refreshOptions(false);
        }


        // โหลดครั้งแรก
        filterWithdrawProduct(
            $('input[id^="withdraw_branch"]:checked').val()
        );

        // =========================
        // เพิ่มสินค้า
        // =========================

        const productTypeOptions =
            $('#producttype').html();

        function filterProductType(branchId) {

            $('#producttype').html(productTypeOptions);

            $('#producttype option').each(function() {

                let branch = $(this).data('branch');

                if (branch && branch != branchId) {
                    $(this).remove();
                }

            });

        }


        // โหลดครั้งแรก

        filterProductType(
            $('.branch-radio-insert:checked').val()
        );
    </script>
</body>

</html>
