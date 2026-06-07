@php
    [$selectedStartDate, $selectedEndDate] = \App\Support\AdminBusinessDay::rangeFromRequest(request());
@endphp

<div class="d-flex align-items-center px-3 py-2 mb-3 rounded"
     style="background:#eef4ff; font-size:13px; font-weight:600;">
    <i class="ti ti-calendar me-2"></i>
    ช่วงวันที่ที่เลือก:
    <span class="ms-2">
        {{ $selectedStartDate->format('d/m/Y H:i') }} - {{ $selectedEndDate->format('d/m/Y H:i') }}
    </span>
</div>
