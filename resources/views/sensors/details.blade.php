@extends('layouts.app')

@section('content')
    <style type="text/css">
        .img-style {
            flex-grow: 1;
            background-color: rgba(116, 147, 162, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 2px;
        }

        .modal-backdrop {
            display: none !important;
        }

        .sensor_icon_mini {
            position: absolute;
            left: 4px !important;
            top: 4px !important;
        }

        .btn-active {
            border-color: #ebedf2 !important;
            background-color: #f4f5f8 !important;
        }

        .filter-option {
            font-weight: bold;
        }

        @media screen and (max-width: 767px) {
            .claim_sensors {
                display: none;
            }

            .heading {
                display: none !important;
            }
        }
    </style>
    <style type="text/css">
        input {
            font-weight: 600;
            /* font-family: 'Open Sans', sans-serif; */
            color: #000000
        }

        .form-control {
            font-family: 'Open Sans';
            color: #000000;
        }
    </style>
    <style>
        .sensorTable td:nth-child(3) a {
            font-family: 'Open Sans';
            font-weight: 600;
        }

        .sensorTable td:nth-child(4) a {
            font-family: 'Open Sans';
            font-weight: 600;
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



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.css"
        integrity="sha512-7/BfnxW2AdsFxJpEdHdLPL7YofVQbCL4IVI4vsf9Th3k6/1pu4+bmvQWQljJwZENDsWePEP8gBkDKTsRzc5uVQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


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
                            <input type="hidden" name="sID" id="sIDS"
                                value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                            <input type="hidden" name="comp_ID" id="comp_ID"
                                value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">
                            <input type="hidden" name="transfer_sensor" id="selected_company_id" value="">

                            <div class="mb-4" id="custome-input">
                                {{-- <input type="search" name="" class="form-control" name="transfer_sensor1"
                                    id="transfer_sensor" placeholder="Start typing a project's name">
                                <div id="search_suggestions"></div> --}}
                                <select class="form-control m-select2" name="transfer_sensor" id="m_select2_1"
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
                            <input type="hidden" name="sID" id="sIDS"
                                value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">

                            <input type="hidden" name="comp_ID" id="comp_ID"
                                value="{{ isset($currentID[0]->id) ? $currentID[0]->id : '' }}">

                            <div class="mb-4" id="custome-input">
                                {{-- <input type="search" name="" class="form-control" name="transfer_sensor1"
                                    id="transfer_sensor" placeholder="Start typing a project's name">
                                <div id="search_suggestions"></div> --}}
                                <select class="form-control m-select2" name="transfer_sensor" id="m_select2_1"
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

    <!--begin::Modal-->
    <div class="modal fade" id="modal-device-docs" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <h5 class="modal-title" id="supportModalLabel">Resources</h5>

                    <button data-dismiss="modal" class="btn btn-primary" data-toggle="modal"
                        data-target="#modal-add-doc">Add Resource</button>

                    <button type="button" class="close closeSpecial" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="60%">Name</th>
                                <th width="20%">Date</th>
                                <th width="20%" class="text-right">Actions</th>
                            </tr>
                        </thead>
                        @php
                            $id_device = $sensor->id ?? '';
                            $docs_devices = \App\DeviceDocument::where('device_id', $id_device)->get();
                        @endphp
                        <tbody>
                            @foreach ($docs_devices as $docs_device)
                                <tr>
                                    <td>
                                        <span class="fw-600">{{ $docs_device->url }}</span>
                                    </td>
                                    <td>{{ $docs_device->created_at }}</td>
                                    <td class="text-right">
                                        <!-- Actions dropdown -->
                                        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                            m-dropdown-toggle="hover" aria-expanded="true">
                                            <a href="#"
                                                class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
                                                <i class="la la-plus m--hide"></i>
                                                <i class="la la-ellipsis-h"></i>
                                            </a>
                                            <div class="m-dropdown__wrapper">
                                                <span
                                                    class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                                <div class="m-dropdown__inner">
                                                    <div class="m-dropdown__body">
                                                        <div class="m-dropdown__content">
                                                            <ul class="m-nav">
                                                                <li class="m-nav__section m-nav__section--first m--hide">
                                                                    <span class="m-nav__section-text">Quick Actions</span>
                                                                </li>
                                                                <li class="m-nav__item mb-3">
                                                                    <a href="" class="m-nav__link">
                                                                        <i class="m-nav__link-icon la 	la-eye"></i>
                                                                        <span class="m-nav__link-text">View</span>
                                                                    </a>
                                                                </li>
                                                                <li class="m-nav__item">
                                                                    <a href="" class="m-nav__link">
                                                                        <i
                                                                            class="m-nav__link-icon la la-trash m--font-danger"></i>
                                                                        <span
                                                                            class="m-nav__link-text m--font-danger">Delete</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Actions dropdown ends -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->


    <!--begin::Modal-->
    <div class="modal fade" id="modal-add-doc" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocModalLabel">Upload file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('companies.uploadSensorDoc') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                        <div class="mb-3">
                            <label>
                                Name
                            </label>
                            <input required type="text" class="form-control" name="sensor_doc_name" value="">
                        </div>
                        <div class="mb-3">
                            <label>
                                Upload
                            </label>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" name="sensor_doc" required=""
                                        style="width: 100%; height: auto;">
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Upload">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->



    <div class="modal fade" id="chooseModals" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocModalLabel">Upload From</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <div style="text-align: center;">
                        <button data-dismiss="modal" class="btn btn-secondary mx-1" data-toggle="modal"
                            data-target="#modal-add-doc">Your device</button>
                        <button type="button" class="btn btn-secondary mx-1 chooseFile" data-toggle="modal"
                            data-target="#chooseFileModal">Document cloud</button>
                    </div>

                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-primary mx-1" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="chooseFileModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('companies.uploadSensorDoc2') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">

                        @csrf

                        <div id="folder-table">

                        </div>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="button" class="btn btn-primary mx-1" data-dismiss="modal" data-toggle="modal"
                                data-target="#upload-doc" value="Upload">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>






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
    <div class="m-grid__item m-grid__item--fluid m-wrapper">

        <!-- BEGIN: Subheader -->
        <div class="m-subheader ">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <h3 class="m-subheader__title heading">
                        Sensors
                    </h3>
                </div>
                {{-- <div class="col-lg-8 col-md-8">
				<div class="d-flex justify-content-end">
					<button data-toggle="modal" data-target="#m_modal_5" class="btn btn-primary claim_sensors shadow-sm" ><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M17 13h-4v4h-2v-4H7v-2h4V7h2v4h4m-5-9A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2Z"></path></svg> Claim new devices</button>
				</div>
			</div> --}}
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (\Session::has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success Message!</strong> {{ \Session::get('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

        </div>

        <!-- END: Subheader -->
        @if ($sensor != '')
            <div class="m-content">

                <!--Begin::Section-->

                <div class="details_wrap">
                    <div class="details_wrap_x">
                        <div class="m-portlet panel-has-radius mb-4 p-4 portlet-height-1">
                            <div class="mb-2">
                                <button type="button" style="width: 100%;" class="btn btn-primary mb-4"
                                    data-toggle="modal" data-target="#m_modal_2" id="identify_touch_sensor">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 2048 2048">
                                        <path fill="currentColor"
                                            d="M1600 896q40 0 75 15t61 41t41 61t15 75v384q0 119-45 224t-124 183t-183 123t-224 46q-144 0-268-55t-226-156l-472-472q-28-28-43-65t-15-76q0-42 16-78t43-64t63-42t78-16q82 0 141 59l107 106V853q-59-28-106-70t-80-95t-52-114t-18-126q0-93 35-174t96-143t142-96T832 0q93 0 174 35t143 96t96 142t35 175q0 93-37 178t-105 149q35 9 63 30t49 52q45-25 94-25q50 0 93 23t69 66q45-25 94-25zM512 448q0 75 34 143t94 113V448q0-40 15-75t41-61t61-41t75-15q40 0 75 15t61 41t41 61t15 75v256q60-45 94-113t34-143q0-66-25-124t-69-101t-102-69t-124-26q-66 0-124 25t-102 69t-69 102t-25 124zm1152 640q0-26-19-45t-45-19q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-37 0-50-23t-16-60t2-77t2-77t-15-59t-51-24q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-37 0-50-23t-16-60t2-77t2-77t-15-59t-51-24q-34 0-47 19t-16 47t-1 62t0 61t-16 48t-48 19q-26 0-45-19t-19-45V448q0-26-19-45t-45-19q-26 0-45 19t-19 45v787q0 23-8 42t-23 35t-35 23t-42 9q-22 0-42-8t-37-24l-139-139q-21-21-50-21t-50 21t-22 51q0 29 21 50l472 473q84 84 184 128t219 45q93 0 174-35t142-96t96-142t36-175v-384z" />
                                    </svg> Identify by touch
                                </button>
                                <div class="bg-light-grey-2 p-2 p-sm-3 my-1" style="border-radius: 8px;">
                                    <h4 class="m-0 p-0">Connected</h4>
                                </div>
                                <ul class="device_caption_list_minimal">
                                    @foreach ($connected as $row)
                                        @php
                                            $selected = '';
                                            if ($row->device_id == $sensor->device_id) {
                                                $selected = 'ribbon-left ribbon-danger';
                                            }
                                        @endphp
                                        <li id="{{ $row->device_id }}"
                                            class="{{ $selected }} @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif">
                                            <a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}">
                                                <figure id="sensor_{{ $row->device_id }}"
                                                    class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                                    @if ($row->event_type == 'temperature')
                                                        <span class="sensor_icon_mini"
                                                            style="background-color: green;"></span>
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
                                                </figure>
                                                <figcaption>
                                                    {{ isset($row->name) && $row->name != '' ? $row->name : $row->device_id }}
                                                </figcaption>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="bg-light-grey-2 p-2 p-sm-3 my-4" style="border-radius: 8px;">
                                    <h4 class="m-0 p-0">Not Connected</h4>
                                </div>

                                <ul class="device_caption_list_minimal">
                                    @foreach ($not_connected as $row)
                                        @php
                                            $selected = '';
                                            if ($row->device_id == $sensor->device_id) {
                                                $selected = 'ribbon-left ribbon-danger';
                                            }
                                        @endphp
                                        <li id="{{ $row->device_id }}"
                                            class="{{ $selected }} @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif">
                                            <a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}">
                                                <figure id="sensor_{{ $row->device_id }}"
                                                    class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                                    @if ($row->event_type == 'temperature')
                                                        <span class="sensor_icon_mini"
                                                            style="background-color: red;"></span>
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
                                                </figure>
                                                <figcaption>
                                                    {{ isset($row->name) && $row->name != '' ? $row->name : $row->device_id }}
                                                </figcaption>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="bg-light-grey-2 p-2 p-sm-3 my-4" style="border-radius: 8px;">
                                    <h4 class="m-0 p-0">Gateways</h4>
                                </div>

                                <ul class="device_caption_list_minimal">
                                    @foreach ($gateways as $row)
                                        @php
                                            $selected = '';
                                            if ($row->device_id == $sensor->device_id) {
                                                $selected = 'ribbon-left ribbon-danger';
                                            }
                                        @endphp
                                        <li id="{{ $row->device_id }}"
                                            class="{{ $selected }} @if (isset($row->is_active) && $row->is_active == 0) is_disabled @endif">
                                            <a
                                                href="{{ url('sensor-details') }}/{{ $company_id }}/{{ $row->device_id }}">
                                                <figure id="sensor_{{ $row->device_id }}"
                                                    class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
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
                                                </figure>
                                                <figcaption>
                                                    {{ isset($row->name) && $row->name != '' ? $row->name : $row->device_id }}
                                                </figcaption>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="details_wrap_y">
                        <div class="m-portlet panel-has-radius mb-4 p-4">
                            <div class="mb-2">
                                <p class="m-0">
                                    <a href="{{ url('sensors') }}/{{ $sensor->company_id }}" class="no-decoration">
                                        <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em"
                                            height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M21 11H6.414l5.293-5.293l-1.414-1.414L2.586 12l7.707 7.707l1.414-1.414L6.414 13H21z" />
                                        </svg>
                                        Back to full list
                                    </a>
                                </p>
                            </div>
                            <div class="bg-light-grey-2 p-2 p-sm-3 mb-4 rounded">
                                <div class="row d-flex align-items-center gutter-10">
                                    <div class="col-sm-8 mb-3 mb-sm-0 col-7">
                                        <div class="d-inline-flex align-items-center">
                                            <figure id="single_sensor_{{ $sensor->device_id }}"
                                                class="fig-60 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                                <span class="sensor_icon_mini" style="background-color: red;"></span>
                                                {{-- <span class="iconify" data-icon="carbon:temperature"></span> --}}
                                                @if ($sensor->event_type == 'temperature')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"
                                                        class="iconify" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em; transform: rotate(360deg);">
                                                        <path fill="currentColor"
                                                            d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z">
                                                        </path>
                                                    </svg>
                                                @elseif($sensor->event_type == 'ccon')
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                        focusable="false" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="-3 0 32 32"
                                                        class="iconify" data-icon="carbon:temperature"
                                                        style="vertical-align: -0.125em;transform: rotate(360deg);">
                                                        <path fill="#4A4A4A" fill-rule="evenodd"
                                                            d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </figure>
                                            <figcaption class="m-0">
                                                @if ($sensor->event_type == 'temperature')
                                                    <p class="m-0 fw-500 text-muted fs-12">
                                                        Temperature Sensor
                                                    </p>
                                                @elseif($sensor->event_type == 'ccon')
                                                    <p class="m-0 fw-500 text-muted fs-12">
                                                        Network Connection
                                                    </p>
                                                @endif
                                                @if ($sensor->event_type == 'temperature')
                                                    <p class="m-0 main_value_1">
                                                        @if (isset($sensor->is_active) && $sensor->is_active == 1)
                                                            {{ isset($sensor->temperature) ? @number_format($sensor->temperature, 2) : 0 }}°C
                                                        @else
                                                            Offline
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="m-0 fs-22 fw-600">
                                                        @if (isset($sensor->temperature) && $sensor->temperature != '')
                                                            {{ $sensor->temperature }}
                                                        @endif
                                                    </p>
                                                @endif
                                                <p class="m-0 fw-500 text-muted fs-12">
                                                    @if (isset($sensor->is_active) && $sensor->is_active == 1)
                                                        @if ($sensor->event_type == 'ccon')
                                                            Online
                                                        @elseif($sensor->event_type != 'equipment')
                                                            Today at
                                                            {{ date('H:i:s', strtotime($sensor->temeprature_last_updated)) }}
                                                        @endif
                                                    @else
                                                        @if ($sensor->event_type != 'temperature')
                                                            Offline
                                                        @else
                                                         @php
                                                $temperatureLastUpdated = Carbon\Carbon::parse($sensor->temeprature_last_updated);

                                                    // Now you can use the diffForHumans() method on the date/time object
                                                    $lastSeen = $temperatureLastUpdated->diffForHumans();
                                                @endphp
                                                            Last seen
                                                            {{ $lastSeen }}
                                                        @endif
                                                    @endif
                                                </p>
                                            </figcaption>
                                        </div>
                                    </div>
                                    @if ($sensor->event_type != 'equipment')
                                        <div class="col-sm-4 col-5">
                                            <div
                                                class="justify-content-between justify-content-sm-end d-flex align-items-center">





                                                <div class="d-flex justify-content-end flex-column align-items-end"
                                                    style="margin-left: auto;">
                                                    <div class="signal-indicator-icon-wrap">
                                                        @if (isset($sensor->is_active) && $sensor->is_active == 0 && $sensor->event_type != 'ccon')
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                aria-hidden="true" focusable="false" width="1em"
                                                                height="1em"
                                                                style="color: rgb(237, 28, 36); vertical-align: -0.125em; transform: rotate(360deg);"
                                                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                                                class="iconify signal-indicator-icon-small"
                                                                data-icon="akar-icons:circle-alert-fill">
                                                                <path fill="currentColor" fill-rule="evenodd"
                                                                    d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                        <ul class="signal-indicator-bar-list">
                                                            <?php
                                            $signal_div='';
                                            $active='active';
                                            if(isset($sensor->is_active) && $sensor->is_active==0){
                                                $active='';
                                            }
                                            if($sensor->event_type=='ccon'){
                                                if($sensor->is_active==1){
                                                ?><img
                                                                src="../../public/assets/img_cellular.svg" alt="">
                                                            {{ isset($sensor->temperature) ? $sensor->temperature : '' }}<?php
                                            }else{
                                                echo 'Offline';
                                            }

                                            }else{
                                                if($sensor->signal_strength<=20){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensor->signal_strength>20 && $sensor->signal_strength<=40) {
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensor->signal_strength>40 && $sensor->signal_strength<=60){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensor->signal_strength>60 && $sensor->signal_strength<=80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>';
                                                }elseif($sensor->signal_strength>80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>';
                                                }
                                            }
                                            ?>
                                                            {!! $signal_div !!}
                                                        </ul>
                                                    </div>
                                                    @if ($sensor->event_type == 'ccon' || $sensor->event_type == 'temperature')
                                                        <div class="mt-2 fs-13 fw-600 text-uppercase connectivity-collapse theme-blue nowrap"
                                                            data-toggle="collapse" href="#collapseExample" role="button"
                                                            aria-expanded="false" aria-controls="collapseExample"
                                                            style="cursor:pointer;">
                                                            Connectivity <svg xmlns="http://www.w3.org/2000/svg"
                                                                width="1em" height="1em"
                                                                preserveAspectRatio="xMidYMid meet"
                                                                viewBox="0 0 1024 1024">
                                                                <path fill="currentColor"
                                                                    d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if ($sensor->event_type == 'ccon' || $sensor->event_type == 'temperature')
                                    <div class="collapse" id="collapseExample">
                                        <div class="mt-4">

                                            @if ($sensor->event_type == 'temperature')
                                                <h5 class="mt-3 mb-3">
                                                    Seen by these Cloud Connectors
                                                </h5>
                                            @endif
                                            @if (isset($connectors) && count($connectors) > 0)
                                                @foreach ($connectors as $connector)
                                                    <div class="mb-4 d-flex align-items-center connID"
                                                        id="connID2-{{ isset($connector->device_id) ? $connector->device_id : '' }}">

                                                        <div class="mr-3 d-inline-flex">
                                                            <img src="{{ asset('public/assets/demo/default/media/img/misc/img_tiny_sensor.svg') }}"
                                                                alt="Icon" class="img-fluid mr-2">
                                                            <div class="signal-indicator-icon-wrap">
                                                                <ul class="signal-indicator-bar-list">
                                                                    @php
                                                                        $active = 'active';
                                                                        if (isset($connector->is_active) && $connector->is_active == 0) {
                                                                            $active = '';
                                                                        }
                                                                        $signal_div = '';
                                                                        if ($connector->signal_strength <= 20) {
                                                                            $signal_div =
                                                                                '<li class="' .
                                                                                $active .
                                                                                '"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                                        } elseif ($connector->signal_strength > 20 && $connector->signal_strength <= 40) {
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
                                                                        } elseif ($connector->signal_strength > 40 && $connector->signal_strength <= 60) {
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
                                                                        } elseif ($connector->signal_strength > 60 && $connector->signal_strength <= 80) {
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
                                                                        } elseif ($connector->signal_strength > 80) {
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
                                                                        }
                                                                    @endphp
                                                                    {!! $signal_div !!}
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        <div class="d-inline-flex align-items-center">
                                                            <figure class="m-0 mr-2">
                                                                <img src="{{ asset('public/assets/demo/default/media/img/misc/img_ccon.svg') }}"
                                                                    alt="Icon" class="img-fluid">
                                                            </figure>
                                                            <div>
                                                                <div class="">
                                                                    <a href="{{ url('sensor-details/' . $company_id . '/' . $connector->device_id) }}"
                                                                        class="no-decoration">{{ $connector->name ?? '' }}</a>
                                                                    {{-- <span class="iconify ml-2" data-icon="zondicons:travel-case"></span> --}}
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <span id="p_{{ $connector->device_id ?? '' }}"
                                                                        class="copyable mr-2">
                                                                        {{ $connector->device_id ?? '' }}
                                                                    </span>
                                                                    <button
                                                                        onclick="copyToClipboard('#p_{{ $connector->device_id ?? '' }}')"
                                                                        type="button" class="btn btn-sm btn-default">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="1em" height="1em"
                                                                            preserveAspectRatio="xMidYMid meet"
                                                                            viewBox="0 0 32 32">
                                                                            <path fill="currentColor"
                                                                                d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z" />
                                                                            <path fill="currentColor"
                                                                                d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z" />
                                                                        </svg> Copy
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <h5 class="mt-3 mb-3">
                                                Historical connectivity
                                            </h5>
                                            <p id="toolTipValueCcon"></p>
                                            @php
                                                $colors = ['#2B4B5D', '#74A7C6', '#8B635C', '#DB624D'];
                                                $lp = 0;
                                                $key = 0;
                                            @endphp
                                            @if (isset($available_ccon) && count($available_ccon) > 0)
                                                @foreach ($available_ccon as $connector)
                                                    @if (isset($key) && $key >= 4)
                                                        @php
                                                            $lp = 0;
                                                        @endphp
                                                    @endif
                                                    @php
                                                        $uniqueName = $colors[$lp];
                                                        $uniqueName = str_replace('#', '', $uniqueName);
                                                        /*if(isset($connector->is_active) && $connector->is_active==0){
                                        continue;
                                    }*/
                                                    @endphp
                                                    <div class="mb-2 d-flex align-items-center connID"
                                                        style="display: none;" id="connID-{{ $connector }}">
                                                        @php
                                                            $device = \App\Device::where('device_id', $connector)->first();
                                                        @endphp
                                                        <span class="signal-dot mr-2"
                                                            style="background-color: {{ $colors[$lp] }};"></span>
                                                        <a href="#"
                                                            class="no-decoration">{{ isset($device) ? $device->name : $connector }}</a>
                                                        {{-- <span class="iconify ml-2" data-icon="zondicons:travel-case"></span> --}}

                                                        <span class="fs-16 fw-600 mx-3 g-value"
                                                            id="signal-{{ $uniqueName }}">--</span>
                                                        <div class="signal-indicator-icon-wrap indicator-small">
                                                            <ul class="signal-indicator-bar-list"
                                                                id="signal-indicator-bar-list-{{ $uniqueName }}">
                                                                <li class="active"></li>
                                                                <li class="active"></li>
                                                                <li class="active"></li>
                                                                <li class="active"></li>
                                                                <li class=""></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $lp++;
                                                        $key++;
                                                    @endphp
                                                @endforeach
                                            @endif


                                            <div class="my-3" id="master-container-connector">
                                                <div class="isloading" style="position:relative;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                            d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                                            opacity=".5" />
                                                        <path fill="currentColor"
                                                            d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                                            <animateTransform attributeName="transform" dur="1s"
                                                                from="0 12 12" repeatCount="indefinite" to="360 12 12"
                                                                type="rotate" />
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <div class="btn-group bg-light-grey p-2 border-radius-1 d-flex align-items-center flex-wrap"
                                                    role="group" aria-label="Basic example">
                                                    <label class="mb-0 mr-3 ml-2 fw-400">
                                                        Zoom range
                                                    </label>
                                                    <button type="button" class="btn btn-default btn-sm range-btn-ccon"
                                                        data-val="hour">Hour</button>
                                                    <button type="button" class="btn btn-default btn-sm range-btn-ccon"
                                                        data-val="day">Day</button>
                                                    <button type="button"
                                                        class="btn btn-default btn-sm range-btn-ccon radio-active"
                                                        data-val="week">Week</button>
                                                    <button type="button" class="btn btn-default btn-sm range-btn-ccon"
                                                        data-val="month">Month</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($sensor->event_type == 'ccon')
                                <div class="row">
                                    <div class="col-md-6">
                            @endif

                            <div class="mb-3">
                                <div class="mb-3">
                                    @if ($sensor->event_type === 'temperature')
                                        <label>
                                            Device Name
                                        </label>
                                    @elseif($sensor->event_type === 'ccon')
                                        <label>
                                            Cloud Connector Name
                                        </label>
                                    @endif
                                    <input type="text" name="sensor_name" class="form-control"
                                        value="{{ $sensor->name }}" id="sensor_name">
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Description
                                    </label>
                                    <input type="text" name="sensor_description" class="form-control"
                                        value="{{ $sensor->description }}" id="sensor_description">
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Specification
                                    </label>
                                    <input type="text" name="sensor_specification" class="form-control"
                                        value="{{ $sensor->specification ?? '' }}" id="sensor_specification">
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        @if ($sensor->event_type == 'temperature')
                                            <label>
                                                Sensor ID
                                            </label>
                                        @elseif($sensor->event_type == 'ccon')
                                            <label>
                                                Cloud Connector ID
                                            </label>
                                        @endif
                                        <div class="d-flex align-items-center">
                                           <span id="p1" class="copyable mr-2">
                                            {{$sensor->device_id??''}}
                                        </span>
                                        <button onclick="copyToClipboard(this)" type="button" class="btn btn-sm btn-default copy-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"></path><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"></path></svg> Copy
                                        </button>
                                        </div>
                                    </div>
                                    @if ($sensor->event_type == 'temperature')
                                        <div class="col-sm-6 mb-3">
                                            <label>
                                                Battery level – Updated
                                                {{ isset($battery_updated_datetime) ? $battery_updated_datetime : '' }}
                                            </label>
                                            <div class="d-flex align-items-center">
                                                <figure class="fig-30 mb-0 mr-3">
                                                    <span class="fs-24">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                            height="1em" preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 24 24">
                                                            <g fill="none" stroke="currentColor"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2">
                                                                <rect width="18" height="12" x="2"
                                                                    y="6" rx="2" />
                                                                <path
                                                                    d="M7 10v4m4-4v4m4-4v4m5-4h1.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H20v-4Z" />
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </figure>
                                                <span class="fw-500">{{ $sensor->battery_level }}%</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">

                                        @if ($sensor->event_type != 'ccon')
                                            @if (!isset($equipment) && $equipment == null)
                                                <div class="mb-2">
                                                    <button type="button" class="btn btn-default" data-toggle="modal"
                                                        data-target="#m_select2_modal" style=" height: 44px;">
                                                        <span class="mr-2"><svg xmlns="http://www.w3.org/2000/svg"
                                                                width="1em" height="1em"
                                                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                                                <path fill="currentColor"
                                                                    d="m13 3l3.293 3.293l-7 7l1.414 1.414l7-7L21 11V3z" />
                                                                <path fill="currentColor"
                                                                    d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z" />
                                                            </svg></span> Move to another Project
                                                    </button>
                                                </div>
                                            @endif
                                            @if (\Auth::user()->id == 1 || (isset($role2) && $role2 == 'valid'))
                                                <div class="mb-2">
                                                    @if (!isset($equipment) && $equipment == null)
                                                        <button style="width: 204px;" data-dismiss="modal"
                                                            id="connection" class="btn btn-primary" data-toggle="modal"
                                                            data-target="#connectionModal">Connect</button>
                                                    @else
                                                        <button data-dismiss="modal" id="connection"
                                                            class="btn btn-default" data-toggle="modal"
                                                            data-target="#connectionCloseModal"
                                                            style="width: 204px;">Disconnect</button>
                                                    @endif
                                                </div>
                                            @endif
                                    </div>

                                </div>
                                <div id="load_message"></div>

                                @if (\Session::has('messageUpload'))
                                    <div class="alert alert-success alert-dismissible"><a href="#" class="close"
                                            data-dismiss="alert" aria-label="close"></a>
                                        <strong>Success!</strong> {{ \Session::get('messageUpload') }}
                                    </div>
                                @endif

                            </div>
                        </div>
        @endif
        @if ($sensor->event_type == 'ccon')
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif
    @if ($sensor != '' && $sensor->event_type != 'equipment' && $sensor->event_type != 'ccon')
        <div class="m-portlet panel-has-radius mb-4 p-4">
            <div class="row">
                <div class="col-lg-8 mb-2 mb-lg-0">
                    <h4>
                        Historical Data
                    </h4>
                </div>
            </div>
            @if ($sensor->device_id != 'c3mj37j8crq000984bf0')
                <p id="toolTipValue"></p>
                <div class="my-3" id="master-container">
                    <div class="isloading" style="position:relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                            </path>
                        </svg>
                    </div>
                </div>
            @endif
            @if ($sensor->event_type == 'temperature' && $sensor->device_id == 'c3mj37j8crq000984bf0')
                <p id="toolTipValue2"></p>
                <div class="my-3" id="Gen2-container">
                    <div class="isloading" style="position:relative;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z"
                                opacity=".5" />
                            <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z">
                                <animateTransform attributeName="transform" dur="1s" from="0 12 12"
                                    repeatCount="indefinite" to="360 12 12" type="rotate" />
                            </path>
                        </svg>
                    </div>
                </div>
            @endif
            <div class="d-flex justify-content-end">
                <div class="btn-group bg-light-grey p-2 border-radius-1 d-flex align-items-center flex-wrap"
                    role="group" aria-label="Basic example">
                    <label class="mb-0 mr-3 ml-2 fw-400">
                        Zoom range
                    </label>
                    <button type="button" class="btn btn-default btn-sm range-btn" data-val="hour">Hour</button>
                    <button type="button" class="btn btn-default btn-sm range-btn" data-val="day">Day</button>
                    <button type="button" class="btn btn-default btn-sm range-btn radio-active"
                        data-val="week">Week</button>
                    <button type="button" class="btn btn-default btn-sm range-btn" data-val="month">Month</button>
                </div>
            </div>

        </div>
        <!--Ends Historical Data-->
    @endif
    </div>
    </div>



    <div class="modal fade" id="connectionCloseModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Disconnect Sensor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('sensor.disconnect') }}" enctype="multipart/form-data">
                        <input type="hidden" name="eID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">

                        @csrf
                        <div>
                            <span>Are you sure you want to disconnect the sensor?</span>
                        </div>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-danger mx-1" value="disconnect">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="connectionModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Equipment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('sensor.connection') }}" enctype="multipart/form-data">
                        <input type="hidden" name="eID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">

                        @csrf

                        <div id="connect-sensor-table">

                        </div>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Connect">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="ImageModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="mediaContainer"></div>
                </div>
                <div class="modal-footer">
                    <a id="downloadButton" class="btn btn-primary" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <style type="text/css">
        .scannerBody {
            position: relative;
        }

        .scannerBody #camera-select2 {
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

        .scannerBody #camera-select2:focus,
        .scannerBody #camera-select2:focus-visible {
            outline: none;
        }
    </style>

    <div class="modal fade" id="m_modal_5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <form action="{{ url('claimSensor') }}" method="post">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company_id }}">
                        <div class="bg-light rounded-top border-bottom p-4 p-lg-5 q_r_scanner_header ">
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

                        <div id="video-container" class="qrscanner claim_qr_scanner d-none position-relative scannerBody">
                            <select id="camera-select2"></select>
                            <video id="qr-video" style="width: 100% !important; "></video>
                            <button type="button" class="btn btn-primary close_scanner mt-2 ml-2 mb-2">Close
                                Scanner</button>
                        </div>


                        <div class="p-0">

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




                                    </tbody>
                                </table>
                            </div>

                        </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-secondary mr-auto" class="close" data-dismiss="modal"
                        aria-label="Close">Cancel</button>
                    <button type="submit" class="btn btn-secondary claimSubmitBtn" disabled><svg
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

    <!--begin2::Modal-->
    <div class="modal fade" id="modal-add-doc2" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocModalLabel2">Add Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('companies.uploadSensorNotes') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                        <input type="hidden" name="company_id"
                            value="{{ isset($sensor->company_id) ? $sensor->company_id : '' }}">
                        <div class="mb-3">
                            <label>
                                Name
                            </label>
                            <input required type="text" class="form-control" name="name" value="">
                        </div>
                        <div class="mb-3">
                            <label>
                                Write Notes
                            </label>

                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <textarea class="form-control" name="notes" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Save">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal2-->

    <div class="modal fade" id="modal-edit-note" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    @if (\Auth::user()->id == 1)
                        <h5 class="modal-title" id="addDocModalLabel2">Update Notes</h5>
                    @endif
                    @if (\Auth::user()->id != 1)
                        <h5 class="modal-title" id="addDocModalLabel2">Notes</h5>
                    @endif
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('companies.editNote') }}" enctype="multipart/form-data">
                        @csrf
                        <div id="modal-edit">

                        </div>
                        {{-- @if (\Auth::user()->id == 1) --}}
                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Update">
                        </div>
                        {{-- @endif --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal3-->



    <!--begin::Modal-->
    <div class="modal fade" id="modal-delete-note" tabindex="-1" role="dialog"
        aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteNoteModalLabel">Delete Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('companies.deleteNote') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sID"
                            value="{{ isset($sensor->device_id) ? $sensor->device_id : '' }}">
                        <p>Are you sure you want to delete this note?</p>

                        <div class="d-flex justify-content-center justify-content-md-end">
                            <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-primary mx-1" value="Delete">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal-->


    <style type="text/css">
        .miniIcon {
            width: 30px;
            height: 30px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 30px;
            font-size: 1rem;
            transition: all .2s ease;
        }

        .miniIcon:hover {
            box-shadow: 2px 2px 4px rgba(0, 0, 0, .1);
            background-color: #207da9;
            color: #fff;
        }
    </style>

    <!-- <div class="m-portlet panel-has-radius mb-4 p-3">
                                    <h4>
                                        Add new label <span class="iconify" data-icon="eva:question-mark-circle-outline"></span>
                                    </h4>

                                    <div class="row mb-4">
                                        <div class="col-lg-5 mb-2">
                                            <input type="text" name="" class="form-control" placeholder="Key">
                                        </div>
                                        <div class="col-lg-5 mb-2">
                                            <input type="text" name="" class="form-control" placeholder="Value">
                                        </div>
                                        <div class="col-lg-2 mb-2">
                                            <button type="button" class="btn btn-block btn-secondary">Add Label</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table has-valign-middle">
                                            <thead>
                                                <tr>
                                                    <th width="45%">
                                                        Key
                                                    </th>
                                                    <th width="45%">
                                                        Value
                                                    </th>
                                                    <th width="10%" align="center">
                                                        Remove
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                
                                                $labels_json = isset($sensor->labels_json) ? $sensor->labels_json : '';
                                                if ($labels_json != '') {
                                                    $jsonDecode = json_decode($labels_json, true);
                                                }
                                                ?>
                                                @if (isset($jsonDecode['Email']))
    <tr>
                                                    <td>
                                                        Email
                                                    </td>
                                                    <td>
                                                        <input type="text" name="email" class="form-control" value="{{ $jsonDecode['Email'] }}">
                                                    </td>
                                                    <td align="center">
                                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                                    </td>
                                                </tr>
    @endif
                                                @if (isset($jsonDecode['MaxTempTrigger']))
    <tr>
                                                    <td>
                                                        MaxTempTrigger
                                                    </td>
                                                    <td>
                                                        <input type="text" name="email" class="form-control" value="{{ $jsonDecode['MaxTempTrigger'] }}">
                                                    </td>
                                                    <td align="center">
                                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                                    </td>
                                                </tr>
    @endif
                                                @if (isset($jsonDecode['MinTempTrigger']))
    <tr>
                                                    <td>
                                                        MinTempTrigger
                                                    </td>
                                                    <td>
                                                        <input type="text" name="email" class="form-control" value="{{ $jsonDecode['MinTempTrigger'] }}">
                                                    </td>
                                                    <td align="center">
                                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                                    </td>
                                                </tr>
    @endif
                                                @if (isset($jsonDecode['Notify']))
    <tr>
                                                    <td>
                                                        Notify
                                                    </td>
                                                    <td>
                                                        <input type="text" name="email" class="form-control" value="{{ $jsonDecode['Notify'] }}">
                                                    </td>
                                                    <td align="center">
                                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                                    </td>
                                                </tr>
    @endif
                                                @if (isset($jsonDecode['kit']))
    <tr>
                                                    <td>
                                                        kit
                                                    </td>
                                                    <td>
                                                        <input type="text" name="email" class="form-control" value="{{ $jsonDecode['kit'] }}">
                                                    </td>
                                                    <td align="center">
                                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                                    </td>
                                                </tr>
    @endif
                                            </tbody>
                                        </table>
                                    </div>

                                </div> -->
    </div>
    </div>

    <!--End::Section-->

    </div>
    </div>


@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('public/crud/forms/widgets/bootstrap-select.js') }}"></script>

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
        $('#m_select2_1').change(function() {
                var selectedCompanyId = $(this).val();
                $('#selected_company_id').val(selectedCompanyId);
            });
    </script>
    <script type="text/javascript">
        // let isSearching=false;
        //const modal = document.getElementById('ImageModal');
        const mediaContainer = document.getElementById('mediaContainer');

        function openModal(url, type) {
            var mediaContainer = document.getElementById('mediaContainer');
            mediaContainer.innerHTML = ''; // Clear any existing content

            var downloadButton = document.getElementById('downloadButton');
            downloadButton.setAttribute('href', url); // Set the download link to the provided URL

            if (type === 'pdf') {
                var pdfViewer = document.createElement('iframe');
                pdfViewer.src = url;
                pdfViewer.style.width = '100%';
                pdfViewer.style.height = '60vh';
                pdfViewer.style.border = 'none';

                mediaContainer.appendChild(pdfViewer);
                downloadButton.style.display = 'block'; // Show the download button for PDFs
            } else if (type === 'image') {
                var imageElement = document.createElement('img');
                imageElement.src = url;
                imageElement.style.maxWidth = '100%';
                imageElement.style.maxHeight = '100%';

                mediaContainer.appendChild(imageElement);
                downloadButton.style.display = 'block'; // Hide the download button for images
            }

            $('#ImageModal').modal('show');
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on('click', '#move_sensor_button', function() {
            $.ajax({

                url: '{{ route('companies.moveSensor') }}',
                type: 'POST',
                dataType: 'JSON',
                data: $('#move_sensor_form').serialize(),
                beforeSend: function() {
                    $('#search-loader').show();
                },
                success: function(data) {
                    if (data.success && data.success == true) {
                        window.location.href = '{{ route('sensors', ['company_id' => $company_id]) }}';
                        // window.location.href = data.url;
                    }
                    /*$('#loadCompaniesList').html('');
                    $('#loadCompaniesList').append(data);*/
                    $('#search-loader').hide();
                }
            });
        });

        $(document).on('click', '#move_sensor_button2', function() {
            $.ajax({

                url: '{{ route('companies.moveSensor2') }}',
                type: 'POST',
                dataType: 'JSON',
                data: $('#move_sensor_form2').serialize(),
                beforeSend: function() {
                    $('#search-loader').show();
                },
                success: function(data) {
                    if (data.success && data.success == true) {
                        window.location.href = '{{ route('sensors', ['company_id' => $company_id]) }}';
                        // window.location.href = data.url;
                    }
                    /*$('#loadCompaniesList').html('');
                    $('#loadCompaniesList').append(data);*/
                    $('#search-loader').hide();
                }
            });
        });

        var device_id = "{{ $sensor->device_id ?? '' }}";
        var company_id = "{{ $parent_company ?? '' }}";
        $('.range-btn-ccon').on('click', function() {
            $('.range-btn-ccon').removeClass('radio-active');
            $(this).addClass('radio-active');
            var val = $(this).attr('data-val');
            loadGraph(val);
        });
        loadGraph('week');
        // Initiate the Pusher JS library
        var pusher = new Pusher('ece81d906376bc8c0bab', {
            cluster: 'ap2',
            encrypted: true
        });


        // var company_id = '{{ $parent_company ?? '' }}';
        var current_company = '{{ $currentCompany->company_id ?? '' }}';
        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('my-channel.' + device_id);

        // Bind a function to a Event (the full Laravel class)
        channel.bind('App\\Events\\HelloPusherEvent', function(data) {
            if (data.data) {
                loadGraph('week');
                console.log('Pusher = ', data.data);
            }
        });

        var channel2 = pusher.subscribe('my-channel-project.' + current_company);

        // Bind a function to a Event (the full Laravel class)
        channel2.bind('App\\Events\\TouchEvent', function(data) {
            console.log(data);
            if (data.data && data.data.event_type && data.data.event_type == 'touch') {
                console.log(data.data.deviceId);
                console.log('inside ', $('#sensor_' + data.data.deviceId));
                $('#sensor_' + data.data.deviceId).addClass('pulse-button');
                $('#single_sensor_' + data.data.deviceId).addClass('pulse-button');
                setTimeout(function() {
                    $('#sensor_' + data.data.deviceId).removeClass('pulse-button');
                    $('#single_sensor_' + data.data.deviceId).removeClass('pulse-button');
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

        function loadGraph(val) {
            if (val != '') {
                val = '/' + val;
            }
            @php
                $dataURL = url('history-details-ccon2');
                if ($sensor != '') {
                    if ($sensor->event_type == 'ccon') {
                        $dataURL = url('history-details-ccon2');
                    } else {
                        $dataURL = url('history-details-ccon2');
                    }
                }
                
            @endphp
            Highcharts.setOptions({
                time: {
                    timezone: 'Europe/Oslo'
                }
            });
            Highcharts.getJSON('{{ $dataURL }}' + '/' + device_id + val, function(data) {
                $('.connID').attr('style', 'display:none !important');

                if (data && data.availed) {

                    $.each(data.availed, function(index, el) {
                        console.log('data exists: ' + el);

                        $('#connID-' + el).removeAttr('style');
                        $('#connID2-' + el).removeAttr('style');

                    });
                }
                if (data && data.availedColors) {
                    $.each(data.availedColors, function(index, el) {
                        $('#connID-' + el.id).find('span.signal-dot').css('background-color', el.color);
                        // $('#connID2-'+el).removeAttr('style');
                        console.log('index', index);
                        console.log('el', el);
                    });
                }
                // Create the chart
                const chart = Highcharts.stockChart('master-container-connector', {

                    chart: {
                        height: 220,
                        zoomType: 'x',
                        events: {
                            load() {
                                let chart = this;

                                chart.yAxis[0].update({
                                    min: 0,
                                    max: 100,
                                    tickInterval: 25,
                                })
                            }
                        }
                    },

                    title: {
                        text: ''
                    },

                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            format: '{value:%b %e}'
                        },
                        minRange: 3600 * 1000 // one hour
                    },

                    yAxis: {
                        opposite: false,
                        allowDecimals: false,
                        showLastLabel: true,
                        labels: {
                            formatter: function() {
                                return this.value + '%';
                            }
                        }
                    },
                    rangeSelector: {
                        enabled: false
                    },
                    series: data.data,
                    // series: [{
                    //     color: '#3e4a4f',
                    //     lineColor : '#3e4a4f',
                    //     name: 'Temperature',
                    //     data: data,
                    //     // type: 'spline',
                    //     // step: true,
                    //     /*tooltip: {
                    //         valueDecimals: 1,
                    //         valueSuffix: '°C'
                    //     }*/
                    // }],
                    /*tooltip:{
                        formatter : function(){
                            var dateVl = Highcharts.dateFormat('%A, %b %e, %l:%M', this.x);
                            var html = this.y.toFixed(2)+'°C on '+dateVl;
                            $("#toolTipValue").html(html);
                            return false;
                        }
                    },*/
                    tooltip: {
                        formatter: function(abc) {
                            // var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);

                            var originalTimestamp = this
                                .x; // Assuming this.x contains the original timestamp

                            // Create a new Date object based on the original timestamp
                            var originalDate = new Date(originalTimestamp);

                            // Add one hour to the original date
                            var newDate = new Date(originalDate.getTime() + 1 * 60 * 60 *
                                1000); // Add 1 hour (1 * 60 * 60 * 1000 milliseconds)

                            // Format the new date as a string using Highcharts.dateFormat
                            var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', newDate
                                .getTime());

                            console.log(dateVl);
                            var html = dateVl;
                            $("#signal_strength").html(parseInt(this.y) + '%');
                            // var html = this.y.toFixed(0)+'% on '+dateVl;
                            var html = dateVl;
                            $("#toolTipValueCcon").html(html);
                            var s = '';
                            $("#signal-2B4B5D,#signal-74A7C6,#signal-8B635C,#signal-DB624D").html(
                                '----');

                            this.points.forEach((el, index) => {
                                console.log('el', el);
                                // console.log('el_x',el.plotX);
                                // console.log('el_y',el.plotY);
                                var uniqueName = el.color.replace('#', '');
                                var signal = get_signal(el.y.toFixed(0));
                                $("#signal-" + uniqueName).html(el.y.toFixed(0) + '%');
                                $('#signal-indicator-bar-list-' + uniqueName).html(signal);
                            });
                            return s;
                        }
                    },

                    credits: {
                        enabled: false
                    },

                    exporting: {
                        enabled: false
                    },
                    navigator: {
                        enabled: false
                    },
                    scrollbar: {
                        enabled: false
                    },
                });
            });
        }

        function get_signal(signal) {
            var signal_div = '';
            var active = 'active';
            if (signal <= 20) {
                signal_div = '<li class="' + active + '"></li>\
                                           <li class=""></li>\
                                           <li class=""></li>\
                                           <li class=""></li>\
                                           <li class=""></li>';
            } else if (signal > 20 && signal <= 40) {
                signal_div = '<li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class=""></li>\
                                           <li class=""></li>\
                                           <li class=""></li>';
            } else if (signal > 40 && signal <= 60) {
                signal_div = '<li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class=""></li>\
                                           <li class=""></li>';
            } else if (signal > 60 && signal <= 80) {
                signal_div = '<li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class=""></li>';
            } else if (signal > 80) {
                signal_div = '<li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>\
                                           <li class="' + active + '"></li>';
            } else {
                signal_div = '';
            }

            return signal_div;
        }
        var sensor_name = $('#sensor_name').val();
        var sensor_description = $('#sensor_description').val();
        var sensor_specification = $('#sensor_specification').val();
        $('#sensor_name').blur(function() {
            var company_id = "{{ $sensor->company_id ?? '' }}"
            var device_id = "{{ $sensor->device_id ?? '' }}";
            var name = $('#sensor_name').val();
            var description = $('#sensor_description').val();
            if (sensor_name != name) {

                $.ajax({
                    url: '{{ url('updateSensorDetails') }}',
                    method: 'post',
                    data: {
                        device_id: device_id,
                        name: name,
                        company_id: company_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $("#load_message").show();
                        $('#load_message').html(
                            '<div class="alert alert-success">Updated successfully</div>');

                        setTimeout(function() {
                            $("#load_message").hide('blind', {}, 500)
                        }, 2000);
                        if (name == '') {
                            $('#' + device_id).find('figcaption').text(device_id);
                        } else {
                            $('#' + device_id).find('figcaption').text(name);
                        }
                        $('.m_selectpicker').selectpicker('destroy');
                        var device = $('#chooseFileInput option:selected').text(name);
                        $('.m_selectpicker').selectpicker('show');

                    }
                });

            }
        });
        $('#sensor_description').blur(function() {
            var company_id = "{{ $sensor->company_id ?? '' }}"
            var device_id = "{{ $sensor->device_id ?? '' }}";
            var description = $('#sensor_description').val();
            if (sensor_description != description) {
                $.ajax({
                    url: '{{ url('updateSensorDetails') }}',
                    method: 'post',
                    data: {
                        device_id: device_id,
                        description: description,
                        company_id: company_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $("#load_message").show();
                        $('#load_message').html(
                            '<div class="alert alert-success">Updated successfully</div>');

                        setTimeout(function() {
                            $("#load_message").hide('blind', {}, 500)
                        }, 2000);

                        $('.m_selectpicker').selectpicker('destroy');
                        var device = $('#chooseFileInput option:selected').text(name);
                        $('.m_selectpicker').selectpicker('show');

                    }
                });
            }
        });

        $('#sensor_specification').blur(function() {
            var company_id = "{{ $sensor->company_id ?? '' }}";
            var device_id = "{{ $sensor->device_id ?? '' }}";
            var specification = $('#sensor_specification').val();
            if (sensor_specification != specification) {

                $.ajax({
                    url: '{{ url('updateSensorDetails') }}',
                    method: 'post',
                    data: {
                        device_id: device_id,
                        specification: specification,
                        company_id: company_id,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $("#load_message").show();
                        $('#load_message').html(
                            '<div class="alert alert-success">Updated successfully</div>');

                        setTimeout(function() {
                            $("#load_message").hide('blind', {}, 500)
                        }, 2000);

                        $('.m_selectpicker').selectpicker('destroy');
                        var device = $('#chooseFileInput option:selected').text(name);
                        $('.m_selectpicker').selectpicker('show');

                    }
                });
            }
        });


        $(document).on('click', '.editnote', function(e) {
            console.log(e);
            var id = $(this).attr('data-id');
            e.preventDefault();

            $.ajax({
                type: 'get',
                data: {
                    id: id,

                },
                url: "{{ route('companies.viewNoteValue') }}",
                success: function(data) {

                    $('#modal-edit').html(data);

                }
            });


            $('#modal-edit-note').modal('show');
        });

        $(document).on('click', '.deleteDoc', function(e) {
            var id = $(this).attr('data-id');
            $('#modal-delete-doc').modal('show');
            $('#modal-delete-doc form').attr('action', $(this).attr('data-url'));
        });

        $(document).on('click', '.deletenote', function(e) {
            var id = $(this).attr('data-id');
            $('#modal-delete-note').modal('show');
            $('#modal-delete-note form').attr('action', $(this).attr('data-url'));
        });
        $(document).on('click', '#modal-edit-note', function(e) {
            console.log(e);
            var id = $(this).attr('data-id');
        });
        $(document).on('click', '#identify_touch_sensor', function() {
            $('.touch_header').removeClass('d-none');
            $('.sensor_touch_info').addClass('d-none');
            $('.try_again').addClass('d-none');
            // isSearching=true;
            setTimeout(function() {
                $('.sensor_touch_info').removeClass('d-none');
                $('.touch_header').addClass('d-none');
                $('.try_again').removeClass('d-none');
                // isSearching=false;
            }, 15000);
        });

        $(document).on('click', '.try_again', function() {
            $('.sensor_touch_info').addClass('d-none');
            $('.try_again').addClass('d-none');
            $('.touch_header').removeClass('d-none');
            // isSearching=true;
            setTimeout(function() {
                $('.sensor_touch_info').removeClass('d-none');
                $('.touch_header').addClass('d-none');
                $('.try_again').removeClass('d-none');
                // isSearching=false;
            }, 15000);
        });
    </script>

    <script>
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
            //defaultOption.disabled = true;
            defaultOption.selected = true;
            defaultOption.value = "environment";
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
                    console.log(data);
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
                        alert('Data Not Found');
                        $('.claimSubmitBtn').prop('disabled', true);
                    }
                    if (alreadyClaimed == 0) {
                        console.log(alreadyClaimed);
                        $('.claimSubmitBtn').prop('disabled', false);

                    }

                },
                error: function(data) {
                    console.log(data);
                }

            });
        }
    </script>
    <script type="text/javascript">
        $("#transfer_sensor").on("input", function() {
            var input = $(this).val().toLowerCase();
            var suggestions = "";
            var companies = {!! $companies !!}; // assuming $companies is a PHP array of company names

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

            $(document).on('click', '.sensorTable', function(e) {
                var radio = $(this).find('input[type="radio"]');
                var deviceId = $(this).data('device');
                console.log(deviceId);
                radio.val(deviceId);
                radio.prop('checked', true);
                radio.trigger('change');
            });


            $('#connection').on('click', function(e) {
                console.log(e);
                var company_id = "{{ $company_id }}";
                // e.preventDefault();

                $.ajax({
                    type: 'get',
                    data: {
                        company_id: company_id,
                    },
                    url: "{{ route('connect.with.equipment') }}",
                    success: function(data) {
                        console.log(data);
                        $('#connect-sensor-table').html(data.html);
                    }
                });
            });


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
