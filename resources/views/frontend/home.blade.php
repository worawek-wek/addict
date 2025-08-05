<!doctype html>
<html lang="th">

<head>
    <title>Addict - Home</title>
    @include('frontend.layout.inc_header')
</head>

<body class="bg-addict bgs-cover">
    @include('frontend.layout.inc_navbarAccount')
    <div class="container">
        <div class="card p-xl-4 mb-5">
            <div class="card-body">
                <h1 class="text-center ff-playfair">Booking</h1>
                {{-- <form id="insert_service" method="POST" action="{{ route('insert') }}"> --}}
                <form id="insert_service" method="POST">

                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label fs-14 mb-0">Date</label>
                            <input type="date" class="form-control" id="inputDate" name="booking_date"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="exampleFormControlInput1" class="form-label fs-14 mb-0">Time</label>
                            <input type="time" class="form-control" id="inputTime" name="booking_time"
                                value="{{ \Carbon\Carbon::now()->format('H:i') }}">
                        </div>
                        <div class="col-12">
                            <h4 class="bg-cream ff-playfair p-2">Staff</h4>
                        </div>
                        <div class="col-12">
                            <form>
                                <div class="search-container mb-3">
                                    <input type="text" id="search-bar" placeholder="Search">
                                    <a href="#"><i class="search-icon fi fi-rr-search"></i></a>
                                </div>
                            </form>
                        </div>
                        <div class="col-12">
                            <div class="scroll">
                                <div class="row g-3">
                                    @foreach ($user as $index => $row)
                                        <div class="col-6 col-sm-3 user-card"
                                            data-name="{{ strtolower($row->name . ' ' . $row->nickname) }}">
                                            <div class="card p-2">
                                                <input class="form-check-input mb-2" type="radio" name="selected_user"
                                                    value="{{ $row->id }}" id="user{{ $index }}"
                                                    data-nickname="{{ $row->nickname ?? $row->name }}"
                                                    data-image="/upload/user/{{ $row->image_name ?? 'default.png' }}"
                                                    data-salary="{{ $row->salary }}"
                                                    {{ $index === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label d-block" for="user{{ $index }}">
                                                    <img src="/upload/user/{{ $row->image_name ?? 'default.png' }}"
                                                        alt="" class="mw-100 mb-2">
                                                    <p class="fw-medium mb-0">{{ $row->nickname ?? $row->name }}</p>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                        {{-- <div class="col-12">
                            <h4 class="bg-cream ff-playfair p-2">Service Course</h4>
                        </div>
                        <div class="col-12">
                            <label for="exampleFormControlInput1" class="form-label fs-14 mb-0">Service
                                Course</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" class="btn-check" name="options" id="option1" autocomplete="off"
                                    checked>
                                <label class="btn btn-purple-check flex-fill rounded-0" for="option1">Service A</label>

                                <input type="radio" class="btn-check" name="options" id="option2"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0" for="option2">Service B</label>

                                <input type="radio" class="btn-check" name="options" id="option3"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0" for="option3">Service C</label>

                                <input type="radio" class="btn-check" name="options" id="option4"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0" for="option4">Service D</label>

                            </div>
                        </div> --}}
                        <div class="col-12">
                            <h4 class="bg-cream ff-playfair p-2">Room</h4>
                        </div>

                        <div class="col-12">
                            <label for="roomType" class="form-label fs-14 mb-0">1. Type Room</label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($rooms as $index => $room)
                                    <input type="radio" class="btn-check" name="roomType"
                                        id="roomType{{ $room->id }}" value="{{ $room->id }}"
                                        data-name="{{ $room->name }}" data-forty="{{ $room->forty_minutes }}"
                                        data-sixty="{{ $room->sixty_minutes }}"
                                        data-ninety="{{ $room->ninety_minutes }}" autocomplete="off"
                                        {{ $index === 0 ? 'checked' : '' }}>

                                    <label
                                        class="btn btn-purple-check d-flex flex-column justify-content-center text-center"
                                        style="width: 19%; min-width: 120px;" for="roomType{{ $room->id }}">

                                        {{-- SVG ไอคอนเตียง --}}
                                        <svg class="mb-2 mx-auto" width="32" viewBox="0 0 53 53"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <g>
                                                    <path
                                                        d="m32.7871094 23.1909828h-12.5800782c-.4140625 0-.75-.3359375-.75-.75s.3359375-.75.75-.75h12.5800781c.4140625 0 .75.3359375.75.75s-.3359374.75-.7499999.75z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m44.546875 23.1909828h-6.2294922c-.4140625 0-.75-.3359375-.75-.75s.3359375-.75.75-.75h5.4794922v-11.0600586c0-.3916016-.2636719-.7241211-.6416016-.8076172-11.6259766-2.4921875-22.8417969-2.5-33.3359375-.0224609-.3583984.0869141-.6220703.4238281-.6220703.7998047v11.090332h5.4892578c.4140625 0 .75.3359375.75.75s-.3359375.75-.75.75h-6.2392578c-.4140625 0-.75-.3359375-.75-.75v-11.840332c0-1.0595703.7460938-2.0092773 1.7734375-2.2587891 10.7138672-2.5292969 22.1523438-2.5244141 34.0039062.015625 1.0751953.2387695 1.8222656 1.1728516 1.8222656 2.2734375v11.8100586c.0000001.4140625-.3359374.75-.7499999.75z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m32.7871094 23.1909828h-12.5800782c-.4140625 0-.75-.3359375-.75-.75s.3359375-.75.75-.75h12.5800781c.4140625 0 .75.3359375.75.75s-.3359374.75-.7499999.75z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m48.2578125 31.0708656c-.2822266 0-.5517578-.1591797-.6796875-.4306641l-3.5068359-7.4492188h-5.7539062c-.4140625 0-.75-.3359375-.75-.75s.3359375-.75.75-.75h6.2294922c.2900391 0 .5546875.1674805.6787109.4306641l3.7099609 7.8798828c.1767578.3745117.015625.8212891-.359375.9980469-.1035157.0483399-.2119141.0712891-.3183594.0712891z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m49.4970703 38.5708656c-.4140625 0-.75-.3359375-.75-.75v-5.6401367c0-.5112305-.3066406-.9697266-.7802734-1.1679688-.0507812-.0205078-.1240234-.0380859-.1933594-.0566406-.9179688-.2011719-1.8398438-.3891602-2.7519531-.5581055-.0263672-.0024414-.0810547-.0141602-.1347656-.0268555-.7949219-.1523438-1.5175781-.2709961-2.2421875-.3901367-.2421875-.0463867-.4726562-.081543-.6972656-.1157227-2.1269531-.3505859-4.1894531-.6210938-6.2226562-.8178711-.5332031-.0571289-1.0605469-.1044922-1.5859375-.1518555-2.0185547-.1674805-3.7753906-.2685547-5.4521484-.3154297-.9960938-.0292969-1.8974609-.0395508-2.7998047-.0395508-1.3623047 0-2.7402344.0302734-4.0966797.0898438l-.3662109.0170898c-.2441406.0107422-.4853516.0214844-.7255859.0410156-.4208984.0200195-.8164062.046875-1.21875.0742188-1.8603516.1279297-3.6005859.3017578-5.2636719.5214844-.8125.1030273-1.6123047.222168-2.4189453.3427734l-.3554688.0556641c-.3613281.059082-.7275391.1186523-1.0878906.1855469-.8623047.1503906-1.7275391.3198242-2.5927734.4887695-.8427734.1762695-1.6943359.3647461-2.5576172.5727539-.0175781.0043945-.0810547.0180664-.0996094.0214844-.2802734.1503906-.6357422.1176758-.8789062-.1123047-.2998047-.2856445-.3164062-.7553711-.0302734-1.0551758.0595703-.0629883.1396484-.125.2177734-.1640625.0791016-.0400391.2099609-.1049805.3837891-.1357422.0244141-.0053711.0478516-.0102539.0625-.0126953.8701172-.2114258 1.7373047-.402832 2.6054688-.5844727.8847656-.1733398 1.7597656-.34375 2.6240234-.4946289.3632812-.0673828.7402344-.1293945 1.1132812-.1899414l.3740234-.0585938c.8183594-.1220703 1.6289062-.2431641 2.4472656-.347168 1.6904297-.2231445 3.4628906-.3999023 5.2626953-.5239258.5068359-.034668.9111328-.0620117 1.3164062-.0805664.2333984-.0195312.4931641-.03125.7558594-.0429688l.3603516-.0170898c2.3037109-.1010742 4.6220703-.1264648 6.9238281-.0507812 1.7919922.0473633 3.5742188.1494141 5.5371094.3120117.6289062.0566406 1.1640625.1044922 1.6982422.1621094 2.0585938.1992188 4.1552734.4741211 6.2216797.815918.3183594.0483398.5683594.0869141.8095703.1328125.7167969.1176758 1.453125.2392578 2.1894531.3798828.0234375.0029297.0751953.0141602.1269531.0258789.9960938.1845703 1.9335938.3764648 2.8613281.578125.203125.0537109.3369141.0913086.4501953.137207 1.0439453.4365234 1.7109375 1.4384766 1.7109375 2.5556641v5.6401367c-.0000001.4140627-.3359376.7500002-.7500001.7500002z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m4.7460938 31.0405922c-.1064453 0-.2158203-.0229492-.3183594-.0717773-.375-.1762695-.5361328-.6235352-.359375-.9980469l3.7001953-7.8496094c.1240234-.2626953.3886719-.4301758.6787109-.4301758h6.2392578c.4140625 0 .75.3359375.75.75s-.3359375.75-.75.75h-5.7636718l-3.4970703 7.4194336c-.1279297.2714844-.397461.4301758-.6796875.4301758z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m48.5693359 46.5513344h-1.3242188c-.5380859 0-1.046875-.2602539-1.3623047-.6962891l-1.4072266-1.9482422c-.1875-.2602539-.4912109-.4155273-.8125-.4155273h-34.3271483c-.3212891 0-.625.1552734-.8125.4155273l-1.4072266 1.9482422c-.3154297.4360352-.8242188.6962891-1.3623047.6962891h-1.3242187c-.9257812 0-1.6796875-.753418-1.6796875-1.6796875v-12.7294922c0-1.2832031.8652344-2.3842773 2.1044922-2.6772461 13.625-3.2182617 28.1689453-3.2104492 43.2324219.0239258 1.2539062.2690429 2.1630859 1.399414 2.1630859 2.6879883v12.6948242c0 .9262695-.7539062 1.6796875-1.6806641 1.6796875zm-39.2333984-4.5600586h34.3271484c.8017578 0 1.5605469.3876953 2.0292969 1.0375977l1.4072266 1.9482422c.0332031.0463867.0878906.0742188.1455078.0742188h1.3242188c.0996094 0 .1806641-.0805664.1806641-.1796875v-12.6948243c0-.5859375-.4111328-1.0996094-.9775391-1.2211914-14.8447266-3.1884766-29.1689453-3.1977539-42.5722656-.0307617-.5507813.1298827-.9501954.6420898-.9501954 1.2172851v12.7294922c0 .0991211.0810547.1796875.1796875.1796875h1.3242188c.0576172 0 .1123047-.027832.1455078-.0742188l1.4072266-1.9482422c.4687499-.6499023 1.227539-1.0375976 2.0292968-1.0375976z" />
                                                </g>
                                                <g>
                                                    <path
                                                        d="m48.5693359 46.5513344h-1.3242188c-.5380859 0-1.046875-.2602539-1.3623047-.6962891l-1.4072266-1.9482422c-.1875-.2602539-.4912109-.4155273-.8125-.4155273h-34.3271483c-.3212891 0-.625.1552734-.8125.4155273l-1.4072266 1.9482422c-.3154297.4360352-.8242188.6962891-1.3623047.6962891h-1.3242187c-.9257812 0-1.6796875-.753418-1.6796875-1.6796875v-7.0541992c0-.4140625.3359375-.75.75-.75h46c.4140625 0 .75.3359375.75.75v7.0541992c0 .9262695-.7539062 1.6796875-1.6806641 1.6796875zm-39.2333984-4.5600586h34.3271484c.8017578 0 1.5605469.3876953 2.0292969 1.0375977l1.4072266 1.9482422c.0332031.0463867.0878906.0742188.1455078.0742188h1.3242188c.0996094 0 .1806641-.0805664.1806641-.1796875v-6.3041992h-44.5000001v6.3041992c0 .0991211.0810547.1796875.1796875.1796875h1.3242188c.0576172 0 .1123047-.027832.1455078-.0742188l1.4072266-1.9482422c.4687499-.6499024 1.227539-1.0375977 2.0292968-1.0375977z" />
                                                </g>
                                                <g>
                                                    <g>
                                                        <path
                                                            d="m17.4492188 25.0879555c-.7607422 0-1.4755859-.2963867-2.0126953-.8339844l-2.9160156-2.9155273c-.5380859-.5375977-.8339844-1.2524414-.8339844-2.0126953 0-.7597656.2958984-1.4746094.8339844-2.012207l2.9160156-2.9155273c1.0742188-1.074707 2.9501953-1.0756836 4.0244141 0l2.9160156 2.9155273c1.109375 1.1098633 1.109375 2.9150391 0 4.0249023l-2.9160156 2.9155273c-.5371095.5375977-1.2519532.8339844-2.0117188.8339844zm0-10.0239258c-.3603516 0-.6982422.1401367-.9521484.3945312l-2.9160156 2.9155273c-.2548828.2543945-.3945312.5927734-.3945312.9516602 0 .359375.1396484.6977539.3945312.9521484l2.9160156 2.9155273c.5068359.5087891 1.3945312.5097656 1.9033203 0l2.9160156-2.9155273c.5244141-.5249023.5244141-1.3789062 0-1.9038086l-2.9160156-2.9155273c-.2539063-.2543945-.5917969-.3945312-.9511719-.3945312zm4.3974609 5.7441406h.0097656z" />
                                                    </g>
                                                    <g>
                                                        <path
                                                            d="m35.5507812 25.0879555c-.7597656 0-1.4746094-.2963867-2.0126953-.8339844l-2.9150391-2.9155273c-.5380859-.5375977-.8339844-1.2524414-.8339844-2.0126953 0-.7597656.2958984-1.4746094.8339844-2.012207l2.9150391-2.9155273c1.0761719-1.0761719 2.9521484-1.074707 4.0253906 0l2.9160156 2.9155273c1.109375 1.1098633 1.109375 2.9150391 0 4.0249023l-2.9160156 2.9155273c-.5371093.5375977-1.2519531.8339844-2.0126953.8339844zm0-10.0239258c-.359375 0-.6972656.1401367-.9521484.3945312l-2.9150391 2.9155273c-.2548828.2543945-.3945312.5927734-.3945312.9516602 0 .359375.1396484.6977539.3945312.9521484l2.9150391 2.9155273c.5107422.5092773 1.3974609.5083008 1.9042969 0l2.9160156-2.9155273c.5244141-.5249023.5244141-1.3789062 0-1.9038086l-2.9160156-2.9155273c-.2539063-.2543945-.5917969-.3945312-.9521485-.3945312zm4.3984376 5.7441406h.0097656z" />
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>

                                        {{ $room->name }}
                                    </label>
                                @endforeach
                            </div>

                        </div>

                        {{-- <div class="col-12">
                            <label for="exampleFormControlInput1" class="form-label fs-14 mb-0">2.Room number</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" class="btn-check" name="roomNumber" id="roomNumber1"
                                    autocomplete="off" checked>
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber1">FR001</label>

                                <input type="radio" class="btn-check" name="roomNumber" id="roomType2"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber2">FR002</label>

                                <input type="radio" class="btn-check" name="roomNumber" id="roomNumber3"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber3">FR003</label>
                                <input type="radio" class="btn-check" name="roomNumber" id="roomNumber4"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber4">FR004</label>

                                <input type="radio" class="btn-check" name="roomNumber" id="roomType5"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber5">FR005</label>

                                <input type="radio" class="btn-check" name="roomNumber" id="roomNumber6"
                                    autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0"
                                    for="roomNumber6">FR006</label>
                            </div>
                        </div> --}}
                        <div class="col-12">
                            <h4 class="bg-cream ff-playfair p-2">Time Period</h4>
                        </div>
                        <div class="col-12">
                            <label class="form-label fs-14 mb-0">Duration of service use</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="radio" class="btn-check" name="timeService" id="40min"
                                    value="forty_minutes" autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0" for="40min">
                                    40 minutes/service
                                </label>

                                <input type="radio" class="btn-check" name="timeService" id="60min"
                                    value="sixty_minutes" autocomplete="off" checked>
                                <label class="btn btn-purple-check flex-fill rounded-0" for="60min">
                                    60 minutes/service
                                </label>

                                <input type="radio" class="btn-check" name="timeService" id="90min"
                                    value="ninety_minutes" autocomplete="off">
                                <label class="btn btn-purple-check flex-fill rounded-0" for="90min">
                                    90 minutes/service
                                </label>
                            </div>

                        </div>


                        <div class="col-12">
                            <div class="card card-body">
                                <h4 class="ff-playfair">Summary</h4>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <h6>Date and time of service use</h6>
                                        <ul>
                                            <li id="summary-date">dd/mm/yyyy</li>
                                            <li id="summary-time">hh:mm</li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        {{-- <h6>Service</h6>
                                        <ul>
                                            <li id="summary-service">Service A</li>
                                        </ul> --}}
                                        <h6>Room</h6>
                                        <ul>
                                            <li id="summary-room-type">Type room - First room</li>
                                            {{-- <li id="summary-room-number">Room number - FR001</li> --}}
                                        </ul>

                                        <h6>Time Period</h6>
                                        <ul>
                                            <li id="summary-duration">60 mins/service</li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <div id="selected-staff-list"></div>
                                    </div>
                                    <div class="col-sm-12">
                                        <h4 class="text-end text-purple">
                                            <span class="fs-12 fw-normal">THB</span>
                                            <span id="summary-price">0.00</span>
                                        </h4>
                                        <button class="btn btn-purple rounded-0 w-100"
                                            type="submit">Checkout</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('frontend.layout.inc_footer')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#menu li').eq(4).addClass('active');
    </script>
    <script>
        document.getElementById('search-bar').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const cards = document.querySelectorAll('.user-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                card.style.display = name.includes(keyword) ? 'block' : 'none';
            });
        });

        function removeSelectedStaff() {
            const checked = document.querySelector('input[name="selected_user"]:checked');
            if (checked) {
                checked.checked = false;
                updateSummary();
            }
        }

        function updateSummary() {
            const inputDate = document.getElementById('inputDate');
            const inputTime = document.getElementById('inputTime');
            const summaryDate = document.getElementById('summary-date');
            const summaryTime = document.getElementById('summary-time');
            const summaryService = document.getElementById('summary-service');
            const summaryRoomType = document.getElementById('summary-room-type');
            const summaryRoomNumber = document.getElementById('summary-room-number');
            const summaryDuration = document.getElementById('summary-duration');
            const staffListContainer = document.getElementById('selected-staff-list');
            const summaryPrice = document.getElementById('summary-price');

            const date = inputDate.value;
            const dateParts = date.split('-');
            if (dateParts.length === 3) {
                summaryDate.textContent = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
            }

            summaryTime.textContent = inputTime.value;

            const selectedService = document.querySelector('input[name="options"]:checked');
            if (selectedService) {
                const label = document.querySelector(`label[for="${selectedService.id}"]`);
                summaryService.textContent = label?.innerText || '-';
            }

            const selectedRoomType = document.querySelector('input[name="roomType"]:checked');
            let roomPrice = 0;
            if (selectedRoomType) {
                const label = document.querySelector(`label[for="${selectedRoomType.id}"]`);
                summaryRoomType.textContent = `Type room - ${label?.innerText.trim()}`;

                const forty = parseFloat(selectedRoomType.dataset.forty || 0);
                const sixty = parseFloat(selectedRoomType.dataset.sixty || 0);
                const ninety = parseFloat(selectedRoomType.dataset.ninety || 0);
                const duration = document.querySelector('input[name="timeService"]:checked')?.id;

                if (duration === '40min') roomPrice = forty;
                else if (duration === '60min') roomPrice = sixty;
                else if (duration === '90min') roomPrice = ninety;
            }

            const selectedRoomNumber = document.querySelector('input[name="roomNumber"]:checked');
            if (selectedRoomNumber) {
                const label = document.querySelector(`label[for="${selectedRoomNumber.id}"]`);
                summaryRoomNumber.textContent = `Room number - ${label?.innerText.trim()}`;
            }

            const selectedDuration = document.querySelector('input[name="timeService"]:checked');
            if (selectedDuration) {
                const label = document.querySelector(`label[for="${selectedDuration.id}"]`);
                summaryDuration.textContent = label?.innerText || '-';
            }

            staffListContainer.innerHTML = '';
            const selectedStaff = document.querySelector('input[name="selected_user"]:checked');
            let staffSalary = 0;
            if (selectedStaff) {
                const nickname = selectedStaff.dataset.nickname;
                const image = selectedStaff.dataset.image;
                staffSalary = parseFloat(selectedStaff.dataset.salary || 0);
                staffListContainer.innerHTML = `
                <div class="card p-2 mb-2">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <img src="${image}" alt="" class="image-square" style="width:40px; height:40px; object-fit:cover;">
                        </div>
                        <div class="flex-grow-1 ms-2">
                            ${nickname}
                            <button type="button" class="btn p-0" onclick="removeSelectedStaff()">
                                <i class="fi fi-rr-circle-xmark text-danger"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            }

            const total = roomPrice + staffSalary;
            summaryPrice.textContent = total.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
        }

        document.addEventListener('DOMContentLoaded', updateSummary);

        ['inputDate', 'inputTime'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateSummary);
        });

        document.querySelectorAll(
            'input[name="options"], input[name="roomType"], input[name="roomNumber"], input[name="timeService"], input[name="selected_user"]'
        ).forEach(el => {
            el.addEventListener('change', updateSummary);
        });
    </script>

    <script>
        $('#insert_service').on('submit', function(e) {
            e.preventDefault(); // หยุดการ submit ปกติ

            // Step 1: แสดง popup แจ้งเตือนก่อน
            Swal.fire({
                icon: 'info',
                title: 'โปรดตรวจสอบวันและเวลา',
                html: `
            🔔 <strong>กรุณาตรวจสอบวันและเวลาก่อนกดยืนยันการจอง</strong><br>
            หากมาช้ากว่า 15 นาทีโดยไม่แจ้ง ทางร้านขอสงวนสิทธิ์ในการยกเลิกการจองค่ะ<br><br>
            🔔 <strong>Please double-check your date and time before confirming.</strong><br>
            If you arrive more than 15 minutes late without notice, your booking may be cancelled.
        `,
                confirmButtonText: 'ตรวจสอบแล้ว',
                showCancelButton: true,
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Step 2: Popup ยืนยันการทำรายการ
                    Swal.fire({
                        title: 'ยืนยันการดำเนินการ?',
                        text: 'คุณต้องการทำรายการหรือไม่?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก',
                        // reverseButtons: true
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            const form = $('#insert_service');
                            const formData = form.serialize();

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('insert') }}",
                                data: formData,
                                beforeSend: function() {
                                    Swal.fire({
                                        title: 'กำลังดำเนินการ...',
                                        text: 'กรุณารอสักครู่',
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        willOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                },
                                success: function(response) {
                                    Swal.close();

                                    const win = window.open('', '_blank');
                                    win.document.open();
                                    win.document.write(response);
                                    win.document.close();

                                    win.onload = () => {
                                        win.focus();
                                        win.print();
                                    };

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ทำรายการสำเร็จ',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง'
                                    });
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>


</body>

</html>
