<div class="modal-content border-0 rounded-3">
    {{-- Header --}}
    <div class="modal-header border-0" style="background-color:#4cc9f0; color:white;">
        <h5 class="modal-title d-flex align-items-center">
            <i class="ti ti-file-description me-2"></i> รายละเอียดการจองห้อง
            <span class="badge ms-3 {{ $orderRoom->badge_class ?? 'bg-secondary' }}">
                {{ $orderRoom->status_label ?? ($orderRoom->status->name ?? 'ไม่ระบุ') }}
            </span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    {{-- Body --}}
    <div class="modal-body bg-light p-4">
        {{-- ข้อมูลหลัก --}}
        <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
            <div class="row mb-3">
                <div class="col-md-6 d-flex">
                    <i class="ti ti-building me-2 text-primary"></i>
                    <div>
                        <strong>สาขา:</strong>
                        <div>{{ $orderRoom->branch->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <i class="ti ti-door me-2 text-primary"></i>
                    <div>
                        <strong>ห้อง:</strong>
                        <div>{{ $orderRoom->room->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 d-flex">
                    <i class="ti ti-user me-2 text-primary"></i>
                    <div>
                        <strong>ลูกค้า:</strong>
                        <div>{{ $orderRoom->customer->name ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <i class="ti ti-phone me-2 text-primary"></i>
                    <div>
                        <strong>เบอร์โทร:</strong>
                        <div>{{ $orderRoom->customer->phone ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 d-flex">
                    <i class="ti ti-user-check me-2 text-success"></i>
                    <div>
                        <strong>พนักงานนวด:</strong>
                        <div>{{ $orderRoom->user->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 d-flex">
                    <i class="ti ti-calendar me-2 text-primary"></i>
                    <div>
                        <strong>วันที่จอง:</strong>
                        <div>{{ \Carbon\Carbon::parse($orderRoom->booking_date)->format('d/m/Y') }}</div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <i class="ti ti-login me-2 text-primary"></i>
                    <div>
                        <strong>เวลาเช็คอิน:</strong>
                        <div>{{ \Carbon\Carbon::parse($orderRoom->start_time)->format('H:i') }}</div>
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                    <i class="ti ti-logout me-2 text-primary"></i>
                    <div>
                        <strong>เวลาเช็คเอาท์:</strong>
                        <div>{{ \Carbon\Carbon::parse($orderRoom->end_time)->format('H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- บริการเสริม --}}
        @if ($orderRoom->addons && $orderRoom->addons->count())
            <div class="bg-white p-3 rounded-3 shadow-sm">
                <strong class="d-flex align-items-center mb-2 text-success">
                    <i class="ti ti-plus me-2"></i> บริการเสริม:
                </strong>
                <ul class="list-unstyled mb-0">
                    @foreach ($orderRoom->addons as $addon)
                        <li class="mb-1 d-flex align-items-center">
                            <i class="ti ti-circle-check text-success me-2"></i>
                            {{ $addon->option->name ?? '-' }}
                            @if (!is_null($addon->price))
                                <span class="text-muted ms-2">
                                    - {{ number_format($addon->price, 2) }} บาท
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
