<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<style>
    .select2-container {
        z-index: 9999;
        /* เพิ่ม z-index เพื่อให้ dropdown แสดงอยู่ข้างบน */
    }

    .swal2-container {
        z-index: 9999 !important;
        /* ปรับ z-index ให้สูงกว่า modal อื่น */
    }
</style>
<div class="modal-content rounded-0">
    <div class="modal-header rounded-0">
        <span class="modal-title">
            <span class="h5" style="color: white;">&nbsp;รายละเอียด บุคลากร&nbsp;</span>
        </span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pb-5" style="padding: 1em 3em;">

        <div class="col-md-12" style="padding-right: unset !important;">

            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                    <div class="nav-align-top mb-4">
                        <ul class="nav nav-pills justify-content-end" role="tablist">
                            <li class="nav-item pe-2" role="presentation">
                                <button
                                    class="buttons-collection btn-label-primary waves-effect waves-light nav-link active"
                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile"
                                    aria-controls="navs-pills-top-profile" aria-selected="true">
                                    <i class="ti ti-user"></i> &nbsp;รายละเอียด
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="buttons-collection btn-label-warning waves-effect waves-light nav-link"
                                    role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-edit"
                                    aria-controls="navs-pills-top-edit" aria-selected="false" tabindex="-1">
                                    <span>
                                        <i class="ti ti-pencil"></i> แก้ไข
                                    </span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" style="box-shadow: unset;padding:0px">
                            <div class="tab-pane fade active show" id="navs-pills-top-profile" role="tabpanel">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom text-primary pb-2">
                                        <i class="ti ti-user"></i> รายละเอียด
                                    </h5>
                                </div>
                                <div class="d-flex">
                                    <div class="col-sm-5">
                                        <ul class="list-unstyled mb-4 mt-2" style="padding: 0 20px;">
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">สาขา:</span>
                                                <span>{{ $product->branch->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-crown text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">คงเหลือ:</span>
                                                <span>{{ \App\Models\CardStocks::where('ref_product_id', $product->id)->latest()->value('remain') ?? 0 }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ชื่อ:</span>
                                                <span>{{ $product->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ราคาขาย:</span>
                                                <span>{{ $product->price }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ต้นทุน:</span>
                                                <span>{{ $product->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-file-description text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">หมายเหตุ:</span>
                                                <span>{{ $product->remark }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-top-edit" role="tabpanel">
                                <div class="col-sm-12 text-start">
                                    <h5 class="border-bottom pb-3 text-warning">
                                        <i class="ti ti-pencil"></i> แก้ไข
                                    </h5>
                                </div>
                                <form id="edit_product" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-3 p-4">
                                        <div class="col-sm-12">
                                            <label for="" class="form-label">สาขา</label><span
                                                class="text-danger"> *</span><br>
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
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ชื่อสินค้า</label><span
                                                class="text-danger"> *</span>
                                            <input name="name" type="text" class="form-control"
                                                placeholder="ชื่อสินค้า" required value="{{ $product->name }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ราคาขาย</label><span
                                                class="text-danger"> *</span>
                                            <input name="price" type="text" class="form-control"
                                                placeholder="ราคาขาย" required value="{{ $product->price }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ต้นทุน</label><span
                                                class="text-danger"> *</span>
                                            <input name="cost" type="text" class="form-control"
                                                placeholder="ต้นทุน" required value="{{ $product->cost }}" />
                                        </div>
                                        {{-- <div class="col-sm-6">
                                    <label for="" class="form-label">คงเหลือ</label><span class="text-danger"> *</span>
                                    <input name="stock" type="text" class="form-control" placeholder="คงเหลือ" required value="{{ $product->stock }}"/>
                                </div> --}}
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
                                            <textarea name="remark" class="form-control">{{ $product->remark }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer rounded-0 justify-content-center">
                                        <button type="button" class="btn btn-label-secondary"
                                            data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" class="btn btn-main">บันทึก</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <button
                        style="padding-right: 14px;padding-left: 14px;"
                        class="btn btn-success buttons-collection btn-label-warning waves-effect waves-light me-2"
                        tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" aria-haspopup="dialog"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-upload"></i>
                            ดาวน์โหลด Excel
                        </span>
                </button> --}}

            </div>

            {{-- <div class="col-sm-12">
            <div class="card shadow-none bg-transparent border mb-3">
                <div class="card-body">
                  <h5 class="card-title">Secondary card title</h5>
                  <p class="card-text">Some quick example text to build on the card title and make up.</p>
                </div>
              </div>
        </div> --}}
        </div>
    </div>
</div>
<script>
    $('#edit_product').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
        if (!this.checkValidity()) {
            // ถ้าฟอร์มไม่ถูกต้อง
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }
        // return alert(123);

        var formData = new FormData(this);
        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการ แก้ไข สินค้า หรือไม่?',
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
                    url: '{{ $page_url }}/{{ $product->id }}', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                    type: 'POST',
                    data: formData,
                    contentType: false, // ✅ ต้องมี
                    processData: false, // ✅ ต้องมี
                    success: function(response) {
                        if (response == true) {
                            Swal.fire('แก้ไขสินค้าเรียบร้อยแล้ว', '', 'success');
                            loadData(page);
                            view('{{ $product->id }}');
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
        $('#select2Position2').select2({
            placeholder: 'เลือกตำแหน่ง',
            allowClear: true
        });
    });
    $('#bs-datepicker-format2').datepicker({
        format: 'dd/mm/yyyy', // กำหนดรูปแบบวันที่
        autoclose: true, // ปิด datepicker เมื่อเลือกวันที่
        todayHighlight: true // ไฮไลต์วันที่ปัจจุบัน
    });
</script>
