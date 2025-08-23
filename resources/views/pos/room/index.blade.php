@extends('pos.layout.app')

@section('content')
    <div class="container-fluid">

        <!-- 🔍 Search -->
        <div class="d-flex flex-wrap align-items-center mb-4 gap-2">
            <div class="input-group" style="max-width:300px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" id="searchRoom" class="form-control" placeholder="Search room...">
            </div>
        </div>

        <!-- 🏠 Room Grid -->
        <div class="row g-3" id="roomGrid">
            @foreach ($rooms as $room)
                <div class="col-6 col-md-4 col-lg-3 room-card" data-name="{{ $room->name }}">
                    <div
                        class="card text-center border-0 shadow-sm
          {{ $room->is_busy ? 'bg-secondary text-white' : 'bg-purple text-white' }}">

                        <div class="card-body py-5">
                            <i class="bi bi-door-closed" style="font-size:2rem;"></i>
                        </div>
                       <div class="card-footer fw-bold {{ $room->is_busy ? 'bg-secondary text-white' : 'bg-light text-dark' }}">
    Room {{ $room->name }}
</div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>

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
@endsection
