{{-- หน้า POS --}}
<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
    data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">

<head>
    @include('admin/layout/inc_header')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <title>Dashboard - CRM | Vuexy - Bootstrap Admin Template</title>
</head>
<link rel="stylesheet" href="../../assets/vendor/libs/spinkit/spinkit.css" />
@if (isset($room->active_order))
    <div
        style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(234, 244, 255, 0.85);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
">
        <div
            style="
        background: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-width: 400px;
        width: 90%;
    ">
            <h4 style="margin-bottom: 10px; color: #d9534f;">
                ห้องนี้กำลังถูกใช้งาน
            </h4>

            <p style="margin-bottom: 15px;">
                ไม่สามารถทำรายการได้ในขณะนี้
            </p>

            {{-- @if (!empty($room->active_order->staff_name))
        <p style="margin-bottom: 20px; font-size: 14px; color: #555;">
            ผู้ดูแล: {{ $room->active_order->staff_name }}
        </p>
        @endif --}}

            <a href="/pos/room"
                style="
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
        ">
                ย้อนกลับ
            </a>
        </div>
    </div>
@endif
<div id="loadingOverlay" style="display: none;">
    <div class="col">
        <!-- Chase -->
        <div class="sk-chase sk-primary m-auto">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
        </div>
    </div>
</div>

