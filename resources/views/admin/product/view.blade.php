<style>
    .select2-container {
        z-index: 9999;
        /* เพิ่ม z-index เพื่อให้ dropdown แสดงอยู่ข้างบน */
    }

    .swal2-container {
        z-index: 9999 !important;
        /* ปรับ z-index ให้สูงกว่า modal อื่น */
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
@php
    $productImagePath = $product->image
        ? asset($product->image)
        : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='220' height='160' viewBox='0 0 220 160'%3E%3Crect width='220' height='160' fill='%23f3f4f6'/%3E%3Cpath d='M79 102l22-25 17 19 11-13 25 30H66z' fill='%23cbd5e1'/%3E%3Ccircle cx='141' cy='53' r='13' fill='%23cbd5e1'/%3E%3C/svg%3E";
@endphp
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
                                                <i class="ti ti-garden-cart text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ประเภทสินค้า:</span>
                                                <span>{{ $product->producttype->name }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ราคาขาย(ลูกค้า):</span>
                                                <span>{{ $product->price }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-user text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ราคาขาย(พนักงาน):</span>
                                                <span>{{ $product->price_staff }}</span>
                                            </li>
                                            <li class="d-flex align-items-center mb-3">
                                                <i class="ti ti-link text-heading"></i><span
                                                    class="fw-medium mx-2 me-4 text-heading">ขายพร้อมคอร์ส:</span>
                                                <span>{{ $product->sell_with_course ? 'ใช่' : 'ไม่ใช่' }}</span>
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
                                    <div class="col-sm-3 text-center">
                                        <img src="{{ $productImagePath }}" alt="{{ $product->name }}"
                                            class="product-image-preview mb-3">
                                    </div>
                                    <div class="col-sm-4" align="right">
                                            {!! QrCode::size(200)->generate(url('/pos/product/'.$product->id)) !!}
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
                                            <div class="product-image-uploader">
                                                <div class="border-2 border-dashed shadow-sm rounded-md p-3">
                                                    <div class="position-relative cursor-pointer mx-auto">
                                                        <img class="product-image-preview imagePreview"
                                                            alt="Product image preview"
                                                            src="{{ $productImagePath }}">
                                                    </div>
                                                    <div class="mx-auto cursor-pointer position-relative mt-3">
                                                        <button type="button" class="btn btn-primary w-100">รูปภาพ</button>
                                                        <input type="file"
                                                            class="w-100 h-100 top-0 start-0 position-absolute opacity-0"
                                                            name="image_name" accept="image/*" onchange="imgChange(this)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="" class="form-label">สาขา</label><span
                                                class="text-danger"> *</span><br>
                                            @foreach ($branch as $bra)
                                                <input class="form-check-input" type="radio" name="ref_branch_id"
                                                    id="branch{{ $bra->id }}" value="{{ $bra->id }}"
                                                    {{ $product->ref_branch_id == $bra->id ? 'checked' : '' }}>
                                                <label class="form-check-label me-4" for="branch{{ $bra->id }}">
                                                    {{ $bra->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                        {{-- <div class="col-sm-6">
                                            <label for="" class="form-label">ประเภทสินค้า</label><span class="text-danger">
                                                *</span>
                                                <input  type="text" class="form-control"
                                                placeholder="ประเภทสินค้า" value="{{ $product->producttype->name }}" disabled>
                                        </div> --}}
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ชื่อสินค้า</label><span
                                                class="text-danger"> *</span>
                                            <input name="name" type="text" class="form-control"
                                                placeholder="ชื่อสินค้า" required value="{{ $product->name }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ราคาขาย(ลูกค้า)</label><span
                                                class="text-danger"> *</span>
                                            <input name="price" type="text" class="form-control"
                                                placeholder="ราคาขาย" required value="{{ $product->price }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">ราคาขาย(พนักงาน)</label><span
                                                class="text-danger"> *</span>
                                            <input name="price_staff" type="text" class="form-control"
                                                placeholder="ราคาขาย" required value="{{ $product->price_staff }}" />
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label d-block">ขายพร้อมคอร์ส</label>
                                            <label class="switch switch-primary mb-0">
                                                <input type="checkbox" name="sell_with_course" value="1" class="switch-input"
                                                    @if ($product->sell_with_course) checked @endif>
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"><i class="ti ti-check"></i></span>
                                                    <span class="switch-off"><i class="ti ti-x"></i></span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="" class="form-label">Minimum Stock แจ้งเตือนที่ต้องการซื้อ</label><span
                                                class="text-danger"> *</span>
                                            <input name="minimum" type="number" class="form-control"
                                                placeholder="Minimum Stock แจ้งเตือนที่ต้องการซื้อ" required value="{{ $product->minimum }}" />
                                        </div>
                                        {{-- <div class="col-sm-6">
                                            <label for="" class="form-label">ต้นทุน</label><span
                                                class="text-danger"> *</span>
                                            <input name="cost" type="text" class="form-control"
                                                placeholder="ต้นทุน" required value="{{ $product->cost }}" />
                                        </div> --}}
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
    function productAjaxErrorMessage(error) {
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
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด',
                            html: productAjaxErrorMessage(error),
                            icon: 'error',
                            width: 800
                        });
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            } else if (result.isDismissed) {
                // Swal.fire('ยกเลิกการดำเนินการ', '', 'info');
            }
        });
    });

</script>
