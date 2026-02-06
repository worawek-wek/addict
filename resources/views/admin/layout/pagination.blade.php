<div class="row align-items-center">
    <!-- ซ้าย -->
    <div class="col-12 col-md-6 ps-4 text-start">
        <div class="dataTables_info" role="status" aria-live="polite">
            All &nbsp; {{ $list_data->total() }} &nbsp; entries
        </div>
    </div>

    <!-- ขวา -->
    <div class="col-12 col-md-6 pe-4 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0">
        <div class="dataTables_paginate paging_simple_numbers">
            @if ($list_data->lastPage() > 1)
                <ul class="pagination mb-0">
                    <!-- First -->
                    <li class="page-item {{ $list_data->currentPage() == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="javascript:void(0)"
                           onclick='loadData("{{ $list_data->url(1) }}")'>First</a>
                    </li>

                    @php
                        $total_links = 9;
                        $half = floor($total_links / 2);
                        $from = max(1, $list_data->currentPage() - $half);
                        $to = min($list_data->lastPage(), $list_data->currentPage() + $half);
                    @endphp

                    @for ($i = $from; $i <= $to; $i++)
                        <li class="page-item {{ $list_data->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="javascript:void(0)"
                               onclick='loadData("{{ $list_data->url($i) }}")'>{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($to < $list_data->lastPage())
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)"
                               onclick='loadData("{{ $list_data->url($list_data->lastPage()) }}")'>
                                {{ $list_data->lastPage() }}
                            </a>
                        </li>
                    @endif

                    <!-- Last -->
                    <li class="page-item {{ $list_data->currentPage() == $list_data->lastPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="javascript:void(0)"
                           onclick='loadData("{{ $list_data->url($list_data->lastPage()) }}")'>Last</a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
