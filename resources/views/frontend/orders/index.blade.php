<!doctype html>
<html lang="en">

<head>
    <title>Addict - Order History</title>
    @include('frontend.layout.inc_header')
    <style>
        .highlighted-title {
            color: #fff;
            background-color: rgba(92, 44, 95, 0.6);
            padding: 10px 20px;
            border: 2px solid #fff;
            border-radius: 10px;
            display: inline-block;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
            font-size: 1.75rem;
        }

        .page-title-chip {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: #3a1b3d;
            background: rgba(255, 255, 255, .85);
            border: 1px solid rgba(92, 44, 95, .15);
            border-radius: 14px;
            padding: .5rem .9rem;
            box-shadow: 0 4px 16px rgba(92, 44, 95, .08);
            backdrop-filter: blur(2px);
        }

        .page-title-chip i {
            color: #5C2C5F;
        }

        .order-table {
            border-radius: 14px;
            overflow: hidden;
        }

        .order-table thead th {
            background: linear-gradient(90deg, #5C2C5F, #7b3b7f);
            color: #fff;
            border: 0;
            letter-spacing: .2px;
        }

        .order-table tbody tr:hover {
            background-color: #fbf6ff;
        }

        .order-table td,
        .order-table th {
            vertical-align: middle;
        }

        .badge-status {
            font-size: .85rem;
            padding: .42em .75em;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, .06);
            white-space: nowrap;
        }

        .badge-pending {
            background: #FFF2D9;
            color: #A56700;
        }

        .badge-success {
            background: #E6F7EA;
            color: #1E7A35;
        }

        .badge-cancel,
        .badge-overdue {
            background: #FFE7E7;
            color: #B42020;
        }

        .order-details strong {
            display: inline-block;
            min-width: 70px;
            text-align: right;
            margin-right: 5px;
        }

        .order-details ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .order-details li {
            font-size: 0.9em;
        }
    </style>
</head>

<body class="bg-addict bgs-100">
    <div class="container py-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
            <i class="fi fi-rr-arrow-small-left me-2"></i> Back
        </a>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="highlighted-title">
                <i class="fi fi-rr-file-invoice me-2"></i> Order History
            </h1>
        </div>

        <div class="card shadow">
            <div class="card-body">
                @if ($orders->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover order-table text-center align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Service Time</th>
                                <th>Details</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($order->booking_date)->format('j M Y') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($order->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($order->end_time)->format('H:i') }}
                                </td>
                                <td class="text-start order-details">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong style="width: 70px;">Branch:</strong>
                                        <span class="ms-2">{{ $order->branch->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <strong style="width: 70px;">Staff:</strong>
                                        <span class="ms-2">{{ $order->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <strong style="width: 70px;">Room:</strong>
                                        <span class="ms-2">{{ $order->room->name ?? 'N/A' }}</span>
                                    </div>

                                    @if ($order->addons->isNotEmpty())
                                        <div class="mt-2">
                                            <strong>Add-ons:</strong>
                                            <ul class="list-unstyled mb-0 ms-4">
                                            @foreach ($order->addons as $addon)
                                                <li>- {{ $addon->option->name ?? 'N/A' }} ({{ number_format($addon->price, 2) }} THB)</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center mt-2">
                                            <strong style="width: 70px;">Add-ons:</strong>
                                            <span class="ms-2">None</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($order->total_price, 2) }}
                                </td>
                                <td>
                                    @php
                                        $orderDateTime = \Carbon\Carbon::parse(
                                            $order->booking_date . ' ' . $order->start_time,
                                        );
                                        $statusName = trim($order->status->name ?? 'N/A');
                                        $class = 'badge-status';

                                        if ($orderDateTime->isPast() && ($order->status->id ?? null) === 1) {
                                            $statusName = 'Overdue';
                                            $class .= ' badge-overdue';
                                        } else {
                                            if ($statusName === 'รอดำเนินการ') {
                                                $statusName = 'Pending';
                                                $class .= ' badge-pending';
                                            } elseif ($statusName === 'สำเร็จ') {
                                                $statusName = 'Completed';
                                                $class .= ' badge-success';
                                            } elseif ($statusName === 'ยกเลิก') {
                                                $statusName = 'Cancelled';
                                                $class .= ' badge-cancel';
                                            }
                                        }
                                    @endphp
                                    <span class="{{ $class }}">{{ $statusName }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fi fi-rr-folder-open fs-1 mb-2"></i>
                    <p class="mb-0">No order history found.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('frontend.layout.inc_footer')
    @include('frontend.layout.inc_js')
</body>

</html>
