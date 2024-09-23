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


    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        @php
            $is_valid = 0;
            $currentRouteName = Request::route()->getName();
            $company = \App\Company::where(['company_id' => $company_id])->first();
            if (isset($company->id)) {
                $is_valid = 1;
            }

            $user_ID = \Auth::user()->id;
            $user_Role = '';
            if ($company_id != '') {
                $user_Role = \App\CompanyMembers::where([
                    'company_id' => $company_id,
                    'user_id' => $user_ID,
                    // , 'company_name' => $company_name
                ])
                    ->select('role')
                    ->first();
            }

            if (isset($company) && $company->parent_id != 0) {
                $child_company = \App\Company::where(['company_id' => $company_id])->first();
            }

            if (isset($child_company) && $child_company->parent_id != 0) {
                $role2 = 'valid';
            }
        @endphp
        <!-- BEGIN: Content -->
        <div class="m-subheader">
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
                        icon: 'error',
                        title: '{{ session('title') }}',
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

            @if (isset($role2) && $role2 == 'valid')
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <h4 class="m-subheader__title "></h4>
                    </div>
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-lg-end flex-column flex-sm-row">

                            <div class="form-group m-form__group mb-2 mb-sm-0">
                                <div class="m-input-icon m-input-icon--right">
                                    <form id="searchForm" action="" method="get">
                                        <div class="d-flex">
                                            <select id="searchTypes" name="searchTypes" class="form-control mr-2"
                                                style="display: inline-block;width: inherit;float: left;">
                                                <option value="1" @if ($search_type == 1) selected @endif>
                                                    Equipment Name</option>
                                                <option value="2" @if ($search_type == 2) selected @endif>
                                                    Equipment ID</option>
                                            </select>

                                            <div class="input-group search-devices-input">
                                                <input type="text" class="form-control m-input"
                                                    placeholder="Search for equipments in project"
                                                    aria-describedby="devicesSearch" name="equipment_search"
                                                    value="{{ $equipment_search ?? '' }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="devicesSearch"><i
                                                            class="fa flaticon-search"></i></span>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                    <!-- <span class="m-input-icon__icon m-input-icon__icon--right"><span><i class="fa flaticon-search"></i></span></span> -->
                                </div>
                            </div>
                            @if (isset($equipment_search) && $equipment_search != '')
                                <button type="button" class="btn btn-primary ml-0 ml-sm-2 reset-filter">Reset
                                    Filter</button>
                            @endif

                        </div>
                    </div>

                </div>

        </div>

        <!-- END: Content -->


        <div class="m-content">

            {{-- Equipment add table  --}}
            <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                <!--begin: Datatable -->

                <div class="row isDefault align-items-center mb-3">
                    <div class="col-6">
                        <h4 class="m-0 fw-700">
                            <strong>Connected</strong>
                        </h4>
                    </div>
                </div>


                <div class="table-responsive">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable has-valign-middle table-borderless"
                        id="m_table_1">
                        <thead>
                            <tr>
                                <th width="5%" style="text-align: center">TYPE</th>
                                <th width="30%">EQUIPMENT</th>
                                <th width="30%" class="d-none d-sm-table-cell">DESCRIPTION</th>
                                <th width="30%">SPECIFICATION</th>
                                <th class="d-none d-sm-table-cell">CONNECTED SENSOR</th>
                                <th width="15%">STATE</th>
                                <th width="10%">SIGNAL</th>
                                <th width="5%" style="text-align: center">ACTIONS</th>

                            </tr>
                        </thead>
                        <tbody>

                            {{-- @if (isset($sensors) && count($sensors) > 0)
                        @php
                        $counter=1;
                        @endphp
                        @foreach ($sensors as $row)


                            <td align="center"><input type="checkbox" name="" class="devices_ids" value="{{$row->device_id}}"></td> --}}
                            @foreach ($connected_equipments as $row)
                                @php
                                    $sensor_data = App\Device::where('device_id', $row->sensor_id)->first();
                                @endphp
                                <tr class="equipmentTable @if (isset($sensor_data->is_active) && $sensor_data->is_active == 0) is_disabled @endif"
                                    data-url="{{ url('equipments') }}/{{ $company_id }}/{{ $row->device_id }}"
                                    data-device="{{ $sensor_data->device_id }}"
                                    data-milliseconds="{{ $sensor_data->milliseconds }}">
                                    <td align="center">

                                        <a id="{{ $row->sensor_id }}"
                                            class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            equipment-device="{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">


                                            <span class="sensor_icon_mini" style="background-color: green;"></span>
                                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                viewBox="0 0 459.359 459.359"
                                                style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                                <g>
                                                    <path style="fill:#020202;"
                                                        d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362 c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912 c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z" />
                                                    <path style="fill:#020202;"
                                                        d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67 c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774 c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z" />
                                                    <path style="fill:#020202;"
                                                        d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93 C153.145,312.05,157.593,307.619,163.061,307.619z" />
                                                    <path style="fill:#020202;"
                                                        d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473 c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294 C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143 c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026 H23.075V236.214h65.787V361.026z" />
                                                    <path style="fill:#020202;"
                                                        d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814 c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8 c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058 c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03 c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8 c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814 c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064 c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738 c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372 l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113 h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366 l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801 c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926 C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337 c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z" />
                                                    <path style="fill:#020202;"
                                                        d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926 c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z" />
                                                </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                            </svg>

                                        </a>
                                    </td>

                                    <td class="fw-500"><a
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">{!! isset($row->name) ? $row->name : $row->device_id !!}</a>
                                    </td>

                                    <td class="d-none d-sm-table-cell"><a
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">{!! isset($row->description) && $row->description != '' ? $row->description : '---' !!}</a>
                                    </td>

                                    <td>
                                        <a href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:inline-block;">
                                            {!! isset($row->specification) && $row->specification != '' ? $row->specification : '---' !!}</a>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <a href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">{!! !empty($sensor_data->name) ? $sensor_data->name : $sensor_data->device_id !!}</a>
                                    </td>
                                    <td>
                                        @if (isset($sensor_data->is_active) && $sensor_data->is_active == 1)
                                            <a
                                                style="color:#212529;text-decoration:none;display:block;">{{ isset($sensor_data->temperature) ? @number_format($sensor_data->temperature, 2) : 0 }}°C</a>
                                        @else
                                            <a style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                        @endif

                                    </td>
                                    <td style="text-align: center;">
                                        <a style="color:#212529;text-decoration:none;display:inline-block;">
                                            <div class="signal-indicator-icon-wrap">

                                                @if (isset($sensor_data->is_active) && $sensor_data->is_active == 0 && $sensor_data->event_type != 'ccon')
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
                                                    @php
                                                        $active = 'active';
                                                        if (isset($is_active) && $is_active == 0) {
                                                            $active = '';
                                                        }
                                                        if ($sensor_data->signal_strength <= 20) {
                                                            $signal_div =
                                                                '<li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class=""></li>
                                                            <li class=""></li>
                                                            <li class=""></li>
                                                            <li class=""></li>';
                                                        } elseif ($sensor_data->signal_strength > 20 && $sensor_data->signal_strength <= 40) {
                                                            $signal_div =
                                                                '<li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class=""></li>
                                                            <li class=""></li>
                                                            <li class=""></li>';
                                                        } elseif ($sensor_data->signal_strength > 40 && $sensor_data->signal_strength <= 60) {
                                                            $signal_div =
                                                                '<li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class=""></li>
                                                            <li class=""></li>';
                                                        } elseif ($sensor_data->signal_strength > 60 && $sensor_data->signal_strength <= 80) {
                                                            $signal_div =
                                                                '<li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class=""></li>';
                                                        } elseif ($sensor_data->signal_strength > 80) {
                                                            $signal_div =
                                                                '<li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>
                                                            <li class="' .
                                                                $active .
                                                                '"></li>';
                                                        } else {
                                                            $signal_div = '';
                                                        }
                                                    @endphp
                                                    {!! isset($signal_div) ? $signal_div : '' !!}
                                                    @if (isset($sensor_data->is_active) && $sensor_data->is_active == 0 && $sensor_data->event_type == 'ccon')
                                                        <button class="btn btn-default bg-light btn-sm fw-500"><i
                                                                class="fa fa-info-circle text-danger mr-2"></i>
                                                            Offline</button>
                                                    @endif

                                                </ul>
                                            </div>
                                        </a>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="miniIcon">
                                            <i data-url="{{ route('equipment.delete', ['id' => $row->id]) }}"
                                                data-id="{{ $row->id }}" class="deleteEquipment la la-trash"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>

            <div class="m-portlet panel-has-radius mb-4 custom-p-5">



                <div class="row isDefault align-items-center mb-3">
                    <div class="col-6">
                        <h4 class="m-0 fw-700">
                            <strong>Not connected</strong>
                        </h4>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary " data-toggle="modal"
                            data-target="#equipment-modal" style="margin-left: 6px;">Add Equipment</button>
                    </div>
                </div>



                <div class="table-responsive">
                    <table
                        class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless"
                        id="m_table_1">
                        <thead>
                            <tr>
                                <th width="5%" style="text-align: center">TYPE</th>
                                <th width="30%">EQUIPMENT NAME</th>
                                <th width="30%" class="d-none d-sm-table-cell">DESCRIPTION</th>
                                <th width="30%">SPECIFICATION</th>

                                <th width="5%" style="text-align: center">ACTIONS</th>

                            </tr>
                        </thead>
                        <tbody>

                            {{-- @if (isset($sensors) && count($sensors) > 0)
                        @php
                        $counter=1;
                        @endphp
                        @foreach ($sensors as $row)

                        <tr class="equipmentTable @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif" data-url="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" data-device="{{$row->device_id}}" data-milliseconds="{{$row->milliseconds}}">

                            <td align="center"><input type="checkbox" name="" class="devices_ids" value="{{$row->device_id}}"></td> --}}
                            @foreach ($equipments as $row)
                                <td align="center">

                                    <a id="{{ $row->device_id }}"
                                        class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                        href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        equipment-device="{{ $row->device_id }}"
                                        style="color:#212529;text-decoration:none;display:block;">



                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 459.359 459.359"
                                            style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                            <g>
                                                <path style="fill:#020202;"
                                                    d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362 c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912 c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z" />
                                                <path style="fill:#020202;"
                                                    d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67 c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774 c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z" />
                                                <path style="fill:#020202;"
                                                    d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93 C153.145,312.05,157.593,307.619,163.061,307.619z" />
                                                <path style="fill:#020202;"
                                                    d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473 c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294 C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143 c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026 H23.075V236.214h65.787V361.026z" />
                                                <path style="fill:#020202;"
                                                    d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814 c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8 c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058 c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03 c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8 c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814 c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064 c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738 c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372 l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113 h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366 l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801 c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926 C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337 c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z" />
                                                <path style="fill:#020202;"
                                                    d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926 c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z" />
                                            </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                            <g> </g>
                                        </svg>

                                    </a>
                                </td>

                                <td class="fw-500"><a
                                        href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        style="color:#212529;text-decoration:none;display:block;">{!! isset($row->name) && $row->name != '' ? $row->name : $row->device_id !!}</a>
                                </td>

                                <td class="d-none d-sm-table-cell"><a
                                        href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        style="color:#212529;text-decoration:none;display:block;">{!! isset($row->description) && $row->description != '' ? $row->description : '---' !!}</a>
                                </td>

                                <td>
                                    <a href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                        style="color:#212529;text-decoration:none;display:inline-block;">
                                        {!! isset($row->specification) && $row->specification != '' ? $row->specification : '---' !!}</a>
                                </td>
                                <td>
                                    <span class="miniIcon">
                                        <i data-url="{{ route('equipment.delete', ['id' => $row->id]) }}"
                                            data-id="{{ $row->id }}" class="deleteEquipment la la-trash"></i>
                                    </span>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if (!(isset($role2) && $role2 == 'valid'))
                <div class="row align-items-center" style="margin-bottom: 50px;">
                    <div class="col-lg-4">
                        <h4 class="m-subheader__title "></h4>
                    </div>
                    <div class="col-lg-8">

                        <div class="d-flex justify-content-lg-end flex-column flex-sm-row">
                            <div class="col-sm-6 d-flex justify-content-sm-end flex-wrap">
                                <div class="d-flex flex-wrap mr-2 mb-2 mb-md-0">
                                    @if (isset($search_inventory) && $search_inventory != '')
                                        <button type="button" style="margin-right: 5px;"
                                            class="btn btn-primary ml-3 reset-filter">Reset Filter</button>
                                    @endif
                                    <div class="form-group m-form__group m-0">
                                        <div class="m-input-icon m-input-icon--right">
                                            <form id="searchForm" action="" method="get">
                                                <div class="d-flex">

                                                    <input type="text"
                                                        class="form-control m-input search-devices-input"
                                                        placeholder="Search inventory" name="search_inventory"
                                                        value="{{ $search_inventory ?? '' }}">
                                                </div>
                                            </form>
                                            <span class="m-input-icon__icon m-input-icon__icon--right"><span><i
                                                        class="fa flaticon-search"></i></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="row align-items-center mb-3">
                                <div class="col-6">
                                    <h4 class="m-subheader__title mb-0"><strong>Inventory</strong></h4>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary d-block d-sm-none" data-toggle="modal"
                                        data-target="#inventory-modal">Add
                                        Inventory</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 d-flex justify-content-md-end flex-wrap align-items-start">
                            <button type="button" class="btn btn-primary d-sm-inline-block mb-2 mb-sm-0 mr-1 d-none"
                                data-toggle="modal" data-target="#inventory-modal">Add
                                Inventory</button>
                            <div>
                                @if (isset($can_manage_users) && $can_manage_users > 0)
                                    <button type="button"
                                        class="btn btn-default border btn-md copy_inventory d-sm-inline-block mb-2 mb-sm-0"
                                        data-toggle="modal" data-target="#m_select2_modal" disabled>
                                        <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="m13 3l3.293 3.293l-7 7l1.414 1.414l7-7L21 11V3z" />
                                                <path fill="currentColor"
                                                    d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z" />
                                            </svg></span> Copy to another Project
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table
                            class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless"
                            id="m_table_1">
                            <thead>
                                <tr>
                                    <th width="3%">#</th>
                                    <th width="5%" style="text-align: center">TYPE</th>
                                    <th width="30%">EQUIPMENT NAME</th>
                                    <th width="30%">DESCRIPTION</th>
                                    <th width="30%" class="d-none d-sm-table-cell">SPECIFICATION</th>
                                    {{-- <th width="5%" style="text-align: center">Copy equipment</th> --}}
                                    <th width="5%" style="text-align: center">ACTIONS</th>


                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($inventory_equipments as $row)
                                    <td align="center"><input type="checkbox" name="" class="devices_ids"
                                            value="{{ $row->device_id }}"></td>
                                    <td align="center">

                                        <a id="{{ $row->device_id }}"
                                            class="iconHolder mx-auto fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            equipment-device="{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">



                                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                viewBox="0 0 459.359 459.359"
                                                style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                                <g>
                                                    <path style="fill:#020202;"
                                                        d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362 c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912 c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z" />
                                                    <path style="fill:#020202;"
                                                        d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67 c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774 c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z" />
                                                    <path style="fill:#020202;"
                                                        d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93 C153.145,312.05,157.593,307.619,163.061,307.619z" />
                                                    <path style="fill:#020202;"
                                                        d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473 c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294 C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143 c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026 H23.075V236.214h65.787V361.026z" />
                                                    <path style="fill:#020202;"
                                                        d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814 c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8 c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058 c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03 c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8 c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814 c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064 c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738 c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372 l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113 h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366 l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801 c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926 C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337 c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z" />
                                                    <path style="fill:#020202;"
                                                        d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926 c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z" />
                                                </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                                <g> </g>
                                            </svg>

                                        </a>

                                    </td>

                                    <td class="fw-500"><a
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">{!! isset($row->name) && $row->name != '' ? $row->name : $row->device_id !!}</a>
                                    </td>

                                    <td class="d-none d-sm-table-cell"><a
                                            href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->device_id }}"
                                            style="color:#212529;text-decoration:none;display:block;">{!! isset($row->description) && $row->description != '' ? $row->description : '---' !!}</a>
                                    </td>

                                    <td>
                                        <a href="{{ url('equipment-details') }}/{{ $company_id }}/{{ $row->equipment_id }}"
                                            style="color:#212529;text-decoration:none;display:inline-block;">
                                            {!! isset($row->specification) && $row->specification != '' ? $row->specification : '---' !!}</a>
                                    </td>
                                    <td>
                                        <span class="miniIcon">
                                            <i data-url="{{ route('inventory.delete', ['id' => $row->id]) }}"
                                                data-id="{{ $row->id }}" class="deleteInventory la la-trash"></i>
                                        </span>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            @endif
        </div>
    </div>
