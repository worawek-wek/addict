
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: center;
    }
    thead th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>
    <table class="datatables-basic table dataTable no-footer dtr-column" id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info">
        <thead class="border-top">
            <tr class=" table-info">
                <th class="text-center" tabindex="0" style="width: 10px;">
                    ลำดับ
                </th>
                <th class="text-center">
                    No_
                </th>
                <th class="text-center">
                    ชื่อสินค้า
                </th>
                <th class="text-center">
                    จำนวน
                </th>
                <th class="text-center">
                    ราคา
                </th>
                <th class="text-center">
                    ลูกค้า
                </th>
                <th class="text-center">
                    สาขา
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list_data as $key => $row)
            <tr class="odd">
                <td class="text-center">
                    {{ $key+1 }}
                </td>
                <td class="text-center">
                    {{ $row->order_number }}
                </td>
                <td class="text-center">
                    {{ $row->product_name }}
                </td>
                <td class="text-center">
                    {{ $row->quantity }}
                </td>
                <td class="text-center">
                    {{ $row->price*$row->quantity }} บาท
                </td>
                <td class="text-center">
                    {{ $row->customer_name }}
                </td>
                <td class="text-center">
                    {{ $row->branch_name }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>