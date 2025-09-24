<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    @include('admin/layout/inc_header')
    <title>ดูรายการ Order พนักงานขาย - CRM</title>
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
                                            <div class="col-sm-12 mb-2">
                                                <h4 class="mb-0">
                                                    <i class="tf-icons ti ti-list-details text-main ti-md me-2"></i>
                                                    รายการ Order ของ {{ $user->name }}{{ $user->nickname ? ' (' . $user->nickname . ')' : '' }}
                                                </h4>
                                                <div class="mt-2 text-muted">
                                                    วันที่: {{ $startDate }} ถึง {{ $endDate }}
                                                </div>
                                            </div>
                                            <div class="col-sm-12 d-flex justify-content-end gap-2">
                                                <a href="{{ route('commission.view_sales') }}" class="btn btn-info">
                                                    <i class="ti ti-arrow-left"></i> กลับหน้าคอมมิชชั่น
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body px-0 pt-0">
                                        <div class="table-responsive">
                                            <table class="datatables-basic table dataTable no-footer dtr-column" id="orders-table-view" aria-describedby="orders-table-view_info">
                                                <thead class="border-top">
                                                    <tr class="table-info">
                                                        <th class="text-center" style="width: 10px;">#</th>
                                                        <th class="text-center">วันที่จอง</th>
                                                        <th class="text-center">เลขที่ Order</th>
                                                        <th class="text-center">ลูกค้า</th>
                                                        <th class="text-center">สาขา</th>
                                                        <th class="text-center">พนักงานนวด</th>
                                                        <th class="text-center">ยอดขาย</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orders as $i => $order)
                                                    <tr>
                                                        <td class="text-center">{{ $i + 1 }}</td>
                                                        <td class="text-center">{{ $order->booking_date }}</td>
                                                        <td class="text-center">
                                                            <a href="#" class="order-detail-link" data-order-id="{{ $order->id }}">{{ $order->order_number }}</a>
                                                        </td>
                                                        <td class="text-center">{{ $order->customer ? $order->customer->name : '-' }}</td>
                                                        <td class="text-center">{{ $order->branch ? $order->branch->name : '-' }}</td>
                                                        <td class="text-center">{{ $order->user ? $order->user->name : '-' }}</td>
                                                        <td class="text-center">{{ number_format($order->total_price, 2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                            <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                                            <script>
                                                $(document).ready(function() {
                                                    $('#orders-table-view').DataTable({
                                                        language: {
                                                            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
                                                        },
                                                        pageLength: 10,
                                                        ordering: true,
                                                        searching: true,
                                                        lengthChange: false
                                                    });

                                                    // Order detail popup
                                                    $('.order-detail-link').on('click', function(e) {
                                                        e.preventDefault();
                                                        var orderId = $(this).data('order-id');
                                                        $.ajax({
                                                            url: '/admin/commission/order-detail/' + orderId,
                                                            type: 'GET',
                                                            success: function(res) {
                                                                $('#orderDetailModal .modal-body').html(res);
                                                                $('#orderDetailModal').modal('show');
                                                            }
                                                        });
                                                    });
                                                });
                                            </script>
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
        @include('admin/layout/inc_js')

        <!-- Order Detail Modal -->
        <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderDetailModalLabel">รายละเอียด Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- AJAX content here -->
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