</div>
    {{-- modal start --}}
    @if (isset($can_manage_users) && $can_manage_users > 0)
        <div class="modal fade" id="transfer_modal" tabindex="-1" role="dialog" aria-labelledby="transfer_modalLabel"
            aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Copy Folder</h5>
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


                        <form method="POST" action="{{ route('copyEquipment') }}">
                            @csrf
                            <input type="hidden" name="equipment_id" id="equipment_id">
                            <input type="hidden" name="selected_company_id" id="selected_company_id">
                            <div class="form-group">
                                @if ($user_id > 1)
                                    {{-- <div class="mb-4" id="custome-input">
                                        <input type="search" class="form-control" name="selected_company_name" id="transfer_sensor" placeholder="Start typing a project's name">

                                        <div id="search_suggestions"></div>
                                    </div> --}}


                                    {{-- <select name="transfer_company" id="transfer_company" class="form-control">
                                        <option value="">Select Project</option>

                                        @if (isset($companies) && count($companies) > 0)
                                            @foreach ($companies as $company2)
                                                @if ($company2->parent_id == 0)
                                                    <option
                                                        value="{{ isset($company2->company_id) ? $company2->company_id : '' }}">
                                                        {{ isset($company2->name) ? $company2->name : '' }} (Inventory
                                                        Account)
                                                    </option>
                                                @else
                                                    <option
                                                        value="{{ isset($company2->company_id) ? $company2->company_id : '' }}">
                                                        {{ isset($company2->name) ? $company2->name : '' }}</option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select> --}}
                                @else
                                    <select name="transfer_company" id="transfer_company" class="form-control">
                                        <option value="">Select Project</option>
                                        @php
                                            $crtComp = isset($currentCompany->company_id) ? $currentCompany->company_id : '';
                                        @endphp
                                        @if (isset($companies) && count($companies) > 0)
                                            @foreach ($companies as $company)
                                                @php
                                                    if ($crtComp == $company->company_id) {
                                                        continue;
                                                    }

                                                @endphp
                                                @if ($company->parent_id == 0)
                                                    <option
                                                        value="{{ isset($company->company_id) ? $company->company_id : '' }}">
                                                        {{ isset($company->name) ? $company->name : '' }}</option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select>
                                @endif
                                <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                            </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ">Copy equipment</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- modal end --}}
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


    <div class="modal fade" id="modal-delete-inventory" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete equipment inventory</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <p>Are you sure you want to delete this equipment inventory?</p>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-default mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-danger mx-1" value="Delete">
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
    <!--begin::Modal-->
    <div class="modal fade" id="inventory-modal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Add equipment inventory</h5>
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
                        function RandomString($length = 20)
                        {
                            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $string = '';
                            for ($i = 0; $i < $length; $i++) {
                                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
                            }
                            return $string;
                        }
                        $randStr = RandomString();
                    @endphp
                    @php
                        $compID = isset($currentCompany->company_id) ? $currentCompany->company_id : '-';
                    @endphp
                    <form method="post" action="{{ route('inventoryStore') }}" enctype="multipart/form-data">
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
                        <div class="mb-3">
                            <label>
                                Document Name
                            </label>
                            <input required type="text" class="form-control" name="sensor_doc_name" value="">
                        </div>
                        <div class="mb-3">
                            <label>
                                Upload
                            </label>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="sensor_doc" 
                                        style="width: 100%; height: auto;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-center justify-content-md-end">
                                <button type="button" class="btn btn-secondary mx-1"
                                    data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary mx-1">Save</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--end::Modal-->

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
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary mr-auto try_again  d-none">Try Again</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    {{-- +++++++++++++++++++++++++++++++++++++++++++++ --}}
    <!--begin::Modal-->
    @if (isset($can_manage_users) && $can_manage_users > 0)
        <div class="modal fade" id="m_select2_modal" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Copy Equipment</h5>
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
                                <input type="hidden" name="comp_ID" id="comp_ID"
                                    value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">
                                <input type="hidden" name="selected_company_id" id="selected_company_id"
                                    value="">

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
                                <input type="hidden" name="comp_ID" id="comp_ID"
                                    value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">
                                <input type="hidden" name="selected_company_id" id="selected_company_id"
                                    value="">

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
                            <button type="button" id="move_sensor_button2"
                                class="btn btn-primary move_sensor_button">Copy</button>
                        @else
                            <button type="button" id="move_sensor_button"
                                class="btn btn-primary move_sensor_button">Copy</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/jquery.timeago.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.search-devices-input').keypress(function(event) {
                if (event.which === 13) { // 13 corresponds to the "Enter" key
                    event.preventDefault(); // Prevent form submission through Enter key
                    $('#searchForm').submit();
                }
            });

            let device_ids = [];
            $('.devices_ids').on('change', function() {
                // alert($('#devices_ids:checked').length);
                if ($('.devices_ids:checked').length > 0) {
                    $('.copy_inventory').attr('disabled', false);
                } else {
                    $('.copy_inventory').attr('disabled', true);
                }
            });


            $('.copy_inventory').on('click', function() {
                device_ids = [];
                $('input[type=checkbox]:checked').each(function(index) {
                    let device_id = $(this).val();
                    device_ids.push(device_id);

                });
                console.log(device_ids);
            });

            $('.move_sensor_button').on('click', function() {
                let company_name = $('#transfer_sensor').val();
                let comp_ID = $('#comp_ID').val();
                let company_id = $('#m_select2_1').val();

                console.log(company_id);
                if (company_id == '') {
                    $('#company_select_error').removeClass('d-none');
                } else {
                    $('#company_select_error').addClass('d-none');
                    $.ajax({

                        url: '{{ route('copyEquipment') }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            device_ids: device_ids,
                            company_name: company_name,
                            comp_ID: comp_ID,
                            transfer_sensor: company_id,
                        },
                        success: function(data) {
                            if (data.success && data.success == true) {
                                //window.location.href = '{{ route('equipments', ['company_id' => $company_id]) }}';
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: data.message
                                    }).then(function() {
                                        window.location.href =
                                            '{{ route('equipments', ['company_id' => $company_id]) }}';
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message
                                    });
                                }
                                // window.location.href = '{{ route('equipments', ['company_id' => $company_id]) }}';
                            }

                            // $('#search-loader').hide();
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });


            $('.reset-filter').on('click', function() {
                window.location.href = '{{ url('equipments/' . $company_id) }}';
            });
            $(document).on('click', '.deleteEquipment', function() {
                var id = $(this).attr('data-id');
                console.log(id);
                $('#modal-delete-equipment').modal('show');
                $('#modal-delete-equipment form').append("<input type='hidden' name='eID' value='" +
                    id + "' />");
                $('#modal-delete-equipment form').attr('action', $(this).attr('data-url'));
            });
            $(document).on('click', '.deleteInventory', function() {
                var id = $(this).attr('data-id');
                console.log(id);
                $('#modal-delete-inventory').modal('show');
                $('#modal-delete-inventory form').append("<input type='hidden' name='eID' value='" +
                    id + "' />");
                $('#modal-delete-inventory form').attr('action', $(this).attr('data-url'));
            });
        })
    </script>
    <script>
        $(document).on('click', '.copy_equipment', function() {
            var folder_id = $(this).attr('equipmentid');
            console.log(folder_id);
            $('#equipment_id').val(folder_id);

        });

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
    </script>
    <script type="text/javascript">
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
                success: function(data) {
                    console.log($.inArray(deviceid, claim_devices_list));


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

            $('#search_suggestions').on('click', '.dropdown-item > a', function() {
                let companyName = $(this).text();
                let companyId = $(this).attr('company-id');
                // Set the selected company name as the value of the transfer_sensor input field
                $('#transfer_sensor').val(companyName);

                // Set the selected company ID as the value of the hidden input field
                $('#selected_company_id').val(companyId);
                $('#search_suggestions').css('display', 'none');
            });




            $('#clear_search').on('click', function() {
                $('#transfer_sensor').val('');
                $('#search_suggestions').css('display', 'none');

            });
        });
    </script>
@endpush
