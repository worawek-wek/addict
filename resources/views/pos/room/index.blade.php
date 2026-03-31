<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
      data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
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

    .modalHeadDecor .modal-header {
        padding: 0;
    }

    .modalHeadDecor .modal-title {
        padding: 1.25rem 1.5rem 1.25rem;
        color: white;
        background-color: #54BAB9;
        position: relative;
    }

    .modalHeadDecor .modal-title::after {
        position: absolute;
        top: 0;
        right: -65px;
        content: '';
        width: 0;
        height: 0;
        border-top: 65px solid #54BAB9;
        border-right: 65px solid transparent;
    }
</style>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar pt-3" style="background-color: #a1beff;">
    <div class="layout-container">

        <!-- Layout container -->
        <div>
            <!-- Navbar -->
            {{-- @include('admin/layout/inc_topmenu') --}}
            {{-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// --}}
        <div class="container-fluid">

            <!-- 🔍 Search -->
            <div class="d-flex align-items-center gap-2 mb-3">

                <!-- search -->
                <div class="input-group" style="max-width:300px;">
                    <span class="input-group-text bg-white">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" id="searchRoom" class="form-control" placeholder="Search room...">
                </div>

                <!-- button -->
                <a href="{{ url('pos/product') }}" class="btn btn-warning d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-shopping-cart"></i>
                    ขายสินค้า
                </a>
                <a href="{{ url('pos/drink') }}" class="btn btn-danger d-flex align-items-center justify-content-center gap-2">
                    <i class="fa fa-wine-glass"></i>
                    ขายดื่ม
                </a>
                <a href="{{ url('admin/order-rooms') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="ti ti-settings"></i>
                    หลังบ้าน
                </a>

            </div>
            
                                                {{-- <div class="input-group input-group-merge">
                                                    <span class="input-group-text" id="basic-addon-search31">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input 
                                                           name="search" type="text" class="form-control p_search"
                                                           placeholder="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-label="ค้นหาคีเวิร์ดที่ต้องการ"
                                                           aria-describedby="basic-addon-search31" />
                                                </div> --}}
    <style>
        .timer-box {
            text-align: center;
            color: white;
            border-radius: 6px;
            padding: 4px 0;
            font-family: monospace;
        }
    </style>
            <!-- 🏠 Room Grid -->
            <div class="row g-3" id="roomGrid">
                @php
                    $prevGroupId = null;
                @endphp

                @foreach ($rooms as $key => $room)

                    {{-- ถ้าเปลี่ยนหมวด → ขึ้นแถวใหม่ --}}
                    @if ($prevGroupId !== null && $prevGroupId != $room->room_group_id)
                        <div class="w-100"></div>
                    @endif
                    <div class="col-sm-1 room-card" data-name="{{ $room->name }}">
                            <div class="timer-box timer m-auto mb-1" data-start="{{ @$room->active_order->start_time }}" style="background-color: {{ $room->is_busy  ? '#6c757d' : '#5e2a5f' }};">00:00</div>
                        <div
                            class="card text-center border-0 shadow-sm {{ $room->is_busy ? 'bg-danger text-white' : 'bg-purple text-white' }}">

                            <div @if(!@$room->is_busy) onclick="window.location.href='{{ 'pos/'.$room->id }}'" @endif class="card-body py-5 px-0" onclick="view({{ @$room->order->id }}); return false;">
                                @if (isset($room->active_order))
                                    <div class="small mt-1">
                                        <span class="badge bg-white text-black">
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $room->active_order->start_time)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $room->active_order->end_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    @if (!empty($room->active_order->staff_name))
                                        <div class="small mt-1 text-white">
                                            <i class="ti ti-user"></i> {{ $room->active_order->staff_name }}
                                        </div>
                                    @endif
                                @else

                                    <i class="ti ti-door" style="font-size:2rem;"></i>
                                @endif
                            </div>
                            <div class="card-footer fw-bold {{ $room->is_busy ? 'bg-danger text-white' : 'bg-light text-dark' }} py-2" >
                                {{ $room->name }}
                                {{-- @if (isset($room->active_order))
                                    
                                @endif --}}
                            </div>

                        </div>
                    </div>
                    @php
                        $prevGroupId = $room->room_group_id;
                    @endphp
                @endforeach
            </div>

        </div>

                <!-- Footer -->
                @include('admin/layout/inc_footer')
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>


    <div class="modal fade" id="viewOrderRoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="view"></div>
    </div>

@include('admin/layout/inc_js')

</body>
</html>
    {{-- ================== STYLES ================== --}}
    <style>
        .bg-purple {
            background-color: #5e2a5f;
        }

        .room-card .card {
            cursor: pointer;
            transition: transform .2s;
        }

        .room-card .card:hover {
            transform: scale(1.03);
        }
    </style>

    {{-- ================== SCRIPT ================== --}}
    <script>
        function view(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('order-rooms.index') }}/" + id,
                success: function(data) {
                    $("#view").html(data);
                    $('#viewOrderRoomModal').modal('show');
                }
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            const rooms = document.querySelectorAll('.room-card');
            const searchInput = document.getElementById('searchRoom');

            // ค้นหาห้อง
            searchInput.addEventListener('input', () => {
                const q = searchInput.value.toLowerCase();
                rooms.forEach(r => {
                    r.style.display = r.dataset.name.toLowerCase().includes(q) ? 'block' : 'none';
                });
            });
        });
    </script>
    <script>
        function parseStartTime(startTimeStr) {
            const now = new Date();
            const [h, m] = startTimeStr.split(':').map(Number);

            let start = new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate(),
                h,
                m,
                0
            );

            // ถ้าเวลาเริ่มมากกว่าเวลาปัจจุบัน → ถือว่าเริ่มเมื่อวาน
            if (start > now) {
                start.setDate(start.getDate() - 1);
            }

            return start;
        }

        function formatTime(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
        }

        function updateAllTimers() {
            document.querySelectorAll('.timer').forEach(timer => {
                const startTimeStr = timer.dataset.start;
                if (!startTimeStr) return;

                const startDate = parseStartTime(startTimeStr);
                const now = new Date();
                const diffSeconds = Math.floor((now - startDate) / 1000);

                timer.innerText = formatTime(diffSeconds);
            });
        }

        // เริ่มทันที
        updateAllTimers();
        setInterval(updateAllTimers, 60000);
    </script>

