<div class="modal-header rounded-0">
    <span class="modal-title">
        <span class="h5" style="color: white;">ห้อง {{ $invoice->room_for_rent->room->name }}</span>
        <span class="ms-2">
            {{-- {{ date('m/Y', strtotime($invoice->month.' '.$invoice->year)) }} --}}
            @php
            $monthNames = [
                            '1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน',
                            '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม',
                            '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                        ];
                        echo $monthNames[$invoice->month].' '.$invoice->year;
            @endphp

        </span>
        <span class="ms-2" style="border: 1px solid #69c2c1;padding: 7px 12px;border-radius: 5px;font-size: smaller;">{{ $invoice->invoice_number }}</span>
    </span>
    <span class="badge bg-label-{{ $invoice->status->color }} m-auto" text-capitalized="" style="font-size: unset;" >
        <span class="ti-md ti {{ $invoice->status->icon }} me-2"></span>{{ $invoice->status->name }}
    </span>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="incomplete_update">
    @csrf
    <input type="hidden" name="id" value="{{$invoice->id}}">
    <div class="modal-body pb-1">
        {{-- ////////////////////////////////////////////////// --}}
        
        <div class="card shadow-none bg-transparent ms-auto mb-4">
                <ul class="nav nav-pills" role="tablist" style="">
                    <li class="nav-item" role="presentation">
                      <button type="button" class="btn btn-outline-primary nav-link active" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-edit" aria-controls="navs-pills-top-edit" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti-md ti ti-file"></i>
                          <b class="dam">
                            รายละเอียด
                          </b>
                        </span>
                      </button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button type="button" class="btn btn-outline-info nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-contract" aria-controls="navs-pills-top-contract" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti-md ti ti-report-money"></i>
                          <b class="dam">
                            คอนเฟิร์มบิล
                          </b>
                        </span>
                      </button>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                      <button type="button" class="btn btn-outline-warning nav-link" 
                        role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-split" aria-controls="navs-pills-top-split" aria-selected="false" tabindex="-1">
                        <span>
                          <i class="ti-md ti ti-report-money"></i>
                          <b class="dam">
                            แบ่งจ่าย
                          </b>
                        </span>
                      </button>
                    </li> --}}
                </ul>
        </div>
        <div class="tab-content" style="box-shadow: unset;padding:0px">
            <div class="tab-pane fade active show mb-5" id="navs-pills-top-edit" role="tabpanel">
              
                <div class="mb-3" style="border: 1px solid #dbdade;padding: 15px 2px;">
                    <div class="d-flex">
                        <div class="flex-grow-1 ms-3">
                        <b class="text-black">รายละเอียดหัวบิล</b> <br>
                            {{ $invoice->room_for_rent->renter->prefix.' '.$invoice->room_for_rent->renter->name.' '.$invoice->room_for_rent->renter->surname }} <br>
                            เลขประจำตัวผู้เสียภาษี {{ $invoice->room_for_rent->renter->id_card_number }} <br>
                            โทร {{ $invoice->room_for_rent->renter->phone }}
                        </div>
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>รายการ</th>
                            <th>จำนวนเงิน (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ค่าเช่าห้อง (Room rate) {{ $invoice->room_for_rent->room->name }} เดือน {{ $invoice->month.'/'.$invoice->year }}</td>
                            <td class="text-end">
                                {{ number_format($invoice->room_for_rent->room->rent) }}
                                <input type="hidden" class="calculate" value="{{$invoice->room_for_rent->room->rent}}">
                            </td>
                        </tr>
                        <tr>
                            <td style="display: flex; align-items: center;">ค่าน้ำ (Water rate) เดือน {{ $invoice->month.'/'.$invoice->year }} ( 180 - 
                                <input name="water_unit" style="width: 18%;background-color: #d6f7fb;border-color: #00bad1;" type="number" class="form-control" id="water_unit" oninput="calculatePrice()" placeholder="จำนวนเงิน" value="{{ $invoice->water_unit }}" required />
                                = {{ $invoice->water_unit-180 }} ยูนิต)
                            </td>
                            <td class="text-end">
                                <input type="hidden" class="calculate" name="water_amount" id="water_amount" value="{{ $invoice->water_amount }}">
                                <span id="text_water_amount">
                                    {{ $invoice->water_amount }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>ค่าไฟฟ้า (Electrical rate) เดือน {{ $invoice->month.'/'.$invoice->year }} ( 7194 - {{ $invoice->electricity_unit }} = 98 ยูนิต)</td>
                            <td class="text-end">
                                {{ $invoice->electricity_amount }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>รวม</th>
                            <th class="text-end mb-0 fw-bold total-price">
                                {{ number_format($invoice->room_for_rent->room->rent + $invoice->water_amount+$invoice->electricity_amount) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
                
        {{-- ////////////////////////////////////////////////// --}}
        <div class="row mt-2" id="discount-container">
            <label>ส่วนลด</label>
        </div>
        <div class="row mt-2" id="expenses-container">
            <label>รายการ</label>
        </div>
        <div class="mt-4 text-end col-12">
            <button
                    id="add_discount"
                    style="padding-right: 14px;padding-left: 14px;"
                    class="btn btn-sm buttons-collection btn-label-danger waves-effect waves-light me-2"
                    tabindex="0" aria-controls="DataTables_Table_0"
                    type="button" aria-haspopup="dialog"
                    aria-expanded="false">
                <span>
                <i class="ti ti-plus"></i> เพิ่มส่วนลด</span>
            </button>
            <button
                    id="add_expenses"
                    style="padding-right: 14px;padding-left: 14px;"
                    class="btn btn-sm buttons-collection btn-label-warning waves-effect waves-light me-2"
                    tabindex="0" aria-controls="DataTables_Table_0"
                    type="button" aria-haspopup="dialog"
                    aria-expanded="false">
                <span>
                <i class="ti ti-plus"></i> เพิ่มรายการ</span>
            </button>
        </div>
        
        <div class="col-sm-11 mt-3">
            <label>หมายเหตุ</label>
            <input type="text" class="form-control" placeholder="หมายเหตุ" />
        </div>
          </div>
            <div class="tab-pane fade" id="navs-pills-top-contract" role="tabpanel">
              {{-- <form id="insert_one_contract"> --}}
                <h4 class="text-center">ยอดค้างชำระเงินทั้งหมด&nbsp; <span class="total-price">{{ number_format($invoice->room_for_rent->room->rent + $invoice->water_amount+$invoice->electricity_amount) }}</span> &nbsp;บาท
                    {{-- ////////////////////////////////////////////////// --}}
                    {{-- ////////////////////////////////////////////////// --}}
                    {{-- ////////////////////////////////////////////////// --}}
                    <br><span class="text-truncate text-success">จ่ายแล้ว 1,000</span>
                    <br><span class="text-truncate text-danger">ค้างจ่าย 3,365</span>
                    {{-- ////////////////////////////////////////////////// --}}
                    {{-- ////////////////////////////////////////////////// --}}
                    {{-- ////////////////////////////////////////////////// --}}
                </h4>
                <div class="mb-3 pb-4" style="border: 1px solid #dbdade;padding: 15px 2px;">
                    <div class="d-flex">
                        <div class="flex-grow-1 ms-3 g-3 row">
                            {{-- <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" />
                                <label class="form-check-label text-black" for="defaultCheck1"> <b></b> </label>
                              </div>
                               --}}
                            <b class="text-black">รูปแบบการชำระเงิน</b> <br>

                            {{-- <div class="col-sm-11" style="pointer-events: none;"> --}}
                            {{-- <div class="col-sm-11">
                                <input name="payment_format" class="form-check-input" type="radio" id="payfull" value="1" checked>
                                <label class="form-check-label" for="payfull"> จ่ายเต็มจำนวน </label>
                            </div>
                            <div class="col-sm-11">
                                <input name="payment_format" class="form-check-input" type="radio" id="checksplit" value="2" > 
                                <label class="form-check-label" for="checksplit"> แบ่งจ่าย </label>
                            </div> --}}
                            {{-- ////////////////////////////////////////////////// --}}
                            {{-- ////////////////////////////////////////////////// --}}
                            {{-- ////////////////////////////////////////////////// --}}
                                    <div class="col-sm-11">
                                        <input name="payment_format" class="form-check-input" type="radio" id="payfull" value="1" checked>
                                        <label class="form-check-label" for="payfull"> จ่ายเต็มจำนวน </label>
                                    </div>
                                    <div class="col-sm-11">
                                        <input name="payment_format" class="form-check-input" type="radio" id="checksplit" value="2" checked> 
                                        <label class="form-check-label" for="checksplit"> แบ่งจ่าย </label>
                                    </div>
                            {{-- ////////////////////////////////////////////////// --}}
                            {{-- ////////////////////////////////////////////////// --}}
                            {{-- ////////////////////////////////////////////////// --}}
                        {{-- <input type="checkbox" name="" id=""><b class="text-black"></b> <br> --}}

                          {{-- <div class="col-sm-3">
                              <label class="form-check-label" for="payfull"> จำนวนเงิน </label>
                              <input name="split" class="form-control" type="text" id="payfull" value="">
                          </div> --}}
                          <div class="col-sm-11" id="divsplit" style="display: block;">
                              <div class="row">
                                  <label>รายการ</label>
                                  <div class="mt-2 col-sm-6">
                                      <input type="text" class="form-control" placeholder="รายการ" value="ค่าห้อง"/>
                                  </div>
                                  {{-- <div class="mt-2 col-sm-6">
                                      <input type="text" class="form-control" placeholder="รายการ" value="ค่าห้อง 2"  readonly/>
                                  </div> --}}
                                  {{-- <div class="mt-2 col-sm-3">
                                      <input name="expenses_price" type="text" class="form-control calculate_2" placeholder="จำนวนเงิน" value="1000"  readonly/>
                                  </div> --}}
                                  {{-- <div class="mt-2 col-sm-6">
                                      <input type="text" class="form-control" placeholder="รายการ" />
                                  </div> --}}
                                  <div class="mt-2 col-sm-3">
                                      <input name="expenses_price" type="text" class="form-control calculate_2" oninput="calculatePrice_2()" placeholder="จำนวนเงิน"
                                      value="1000"
                                      />
                                  </div>
                              </div>
                                  <div class="row mt-2" id="expenses-split-container">
                                  </div>
                                  <div class="mt-4 text-end col-12">
                                      <button
                                              id="add_expenses_split"
                                              style="padding-right: 14px;padding-left: 14px;"
                                              class="btn btn-sm buttons-collection btn-label-warning waves-effect waves-light me-2"
                                              tabindex="0" aria-controls="DataTables_Table_0"
                                              type="button" aria-haspopup="dialog"
                                              aria-expanded="false">
                                          <span>
                                          <i class="ti ti-plus"></i> เพิ่มรายการ</span>
                                      </button>
                                  </div>
                                  <div class="col-sm-11 mt-3 mb-3">
                                      <label>หมายเหตุ</label>
                                      <input type="text" class="form-control" placeholder="หมายเหตุ" />
                                  </div>
                                  
                              {{-- <b>ยอดชำระเงินทั้งหมด&nbsp; <span class="total-price">{{ number_format($invoice->room_for_rent->room->rent + $invoice->water_amount+$invoice->electricity_amount) }}</span> &nbsp;บาท</b> --}}
                          </div>
                          <script>
                            document.getElementById('checksplit').addEventListener('change', function() {
                                document.getElementById('divsplit').style.display = this.checked ? 'block' : 'none';
                                document.getElementById('totalsplit').style.display = this.checked ? 'block' : 'none';
                                document.getElementById('totalpayfull').style.display = this.checked ? 'none' : 'block';
                            });

                            document.getElementById('payfull').addEventListener('change', function() {
                                document.getElementById('divsplit').style.display = this.checked ? 'none' : 'block';
                                document.getElementById('totalsplit').style.display = this.checked ? 'none' : 'block';
                                document.getElementById('totalpayfull').style.display = this.checked ? 'block' : 'none';
                            });
                          </script>
                      </div>
                    </div>
                </div>

                <div class="mb-3 pb-4" style="border: 1px solid #dbdade;padding: 15px 2px;">
                    <div class="d-flex">
                        <div class="flex-grow-1 ms-3 g-3 row">
                            <b class="text-black">ช่องทางการชำระเงิน</b> <br>
                            <div class="col-sm-11">
                                <input name="payment_channel" class="form-check-input" type="radio" id="defaultRadio1" value="1" checked onclick="togglePaymentFields()">
                                <label class="form-check-label" for="defaultRadio1"> เงินสด </label>
                            </div>
                            
                            <div id="paymentDetails2">
                                <div class="col-sm-6 mb-2">
                                    <label for="transfer_date">วันที่ชำระเงิน</label>
                                    <input type="text" name="transfer_date" class="form-control" placeholder="" id="transfer_date" required autocomplete="off" value="{{date('d/m/Y')}}"/>
                                </div>
                            </div>
                            <div class="col-sm-11">
                                <input name="payment_channel" class="form-check-input" type="radio" id="defaultRadio2" value="2" onclick="togglePaymentFields()"> 
                                <label class="form-check-label" for="defaultRadio2"> โอนเงิน </label>
                            </div>
                
                            <!-- แสดงเมื่อเลือก โอนเงิน -->
                            <div id="paymentDetails" style="display:none;">
                                
                                <div class="col-sm-6 mb-2">
                                      <label>เลือกบัญชีธนาคาร</label>
                                      <select class="select2 form-select mb-2" name="bank" id="exampleFormControlSelect1">
                                        {{-- <option value="" disabled="" selected="selected">บัญชีธนาคาร</option> --}}
                                        @foreach ($bank as $r_bank)
                                            <option value="{{ $r_bank->id }}">{{ $r_bank->bank.' '.$r_bank->bank_account_name }}</option>
                                        @endforeach

                                  </div>
                                <div class="col-sm-4 mb-2">
                                    <input type="hidden" name="">
                                </div>
                                    <div class="col-sm-3 mb-2">
                                        <label for="transfer_time">เวลาโอนเงิน</label>
                                        <input type="time" name="transfer_time" class="form-control" placeholder="" id="transfer_time" required autocomplete="off"/>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label for="transfer_date">วันที่โอนเงิน</label>
                                        <input type="text" name="transfer_date" class="form-control" placeholder="" id="transfer_date" required autocomplete="off" value="{{date('d/m/Y')}}"/>
                                    </div>
                                <div class="col-sm-10 mt-3">
                                    <label for="paymentReceipt">แนบหลักฐานการโอน</label>
                                    <input type="file" class="form-control mb-2" id="paymentReceipt">
                                    <div class="preview-container">
                                        <img id="preview1" src="" alt="Preview 1" style="display: none; width:30%">
                                    </div>
                                </div>
                            </div>
                
                            <div class="col-sm-11 mt-2">
                                <b id="totalpayfull">ยอดชำระเงินทั้งหมด&nbsp; <span class="total-price">{{ number_format($invoice->room_for_rent->room->rent + $invoice->water_amount+$invoice->electricity_amount) }}</span> &nbsp;บาท</b>
                                <b id="totalsplit" style="display: none">ยอดชำระเงินทั้งหมด&nbsp; <span class="total-price_2">0</span> &nbsp;บาท</b>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    function togglePaymentFields() {
                        const paymentChannel = document.querySelector('input[name="payment_channel"]:checked').value;
                        const paymentDetails = document.getElementById('paymentDetails');
                        const paymentDetails2 = document.getElementById('paymentDetails2');
                        
                        // หากเลือก โอนเงิน (value=2) ให้แสดงฟอร์มเพิ่ม
                        if (paymentChannel == '2') {
                            paymentDetails.style.display = 'block';
                            paymentDetails2.style.display = 'none';
                        } else {
                            paymentDetails.style.display = 'none';
                            paymentDetails2.style.display = 'block';
                        }
                    }
                    function handleFileInput(fileInputId, previewId) {
                        const fileInput = document.getElementById(fileInputId);
                        const previewImage = document.getElementById(previewId);

                        fileInput.addEventListener('change', function () {
                            const file = fileInput.files[0];

                            if (file) {
                                const reader = new FileReader();

                                reader.onload = function (e) {
                                    previewImage.src = e.target.result;
                                    previewImage.style.display = 'block';  // แสดงภาพพรีวิว
                                };

                                reader.readAsDataURL(file);
                            } else {
                                previewImage.style.display = 'none'; // ซ่อนพรีวิวถ้าไม่ได้เลือกไฟล์
                            }
                        });
                    }
                
                    handleFileInput('paymentReceipt', 'preview1');
                    // เรียกใช้ฟังก์ชั่นเริ่มต้นเมื่อเพจโหลด
                    togglePaymentFields();
                </script>
                
                  
                  <div class="modal-footer rounded-0 justify-content-center">
                      {{-- <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button> --}}
                      <button class="btn btn-info" type="submit">
                        <span>
                        <i class="ti-md ti ti-report-money"></i>
                        <b class="dam">
                          ชำระ
                        </b>
                      </span></button>
                  </div>
              {{-- </form> --}}
            </div>
            <div class="tab-pane fade" id="navs-pills-top-split" role="tabpanel">
              {{-- <form id="insert_one_contract"> --}}
                  
                  
                  <div class="modal-footer rounded-0 justify-content-center">
                      {{-- <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">ยกเลิก</button> --}}
                      <button class="btn btn-info">
                        <span>
                        <i class="ti-md ti ti-report-money"></i>
                        <b class="dam">
                          ชำระ
                        </b>
                      </span></button>
                  </div>
              {{-- </form> --}}
            </div>
        </div>
        
    </div>

    <div class="modal-footer rounded-0 justify-content-start">
        <button type="button" class="btn btn-primary waves-effect"><span
                class="ti-md ti ti-printer me-2"></span>พิมพ์ใบแจ้งหนี้</button>
                
        {{-- <button type="button" class="btn btn-secondary waves-effect"><span
            class="ti-md ti ti-pencil"></span></button> --}}
        {{-- <input type="hidden" id="payment_channel" name="payment_channel"> --}}
        {{-- <button type="submit" class="btn btn-warning waves-effect ms-auto" onclick="paymentChannel(1)">
            <span class="ti-md ti ti-report-money me-2"></span>ชำระโดย เงินสด
        </button>
        <button type="submit" class="btn btn-warning waves-effect ms-auto" onclick="paymentChannel(2)">
            <span class="ti-md ti ti-report-money me-2"></span>ชำระโดย โอนเงิน
        </button> --}}
            {{-- <button type="submit" class="btn btn-info waves-effect ms-auto">
                <span class="ti-md ti ti-report-money me-2"></span>
            </button> --}}
            {{-- //////////////////////////////////////////////////////////////// --}}
            {{-- //////////////////////////////////////////////////////////////// --}}
    </div>
</form>
<script>
    
        $('#transfer_date').datepicker({
            format: 'dd/mm/yyyy', // กำหนดรูปแบบของวันที่
            todayBtn: "linked",   // เพิ่มปุ่มวันนี้
            clearBtn: true,       // เพิ่มปุ่มล้างข้อมูล
            autoclose: true       // เมื่อเลือกวันที่แล้วจะปิดปฏิทิน
        })
        calculatePrice();
        document.getElementById('add_discount').addEventListener('click', function() {
            // Create a new discount form row
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-2');
            
            newRow.innerHTML = `
                <div class="col-sm-8">
                    <input name="discount_title" type="text" class="form-control" placeholder="หัวข้อส่วนลด" required />
                </div>
                <div class="col-sm-4">
                    <input name="discount_price" type="text" class="form-control calculate discount_price" oninput="calculatePrice()" placeholder="จำนวนเงิน" required />
                </div>
            `;
            
            // Append the new row to the container
            document.getElementById('discount-container').appendChild(newRow);
        });
        document.getElementById('add_expenses').addEventListener('click', function() {
            // Create a new discount form row
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-2');
            
            newRow.innerHTML = `
                <div class="col-sm-8">
                    <input name="add_expenses_title" type="text" class="form-control" placeholder="หัวข้อรายการ" required />
                </div>
                <div class="col-sm-4">
                    <input name="add_expenses_price" type="text" class="form-control calculate add_expenses_price" oninput="calculatePrice()" placeholder="จำนวนเงิน" required />
                </div>
            `;
            
            // Append the new row to the container
            document.getElementById('expenses-container').appendChild(newRow);
        });
        document.getElementById('add_expenses_split').addEventListener('click', function() {
            // Create a new expenses form row
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-2');
            
            newRow.innerHTML = `
                <div class="col-sm-6">
                    <input name="expenses_title" type="text" class="form-control" placeholder="รายการ" required />
                </div>
                <div class="col-sm-3">
                    <input name="expenses_price" type="text" class="form-control calculate_2" oninput="calculatePrice_2()" placeholder="จำนวนเงิน" required />
                </div>
                <div class="col-sm-3">
                    <button type="button" class="btn btn-danger btn-sm remove-expense">
                        <i class="ti ti-trash"></i> ลบรายการ
                    </button>
                </div>
            `;
            
            // Append the new row to the container
            const container = document.getElementById('expenses-split-container');
            container.appendChild(newRow);
            
            // Add event listener for the remove button
            const removeButton = newRow.querySelector('.remove-expense');
            removeButton.addEventListener('click', function() {
                container.removeChild(newRow);  // Remove the row
            });
        });
        
        function paymentChannel(i) {
            $('#payment_channel').val(i);
        }
        function calculatePrice() { 
            var water_amount = ($('#water_unit').val()-180)*18
            $('#text_water_amount').html(water_amount.toLocaleString());
            $('#water_amount').val(water_amount);
            const inputs = document.querySelectorAll('.calculate');  // เลือกทุก input ที่มี class="calculate"
            let total = 0;

            inputs.forEach(input => {
                // ลบเครื่องหมายจุลภาคจากค่าที่รับมา
                let value = input.value.replace(/,/g, ''); 
                
                if (value.trim() !== "" && !isNaN(value)) {
                    // ตรวจสอบว่า input มี class="discount_price" หรือไม่
                    if (input.classList.contains('discount_price')) {
                        // ถ้ามี class="discount_price", ลบค่าออกจาก total
                        total -= parseFloat(value.replace(/[^0-9.-]+/g, ""));
                    } else {
                        // ถ้าไม่มี class="discount_price", เพิ่มค่าเข้าไปใน total
                        if (!isNaN(value) && value.trim() !== "") {
                            total += parseFloat(value);
                        }
                    }
                }
            });
            $('.total-price').html(total.toLocaleString());

            // อัปเดตค่า total ใน span#total-price
            // document.getElementById('total-price').innerText = total.toLocaleString();
        }
        function calculatePrice_2() { 
            const inputs = document.querySelectorAll('.calculate_2');  // เลือกทุก input ที่มี class="calculate"
            let total = 0;

            inputs.forEach(input => {
                // ลบเครื่องหมายจุลภาคจากค่าที่รับมา
                let value = input.value.replace(/,/g, ''); 
                
                if (value.trim() !== "" && !isNaN(value)) {
                    // ตรวจสอบว่า input มี class="discount_price" หรือไม่
                    if (input.classList.contains('discount_price')) {
                        // ถ้ามี class="discount_price", ลบค่าออกจาก total
                        total -= parseFloat(value.replace(/[^0-9.-]+/g, ""));
                    } else {
                        // ถ้าไม่มี class="discount_price", เพิ่มค่าเข้าไปใน total
                        if (!isNaN(value) && value.trim() !== "") {
                            total += parseFloat(value);
                        }
                    }
                }
            });
            $('.total-price_2').html(total.toLocaleString());

            // อัปเดตค่า total ใน span#total-price
            // document.getElementById('total-price').innerText = total.toLocaleString();
        }
        
        $('#incomplete_update').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if(!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            // return alert(123);
            Swal.fire({
                title: 'ยืนยันการดำเนินการ?',
                text: 'คุณต้องการ บันทึกการเปลี่ยนแปลง หรือไม่?',
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
                        url: '{{ $page_url }}/incomplete_update', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            if(response == true){
                                $('#invoice').modal('hide');
                                summary();
                                loadData(page);
                                Swal.fire('บันทึกเรียบร้อยแล้ว', '', 'success');
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
</script>