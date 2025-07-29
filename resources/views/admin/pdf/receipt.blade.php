<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบแจ้งหนี้ชั่วคราว</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .invoice { width: 69mm; margin: auto; font-size: 11px; }
        .header { display: flex; justify-content: space-between; align-items: center; font-weight: bold; font-size: 10px; }
        .title { flex-grow: 1; text-align: center; font-size: 11px; }
        .right-align { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11px; border-top: 1px solid #000; }
        th, td { padding: 2px; text-align: left; font-size: 11px; }
        th { border-bottom: 1px solid #000; }
        td { border-bottom: 1px solid #000; }
        @media print {
            body { width: 69mm; }
            .invoice { width: 69mm; }
        }
    </style>
    <script>
        window.onload = function() {
                window.print(); // เรียกใช้ฟังก์ชันพิมพ์หลังจากหน่วงเวลา
        }
    </script>
</head>
<body>
    <div class="invoice">
        <div class="header">
            <span class="title">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ใบแจ้งหนี้ชั่วคราว</span>
            <span class="right-align">No_: 603</span>
        </div>
        <p class="right-align"><strong>แคชเชียร์:</strong> Addict</p>
        <p><strong>ห้อง:</strong> 9</p>
        <p><strong>เปิดห้อง:</strong> 04/03/2025 12:38</p>
        <p><strong>เช็คบิล:</strong> 04/03/2025 12:39:33</p>
        <table>
            <tr>
                <th>จำนวน</th>
                <th>รายการสินค้า</th>
                <th>@ ราคา</th>
                <th>รวม</th>
            </tr>
            <tr>
                <td>1</td>
                <td>MANOW + 40 min Shower</td>
                <td>1,800</td>
                <td>1,800</td>
            </tr>
        </table>
    </div>
</body>
</html>
