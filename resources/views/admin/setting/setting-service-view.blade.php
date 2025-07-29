<div class="row g-3">
    @foreach ($service as $item)
    <input type="hidden" name="ref_room_id" value="{{ $room_id }}">
        <div class="col-sm-4">
            <div class="form-check custom-option custom-option-basic" id="service{{ $item->id }}"
                @if (in_array($item->id, $service_id))
                    style="background-color: #c0fafa;"
                @endif
                >
                <label class="form-check-label custom-option-content p-2 px-3" for="ref_service_id{{ $item->id }}">
                    <input name="ref_service_id[]" class="form-check-input" type="checkbox" value="{{ $item->id }}"
                        id="ref_service_id{{ $item->id }}" 
                        @if (in_array($item->id, $service_id))
                            checked=""
                        @endif
                        onchange="handleCheckboxChange(this,{{ $item->id }})">
                    <span class="custom-option-header">
                        <span class="h6 mb-0">{{ $item->name }}</span>
                    </span>
                    <!-- <span class="custom-option-body">
                        <small>Get 1 project with 1 teams members.</small>
                    </span> -->
                    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="{{ $item->price }}" />
                </label>
            </div>
        </div>
    @endforeach
</div>
<script>
    function handleCheckboxChange(checkbox, id) {
        if (checkbox.checked) {
            // ทำอะไรเมื่อ checkbox ถูกเลือก
            console.log('Checkbox is checked');
            $('#service'+id).css('background-color','#c0fafa')
        } else {
            // ทำอะไรเมื่อ checkbox ไม่ถูกเลือก
            console.log('Checkbox is unchecked');
            $('#service'+id).css('background-color','unset')
        }
    }
</script>