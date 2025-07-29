<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
    <style>
        body {
            font-family: "Sarabun", sans-serif;
            font-size: 16px;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .receipt {
            background: white;
            padding: 15px;
            width: 210mm;
            max-width: 210mm;
            height: 297mm;
        }
        .header {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            /* margin-top: 20px; */
        }
        .section-container {
            width: 100%;
            margin-top: 8px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }
        .table-info td {
            vertical-align: top;
            font-size: 14px;
            padding: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .table th {
            border-bottom: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 16px;
        }
        .table td {
            padding: 8px;
            font-size: 16px;
        }
        .table td:nth-child(1),
        .table th:nth-child(1) {
            text-align: center;
        }
        .table td:nth-child(2) {
            text-align: left;
        }
        .table td:nth-child(3),
        .table th:nth-child(3) {
            text-align: right;
        }
        .total-table {
            width: 100%;
            margin-top: 10px;
        }
        .total-table td {
            padding: 8px;
            font-size: 14px;
        }
        .total-table td:nth-child(1) {
            text-align: left;
        }
        .total-table td:nth-child(2) {
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        .total-table td:nth-child(3) {
            text-align: right;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }
        .full-width {
            width: 100%;
            margin-top: 10px;
        }
        .signature {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }
        .signature-line {
            font-size: 14px;
            display: flex;
            justify-content: center;
            gap: 100px;
            margin-top: 10px;
        }
        .note {
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">ใบเสร็จรับเงิน (Receipt)</div>
        <table class="table-info">
            <tr>
                <td>
                    <strong>กิจติณธร เจริญใจ</strong><br>
                    333,333/4 ม.6 แขวงเทพารักษ์ ถ.เทพารักษ์ ต.เทพารักษ์ อ.เมือง สมุทรปราการ จ.สมุทรปราการ<br>
                    โทร. 0922616777<br>
                    <strong>ลูกค้า (Customer)</strong><br>
                    นางสาว กนกวรรณ ยินดีเหลือ<br>
                    เลขประจำตัวผู้เสียภาษี 1331000216115<br>
                    โทร 0644987778
                </td>
                <td style="text-align: right;">
                    <strong>ต้นฉบับ (Original)</strong><br>
                    เลขที่ (ID) RC202409000064<br>
                    วันที่ (Date) 05/10/2024<br>
                    ห้อง (Room) A101<br>
                    พนักงาน (Staff): นิดา มูลสาร<br>
                    เลขที่อ้างอิง (Ref) INV202409000001
                </td>
            </tr>
        </table>
        <div class="full-width">
            <table class="table">
                <tr>
                    <th>ลำดับ (#)</th>
                    <th>รายการชำระ (Description)</th>
                    <th>ราคา (Price)</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ค่าเช่าห้อง (Room rate) A101 เดือน 9/2024</td>
                    <td>4,000.00</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ค่าน้ำ (Water rate) เดือน 9/2024 (9 ยูนิต)</td>
                    <td>162.00</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>ค่าไฟฟ้า (Electrical rate) เดือน 9/2024 (221 ยูนิต)</td>
                    <td>1,547.00</td>
                </tr>
            </table>
        </div>
        <table class="total-table">
            <tr>
                <td>(ห้าพันเจ็ดร้อยเก้าบาทถ้วน)</td>
                <td>จำนวนเงินรวมทั้งหมด <br>(Total amount)</td>
                <td>5,709.00 บาท</td>
            </tr>
        </table>
        <div class="note">หมายเหตุ (Note):</div>
        <div class="signature">
            <div class="signature-line">
                <span>ลงชื่อ ................................................. ผู้รับเงิน</span>
            </div>
            <div class="signature-line">
                <span>( ................................................. )</span>
            </div>
        </div>
        <hr style="border: 1px dashed #404040;margin:10px 0;">
        <div class="header">ใบเสร็จรับเงิน (Receipt)</div>
        <table class="table-info">
            <tr>
                <td>
                    <strong>กิจติณธร เจริญใจ</strong><br>
                    333,333/4 ม.6 แขวงเทพารักษ์ ถ.เทพารักษ์ ต.เทพารักษ์ อ.เมือง สมุทรปราการ จ.สมุทรปราการ<br>
                    โทร. 0922616777<br>
                    <strong>ลูกค้า (Customer)</strong><br>
                    นางสาว กนกวรรณ ยินดีเหลือ<br>
                    เลขประจำตัวผู้เสียภาษี 1331000216115<br>
                    โทร 0644987778
                </td>
                <td style="text-align: right;">
                    <strong>สำเนา (Copy)</strong><br>
                    เลขที่ (ID) RC202409000064<br>
                    วันที่ (Date) 05/10/2024<br>
                    ห้อง (Room) A101<br>
                    พนักงาน (Staff): นิดา มูลสาร<br>
                    เลขที่อ้างอิง (Ref) INV202409000001
                </td>
            </tr>
        </table>
        <div class="full-width">
            <table class="table">
                <tr>
                    <th>ลำดับ (#)</th>
                    <th>รายการชำระ (Description)</th>
                    <th>ราคา (Price)</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ค่าเช่าห้อง (Room rate) A101 เดือน 9/2024</td>
                    <td>4,000.00</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ค่าน้ำ (Water rate) เดือน 9/2024 (9 ยูนิต)</td>
                    <td>162.00</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>ค่าไฟฟ้า (Electrical rate) เดือน 9/2024 (221 ยูนิต)</td>
                    <td>1,547.00</td>
                </tr>
            </table>
        </div>
        <table class="total-table">
            <tr>
                <td>(ห้าพันเจ็ดร้อยเก้าบาทถ้วน)</td>
                <td>จำนวนเงินรวมทั้งหมด <br>(Total amount)</td>
                <td>5,709.00 บาท</td>
            </tr>
        </table>
        <div class="note">หมายเหตุ (Note):</div>
        <div class="signature">
            <div class="signature-line">
                <span>ลงชื่อ ................................................. ผู้รับเงิน</span>
            </div>
            <div class="signature-line">
                <span>( ................................................. )</span>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    window.print();
</script>