<style>
    /* พื้นหลังทึบ */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(234, 244, 255, 0.8);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* สปินเนอร์หมุน */
    .spinner {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #28c76f;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
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

    .btn-course {
        background-color: #ed2eed;
        color: white;
        border: 1px solid #ed2eed;
    }

    .btn-check:checked+.btn-course {
        background-color: #a31ea3;
        /* สีตอนเลือก */
        border-color: #a31ea3;
        color: #fff;
    }

    .btn-course:hover {
        background-color: #c91ec9 !important;
        border-color: #c91ec9 !important;
        color: #fff !important;
    }

    .time-period-grid {
        margin-left: -0.25rem;
        margin-right: -0.25rem;
    }

    .time-period-option {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }

    .time-period-label {
        min-height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
    }

    .qty-input {
        font-weight: 600;
        border-left: 0;
        border-right: 0;
    }

    .qty-minus,
    .qty-plus {
        width: 32px;
    }

    .payment-card {
        cursor: pointer;
        border: 2px solid #eee;
        transition: all .2s ease;
        border-radius: 12px;
    }

    .payment-card:hover {
        border-color: #0d6efd;
        transform: translateY(-3px);
    }

    .btn-check:checked+.payment-card {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-content-navbar pt-3">
        <div>
            <div>
                <div class="container-fluid">
                    <style>
                        .timer-box {
                            text-align: center;
                            color: white;
                            border-radius: 6px;
                            padding: 4px 0;
                            font-family: monospace;
                        }

                        .label-pos {
                            background-color: antiquewhite;
                            border-radius: 2px;
                        }
                    </style>
                    <form method="POST" id="insert_product" action="{{ route('pos.checkout') }}">
                        {{-- <form id="insert_order"> --}}
                        @csrf
                        <input type="hidden" name="ref_room_id" value="{{ $room_id }}">
                        <div class="container-fluid">
                            <div class="row">

                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="col-12">
                                                <h4 class="label-pos ff-playfair p-2">รูปแบบห้อง</h4>
                                            </div>
                                            <div class="row">
                                                @foreach ($room_type as $key => $type)
                                                    <div class="col-sm-2">
                                                        <div class="form-check custom-option custom-option-basic">
                                                            <label class="form-check-label custom-option-content"
                                                                for="room_type{{ $type->id }}">
                                                                <input name="ref_room_type_id"
                                                                    class="form-check-input calculate" type="radio"
                                                                    value="{{ $type->id }}"
                                                                    id="room_type{{ $type->id }}"
                                                                    @if ($key == 0) checked @endif
                                                                    onchange="calculate()" />
                                                                <svg class="mb-2 mx-auto" width="32"
                                                                    viewBox="0 0 53 53"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    style="margin-left: 20% !important;">
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
                                                                <span class="custom-option-header">
                                                                    <span class="h6 mb-0">{{ $type->name }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="col-12 mt-2">
                                                <h4 class="label-pos ff-playfair p-2 mt-4">Time Period</h4>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fs-14 mb-0">Duration of service use</label>
                                                <div class="row g-2 time-period-grid">
                                                    @foreach ($course->groupBy(fn ($item) => mb_substr(trim($item->name), 0, 1)) as $courseGroup)
                                                        @if (!$loop->first)
                                                            <div class="w-100"></div>
                                                        @endif
                                                        @foreach ($courseGroup as $course_item)
                                                        <div class="col-6 col-md-2 col-xl-3 time-period-option">
                                                            <input type="radio" class="btn-check calculate"
                                                                name="ref_course_id" id="course{{ $course_item->id }}"
                                                                autocomplete="off" required
                                                                onchange="calculate(); updateCourseProductControls();"
                                                                value="{{ $course_item->id }}">

                                                            <label
                                                                class="btn btn-course w-100 rounded-0 time-period-label"
                                                                style="font-size: 14px; padding: 6px 10px;"
                                                                for="course{{ $course_item->id }}">
                                                                {{ $course_item->name }}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <h4 class="label-pos ff-playfair p-2 mt-4">Add-on Options</h4>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex gap-2 flex-wrap" id="addon-options-list">
                                                    @foreach ($addonOptions as $item)
                                                        <div class="snack-item"
                                                            style="position: relative; width: 19%; min-width: 200px;">
                                                            <input type="checkbox"
                                                                class="btn-check addon-checkbox calculate"
                                                                name="ref_option_id[]" onchange="calculate()"
                                                                id="addon{{ $item->id }}"
                                                                value="{{ $item->id }}"
                                                                data-name="{{ $item->name }}"
                                                                data-price="{{ $item->price }}" autocomplete="off">
                                                            <label
                                                                class="btn btn-purple-check d-flex flex-column justify-content-center text-center"
                                                                for="addon{{ $item->id }}">
                                                                <svg class="w-6 h-6 text-gray-800 dark:text-white mx-auto mb-2"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" fill="none" viewBox="0 0 24 24">
                                                                    <path stroke="currentColor" stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                                                </svg>
                                                                {{ $item->name }}
                                                                <br>
                                                                <small>{{ number_format($item->price, 2) }} ฿</small>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <h4 class="label-pos ff-playfair p-2">สินค้า</h4>
                                            </div>
                                            <div class="row" id="productGrid">
                                                @include('pos.partials.room-product-grid', [
                                                    'products' => $products,
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="card shadow-sm border-0">
                                        <div
                                            class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Invoice</span>
                                            {{-- <span class="text-muted">#0001</span> --}}
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">เลือกพนักงานขาย</label>
                                            <div
                                                class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                <input name="reception_name" type="text" id="reception"
                                                    placeholder="แตะบัตรพนักงาน หรือ ป้อนรหัสพนักงาน"
                                                    class="form-control me-2 reception-input" data-ref-position-id="1" required />
                                                <input name="reception_id" type="hidden" id="salesReceptionSelect">
                                                <input type="hidden" name="ref_position_id" value="1">
                                            </div>
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">เลือกพนักงานนวด</label>
                                            <div
                                                class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                <input name="staff_name" type="text" id="staff"
                                                    placeholder="แตะบัตรพนักงานนวด หรือ ป้อนพนักงานนวด"
                                                    class="form-control me-2 staff-input" data-ref-position-id="2" required />
                                                <input name="staff_id" type="hidden" id="salesStaffSelect">
                                                <input type="hidden" name="ref_position_id" value="2">
                                            </div>
                                        </div>
                                        <div class="mb-3 px-4">
                                            <label class="form-label fw-bold">ส่วนลด</label>
                                            <div
                                                class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                <input name="discount" type="text" placeholder="ส่วนลด"
                                                    class="form-control me-2 calculate" oninput="calculate()" />
                                            </div>
                                        </div>
                                        <div class="row g-3 payment-methods px-4">

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check calculate"
                                                    name="payment_method" id="pay-cash" value="cash" checked
                                                    required>

                                                <label class="card payment-card text-center p-3" for="pay-cash">
                                                    <i class="bi bi-cash-coin fs-1 text-success"></i>
                                                    <div class="mt-2 fw-bold">เงินสด</div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check calculate"
                                                    name="payment_method" id="pay-credit" value="credit_card"
                                                    required>

                                                <label class="card payment-card text-center p-3" for="pay-credit">
                                                    <i class="bi bi-credit-card-2-front fs-1 text-primary"></i>
                                                    <div class="mt-2 fw-bold">บัตรเครดิต</div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check calculate"
                                                    name="payment_method" id="pay-alipay" value="alipay" required>

                                                <label class="card payment-card text-center p-3" for="pay-alipay">
                                                    <i class="bi bi-phone fs-1 text-info"></i>
                                                    <div class="mt-2 fw-bold">Alipay / WeChat </div>
                                                </label>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check calculate"
                                                    name="payment_method" id="pay-qr" value="qr_code" required>

                                                <label class="card payment-card text-center p-3" for="pay-qr">
                                                    <i class="bi bi-qr-code-scan fs-1 text-dark"></i>
                                                    <div class="mt-2 fw-bold">QR Code</div>
                                                </label>
                                            </div>

                                        </div>
                                        {{-- <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                                            @forelse($cart as $item)
                                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-cup-straw me-2" style="font-size: 1.6rem;"></i>
                                                        <div>
                                                            <div class="text-truncate" style="max-width: 180px" title="{{ $item['name'] }}">
                                                                {{ $item['name'] }}
                                                            </div>
                                                            <small class="text-muted">THB {{ number_format($item['price'], 2) }}</small>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-2">

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" name="qty" value="{{ $item['qty'] - 1 }}"
                                                                class="btn btn-sm btn-outline-dark">-</button>
                                                        </form>

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST" class="mx-1">
                                                            @csrf
                                                            <input type="number" name="qty"
                                                                class="form-control form-control-sm text-center qty-input"
                                                                value="{{ $item['qty'] }}" min="0" max="{{ $item['stock'] ?? 999 }}"
                                                                step="1" style="width:72px">
                                                        </form>

                                                        <form action="{{ route('pos.update', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" name="qty" value="{{ $item['qty'] + 1 }}"
                                                                class="btn btn-sm btn-outline-dark">+</button>
                                                        </form>

                                                        <form action="{{ route('pos.remove', $item['id']) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted text-center mb-0">No items in cart</p>
                                            @endforelse
                                        </div> --}}
                                        <style>
                                            .product-row {
                                                display: flex;
                                                justify-content: space-between;
                                                font-size: 14px;
                                                margin-bottom: 4px;
                                            }

                                            .product-name {
                                                color: #555;
                                            }

                                            .product-price {
                                                white-space: nowrap;
                                            }

                                            .summary-row {
                                                display: flex;
                                                justify-content: space-between;
                                                font-size: 14px;
                                                margin-bottom: 4px;
                                            }

                                            .summary-left {
                                                color: #555;
                                            }

                                            .summary-right {
                                                white-space: nowrap;
                                            }
                                        </style>
                                        <div class="card-footer">
                                            <h6 class="fw-bold mb-3">รายละเอียดการเลือก</h6>

                                            <div id="summary-room"></div>
                                            <div id="summary-course"></div>

                                            <hr class="my-2">

                                            <div id="summary-price"></div>

                                            <div class="mt-2">
                                                <div class="fw-bold mb-2">Options</div>
                                                <div id="summary-option-list"></div>
                                                <hr>
                                            </div>
                                            <div class="mt-2">
                                                <div class="fw-bold mb-2">รายการสินค้า</div>
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>สินค้า</th>
                                                            <th class="text-center">จำนวน</th>
                                                            <th class="text-end">รวม</th>
                                                            <th class="text-center">ลบ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="summary-product-list">
                                                        <tr>
                                                            <td class="text-muted">ยังไม่มีสินค้า</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="d-flex justify-content-between"><span>Subtotal</span><span>THB
                                                    <span
                                                        id="subtotal">{{ number_format($subtotal, 2) }}</span></span>
                                            </div>
                                            <div class="d-flex justify-content-between"><span>Discount</span><span>-
                                                    THB <span
                                                        id="discount">{{ number_format($discount, 2) }}</span></span>
                                            </div>
                                            {{-- <div class="d-flex justify-content-between"><span>Tax</span><span>THB  <span id="tax">{{ number_format($tax, 2) }}</span></span></div> --}}
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>Total</span><span>THB <span
                                                        id="total">{{ number_format($total, 2) }}</span></span>
                                            </div>
                                            <input type="hidden" name="total_price" id="total_value">
                                            <button type="submit" class="btn btn-dark w-100 mt-3">
                                                Checkout
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                    {{-- ================== MODAL #1 : เลือกห้อง + customer ================== --}}
                    <div class="modal fade" id="checkoutRoomModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow rounded-4 p-3">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">Choose Room & Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกพนักงานขาย</label>
                                        {{-- <select id="salesStaffSelect" class="form-select"></select> --}}
                                        <form id="form_staff">
                                            <div
                                                class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                <input name="user_code" type="text" placeholder="แสกนบัตรพนักงาน"
                                                    class="form-control me-2" />
                                                <input type="hidden" id="salesStaffSelect">
                                                <input type="hidden" name="ref_position_id" value="1">
                                            </div>
                                        </form>
                                    </div>

                                    <hr class="my-3">

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Choose a room</label>
                                        <div class="store-pill mb-2">{{ $storefrontName ?? 'Cashier' }}</div>

                                        @foreach ($roomGroups ?? [] as $group)
                                            <div class="fw-bold mb-2">{{ $group['name'] }}</div>
                                            <div class="mb-3 d-flex flex-wrap gap-2">
                                                @foreach ($group['rooms'] as $room)
                                                    <button type="button"
                                                        class="room-chip btn btn-outline-secondary room-chip-disabled"
                                                        data-room-id="{{ $room['id'] }}" disabled
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Please select a sales staff first">{{ $room['label'] }}</button>
                                                @endforeach
                                            </div>
                                        @endforeach

                                        <input type="hidden" id="selectedRoomId">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Customers in this room</label>
                                        <select id="customerSelect" class="form-select" disabled>
                                            <option value="">-- กรุณาเลือก --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-dark w-100" id="nextToPaymentBtn" disabled>
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================== MODAL #Walkin ================== --}}
                    <div class="modal fade" id="walkinModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow rounded-4 p-3">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">Walk-in Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกเบอร์สมาชิก (ไม่บังคับ)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-telephone fs-5 text-muted"></i>
                                            <select id="walkinPhoneSelect" class="form-select"
                                                style="width: 100%"></select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกพนักงาน (บังคับ)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-person-badge fs-5 text-muted"></i>

                                            <form id="form_user">
                                                <div
                                                    class="d-flex align-items-center justify-content-between app-academy-md-80">
                                                    <input name="user_code" type="text" id="user"
                                                        placeholder="แสกนบัตรพนักงาน" onclick="clearInput('user')"
                                                        class="form-control me-2" />
                                                    <input type="hidden" id="walkinStaffSelect">
                                                    <input type="hidden" name="ref_position_id" value="2">
                                                </div>
                                                {{-- <button type="submit" class="btn btn-primary mt-2" onclick="focusInput()">คลิ๊กที่นี่เมื่อแตะบัตรไม่ได้</button> --}}
                                            </form>
                                            {{-- <select id="walkinStaffSelect" class="form-select" style="width: 100%"></select> --}}
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกเวลา (บังคับ)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-clock fs-5 text-muted"></i>
                                            <select id="walkinTimeSelect" class="form-select" style="width: 100%">
                                                <option value="" selected disabled>-- กรุณาเลือกเวลา --</option>
                                                <option value="40">40 นาที</option>
                                                <option value="60">60 นาที</option>
                                                <option value="90">90 นาที</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกสินค้าเสริม (ไม่บังคับ)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-plus-circle fs-5 text-muted"></i>
                                            <select id="walkinAddonSelect" class="form-select"
                                                style="width: 100%"></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-dark w-100" id="walkinNextBtn" disabled>
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================== MODAL #2 : Payment Method ================== --}}
                    <div class="modal fade" id="checkoutPaymentModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow rounded-4 p-3">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title">วิธีการชำระเงิน</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form method="POST" action="{{ route('pos.checkout') }}">
                                    @csrf
                                    <input type="hidden" name="room_id" id="formRoomId">
                                    <input type="hidden" name="order_id" id="formOrderId">
                                    <input type="hidden" name="payment_method" id="paymentMethod">
                                    <input type="hidden" name="customer_id" id="formCustomerId">
                                    <input type="hidden" name="staff_id" id="formStaffId">
                                    <input type="hidden" name="addon_id" id="formAddonId">
                                    <input type="hidden" name="mama_id" id="formMamaId">
                                    <input type="hidden" name="duration_minutes" id="formDuration">
                                    <input type="hidden" name="total_price" id="formTotalPrice">

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <h6 class="fw-bold">สรุปรายการ</h6>
                                            <div id="paymentSummary" class="border-top pt-2">
                                                <p class="text-center text-muted py-3">กำลังโหลด...</p>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between fw-bold fs-5">
                                                <span>ยอดรวมสุทธิ</span>
                                                <span id="paymentTotal">THB 0.00</span>
                                            </div>
                                        </div>

                                        <hr class="my-3">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">เลือกวิธีการชำระเงิน</label>
                                            <div class="list-group">
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="cash">
                                                    เงินสด (Cash)
                                                </label>
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="promptpay">
                                                    โอน/สแกน QR Code (PromptPay)
                                                </label>
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="credit_card">
                                                    บัตรเครดิต/เดบิต (Credit/Debit Card)
                                                </label>
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="wechat">
                                                    WeChat Pay
                                                </label>
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="alipay">
                                                    Alipay
                                                </label>
                                                <label class="list-group-item">
                                                    <input class="form-check-input me-2" type="radio"
                                                        name="payment_method_radio" value="ewallet">
                                                    TrueMoney Wallet / LINE Pay (E-Wallet)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0 pt-0">
                                        <button type="submit" class="btn btn-dark w-100" id="confirmBtn" disabled>
                                            Confirm Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
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

    <iframe id="print-iframe" style="display: none;"></iframe>

    @include('admin/layout/inc_js')

</body>

</html>

{{-- ================== STYLES ================== --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .room-chip-disabled {
        pointer-events: none;
        opacity: 0.5;
    }

    .room-chip.active,
    .cash-btn.active,
    .other-btn.active {
        background-color: #5e2a5f;
        color: #fff;
    }
</style>
<script>
    $('#insert_product').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
        const iframe = document.getElementById('print-iframe');

        if (!this.checkValidity()) {
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }
        if ($('.reception-input').val() && !$('#salesReceptionSelect').val()) {

            const data = $('#salesReceptionSelect').val();

            if (data != '') {

            } else {
                Swal.fire('โปรดตรวจสอบพนักงานขาย.!', '', 'warning');
                return;
            }
        }
        if ($('.staff-input').val() && !$('#salesStaffSelect').val()) {

            const data = $('#salesStaffSelect').val();

            if (data != '') {

            } else {
                Swal.fire('โปรดตรวจสอบพนักงานนวด.!', '', 'warning');
                return;
            }
        }
        var formData = new FormData(this);

        Swal.fire({
            title: 'ยืนยันการดำเนินการ?',
            text: 'คุณต้องการเพิ่มคำสั่งซื้อหรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ตกลง',
            cancelButtonText: 'ยกเลิก',
            didOpen: () => {
                Swal.getConfirmButton().focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/pos/checkout',
                    type: 'POST',
                    data: formData,
                    contentType: false, // ✅ ต้องมี
                    processData: false, // ✅ ต้องมี
                    success: function(response) {
                        if (response.status == true) {

                            Swal.fire({
                                title: 'เพิ่มคำสั่งซื้อเรียบร้อยแล้ว',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                // if (paymentStatus == '0') {
                                //     location.reload();
                                // } else {
                                const newWindow = window.open('', '_blank');
                                newWindow.document.write(`
                                            <html>
                                            <head>
                                                <title>Print</title>
                                            </head>
                                            <body>
                                                ${response.data}
                                                <script>
                                                    window.onload = function () {
                                                        window.print();
                                                    };
                                                    window.onafterprint = function () {
                                                        window.close();
                                                    };
                                                <\/script>
                                            </body>
                                            </html>
                                        `);
                                newWindow.document.close();
                                window.location.href = '/pos/room';
                                // location.reload();
                                // }
                            });
                            // $('#addserviceModal').modal('hide');
                            // loadData(page);
                        }
                    },
                    error: function(error) {
                        Swal.fire('เกิดข้อผิดพลาด', '', 'error');
                        console.error('เกิดข้อผิดพลาด:', error);
                    }
                });
            }
        });
    });
    $('#insert_order').on('submit', function(event) {
        event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
        if (!this.checkValidity()) {
            // ถ้าฟอร์มไม่ถูกต้อง
            this.reportValidity();
            return console.log('ฟอร์มไม่ถูกต้อง');
        }
        return alert(456)
        $.ajax({
            url: '/pos/insert-order', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
            type: 'GET',
            data: $(this).serialize(),
            success: function(response) {

            },
            error: function(error) {

            }
        });
    });
    document.addEventListener('click', function(e) {

        // ➕ เพิ่มจำนวน
        if (e.target.classList.contains('qty-plus')) {
            if (e.target.disabled) return;
            const input = e.target.previousElementSibling;
            const max = parseInt(input.max || 9999);
            let val = parseInt(input.value || 1);
            if (val < max) input.value = val + 1;
            input.dispatchEvent(new Event('change'));
        }

        // ➖ ลดจำนวน
        if (e.target.classList.contains('qty-minus')) {
            if (e.target.disabled) return;
            const input = e.target.nextElementSibling;
            const min = parseInt(input.min || 0);
            let val = parseInt(input.value || 0);

            if (val > min) input.value = val - 1;
            input.dispatchEvent(new Event('change'));
        }
    });

    function updateCourseProductControls() {
        const hasCourse = !!document.querySelector('input[name="ref_course_id"]:checked');

        document.querySelectorAll('.course-product-control').forEach(el => {
            el.disabled = !hasCourse;
            if (!hasCourse && el.classList.contains('qty-input')) {
                el.value = 0;
            }
        });

        document.querySelectorAll('.course-product-hint').forEach(el => {
            el.classList.toggle('d-none', hasCourse);
        });

        if (!hasCourse) {
            renderProductSummary();
        }
    }
    updateCourseProductControls();
    calculate();
    const bindEmployeeLookup = (input, hiddenId, errorText) => {
        if (!input || input.dataset.lookupBound === '1') return;

        input.dataset.lookupBound = '1';

        const hiddenInput = input.closest('.d-flex')?.querySelector(`#${hiddenId}`) || document.getElementById(hiddenId);

        const lookupEmployee = async () => {
            const userCode = input.value.trim();
            if (!userCode) return;

            if (hiddenInput?.value && input.dataset.selectedName === userCode) {
                return;
            }

            const refPositionId = input.dataset.refPositionId || '';

            try {
                const response = await fetch(`/pos/get-user?user_code=${encodeURIComponent(userCode)}&ref_position_id=${encodeURIComponent(refPositionId)}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    input.value = '';
                    input.dataset.selectedName = '';
                    if (hiddenInput) hiddenInput.value = '';
                    Swal.fire('แจ้งเตือน', data.message || 'ไม่พบพนักงาน', 'warning');
                    return;
                }

                input.value = data.name;
                input.dataset.selectedName = data.name;
                if (hiddenInput) hiddenInput.value = data.id;
                input.blur(); // กันยิงซ้ำจากเครื่องสแกน
            } catch (err) {
                console.error(err);
                Swal.fire('เกิดข้อผิดพลาด', errorText, 'error');
            }
        };

        input.addEventListener('click', function() {
            this.value = '';
            this.dataset.selectedName = '';
            if (hiddenInput) hiddenInput.value = '';
            this.focus();
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault(); // กัน submit form
                lookupEmployee();
            }
        });

        input.addEventListener('change', lookupEmployee);
    };

    document.querySelectorAll('.staff-input').forEach(input => {
        bindEmployeeLookup(input, 'salesStaffSelect', 'ไม่สามารถค้นหาพนักงานได้');
    });

    document.querySelectorAll('.reception-input').forEach(input => {
        bindEmployeeLookup(input, 'salesReceptionSelect', 'ไม่สามารถค้นหาพนักงานขายได้');
    });

    function collectCalculatePayload() {
        const payload = {};

        document.querySelectorAll('.calculate').forEach(el => {
            if (!el.name) return;

            let name = el.name;

            // 🧠 qty[123] → payload.qty[123] = value
            if (name.startsWith('qty[')) {
                const match = name.match(/\[(\d+)\]/);
                if (!match) return;

                const id = match[1];
                const value = parseInt(el.value, 10) || 0;

                if (value > 0) {
                    if (!payload.qty) payload.qty = {};
                    payload.qty[id] = value;
                }
                return;
            }

            // 🔧 แปลง ref_option_id[] → ref_option_id
            const isArray = name.endsWith('[]');
            if (isArray) {
                name = name.replace('[]', '');
            }

            // 🟢 radio
            if (el.type === 'radio') {
                if (el.checked) {
                    payload[name] = el.value;
                }
            }

            // 🟢 checkbox (หลายค่า)
            else if (el.type === 'checkbox') {
                if (el.checked) {
                    if (!Array.isArray(payload[name])) {
                        payload[name] = [];
                    }
                    payload[name].push(el.value);
                }
            }

            // 🟢 text / number / hidden
            else {
                const value = el.value.trim();
                if (value === '') return;

                payload[name] = isNaN(value) ? value : Number(value);
            }
        });

        return payload;
    }

    function summaryRow(label, value, boldLabel = false) {
        return `
            <div class="d-flex justify-content-between align-items-start mb-1">
                <span class="${boldLabel ? 'fw-bold' : 'text-muted'}">${label}</span>
                <span class="fw-semibold text-end">${value}</span>
            </div>
        `;
    }

    function renderSummary() {

        let roomName = $('input[name="ref_room_type_id"]:checked')
            .closest('label')
            .find('.h6')
            .text();

        let courseName = $('input[name="ref_course_id"]:checked')
            .next('label')
            .text()
            .trim();

        $('#summary-room').html(
            summaryRow('รูปแบบห้อง', roomName, true) // 👈 ตัวหนา
        );

        $('#summary-course').html(
            summaryRow('Time Period', courseName, true) // 👈 ตัวหนา
        );
    }

    function renderProductSummary() {

        let rows = [];

        $('.qty-input').each(function() {

            const input = this; // ✅ ประกาศให้ชัด
            const productId = input.name.match(/\[(.*?)\]/)[1];

            let qty = parseInt($(this).val()) || 0;
            if (qty <= 0) return;

            let card = $(this).closest('.card-body');
            let name = card.find('.card-title').text();

            // ราคา/ชิ้น
            let priceText = card.find('.fw-bold').text().replace(/[^\d.]/g, '');
            let price = Number(priceText);

            // ราคารวมต่อสินค้า
            let total = price * qty;

            rows.push(`
                <tr>
                    <td>${name}</td>
                    <td class="text-center">${qty}</td>
                    <td class="text-end">
                        ฿${total.toLocaleString('th-TH', { minimumFractionDigits: 2 })}
                    </td>
                    <td class="text-center">
                        <a href="javascript:;"
                            class="btn btn-xs btn-danger rounded-pill px-2 py-1"
                            onclick="removeItem(${productId})">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            `);
        });

        $('#summary-product-list').html(
            rows.length ?
            rows.join('') :
            `<tr>
                        <td colspan="4" class="text-center text-muted">
                            ยังไม่มีสินค้า
                        </td>
                </tr>`
        );
    }

    function removeItem(productId) {

        const input = document.querySelector(`input[name="qty[${productId}]"]`);

        if (input) {
            input.value = 0;
        }

        calculate();
    }
    // function setRoomCoursePrice() {

    //     let roomId = $('input[name="ref_room_type_id"]:checked').val();
    //     let courseId = $('input[name="ref_course_id"]:checked').val();

    //     let price = 0;

    //     if (roomId == 1 && courseId == 1) price = 1000;
    //     if (roomId == 1 && courseId == 2) price = 2000;
    //     if (roomId == 1 && courseId == 3) price = 1000;

    //     renderRoomCoursePrice(price);
    // }
    // function renderRoomCoursePrice(price) {
    //     $('#summary-price').html(
    //         summaryRow(
    //             'ราคา',
    //             `<span class="fw-bold fs-6">${Number(price).toLocaleString()} ฿</span>`
    //         )
    //     );
    // }
    function renderOptionSummary() {

        let rows = [];

        $('.addon-checkbox:checked').each(function() {

            let name = $(this).data('name');
            let price = Number($(this).data('price'));

            rows.push(`
                <div class="summary-row">
                    <span class="summary-left">${name}</span>
                    <span class="summary-right">฿${price.toLocaleString()}</span>
                </div>
            `);
        });

        $('#summary-option-list').html(
            rows.length ?
            rows.join('') :
            `<div class="text-muted">- ไม่มี Options -</div>`
        );
    }

    function calculate() {
        document.getElementById('loadingOverlay').style.display = 'flex';


        renderSummary(); // แสดงห้อง + course
        // setRoomCoursePrice();   // คุณเป็นคนคำนวณราคาเอง
        renderOptionSummary(); // 👈 Options
        renderProductSummary(); // สินค้า

        const payload = collectCalculatePayload();

        fetch('/pos/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                $('#summary-price').html(
                    summaryRow(
                        'ราคา',
                        `<span class="fw-bold fs-6">${data.room_course} ฿</span>`
                    )
                );
                $('#subtotal').html(data.subtotal);
                $('#discount').html(data.discount);
                // $('#tax').html(data.tax);
                $('#total').html(data.total);
                $('#total_value').val(data.total);
                document.getElementById('loadingOverlay').style.display = 'none';
                // update UI
                // document.getElementById('subtotal').innerText = data.subtotal;
                // document.getElementById('discount').innerText = data.discount;
                // document.getElementById('tax').innerText = data.tax;
                // document.getElementById('total').innerText = data.total;
            })
            .catch(err => console.error(err));
    }
</script>

{{-- ================== SCRIPTS ================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
            tooltipTriggerEl))
    });

    document.addEventListener('DOMContentLoaded', () => {
        // --- Element Selections ---
        const salesStaffSelect = document.getElementById('salesStaffSelect');
        const customerSelect = document.getElementById('customerSelect');
        const roomIdInput = document.getElementById('selectedRoomId');
        const nextBtn = document.getElementById('nextToPaymentBtn');

        const formRoomId = document.getElementById('formRoomId');
        const formOrderId = document.getElementById('formOrderId');
        const formCustomerId = document.getElementById('formCustomerId');
        const formStaffId = document.getElementById('formStaffId');
        const formAddonId = document.getElementById('formAddonId');
        const formMamaId = document.getElementById('formMamaId');
        const formDuration = document.getElementById('formDuration');
        const formTotalPrice = document.getElementById('formTotalPrice');

        const walkinNextBtn = document.getElementById('walkinNextBtn');
        const walkinTimeSelect = document.getElementById('walkinTimeSelect');

        const confirmBtn = document.getElementById('confirmBtn');
        const roomModalEl = document.getElementById('checkoutRoomModal');
        const walkinModalEl = document.getElementById('walkinModal');
        const paymentModalEl = document.getElementById('checkoutPaymentModal');

        // --- Temporary State Variables ---
        let tempRoomId = null;
        let tempMamaId = null;

        // --- Validation Functions ---
        const checkNextBtnStatus = () => {
            const isStaffSelected = salesStaffSelect.value !== '';
            const isCustomerSelected = customerSelect.value !== '';
            nextBtn.disabled = !(isStaffSelected && isCustomerSelected);
            // alert(isStaffSelected);
        };

        const checkWalkinNextBtnStatus = () => {
            const isStaffSelected = document.getElementById('walkinStaffSelect').value;
            const isTimeSelected = walkinTimeSelect.value;
            walkinNextBtn.disabled = !(isStaffSelected && isTimeSelected);
        };

        // --- Initialize Sales Staff Select2 ---
        // $('#salesStaffSelect').select2({
        //     dropdownParent: $("#checkoutRoomModal"),
        //     placeholder: '-- เลือกพนักงานขาย --',
        //     allowClear: true,
        //     ajax: {
        //         url: '{{ route('pos.api.searchSalesStaff') }}',
        //         dataType: 'json',
        //         delay: 250,
        //         data: params => ({ q: params.term }),
        //         processResults: data => ({ results: data })
        //     }
        // }).on('select2:select', e => {
        //     tempMamaId = e.params.data.id;
        //     document.querySelectorAll('.room-chip').forEach(btn => {
        //         btn.disabled = false;
        //         btn.classList.remove('room-chip-disabled');
        //         const tooltip = bootstrap.Tooltip.getInstance(btn);
        //         if(tooltip) tooltip.disable();
        //     });
        //     checkNextBtnStatus();
        // }).on('select2:clear', () => {
        //     tempMamaId = null;
        // });

        // --- Room Selection ---
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('room-chip') && !e.target.disabled) {
                document.querySelectorAll('.room-chip').forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                tempRoomId = e.target.dataset.roomId;
                roomIdInput.value = tempRoomId;

                checkNextBtnStatus();

                fetch(`/pos/room/${tempRoomId}/customers`)
                    .then(res => res.json())
                    .then(data => {
                        customerSelect.innerHTML =
                            '<option value="" disabled selected>-- กรุณาเลือก --</option>';
                        customerSelect.innerHTML +=
                            '<option value="walkin">+ Walk-in Customer</option>';
                        data.forEach(c => {
                            customerSelect.innerHTML +=
                                `<option value="${c.order_id}" data-customer-id="${c.customer_id}">[Order #${c.order_id}] ${c.name}</option>`;
                        });
                        customerSelect.disabled = false;
                    });
            }
        });

        customerSelect.addEventListener('change', checkNextBtnStatus);

        // --- Flow Control: Normal Checkout ---
        nextBtn.addEventListener('click', () => {
            if (customerSelect.value === 'walkin') return;

            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            formRoomId.value = roomIdInput.value;
            formOrderId.value = customerSelect.value;
            formCustomerId.value = selectedOption.dataset.customerId || null;
            formStaffId.value = salesStaffSelect.value;
            formMamaId.value = salesStaffSelect.value;
            formDuration.value = '';

            const roomModal = bootstrap.Modal.getInstance(roomModalEl);
            roomModal.hide();
            roomModalEl.addEventListener('hidden.bs.modal', () => {
                const paymentModal = new bootstrap.Modal(paymentModalEl);
                paymentModal.show();
            }, {
                once: true
            });
        });

        // --- Flow Control: Walk-in ---
        customerSelect.addEventListener('change', (e) => {
            if (e.target.value === 'walkin') {
                const roomModal = bootstrap.Modal.getInstance(roomModalEl);
                roomModal.hide();
                roomModalEl.addEventListener('hidden.bs.modal', () => {
                    const walkinModal = new bootstrap.Modal(walkinModalEl, {
                        focus: false
                    });
                    walkinModal.show();
                }, {
                    once: true
                });
            }
        });

        walkinNextBtn.addEventListener('click', () => {
            formRoomId.value = tempRoomId;
            formOrderId.value = 'walkin';
            formCustomerId.value = $('#walkinPhoneSelect').val() || null;
            formStaffId.value = $('#walkinStaffSelect').val() || null;
            formMamaId.value = tempMamaId;
            formAddonId.value = $('#walkinAddonSelect').val() || null;
            formDuration.value = walkinTimeSelect.value;

            const walkinModal = bootstrap.Modal.getInstance(walkinModalEl);
            walkinModal.hide();
            walkinModalEl.addEventListener('hidden.bs.modal', () => {
                const paymentModal = new bootstrap.Modal(paymentModalEl);
                paymentModal.show();
            }, {
                once: true
            });
        });

        // --- Walk-in Modal Select2 Initializers & Event Listeners ---
        $('#walkinPhoneSelect').select2({
            dropdownParent: $("#walkinModal"),
            placeholder: '-- ค้นหาเบอร์โทร --',
            allowClear: true,
            ajax: {
                url: '/pos/api/search-users',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.map(u => ({
                        id: u.id,
                        text: `${u.phone} - ${u.name}`
                    }))
                })
            }
        });

        // $('#walkinStaffSelect').select2({
        //     dropdownParent: $("#walkinModal"),
        //     placeholder: '-- ค้นหาพนักงาน --',
        //     allowClear: true,
        //     ajax: {
        //         url: '{{ route('pos.api.searchStaff') }}',
        //         dataType: 'json',
        //         delay: 250,
        //         data: params => ({ q: params.term }),
        //         processResults: data => ({
        //             results: data.map(u => ({
        //                 id: u.id,
        //                 text: `${u.user_code ? '['+u.user_code+'] ' : ''}${u.nickname ?? ''} | Salary: ${u.salary ?? 0}฿`
        //             }))
        //         })
        //     }
        // }).on('select2:select', checkWalkinNextBtnStatus)
        //   .on('select2:clear', checkWalkinNextBtnStatus);

        walkinTimeSelect.addEventListener('change', checkWalkinNextBtnStatus);
        document.getElementById('walkinStaffSelect').addEventListener('input', checkWalkinNextBtnStatus);

        $('#walkinAddonSelect').select2({
            dropdownParent: $("#walkinModal"),
            placeholder: '-- ค้นหาสินค้าเสริม --',
            allowClear: true,
            ajax: {
                url: '/pos/api/search-addons',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.map(a => ({
                        id: a.id,
                        text: `${a.name} | ${a.price}฿`
                    }))
                })
            }
        });

        // --- Payment Logic ---
        const paymentMethod = document.getElementById('paymentMethod');
        // const confirmBtn = document.getElementById('confirmBtn'); // Already declared above
        // เมื่อเลือกวิธีการชำระเงิน ให้เซ็ตค่าและ enable ปุ่ม
        document.querySelectorAll('input[name="payment_method_radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                paymentMethod.value = this.value;
                confirmBtn.disabled = false;
            });
        });

        // --- Cart & Search Logic ---
        // document.querySelectorAll('.qty-input').forEach(input => {
        //     input.addEventListener('change', function() { this.closest('form').submit(); });
        // });

        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const productGrid = document.getElementById('productGrid');
        let searchTimeout;
        if (searchInput && clearSearchBtn && productGrid) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetch(`?q=${this.value}&ajax=true`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            productGrid.innerHTML = html;
                            updateCourseProductControls();
                        });
                }, 500);
            });
            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            });
        }

        // --- Modal Event Listeners (Cleanup & API Call) ---
        roomModalEl.addEventListener('show.bs.modal', () => {
            tempRoomId = null;
            tempMamaId = null;
        });

        paymentModalEl.addEventListener('show.bs.modal', () => {
            const summaryContainer = document.getElementById('paymentSummary');
            const totalContainer = document.getElementById('paymentTotal');

            summaryContainer.innerHTML = '<p class="text-center text-muted py-3">กำลังโหลด...</p>';
            totalContainer.textContent = 'THB 0.00';

            const addonId = formAddonId.value;
            const roomId = formRoomId.value;
            const duration = formDuration.value;
            const staffId = formStaffId.value;
            const csrfToken = '{{ csrf_token() }}';

            fetch('{{ route('pos.api.calculateSummary') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        addon_id: addonId,
                        room_id: roomId,
                        duration_minutes: duration,
                        staff_id: staffId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    summaryContainer.innerHTML = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const itemDiv = document.createElement('div');
                            itemDiv.className =
                                'd-flex justify-content-between text-muted small py-1';
                            itemDiv.innerHTML = `
                            <span>${item.name} <small>(${item.details})</small></span>
                            <span>${parseFloat(item.total).toFixed(2)}</span>
                        `;
                            summaryContainer.appendChild(itemDiv);
                        });
                    } else {
                        summaryContainer.innerHTML =
                            '<p class="text-center text-muted py-3">ไม่มีรายการ</p>';
                    }
                    totalContainer.textContent = `THB ${parseFloat(data.total).toFixed(2)}`;
                    formTotalPrice.value = data.total;
                })
                .catch(error => {
                    summaryContainer.innerHTML =
                        '<p class="text-center text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</p>';
                    console.error('Error fetching summary:', error);
                });
        });

        roomModalEl.addEventListener('hidden.bs.modal', () => {
            $('#salesStaffSelect').val(null).trigger('change');

            document.querySelectorAll('.room-chip').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('room-chip-disabled');
                btn.classList.remove('active');
                const tooltip = bootstrap.Tooltip.getInstance(btn);
                if (tooltip) tooltip.enable();
            });

            customerSelect.innerHTML = '<option value="" disabled selected>-- กรุณาเลือก --</option>';
            customerSelect.disabled = true;

            roomIdInput.value = '';

            nextBtn.disabled = true;
        });

        walkinModalEl.addEventListener('hidden.bs.modal', () => {
            $('#walkinPhoneSelect').val(null).trigger('change');
            $('#walkinStaffSelect').val(null).trigger('change');
            $('#walkinAddonSelect').val(null).trigger('change');
            walkinTimeSelect.value = '';
            walkinNextBtn.disabled = true;
        });

        paymentModalEl.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.cash-btn, .other-btn').forEach(b => b.classList.remove(
                'active'));
            cashInput.value = '';
            paymentMethod.value = '';
            cashAmount.value = '';
            confirmBtn.disabled = true;
        });

        $('#form_staff').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if (!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            $.ajax({
                url: '/pos/get-user', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                type: 'GET',
                data: $(this).serialize(),
                success: function(response) {
                    tempMamaId = response.id;
                    document.querySelectorAll('.room-chip').forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove('room-chip-disabled');
                        const tooltip = bootstrap.Tooltip.getInstance(btn);
                        if (tooltip) tooltip.disable();
                    });
                    checkNextBtnStatus();
                    document.getElementById("staff").value = response.name;
                    document.getElementById("salesStaffSelect").value = response.id;
                    document.getElementById('staff').blur();

                },
                error: function(error) {
                    document.getElementById("staff").value = "";
                    document.getElementById("salesStaffSelect").value = "";
                    Swal.fire('แจ้งเตือน', error.responseJSON?.message || 'ไม่พบพนักงาน', 'warning');
                    console.error('เกิดข้อผิดพลาด:', error);
                }
            });
        });
        $('#form_user').on('submit', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มปกติ
            if (!this.checkValidity()) {
                // ถ้าฟอร์มไม่ถูกต้อง
                this.reportValidity();
                return console.log('ฟอร์มไม่ถูกต้อง');
            }
            $.ajax({
                url: '/pos/get-user', // เปลี่ยน URL เป็นจุดหมายที่ต้องการ
                type: 'GET',
                data: $(this).serialize(),
                success: function(response) {
                    document.getElementById("user").value = response.name;
                    document.getElementById("walkinStaffSelect").value = response.id;
                    document.getElementById('user').blur();
                    checkWalkinNextBtnStatus();

                },
                error: function(error) {
                    document.getElementById("user").value = "";
                    document.getElementById("walkinStaffSelect").value = "";
                    Swal.fire('แจ้งเตือน', error.responseJSON?.message || 'ไม่พบพนักงาน', 'warning');
                    console.error('เกิดข้อผิดพลาด:', error);
                }
            });
        });
    });

    function clearInput(id) {
        document.getElementById(id).value = '';
    }

    function clearStaffInput(id) {
        document.getElementById(id).value = '';
        tempMamaId = null;
    }
</script>

<script>
    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error')),
            confirmButtonColor: '#5e2a5f'
        })
    @endif
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success')),
            confirmButtonColor: '#5e2a5f'
        })
    @endif
</script>
