        <div class="col-sm-5">
            <label for="exampleFormControlInput1" class="form-label">ชื่อผู้เข้าพัก</label>
            <input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="" value="{{ $room_for_rent->prefix.' '.$room_for_rent->name.' '.$room_for_rent->surname }}" />
        </div>
        <div class="col-sm-7">
            <label for="exampleFormControlInput2" class="form-label">ที่อยู่ผู้เข้าพัก</label>
            <input type="text" name="homeland" class="form-control" id="exampleFormControlInput2" placeholder="" value="{{ $room_for_rent->id_card_number }}" />
        </div>
        <div class="col-sm-6">
            <label for="exampleFormControlInput3" class="form-label">เบอร์โทรผู้เข้าพัก</label>
            <input type="text" name="phone" class="form-control" id="exampleFormControlInput3" placeholder="" value="{{ $room_for_rent->phone }}" />
        </div>
        <div class="col-sm-6">
            <label for="exampleFormControlInput4" class="form-label">หมายเลขบัตรประชาชนผู้เข้าพัก</label>
            <input type="text" name="id_card_number" class="form-control" id="exampleFormControlInput4" placeholder="" value="{{ $room_for_rent->id_card_number }}" />
        </div>
        <div class="col-sm-6">
            <label for="contract_date" class="form-label">วันที่ทำสัญญา</label>
            <input type="text" name="contract_date" class="form-control" placeholder="" id="contract_date" required autocomplete="off" value="{{date('d/m/Y', strtotime('+1 day'))}}"/>
        </div>
        <div class="col-sm-6">
            <label class="form-label">ระยะเวลาสัญญา(เดือน)</label>
            <input type="number" name="period" class="form-control" placeholder="" value="" required id="period"/>
        </div>
        <div class="col-sm-6">
            <label for="contract_date_to" class="form-label">วันที่สิ้นสุดสัญญา</label>
            <input type="text" name="contract_date_to" class="form-control" placeholder="" id="contract_date_to" required autocomplete="off"/>
        </div>
        <div></div>
        <div></div>
        <div class="col-sm-12">
            <label for="security_deposit" class="form-label">ค่าบริการ</label>
        </div>
        <div class="col-sm-12">
            <input name="customRadioTemp" class="form-check-input" type="checkbox" value="" id="customRadioTemp">
            <span class="custom-option-header">
                <span class="h6 mb-0">&nbsp; ค่าที่จอดรถยนต์</span>
            </span>
        </div>
        <div class="col-sm-12">
            <input name="customRadioTemp" class="form-check-input" type="checkbox" value="" id="customRadioTemp2">
            <span class="custom-option-header">
                <span class="h6 mb-0">&nbsp; ค่าที่จอดรถมอเตอร์ไซค์</span>
            </span>
        </div>
        <div></div>
        <div></div>
        <div class="col-sm-6">
            <label for="security_deposit" class="form-label">เงินประกันห้อง(บาท)</label>
            <input type="text" name="contract[1][security_deposit]" class="form-control" id="security_deposit" placeholder="" value="2000"/>
        </div>
        <div class="col-sm-6 d-flex align-items-end pb-1">
            <button
                id="add_expenses"
                class="btn btn-sm buttons-collection btn-warning waves-effect waves-light me-2"
                tabindex="0" aria-controls="DataTables_Table_0"
                type="button" aria-haspopup="dialog"
                aria-expanded="false">
                <span>
                <i class="ti ti-plus"></i> เพิ่มรายการเงินประกัน</span>
            </button>
        </div>
        <div></div>

        <!-- Container where new items will be appended -->
        <div id="expenses-list"></div>

        <!-- Template for the new expense item (hidden by default) -->
        <div id="new-expense-template" style="display: none;">
            <div class="expense-row d-flex mb-2">
                <div class="col-sm-2">
                    <button
                        class="btn btn-sm buttons-collection btn-danger waves-effect waves-light me-2 remove-expense"
                        tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" aria-haspopup="dialog"
                        aria-expanded="false">
                        <span>
                            <i class="ti ti-subtract"></i> ลบรายการ
                        </span>
                    </button>
                </div>
                <div class="col-sm-2 text-end me-2">
                    <label class="form-label">รายละเอียด </label>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" placeholder="" />
                </div>
                <div class="col-sm-2 text-end me-2">
                    <label class="form-label">จำนวนเงิน(บาท)</label>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" placeholder="" />
                </div>
            </div>
        </div>

        <!-- JavaScript to handle the click event and append the template -->
        <script>
            document.getElementById('add_expenses').addEventListener('click', function() {
                // Clone the hidden template
                var newExpense = document.getElementById('new-expense-template').cloneNode(true);
                
                // Remove the ID and make it visible
                newExpense.style.display = 'block';
                newExpense.removeAttribute('id');

                // Append the new expense to the list
                document.getElementById('expenses-list').appendChild(newExpense);
                
                // Add event listener to remove button
                newExpense.querySelector('.remove-expense').addEventListener('click', function() {
                    newExpense.remove();
                });
            });
            const contractDateField = $('#contract_date');
            const periodField = $('#period');
            const contractDateToField = $('#contract_date_to');

            // เมื่อมีการเปลี่ยนแปลงวันที่ทำสัญญา หรือระยะเวลาสัญญา
            contractDateField.change(updateContractDateTo);
            periodField.change(updateContractDateTo);

            function updateContractDateTo() {
                // ดึงค่าจากฟิลด์
                const contractDate = contractDateField.val();
                const periodMonths = parseInt(periodField.val(), 10);

                if (contractDate && periodMonths && !isNaN(periodMonths)) {
                    // แปลงวันที่จากรูปแบบ dd/mm/yyyy เป็น Date object
                    const dateParts = contractDate.split('/');
                    const contractDateObject = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                    // บวกเดือนเข้าไป
                    contractDateObject.setMonth(contractDateObject.getMonth() + periodMonths);

                    // แสดงวันที่ที่สิ้นสุดสัญญาในฟิลด์ contract_date_to
                    const endDate = contractDateObject.toLocaleDateString('en-GB'); // รูปแบบ dd/mm/yyyy
                    contractDateToField.val(endDate);
                }
            }

            // คำนวณวันที่เริ่มต้นเมื่อเอกสารโหลดเสร็จ
            updateContractDateTo();
        </script>
        <div class="col-sm-6">
            <label for="deduction_booking_amount" class="form-label">ยอดยกจากค่าจองห้อง(บาท)</label>
            <input type="text" name="contract[1][deduction_booking_amount]" class="form-control" id="deduction_booking_amount" placeholder="" value="2000" />
        </div>
        <div class="col-sm-6">
            <label for="deduction_booking_date" class="form-label">หักค่าจองห้องเมื่อวันที่</label>
            <input type="text" name="contract[1][deduction_booking_date]" class="form-control" id="deduction_booking_date" placeholder="" autocomplete="off"/>
        </div>
        <div class="col-sm-6">
            <label for="receipt_no" class="form-label">อ้างอิงจากใบเสร็จค่าจองเลขที่</label>
            <input type="text" name="contract[1][receipt_no]" class="form-control" id="receipt_no" placeholder="" value="2000"/>
        </div>
        <div></div>
        <div class="col-sm-6">
            <label for="water_meter_start_living" class="form-label">เลขมิเตอร์น้ำประปา(เข้าพัก)*</label>
            <input type="text" name="contract[1][water_meter_start_living]" class="form-control" id="water_meter_start_living" placeholder="" value="10"/>
        </div>
        <div class="col-sm-6">
            <label for="electricity_meter_start_living" class="form-label">เลขมิเตอร์ค่าไฟ(เข้าพัก)*</label>
            <input type="text" name="contract[1][electricity_meter_start_living]" class="form-control" placeholder="" required value="20"/>
        </div>
        <div class="col-sm-12">
            <label for="" class="form-label">หมายเหตุ</label>
            <textarea name="remark" class="form-control"></textarea>
        </div>