@extends('layouts.app')

@section('content')
    <style>
        .sensorTable td:nth-child(3) a {
            font-family: 'Open Sans';
            font-weight: 600;
        }

        .sensorTable td:nth-child(4) a {
            font-family: 'Open Sans';
            font-weight: 600;
        }

        .sensor_icon_mini {
            position: absolute;
            left: 4px !important;
            top: 4px !important;
        }

        .parent {
            display: flex;
            align-items: center;
            justify-content: space-around;
            height: 100%;
        }

        #clear_search {
            position: relative;
            text-align: center;
            cursor: pointer;
            border: none;
            border-radius: 50%;
            padding: 0;
            font-size: 12px;
            background: transparent;
            margin: auto 5px;
        }

        .typeahead {
            width: 100% !important;
        }

        .input-group-text {
            background-color: white !important;
        }

        .dropdown-menu li {
            display: block;
            padding: 10px;
            cursor: pointer;
        }

        .dropdown-menu li:hover {
            background-color: #eee;
        }

        .dropdown-menu li a {
            display: block;
            padding: 5px;
        }

        /*for Suggestions  */
        #search_suggestions {
            border: 1px solid #ebedf2;
        }

        #search_suggestions>.dropdown-item {
            padding: .65rem 1.5rem;
        }

        #search_suggestions>.dropdown-item:not(:last-child) {
            border-bottom: 1px solid #ebedf2;
        }

        #search_suggestions .dropdown-item .dropdown-item {
            padding: 0;
        }

        .scannerBody .q_r_scanner_header+#camera-select2 {
            display: none;
        }

        .scannerBody .q_r_scanner_header.d-none+#camera-select2 {
            display: inline-block;
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            z-index: 10;
            width: auto;
            border-radius: 0.375rem;
            padding-left: 1rem;
            padding-right: 1rem;
            background-color: transparent;
            color: #000;
            border-color: #000;
            line-height: 1.4;
            padding-right: 30px;
        }

        .scannerBody .q_r_scanner_header.d-none+#camera-select2:focus,
        .scannerBody .q_r_scanner_header.d-none+#camera-select2:focus-visible {
            outline: none;
        }

        input[type="search"]::-webkit-search-cancel-button {

            /* Remove default */
            -webkit-appearance: none;

            /* Now your own custom styles */
            height: 14px;
            width: 14px;
            display: block;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAn0lEQVR42u3UMQrDMBBEUZ9WfQqDmm22EaTyjRMHAlM5K+Y7lb0wnUZPIKHlnutOa+25Z4D++MRBX98MD1V/trSppLKHqj9TTBWKcoUqffbUcbBBEhTjBOV4ja4l4OIAZThEOV6jHO8ARXD+gPPvKMABinGOrnu6gTNUawrcQKNCAQ7QeTxORzle3+sDfjJpPCqhJh7GixZq4rHcc9l5A9qZ+WeBhgEuAAAAAElFTkSuQmCC);
            /* setup all the background tweaks for our custom icon */
            background-repeat: no-repeat;

            /* icon size */
            background-size: 14px;

        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>


    <div class="m-grid__item m-grid__item--fluid m-wrapper">

        <!-- BEGIN: Subheader -->
        <div class="m-subheader ">
            <div class="row">
                <div class="col-sm-6">
                    <!-- <h3 class="m-subheader__title">Sensors</h3> -->
                    <button type="button" class="btn btn-primary mb-2 mr-2 mb-md-0" data-toggle="modal"
                        data-target="#m_modal_2" id="identify_touch_sensor">

                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 2048 2048">
                            <path fill="currentColor"
                                d="M1600 896q40 0 75 15t61 41t41 61t15 75v384q0 119-45 224t-124 183t-183 123t-224 46q-144 0-268-55t-226-156l-472-472q-28-28-43-65t-15-76q0-42 16-78t43-64t63-42t78-16q82 0 141 59l107 106V853q-59-28-106-70t-80-95t-52-114t-18-126q0-93 35-174t96-143t142-96T832 0q93 0 174 35t143 96t96 142t35 175q0 93-37 178t-105 149q35 9 63 30t49 52q45-25 94-25q50 0 93 23t69 66q45-25 94-25zM512 448q0 75 34 143t94 113V448q0-40 15-75t41-61t61-41t75-15q40 0 75 15t61 41t41 61t15 75v256q60-45 94-113t34-143q0-66-25-124t-69-101t-102-69t-124-26q-66 0-124 25t-102 69t-69 102t-25 124zm1152 640q0-26-19-45t-45-19q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-37 0-50-23t-16-60t2-77t2-77t-15-59t-51-24q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-37 0-50-23t-16-60t2-77t2-77t-15-59t-51-24q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-26 0-45-19t-19-45V448q0-26-19-45t-45-19q-26 0-45 19t-19 45v787q0 23-8 42t-23 35t-35 23t-42 9q-22 0-42-8t-37-24l-139-139q-21-21-50-21t-50 21t-22 51q0 29 21 50l472 473q84 84 184 128t219 45q93 0 174-35t142-96t96-142t36-175v-384z" />
                        </svg> Identify by touch
                    </button>
                    <button data-toggle="modal" data-target="#m_modal_5" class="btn btn-primary mb-2 mb-md-0"><svg
                            xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M17 13h-4v4h-2v-4H7v-2h4V7h2v4h4m-5-9A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2Z">
                            </path>
                        </svg> Claim new devices</button>
                </div>
                <div class="col-sm-6 d-flex justify-content-sm-end flex-wrap">
                    <div class="d-flex flex-wrap mr-2 mb-2 mb-md-0">
                        @if (isset($search) && $search != '')
                            <button type="button" style="margin-right: 5px;"
                                class="btn btn-primary ml-3 reset-filter">Reset Filter</button>
                        @endif
                        <div class="form-group m-form__group m-0">
                            <div class="m-input-icon m-input-icon--right">
                                <form id="searchForm" action="" method="get">
                                    <div class="d-flex">
                                        <select id="searchTypes" name="searchTypes" class="form-control mr-2"
                                            style="display: inline-block;width: inherit;float: left;">
                                            <option value="1" @if ($search_type == 1) selected @endif>Sensor
                                                Name</option>
                                            <option value="2" @if ($search_type == 2) selected @endif>Sensor
                                                ID</option>
                                        </select>
                                        <input type="text" class="form-control m-input search-devices-input"
                                            placeholder="Search" name="search" value="{{ $search ?? '' }}">
                                    </div>
                                </form>
                                <span class="m-input-icon__icon m-input-icon__icon--right"><span><i
                                            class="fa flaticon-search"></i></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (\Session::has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success Message!</strong> {{ \Session::get('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({
                        title: '{{ session('title') ?? '' }}',
                        icon: 'error',
                        text: '{{ session('error') }}'
                    });
                </script>
            @endif
            @if (session('success'))
                <script>
                    Swal.fire({
                        title: '{{ session('title') ?? '' }}',
                        icon: "success",
                        text: '{{ session('success') }}'
                    });
                </script>
            @endif

        </div>

        <!-- END: Subheader -->
        <div class="m-content">

            <!--Begin::Section-->


            <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <h4 class="m-subheader__title mb-3"><strong>Connected</strong></h4>
                    </div>


                </div>

                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless"
                        id="m_table_1">
                        <thead>
                            <tr>
                                <th width="5%" style="text-align:center">TYPE</th>
                                <th width="55%">NAME</th>
                                <th width="15%" class="d-none d-sm-table-cell">CONNECTED TO </th>
                                <th width="15%">STATE</th>
                                <th width="15%" class="d-none d-sm-table-cell">LAST SEEN</th>
                                <th width="10%">SIGNAL</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (isset($connected_sensor) && count($connected_sensor) > 0)
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($connected_sensor as $row)
                                    <tr class="sensorTable @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif"
                                        data-url="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        data-device="{{ $row->device_id }}" data-milliseconds="{{ $row->milliseconds }}">
                                        {{-- <td><a href="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}
                                        <td align="center">

                                            <a id="{{ $row->device_id }}"
                                                class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;">

                                                @if ($row->event_type == 'temperature')
                                                    <span class="sensor_icon_mini" style="background-color: green;"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em; transform: rotate(360deg);">
                                                        <path fill="currentColor"
                                                            d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z">
                                                        </path>
                                                    </svg>
                                                    {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                                @elseif($row->event_type == 'ccon')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <path fill="#4A4A4A" fill-rule="evenodd"
                                                            d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <g fill="#4A4A4A" fill-rule="nonzero">
                                                            <path
                                                                d="M7 22H5.5a.5.5 0 0 1-.5-.5V.5a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5V21a.5.5 0 0 1-.398.49l-12 2.5A.5.5 0 0 1 7 23.5V22zm0-1V3a.5.5 0 0 1 .432-.495l11-1.5.136.99L8 3.436v19.45l11-2.293V1H6v20h1z">
                                                            </path>
                                                            <path
                                                                d="M9.75 14.5c-.727 0-1.25-.698-1.25-1.5s.523-1.5 1.25-1.5S11 12.198 11 13s-.523 1.5-1.25 1.5zm0-1c.102 0 .25-.198.25-.5s-.148-.5-.25-.5c-.102 0-.25.198-.25.5s.148.5.25.5z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="fw-500"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;font-weight:600;display:block;">{{ !empty($row->name) ? $row->name : $row->device_id }}</a>
                                        </td>


                                        @if ($row->event_type == 'temperature')
                                            @php
                                                $equipment = App\Device::where('sensor_id', $row->device_id)->first();
                                            @endphp
                                            <td class="d-none d-sm-table-cell">
                                                <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                    style="color:#212529;text-decoration:none;font-weight:600;display:block;">{{ $equipment->name }}</a>

                                            </td>
                                            <td>
                                                @if (isset($row->is_active) && $row->is_active == 1)
                                                    <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                        style="color:#212529;text-decoration:none;display:block;">{{ isset($row->temperature) ? @number_format($row->temperature, 2) : 0 }}°C</a>
                                                @else
                                                    <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                        style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                                @endif

                                            </td>
                                        @else
                                            <td style="text-align: center;"><a
                                                    href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                    style="color:#212529;text-decoration:none;display:inline-block;">{{ isset($row->temperature) ? $row->temperature : '' }}</a>
                                            </td>
                                        @endif
                                        <td class="d-none d-sm-table-cell"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;"><time
                                                    class="timeago-{{ $row->device_id }}"
                                                    datetime="{{ $row->temeprature_last_updated ?? '' }}">{{ isset($row->time_ago) && $row->time_ago != '' ? $row->time_ago : '---' }}</time></a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:inline-block;">
                                                <div class="signal-indicator-icon-wrap">

                                                    @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type != 'ccon')
                                                        <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                                height="1em" preserveAspectRatio="xMidYMid meet"
                                                                viewBox="0 0 24 24">
                                                                <path fill="currentColor" fill-rule="evenodd"
                                                                    d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    <ul class="signal-indicator-bar-list">

                                                        {!! isset($row->signal) ? $row->signal : '' !!}
                                                        @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type == 'ccon')
                                                            <button class="btn btn-default bg-light btn-sm fw-500"><i
                                                                    class="fa fa-info-circle text-danger mr-2"></i>
                                                                Offline</button>
                                                        @endif

                                                    </ul>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>



            <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                <div class="row mb-3 align-items-start">
                    <div class="col-sm-4 col-4">
                        <h4 class="m-subheader__title mb-3"><strong>Not Connected</strong></h4>
                    </div>
                    <div class="col-sm-8 col-8 d-flex justify-content-end flex-wrap">
                        <button type="button" class="btn btn-default btn-sm move_sensor border" data-toggle="modal"
                            data-target="#m_select2_modal" disabled>
                            <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="m13 3l3.293 3.293l-7 7l1.414 1.414l7-7L21 11V3z" />
                                    <path fill="currentColor"
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z" />
                                </svg></span> Move to another Project
                        </button>
                    </div>

                </div>

                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless"
                        id="m_table_1">
                        <thead>
                            <tr>
                                <th width="3%" style="text-align:center">#</th>
                                <th width="5%" style="text-align:center">TYPE</th>
                                <th width="55%">NAME</th>
                                <th width="15%">STATE</th>
                                <th width="15%" class="d-none d-sm-table-cell">LAST SEEN</th>
                                <th width="10%" style="text-align: right;">SIGNAL</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (isset($not_connected) && count($sensors) > 0)
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($not_connected as $row)
                                    <tr class="sensorTable @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif"
                                        data-url="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        data-device="{{ $row->device_id }}"
                                        data-milliseconds="{{ $row->milliseconds }}">
                                        {{-- <td><a href="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}
                                        <td align="center"><input type="checkbox" name="" class="devices_ids"
                                                value="{{ $row->device_id }}"></td>
                                        <td align="center">

                                            <a id="{{ $row->device_id }}"
                                                class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;">

                                                @if ($row->event_type == 'temperature')
                                                    <span class="sensor_icon_mini" style="background-color: red;"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em; transform: rotate(360deg);">
                                                        <path fill="currentColor"
                                                            d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z">
                                                        </path>
                                                    </svg>
                                                    {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                                @elseif($row->event_type == 'ccon')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <path fill="#4A4A4A" fill-rule="evenodd"
                                                            d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <g fill="#4A4A4A" fill-rule="nonzero">
                                                            <path
                                                                d="M7 22H5.5a.5.5 0 0 1-.5-.5V.5a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5V21a.5.5 0 0 1-.398.49l-12 2.5A.5.5 0 0 1 7 23.5V22zm0-1V3a.5.5 0 0 1 .432-.495l11-1.5.136.99L8 3.436v19.45l11-2.293V1H6v20h1z">
                                                            </path>
                                                            <path
                                                                d="M9.75 14.5c-.727 0-1.25-.698-1.25-1.5s.523-1.5 1.25-1.5S11 12.198 11 13s-.523 1.5-1.25 1.5zm0-1c.102 0 .25-.198.25-.5s-.148-.5-.25-.5c-.102 0-.25.198-.25.5s.148.5.25.5z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="fw-500"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;">{{ !empty($row->name) ? $row->name : $row->device_id }}</a>
                                        </td>


                                        @if ($row->event_type == 'temperature')
                                            <td>
                                                @if (isset($row->is_active) && $row->is_active == 1)
                                                    <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                        style="color:#212529;text-decoration:none;display:block;">{{ isset($row->temperature) ? @number_format($row->temperature, 2) : 0 }}°C</a>
                                                @else
                                                    <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                        style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                                @endif

                                            </td>
                                        @else
                                            <td style="text-align: center;"><a
                                                    href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                    style="color:#212529;text-decoration:none;display:inline-block;">{{ isset($row->temperature) ? $row->temperature : '' }}</a>
                                            </td>
                                        @endif
                                        <td class="d-none d-sm-table-cell"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;"><time
                                                    class="timeago-{{ $row->device_id }}"
                                                    datetime="{{ $row->temeprature_last_updated ?? '' }}">{{ isset($row->time_ago) && $row->time_ago != '' ? $row->time_ago : '---' }}</time></a>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:inline-block;">
                                                <div class="signal-indicator-icon-wrap">

                                                    @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type != 'ccon')
                                                        <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                                height="1em" preserveAspectRatio="xMidYMid meet"
                                                                viewBox="0 0 24 24">
                                                                <path fill="currentColor" fill-rule="evenodd"
                                                                    d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    <ul class="signal-indicator-bar-list">

                                                        {!! isset($row->signal) ? $row->signal : '' !!}
                                                        @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type == 'ccon')
                                                            <button class="btn btn-default bg-light btn-sm fw-500"><i
                                                                    class="fa fa-info-circle text-danger mr-2"></i>
                                                                Offline</button>
                                                        @endif

                                                    </ul>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>



            <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                <div class="row mb-3 align-items-start">
                    <div class="col-sm-4 col-4">
                        <h4 class="m-subheader__title mb-3"><strong>Gateways</strong></h4>
                    </div>
                    <div class="col-sm-8 col-8 d-flex justify-content-end flex-wrap">
                        <button type="button" class="btn btn-default btn-sm move_sensor border" data-toggle="modal"
                            data-target="#m_select2_modal" disabled>
                            <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="m13 3l3.293 3.293l-7 7l1.414 1.414l7-7L21 11V3z" />
                                    <path fill="currentColor"
                                        d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z" />
                                </svg></span> Move to another Project
                        </button>
                    </div>

                </div>




                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless"
                        id="m_table_1">
                        <thead>
                            <tr>
                                <th width="3%" style="text-align:center">#</th>
                                <th width="5%" style="text-align:center">TYPE</th>
                                <th width="55%">NAME</th>
                                <th width="15%">STATE</th>
                                <th width="15%" class="d-none d-sm-table-cell">LAST SEEN</th>
                                <th width="10%" style="text-align: right;">SIGNAL</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (isset($gateways) && count($gateways) > 0)
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($gateways as $row)
                                    <tr class="sensorTable @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif"
                                        data-url="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        data-device="{{ $row->device_id }}"
                                        data-milliseconds="{{ $row->milliseconds }}">
                                        {{-- <td><a href="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}
                                        <td align="center"><input type="checkbox" name="" class="devices_ids"
                                                value="{{ $row->device_id }}"></td>
                                        <td align="center">

                                            <a id="{{ $row->device_id }}"
                                                class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;">

                                                @if ($row->event_type == 'temperature')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em; transform: rotate(360deg);">
                                                        <path fill="currentColor"
                                                            d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z">
                                                        </path>
                                                    </svg>
                                                    {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                                @elseif($row->event_type == 'ccon')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <path fill="#4A4A4A" fill-rule="evenodd"
                                                            d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                        class="iconify fs-22" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <g fill="#4A4A4A" fill-rule="nonzero">
                                                            <path
                                                                d="M7 22H5.5a.5.5 0 0 1-.5-.5V.5a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5V21a.5.5 0 0 1-.398.49l-12 2.5A.5.5 0 0 1 7 23.5V22zm0-1V3a.5.5 0 0 1 .432-.495l11-1.5.136.99L8 3.436v19.45l11-2.293V1H6v20h1z">
                                                            </path>
                                                            <path
                                                                d="M9.75 14.5c-.727 0-1.25-.698-1.25-1.5s.523-1.5 1.25-1.5S11 12.198 11 13s-.523 1.5-1.25 1.5zm0-1c.102 0 .25-.198.25-.5s-.148-.5-.25-.5c-.102 0-.25.198-.25.5s.148.5.25.5z">
                                                            </path>
                                                        </g>
                                                    </svg>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="fw-500"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;">{{ !empty($row->name) ? $row->name : $row->device_id }}</a>
                                        </td>



                                        <td><a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:inline-block;">
                                                @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type == 'ccon')
                                                    <button class="btn btn-default bg-light btn-sm fw-500"><i
                                                            class="fa fa-info-circle text-danger mr-2"></i>
                                                        Offline</button>
                                                @else
                                                    <button class="btn btn-default bg-light btn-sm fw-500"><i
                                                            class="fa fa-info-circle text-success mr-2"></i>
                                                        Online</button>
                                                @endif
                                            </a>
                                        </td>
                                        <td class="d-none d-sm-table-cell"><a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:block;"><time
                                                    class="timeago-{{ $row->device_id }}"
                                                    datetime="{{ $row->temeprature_last_updated ?? '' }}">{{ isset($row->time_ago) && $row->time_ago != '' ? $row->time_ago : '---' }}</time></a>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                                style="color:#212529;text-decoration:none;display:inline-block;">
                                                <div class="signal-indicator-icon-wrap">

                                                    @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type != 'ccon')
                                                        <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                                height="1em" preserveAspectRatio="xMidYMid meet"
                                                                viewBox="0 0 24 24">
                                                                <path fill="currentColor" fill-rule="evenodd"
                                                                    d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                    <ul class="signal-indicator-bar-list">

                                                        {!! isset($row->signal) ? $row->signal : '' !!}
                                                        @if (isset($row->is_active) && $row->is_active == 0 && $row->event_type == 'ccon')
                                                            {{ '----' }}
                                                        @endif

                                                    </ul>
                                                </div>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $counter++;
                                    @endphp
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-delete-equipment" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete equipment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <p>Are you sure you want to delete this equipment?</p>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Delete">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!--begin::Modal-->
    <div class="modal fade" id="equipment-modal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Save Equipment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @php
                        $email = \Auth::user()->email;
                        $sqlCompany_member = \DB::table('companies')
                            ->where('company_id', $company_id)
                            ->first();
                        $sql_Id = $sqlCompany_member->id;
                        function getRandomString($length = 20)
                        {
                            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $string = '';
                            for ($i = 0; $i < $length; $i++) {
                                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
                            }
                            return $string;
                        }
                        $randStr = getRandomString();
                    @endphp
                    @php
                        $compID = isset($currentCompany->company_id) ? $currentCompany->company_id : '-';
                    @endphp
                    <form method="post" action="{{ route('equipmentStore') }}">
                        @csrf
                        <input type="text" name="company_id" value="{{ $currentCompany->company_id }}" hidden>
                        <div class="mb-3">
                            <label>
                                Equipment name

                            </label>
                            <input type="text" class="form-control" name="equipment_name" required>
                        </div>

                        <div class="mb-3">
                            <label>
                                Description

                            </label>
                            <input type="text" class="form-control" name="description">
                        </div>

                        <div class="mb-3">
                            <label>
                                Specification
                            </label>
                            <input name="specification" class="form-control">
                        </div>


                        <div class="mb-3">
                            <label>Equipment ID</label>
                            <input type="text" name="equipment_id" class="form-control" value="{{ $randStr }}"
                                readonly>

                        </div>
                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary mx-1">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->


    {{-- @if (isset($can_manage_users) && $can_manage_users > 0)
<div class="modal fade" id="transfer_modal" tabindex="-1" role="dialog" aria-labelledby="transfer_modalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Move Sensor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
       

                    $currentID = App\Company::select('id')->where(array('name'=>$currentCompany->name,'company_id'=>$currentCompany->company_id))->get();
                  
                    $parent_ID = App\Company::select('parent_id')->get();
                    $id = \Auth::user()->id;
                    $companies2 = App\Company::where('user_id', $id)->get();
                    $flag = false;
                
                for ($i = 0; $i < sizeof($parent_ID); $i++){
                    if ($parent_ID[$i]->parent_id == $currentID[0]->id){
                        $flag = true;
                    }
                }
                if($user_id==1){
                    $companies = \App\Company::where('parent_id',0)->where('company_id','!=',$company_id)
                    ->select('id','name','company_id')
                    ->orderBy('name','ASC')
                    ->get();
                }else{
                    $companies = \App\Company::where(function($q) use ($cID,$selectedParent){
                        $q->where('id',$selectedParent);
                        $q->orWhere('parent_id',$cID);    
                        if($selectedParent>0){
                            $q->orWhere('parent_id',$selectedParent);    
                        }
                        
                    })->where('company_id','!=',$company_id)->get();
                }
                @endphp
                @if ($user_id > 1)

                <form id="move_sensor_form2">
                    <input type="hidden" name="comp_ID" id="comp_ID" value="{{isset($currentID[0]->id)?$currentID[0]->id:''}}">

                    <div class="form-group">
                    
                        <select name="transfer_sensor" id="transfer_sensor" class="form-control"> 
                            <option value="">Select Project</option>
                            
                            @if (isset($companies) && count($companies) > 0)
                            @foreach ($companies as $company2)

                            @if ($company2->parent_id == 0)
                                <option value="{{isset($company2->company_id)?$company2->company_id:''}}">{{isset($company2->name)?$company2->name:''}} (Inventory Account)</option>
                                @else
                                <option value="{{isset($company2->company_id)?$company2->company_id:''}}">{{isset($company2->name)?$company2->name:''}}</option>
                            @endif
                            
                            @endforeach
                            @endif
                            
                        </select>
                        <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                    </div>
                </form>
                @else
                <form id="move_sensor_form">

                    <input type="hidden" name="comp_ID" id="comp_ID" value="{{isset($currentID[0]->id)?$currentID[0]->id:''}}">
                    <div class="form-group">
                        <select name="transfer_sensor" id="transfer_sensor" class="form-control">
                            <option value="">Select Project</option>
                            @php
                            $crtComp = isset($currentCompany->company_id)?$currentCompany->company_id:'';
                            @endphp
                            @if (isset($companies) && count($companies) > 0)
                            @foreach ($companies as $company)
                            @php
                            if($crtComp==$company->company_id){
                            continue;
                            }

                            @endphp
                            @if ($company->parent_id == 0)
                            <option value="{{isset($company->company_id)?$company->company_id:''}}">{{isset($company->name)?$company->name:''}}</option>
                            @endif
                            @endforeach
                            @endif
                            
                        </select>
                        <p id="company_select_error" class="text-danger d-none">Please select any Project</p>
                   </div>
                </form>
                @endif

        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                @if ($user_id > 1)
                

                <button type="button" id="move_sensor_button2" class="btn btn-primary move_sensor_button">Move Sensor</button>
                @else
                <button type="button" id="move_sensor_button" class="btn btn-primary move_sensor_button">Move Sensor</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif --}}

    <div class="modal fade" id="m_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body bg-light">
                    <div class="text-center">
                        <h4 style="line-height: 1.6;">
                            <small class="text-muted d-block">
                                Listening to:
                            </small>
                            All sensors in {{ isset($currentCompany->name) ? $currentCompany->name : '' }}

                        </h4>

                        <div class="text-center my-4 touch_header">
                            <figure class="m-0">
                                <img src="{{ asset('public/assets/app/media/img/misc/sensor-animation.svg') }}"
                                    alt="Sensor" class="img-fluid" />
                            </figure>
                            <figcaption>
                                Touch your sensor to identify
                            </figcaption>
                        </div>

                        <!-- if Sensor not found -->
                        <div class="text-left sensor_touch_info d-none">
                            <p class="text-danger text-center mt-3 mb-4 fw-500">
                                Recasoft was unable to find the sensor
                            </p>
                            <ul>
                                <li>
                                    Is the sensor in range of a Cloud Connector?
                                </li>
                                <li>
                                    Could it be in a different Project?
                                </li>
                                <li>
                                    Is the sensor being listened to?
                                    {{-- Clear anything you entered in the search box and try again. --}}
                                </li>
                            </ul>
                        </div>
                        <!-- if Sensor not found ends -->

                        {{-- <div class="bg-white border rounded p-3 text-left shadow">
								Tip: Search for the Kit ID on your box (e.g. abc-00-abc) to only listen to sensors in that box.
							</div> --}}

                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary mr-auto try_again  d-none">Try Again</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0 position-relative scannerBody">

                    <form action="{{ url('claimSensor') }}" method="post">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company_id }}">
                        <div class="bg-light rounded-top border-bottom p-4 q_r_scanner_header ">
                            <div>
                                <h4>
                                    Claim devices to project {{ $currentCompany->name }}
                                </h4>
                                <p class="mb-4">
                                    This will activate the prepaid device subscription and set ownership to your
                                    organization
                                </p>
                            </div>
                            <div class="claim_devices_top_form">
                                <div class="cd_form_col_one">
                                    <button type="button" class="btn btn-light border q_r_scan_btn" data-toggle="modal"
                                        data-target="#scanner_claim_modal" onclick="scnn()"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M8 21H4a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h4a1 1 0 0 0 0-2Zm14-6a1 1 0 0 0-1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 0 0 2h4a3 3 0 0 0 3-3v-4a1 1 0 0 0-1-1ZM20 1h-4a1 1 0 0 0 0 2h4a1 1 0 0 1 1 1v4a1 1 0 0 0 2 0V4a3 3 0 0 0-3-3ZM2 9a1 1 0 0 0 1-1V4a1 1 0 0 1 1-1h4a1 1 0 0 0 0-2H4a3 3 0 0 0-3 3v4a1 1 0 0 0 1 1Zm8-4H6a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM9 9H7V7h2Zm5 2h4a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1Zm1-4h2v2h-2Zm-5 6H6a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1Zm-1 4H7v-2h2Zm5-1a1 1 0 0 0 1-1a1 1 0 0 0 0-2h-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1Zm4-3a1 1 0 0 0-1 1v3a1 1 0 0 0 0 2h1a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1Zm-4 4a1 1 0 1 0 1 1a1 1 0 0 0-1-1Z">
                                            </path>
                                        </svg> Scan QR Code</button><span>or</span>
                                </div>
                                <div class="cd_form_col_two">
                                    <div class="input-group m-input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">
                                                <div class="btn-group btn-toggle">
                                                    <button type="button"
                                                        class="btn btn-lg btn-light btn-sm kit_id btn-primary active">Kit
                                                        ID</button>
                                                    <button type="button"
                                                        class="btn btn-lg btn-light btn-sm devi_id ">Device ID</button>
                                                </div>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control m-input claim_input" name="kit_id"
                                            placeholder="E.g. ABC-42-DEF" aria-describedby="basic-addon1">
                                        {{-- <input type="text" class="form-control m-input devi_input" name="devi_id" placeholder="E.g. b6sfpst7rihg0dm4v01g" aria-describedby="basic-addon1"> --}}
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-light border addToList"> Add Kit to
                                                List</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <select id="camera-select2"></select>
                        <div id="video-container" class="qrscanner claim_qr_scanner d-none">
                            <video id="qr-video" style="width: 100% !important; "></video>

                            {{-- <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
                                    <canvas id="canvas" style="width: 100%;" hidden></canvas> --}}
                            <button type="button" class="btn btn-primary close_scanner mt-2 ml-2 mb-2">Close
                                Scanner</button>
                            {{-- <div id="output" hidden>

                                    <div hidden><b hidden>Data:</b> <span id="outputData" hidden></span></div>
								</div> --}}
                        </div>


                        <div class="p-0">
                            <!-- Loader -->
                            <div id="loader" class="loader d-none" style="text-align: center;min-height: 33px;">
                                <div style="margin-top: 150px;">
                                    <div class="pulse-button text-primary" role="status"></div>
                                </div>
                            </div>
                            <div style="text-align: center;" class="waitingDiv d-none">
                                <p class="mt-3">Please wait....</p>
                            </div>
                            <div class="claim_devices_highlited p-3 p-lg-4 m-4 q_r_scan_image">
                                <p class="mb-2 fw-500 text-center">
                                    Scan Kit ID or Device ID
                                </p>
                                <p class="text-muted text-center mb-4">
                                    Claim multiple devices by scanning or enter ID manually if needed.
                                    <br>
                                    Kit IDs are located on the backside of the device packaging.
                                </p>
                                <figure style="width: 100%;">
                                    <img src="{{ asset('public/assets/app/media/img/misc/img_scan_id.svg') }}"
                                        alt="Image" class="img-fluid" style="width: 100%;height: 100%;">
                                </figure>
                            </div>

                            <div class="table-responsive q_r_scan_table d-none">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="30%">
                                                Identifier
                                            </th>
                                            <th width="30%">
                                                Type
                                            </th>
                                            <th width="30%">
                                                Devices
                                            </th>
                                            <th width="10%">
                                                Remove
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="claim_sensor_list">
                                        {{-- <tr>
										<td>
											<span class="copyable d-inline-block">
												bjmddptntbig00dagm40
											</span>
										</td>
										<td>
											Temperature Sensor
										</td>
										<td>
											Device already claimed
										</td>
										<td class="text-center">
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="color: rgb(244, 81, 108); vertical-align: -0.125em; transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36" class="iconify fs-18" data-icon="clarity:remove-solid"><path fill="currentColor" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
										</td>
									</tr> --}}




                                    </tbody>
                                </table>
                            </div>

                        </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary mr-auto" class="close" data-dismiss="modal"
                        aria-label="Close">Cancel</button>
                    <button type="submit" class="btn btn-secondary claimSubmitBtn border" disabled><svg
                            xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="m10.6 16.6l7.05-7.05l-1.4-1.4l-5.65 5.65l-2.85-2.85l-1.4 1.4ZM12 22q-2.075 0-3.9-.788q-1.825-.787-3.175-2.137q-1.35-1.35-2.137-3.175Q2 14.075 2 12t.788-3.9q.787-1.825 2.137-3.175q1.35-1.35 3.175-2.138Q9.925 2 12 2t3.9.787q1.825.788 3.175 2.138q1.35 1.35 2.137 3.175Q22 9.925 22 12t-.788 3.9q-.787 1.825-2.137 3.175q-1.35 1.35-3.175 2.137Q14.075 22 12 22Z">
                            </path>
                        </svg> Claim to {{ $currentCompany->name }}</button>
                </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scanner_claim_moda" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body bg-light">
                    <div class="text-center">

                        <div class="text-center my-4 touch_header">
                            {{-- <div id="video-container" class="qrscanner" >
								    <video id="qr-video" style="width: 100% !important; "></video>
								</div> --}}
                            <div class="d-none">
                                <label>
                                    Highlight Style
                                    <select id="scan-region-highlight-style-select">
                                        <option value="default-style">Default style</option>
                                        <option value="example-style-1">Example custom style 1</option>
                                        <option value="example-style-2">Example custom style 2</option>
                                    </select>
                                </label>
                                <label>
                                    <input id="show-scan-region" type="checkbox">
                                    Show scan region canvas
                                </label>
                            </div>
                            <div class="d-none">
                                <select id="inversion-mode-select">
                                    <option value="original">Scan original (dark QR code on bright background)</option>
                                    <option value="invert">Scan with inverted colors (bright QR code on dark background)
                                    </option>
                                    <option value="both">Scan both</option>
                                </select>
                                {{-- <br> --}}
                            </div>
                            <b class="d-none">Device has camera: </b>
                            <span id="cam-has-camera" class="d-none"></span>
                            {{-- <br> --}}
                            <div class="d-none">
                                <b>Preferred camera:</b>
                                <select id="cam-list">
                                    <option value="environment" selected>Environment Facing (default)</option>
                                    <option value="user">User Facing</option>
                                </select>
                            </div>
                            <b class="d-none">Camera has flash: </b>
                            <span class="d-none" id="cam-has-flash"></span>
                            <div class="d-none">
                                <button id="flash-toggle">📸 Flash: <span id="flash-state">off</span></button>
                            </div>
                            {{-- <br> --}}
                            <b class="d-none">Detected QR code: </b>
                            <span class="d-none" id="cam-qr-result">None</span>
                            {{-- <br> --}}
                            <b class="d-none">Last detected at: </b>
                            <span class="d-none" id="cam-qr-result-timestamp"></span>
                            {{-- <br> --}}
                            <button id="start-button" class="d-none">Start</button>
                            <button id="stop-button" class="d-none">Stop</button>
                            {{-- <hr> --}}

                            <h1 class="d-none">Scan from File:</h1>
                            <input class="d-none" type="file" id="file-selector">
                            <b class="d-none">Detected QR code: </b>
                            <span class="d-none" id="file-qr-result">None</span>
                        </div>

                        <!-- if Sensor not found -->
                        <div class="text-left sensor_touch_info ">
                            <p class="text-dark h5 text-center mt-3 mb-4 fw-500">
                                Scan device QR Code
                            </p>
                            <p class="text-center">Scan device QR codes to identify Sensors & Cloud Connectors</p>

                            <p class="scansensor_error text-danger h5 d-none">You don't have access to this device</p>

                        </div>
                        <!-- if Sensor not found ends -->

                        {{-- <div class="bg-white border rounded p-3 text-left shadow">
								Tip: Search for the Kit ID on your box (e.g. abc-00-abc) to only listen to sensors in that box.
							</div> --}}

                    </div>
                </div>
                <div class="modal-footer p-3">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    {{-- +++++++++++++++++++++++++++++++++++++++++++++ --}}
    <!--begin::Modal-->
    <div class="modal fade" id="m_select2_modal" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Move Sensor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @php
                        
                        $currentID = App\Company::select('id')
                            ->where(['name' => $currentCompany->name, 'company_id' => $currentCompany->company_id])
                            ->get();
                        
                        $parent_ID = App\Company::select('parent_id')->get();
                        $id = \Auth::user()->id;
                        $companies2 = App\Company::where('user_id', $id)->get();
                        $flag = false;
                        
                        for ($i = 0; $i < sizeof($parent_ID); $i++) {
                            if ($parent_ID[$i]->parent_id == $currentID[0]->id) {
                                $flag = true;
                            }
                        }
                        if ($user_id == 1) {
                            $companies = \App\Company::where('parent_id', 0)
                                ->where('company_id', '!=', $company_id)
                                ->select('id', 'name', 'company_id')
                                ->orderBy('name', 'ASC')
                                ->get();
                        } else {
                            $companies = \App\Company::where(function ($q) use ($cID, $selectedParent) {
                                $q->where('id', $selectedParent);
                                $q->orWhere('parent_id', $cID);
                                if ($selectedParent > 0) {
                                    $q->orWhere('parent_id', $selectedParent);
                                }
                            })
                                ->where('company_id', '!=', $company_id)
                                ->get();
                        }
                    @endphp
                    @if ($user_id > 1)
                        <form id="move_sensor_form2">
                        @csrf

                            <input type="hidden" name="comp_ID" id="comp_ID"
                                value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">
                            <input type="hidden" name="selected_company_id" id="selected_company_id" value="">

                            <div class="mb-4" id="custome-input">
                                {{-- <input type="search" name="" class="form-control" name="transfer_sensor1"
                                    id="transfer_sensor" placeholder="Start typing a project's name">
                                <div id="search_suggestions"></div> --}}
                                <select class="form-control m-select2" name="transfer_sensor1" id="m_select2_1"
                                    name="param">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                            </div>

                        </form>
                    @else
                        <form id="move_sensor_form">
                            @csrf
                            <input type="hidden" name="comp_ID" id="comp_ID"
                                value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">
                            <input type="hidden" name="selected_company_id" id="selected_company_id" value="">

                            <div class="mb-4" id="custome-input">
                                {{-- <input type="search" name="" class="form-control" name="transfer_sensor1"
                                    id="transfer_sensor" placeholder="Start typing a project's name">
                                <div id="search_suggestions"></div> --}}
                                 <select class="form-control m-select2" name="transfer_sensor1" id="m_select2_1"
                                    name="param">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>

                                <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                            </div>
                        </form>
                    @endif


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @if ($user_id > 1)
                        <button type="button" id="move_sensor_button2" class="btn btn-primary move_sensor_button">Move
                            Sensor</button>
                    @else
                        <button type="button" id="move_sensor_button" class="btn btn-primary move_sensor_button">Move
                            Sensor</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.timeago.js') }}"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var pusher = new Pusher('ece81d906376bc8c0bab', {
            cluster: 'ap2',
            encrypted: true
        });

        var par_company_id = '{{ $parent_company ?? '' }}';
        var current_company = '{{ $currentCompany->company_id ?? '' }}';
        var channel = pusher.subscribe('my-channel-project.' + par_company_id);

        // Bind a function to a Event (the full Laravel class)
        channel.bind('App\\Events\\TouchEvent', function(data) {
            console.log(data);
            if (data.data && data.data.event_type && data.data.event_type == 'touch') {
                console.log('inside ', $('#' + data.data.deviceId));
                $('#' + data.data.deviceId).addClass('pulse-button');
                setTimeout(function() {
                    $('#' + data.data.deviceId).removeClass('pulse-button');
                }, 2000);
                let device_exist = false;
                @foreach ($sensors as $sensorr)
                    var device_id = "{{ $sensorr->device_id ?? '' }}";
                    if (data.data.deviceId == device_id) {
                        device_exist = true;
                    }
                @endforeach
                console.log(device_exist);
                if ($('#m_modal_2').is(':visible') == true && device_exist == true) {
                    window.location.href = '{{ url('sensor-details') }}/' + current_company + '/' + data.data
                        .deviceId;
                }
                console.log('Pusher = ', data.data);
            }
        });
        $('.search-devices-input').keypress(function(event) {
            if (event.which === 13) { // 13 corresponds to the "Enter" key
                event.preventDefault(); // Prevent form submission through Enter key
                $('#searchForm').submit();
            }
        });
        // $(document).ready(function(){
        //     moment.tz.setDefault('Europe/Oslo');
        //     $('#m_table_1 tbody tr').on('click',function(){
        //         window.location.href=$(this).attr('data-url');
        //     }) ;
        // });
        $('.reset-filter').on('click', function() {
            window.location.href = '{{ url('sensors/' . $company_id) }}';
        });

        $('table tr').each(function() {
            var device_id = $(this).attr('data-device');
            var milliseconds = $(this).attr('data-milliseconds');
            if (typeof device_id !== "undefined" && typeof milliseconds !== "undefined") {
                var now = new Date();
                var UTC_DIFFERENCE = now.getTimezoneOffset() * 60;
                var newTime = parseInt(milliseconds) + (UTC_DIFFERENCE);
                var newTime2 = new Date(newTime);
                setInterval(function() {
                    $("time.timeago-" + device_id).timeago('update', newTime2);
                }, 1000);
            }
        });



        $(document).ready(function() {
            let device_ids = [];
            $('.devices_ids').on('change', function() {
                // alert($('#devices_ids:checked').length);
                if ($('.devices_ids:checked').length > 0) {
                    $('.move_sensor').attr('disabled', false);
                } else {
                    $('.move_sensor').attr('disabled', true);
                }


            });


            $('.move_sensor').on('click', function() {
                console.log('sdfsdf');
                device_ids = [];
                $('input[type=checkbox]:checked').each(function(index) {
                    let device_id = $(this).val();
                    device_ids.push(device_id);


                    //part where the magic happens
                    // console.log(index+' checkbox has value' +$(this).val());
                });

                console.log(device_ids);
            });
            $('.move_sensor_button').on('click', function() {
                let company_name = $('#transfer_sensor').val();
                let comp_ID = $('#comp_ID').val();
                let company_id = $('#m_select2_1').val();

                console.log(device_ids);
                if (company_id == '') {
                    $('#company_select_error').removeClass('d-none');
                } else {
                    $('#company_select_error').addClass('d-none');
                    $.ajax({

                        url: '{{ route('companies.moveSensors') }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            device_ids: device_ids,
                            company_name: company_name,
                            comp_ID: comp_ID,
                            transfer_sensor: company_id,
                        },
                        // beforeSend:function(){
                        //     $('#search-loader').show();
                        // },
                        success: function(data) {
                            console.log(data);
                            if (data.success && data.success == true) {
                            location.reload(true);
                               // window.location.href = '{{ route('sensors', ['company_id' => $company_id]) }}';
                            }

                            // $('#search-loader').hide();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
            $(document).on('click', '#identify_touch_sensor', function() {
                $('.touch_header').removeClass('d-none');
                $('.sensor_touch_info').addClass('d-none');
                $('.try_again').addClass('d-none');
                setTimeout(function() {
                    $('.sensor_touch_info').removeClass('d-none');
                    $('.touch_header').addClass('d-none');
                    $('.try_again').removeClass('d-none');
                }, 10000);
            });

            $(document).on('click', '.try_again', function() {
                $('.sensor_touch_info').addClass('d-none');
                $('.try_again').addClass('d-none');
                $('.touch_header').removeClass('d-none');
                setTimeout(function() {
                    $('.sensor_touch_info').removeClass('d-none');
                    $('.touch_header').addClass('d-none');
                    $('.try_again').removeClass('d-none');
                }, 10000);
            });
        });
    </script>
    <script type="text/javascript">
        /* var qrscan = document.createElement("video");
                var canvasElement = document.getElementById("canvas");
               var canvas = canvasElement.getContext("2d");
                var loadingMessage = document.getElementById("loadingMessage");
                var outputContainer = document.getElementById("output");
                // var outputMessage = document.getElementById("outputMessage");
                var outputData = document.getElementById("outputData");
                var hasStarted = false;
                function drawLine(begin, end, color) {
                  canvas.beginPath();
                  canvas.moveTo(begin.x, begin.y);
                  canvas.lineTo(end.x, end.y);
                  canvas.lineWidth = 4;
                  canvas.strokeStyle = color;
                  canvas.stroke();
                }

                // Use facingMode: environment to attemt to get the front camera on phones
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
                  qrscan.srcObject = stream;
                  qrscan.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                  qrscan.play();
                  hasStarted = true;
                  requestAnimationFrame(tick);
                }); */
    </script>
    <script>
        $("#select").select2({
            tags: true,
            // dropdownParent: $('#modal), // if select in modal
            theme: "bootstrap",
        });
        let claim_devices_list = [],
            device_counter = 0;
        const videoo = document.getElementById('qr-video');
        let alreadyClaimed = 1;

        function setResultt(label, result) {
            console.log(result.data);


            label.textContent = result.data;
            camQrResultTimestamp.textContent = new Date().toString();
            label.style.color = 'teal';
            // clearTimeout(label.highlightTimeout);
            // label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);

            deviceid = result.data;

            let company_id = "{{ $company_id ?? '' }}";

            // claimSensor(deviceid);
            setTimeout(function() {
                claimSensor(deviceid);
            }, 2000);
            $('.kit_id').click();

        }

        const scannerr = new QrScanner(videoo, result => setResultt(camQrResult, result), {
            onDecodeError: error => {
                camQrResult.textContent = error;
                camQrResult.style.color = 'inherit';
            },
            highlightScanRegion: true,
            highlightCodeOutline: true,
        });
        let activeCameras = null;

        function scnn() {
            if (activeCameras) {
                // Stop the scanner and remove the active camera
                scannerr.stop();
            }

            const cameraSelect = document.getElementById('camera-select2');
            cameraSelect.innerHTML = ''; // Clear all options

            const selectedCamera = cameraSelect.value;

            const updateFlashAvailability = () => {
                scannerr.hasFlash().then(hasFlash => {
                    camHasFlash.textContent = hasFlash;
                    flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
                });
            };

            // Add a default option as the first option
            const defaultOption = document.createElement('option');
            defaultOption.text = 'Default Camera';
            defaultOption.selected = true;
            defaultOption.value = 'environment';
            cameraSelect.add(defaultOption);

            scannerr.start(selectedCamera).then(() => {
                activeCameras = selectedCamera;
                updateFlashAvailability();
                QrScanner.listCameras(true).then(cameras => {
                    cameras.forEach(camera => {
                        const option = document.createElement('option');
                        option.value = camera.id;
                        option.text = camera.label;
                        cameraSelect.add(option);
                    });

                    // Update the selected option based on the initially selected camera
                    cameraSelect.value = selectedCamera || defaultOption.value;
                });
            });

            cameraSelect.addEventListener('change', event => {
                scannerr.setCamera(event.target.value).then(updateFlashAvailability);
            });
        }



        QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);

        // for debugging
        window.scannerr = scannerr;

        document.getElementById('scan-region-highlight-style-select').addEventListener('change', (e) => {
            videoContainer.className = e.target.value;
            scannerr._updateOverlay(); // reposition the highlight because style 2 sets position: relative
        });

        document.getElementById('show-scan-region').addEventListener('change', (e) => {
            const input = e.target;
            const label = input.parentNode;
            label.parentNode.insertBefore(scannerr.$canvas, label.nextSibling);
            scannerr.$canvas.style.display = input.checked ? 'block' : 'none';
        });

        document.getElementById('inversion-mode-select').addEventListener('change', event => {
            scannerr.setInversionMode(event.target.value);
        });

        camList.addEventListener('change', event => {
            scannerr.setCamera(event.target.value).then(updateFlashAvailability);
        });

        flashToggle.addEventListener('click', () => {
            scannerr.toggleFlash().then(() => flashState.textContent = scannerr.isFlashOn() ? 'on' : 'off');
        });

        document.getElementById('start-button').addEventListener('click', () => {
            scannerr.start();
        });

        document.getElementById('stop-button').addEventListener('click', () => {
            scannerr.stop();
        });

        // ####### File Scanning #######

        fileSelector.addEventListener('change', event => {
            const file = fileSelector.files[0];
            if (!file) {
                return;
            }
            QrScanner.scanImage(file, {
                    returnDetailedScanResult: true
                })
                .then(result => setResult(fileQrResult, result))
                .catch(e => setResult(fileQrResult, {
                    data: e || 'No QR code found.'
                }));
        });


        $('#m_modal_5').on('hidden.bs.modal', function() {

            scannerr.stop();
            $('.claim_input').val('');
            claim_devices_list = [];

            $('.q_r_scan_table').addClass('d-none');
            $('.q_r_scan_image').removeClass('d-none');
            $('.claimSubmitBtn').prop('disabled', true);

            $('.claim_qr_scanner').addClass('d-none');
            $('.q_r_scanner_header').removeClass('d-none');

            scannerr.stop();
            $('.kit_id').click();


        });
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                //event.preventDefault(); // Prevent the default form submission behavior

                // Show the loader
                $('#loader').removeClass('d-none');
                $('.waitingDiv').removeClass('d-none');
                $('.q_r_scan_table').addClass('d-none');

                // Perform the AJAX form submission
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    success: function(response) {
                        // Hide the loader
                        //$('#loader').addClass('d-none');

                        // Handle the success response here
                        // For example, you can display a success message or perform any other necessary actions

                        // Reload the page to update the UI if needed
                        //location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Hide the loader
                        $('#loader').hide();

                        // Handle the error response here
                        // For example, you can display an error message or perform any other necessary actions
                    }
                });
            });
        });

        $(document).on('click', '.q_r_scan_btn', function() {
            $('.q_r_scan_table').removeClass('d-none');
            $('.claim_qr_scanner').removeClass('d-none');
            $('.q_r_scanner_header').addClass('d-none');
            $('.q_r_scan_image').addClass('d-none');
            $('.claim_input').val('');


        });
        $(document).on('click', '.close_scanner ', function() {
            if (device_counter == 0) {
                $('.q_r_scan_table').addClass('d-none');
                $('.q_r_scan_image').removeClass('d-none');
            }

            $('.claim_qr_scanner').addClass('d-none');
            $('.q_r_scanner_header').removeClass('d-none');

            scannerr.stop();
            $('.kit_id').click();
        });

        $(document).on('click', '.kit_id', function() {
            $(this).addClass('active');
            $('.devi_id').removeClass('active');
            $('.devi_id').removeClass('btn-primary btn-default');
            $('.claim_input').attr('placeholder', 'E.g. ABC-42-DEF');

            $(this).addClass('btn-primary btn-default');
            $('.addToList').text('Add Kit to List');
        });
        $(document).on('click', '.devi_id', function() {
            $(this).addClass('active');
            $(this).addClass('btn-primary btn-default');

            $('.claim_input').attr('placeholder', 'E.g. b6sfpst7rihg0dm4v01g');
            $('.kit_id').removeClass('active');
            $('.kit_id').removeClass('btn-primary btn-default');

            $('.addToList').text('Add Device to List');

        });

        $(document).on('click', '.addToList', function() {

            let devi_id = $('.claim_input').val();
            claimSensor(devi_id);
            $('.claim_input').val('');
        });


        $(document).on('blur', '.claim_input', function() {
            let val = $(this).val();

            if (val != '') {


                let firtst_dash = val.charAt(3);
                let second_dash = val.charAt(6);
                if (firtst_dash == '-' && second_dash == '-') {
                    $('.kit_id').addClass('active');
                    $('.devi_id').removeClass('active');
                    $('.devi_id').removeClass('btn-primary btn-default');

                    $('.kit_id').addClass('btn-primary btn-default');
                    $('.addToList').text('Add Kit to List');
                } else {

                    $('.devi_id').addClass('active');
                    $('.devi_id').addClass('btn-primary btn-default');
                    $('.kit_id').removeClass('active');
                    $('.kit_id').removeClass('btn-primary btn-default');

                    $('.addToList').text('Add Device to List');
                }
            }
        });



        $(document).on('click', '.removeDevice', function() {
            let device_id = $(this).attr('deviceId');
            console.log(device_id);
            $('.claim_input').val('');

            $('.list-' + device_id).remove();
            console.log(claim_devices_list.length);
            let index = claim_devices_list.indexOf(device_id);
            delete claim_devices_list[index];
            device_counter--;
            console.log(claim_devices_list.length);
            if (device_counter == 0) {

                $('.q_r_scan_table').addClass('d-none');
                $('.q_r_scan_image').removeClass('d-none');
                $('.claimSubmitBtn').prop('disabled', true);
                alreadyClaimed = 1;
            }
            // devices_counter--;

            // $('.save_notification').prop('disabled',false);

            // if(devices_counter == 0){

            // 	$('.add_action').prop('disabled',true);
            // 	$('.save_notification').prop('disabled',true);


            // }

            console.log(claim_devices_list);
        });


        function claimSensor(deviceid) {

            $.ajax({

                url: '{{ route('claim.sensor') }}',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    device_id: deviceid,

                },
                // beforeSend:function(){
                //     $('#search-loader').show();
                // },
                success: function(data) {

                    console.log(data);
                    // claim_devices_list.each(function(index, el) {
                    // 	alert(el);
                    // });
                    console.log($.inArray(deviceid, claim_devices_list));
                    //      	jQuery.each(claim_devices_list, function(index, item) {
                    //     alert(item);
                    // });

                    // console.log(data.response.kit.devices.length);



                    if (data.error != true) {
                        $('.q_r_scan_table').removeClass('d-none');
                        $('.q_r_scan_image').addClass('d-none');
                        if ($.inArray(deviceid, claim_devices_list) == -1) {

                            claim_devices_list.push(deviceid);
                            device_counter++;
                            console.log(claim_devices_list);
                            $('.claim_sensor_list').append(data.claim_html);
                            if (data.response.type == 'DEVICE') {
                                if (data.isClaimed != true) {

                                    alreadyClaimed = 0;
                                }
                            } else {
                                if (data.claimedDevices != data.response.kit.devices.length) {
                                    alreadyClaimed = 0;
                                }
                            }

                        }

                    }

                    if (data.error == true) {
                        // alert('Data Not Found');
                        $('.claimSubmitBtn').prop('disabled', true);
                    }

                    if (alreadyClaimed == 0) {
                        console.log(alreadyClaimed);
                        // if(device_counter==1 || device_counter==0){
                        $('.claimSubmitBtn').prop('disabled', false);
                        // }else{
                        // 	$('.claimSubmitBtn').prop('disabled',false);
                        // }

                    }

                },
                error: function(data) {
                    console.log(data);
                }

            });

        }
        /*    function tick() {
                    if (!hasStarted) return;
          loadingMessage.innerText = "⌛ Loading video..."
          if (qrscan.readyState === qrscan.HAVE_ENOUGH_DATA) {
            loadingMessage.hidden = true;
            canvasElement.hidden = false;
            outputContainer.hidden = false;

            canvasElement.height = qrscan.videoHeight;
            canvasElement.width = qrscan.videoWidth;
            canvas.drawImage(qrscan, 0, 0, canvasElement.width, canvasElement.height);
            var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            var code = jsQR(imageData.data, imageData.width, imageData.height, {
              inversionAttempts: "dontInvert",
            });
            if (code) {
              drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
              drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
              drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
              drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
              // outputMessage.hidden = true;
              outputData.parentElement.hidden = false;
              outputData.innerText = code.data;
             var deviceid = code.data;
              claimSensor(deviceid);
            } else {
              // alert(outputData);
              // outputMessage.hidden = false;
              outputData.parentElement.hidden = true;
            }
          }
          requestAnimationFrame(tick);
        } */
        $(document).ready(function() {

            $(document).on('click', '.deleteEquipment', function(e) {
                var id = $(this).attr('data-id');
                $('#modal-delete-equipment').modal('show');
                $('#modal-delete-equipment form').append("<input type='hidden' name='eID' value='" +
                    id + "' />");
                $('#modal-delete-equipment form').attr('action', $(this).attr('data-url'));
            });
        });


        $("#m_select2_6").select2({
            placeholder: "Search for git repositories",
            allowClear: true,
            ajax: {
                url: "https://api.github.com/search/repositories",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 2,
            //templateResult: formatRepo, // omitted for brevity, see the source of this page
            //templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
    </script>
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}
    {{-- <script>
    $(document).ready(function() {
      $('#my-select').select2({
        placeholder: 'Search for project...',
        width:'100%',
        ajax: {
          url: '/path/to/your/api',
          dataType: 'json',
          delay: 250,
          processResults: function (data) {
            return {
              results: $.map(data, function (item) {
                return {
                  text: item.text,
                  id: item.id
                }
              })
            };
          },
          cache: true
        },
        minimumInputLength: 2
      });
    });
  </script> --}}

    {{-- <script src="{{url('public/assets/demo/default/custom/crud/forms/widgets/select2.js')}}" type="text/javascript"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        var Select2 = {
            init: function() {
                $("#m_select2_1, #m_select2_1_validate").select2({
                        placeholder: "Select a state"
                    }),
                    $("#m_select2_modal").on("shown.bs.modal", function() {
                        $("#m_select2_1_modal").select2({
                            allowClear: true,
                            minimumInputLength: 2,
                            placeholder: "Select a project"
                        }), $("#m_select2_2_modal").select2({
                            placeholder: "Select a project"
                        }), $("#m_select2_3_modal").select2({
                            placeholder: "Select a state"
                        }), $("#m_select2_4_modal").select2({
                            placeholder: "Select a state",
                            allowClear: !0
                        })
                    })
            }
        };
        jQuery(document).ready(function() {
            Select2.init()
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script type="text/javascript">
        $("#transfer_sensor").on("input", function() {
            var input = $(this).val().toLowerCase();
            var suggestions = "";
            var companies = {!! $companies ?? '' !!}; // assuming $companies is a PHP array of company names

            for (var i = 0; i < companies.length; i++) {
                var name = companies[i].name.toLowerCase();
                if (name.indexOf(input) !== -1) {
                    suggestions +=
                        ' <li class="dropdown-item"><a class="dropdown-item" id="select_company" href="#" company-id="' +
                        companies[i].company_id + '" role="option">' + companies[i].name + '</a></li>';
                }
            }

            $("#search_suggestions").html(suggestions);
        });

        $(document).ready(function() {
            $('#transfer_sensor').on('focus', function() {

                $('#search_suggestions').css('display', 'block');

            });

            $('#transfer_sensor').on('blur', function() {
                if ($('#search_suggestions:hover').length === 0) {
                    $('#search_suggestions').css('display', 'none');
                }
            });

            /*   $('#search_suggestions').on('click', '.dropdown-item > a', function() {
                   let companyName = $(this).text();
                   let companyId = $(this).attr('company-id');
                   $('#transfer_sensor').val(companyName);

                   $('#selected_company_id').val(companyId);
                   $('#search_suggestions').css('display', 'none');
               }); */

            $('#m_select2_1').change(function() {
                var selectedCompanyId = $(this).val();
                $('#selected_company_id').val(selectedCompanyId);
            });



            $('#clear_search').on('click', function() {
                $('#transfer_sensor').val('');
                $('#search_suggestions').css('display', 'none');

            });
        });
    </script>
@endpush
