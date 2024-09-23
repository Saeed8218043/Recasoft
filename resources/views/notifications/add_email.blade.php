

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script src="{{url('public/assets/demo/default/custom/crud/forms/widgets/select2.js')}}" type="text/javascript"></script>


    <!--end::Web font -->

    <!--RTL version:<link href="../../../assets/demo/default/base/style.bundle.rtl.css" rel="stylesheet" type="text/css" />-->

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="../../../assets/demo/default/media/img/logo/favicon.ico" />

    <style>
        #panel,
        #flip {
            background-color: #e5eecc;
            border: solid 1px #c3c3c3;
        }

        .email_variable_box {
            opacity: 0.5;
            transition: .4s all;
            margin: 10px;
            white-space: normal;
        }

        .email_variable_box:hover {
            opacity: 1.0;
            transform: scale(1.05)
        }

        #panel {
            padding: 50px;
            display: none;
        }
       
    </style>
<div class="m-portlet panel-has-radius mb-4 p-4 email_div{{ $email_counter + 1 }}">

    <div class="mb-4 panel bg-light border shadow rounded p-3 d-flex align-items-center justify-content-between">
        <div class="d-inline-flex align-items-center">
            <figure class="mb-0 fig-30 relative mr-2">
                <svg class="is_centered fs-16" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414L.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z">
                    </path>
                </svg>
            </figure>
            <h5 class="m-0">
                Send Email
            </h5>
        </div>
        <div>
            <i class="fl flaticon-delete-1 fs-16 text-muted deleteEmailSection btn p-0"
                emailCounter="{{ $email_counter + 1 }}"></i>
        </div>
    </div>

    {{-- <select class="form-control select2" multiple="multiple" name="select2[]" style="width: 100%;"> --}}

    </select>




    <div class="mb-4 email_div">
    {{-- <form autocomplete="off">
     <select class="select select2 custom-select" autocomplete="off" required multiple>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
      </form> --}}
        {{-- <input type="text" placeholder="Recipients" emailCounter="{{$email_counter+1}}" value="{{$email->email??''}}" class="form-control-fancy w-100 email_field" name="emails[]" style="font-weight:600; font-family: 'Open Sans';color:black" required> --}}
        <label for="">Emails</label>

        <select type="text" class="form-control-fancy email_field  m-select2  m_select2_11"
            name="emails[{{ $email_counter  }}][]" value="{{ $email->email ?? '' }}"
            id="email_field{{ $email_counter }}" emailCounter="{{ $email_counter }}" multiple="multiple"
            required>
            {{-- <option value="{{ isset($email)?$email->email:'' }}"></option> --}}

                {{-- @if (isset($seperated_email))
                    @foreach ($seperated_email as $uemail)
                        <option value="{{ $uemail }}" selected>{{ $uemail }}</option>
                    @endforeach
                @endif --}}

                @if (isset($notification_emails))
                    @foreach ($notification_emails as $key => $email2)
                        <option value="{{ $email2->email }}">{{ $email2->name }}</option>
                    @endforeach
                @endif

        </select>

        {{-- <span class="error text-danger @if (isset($email)) d-none @endif email_error{{$email_counter+1}}">At least one email is required</span> --}}
    </div>
            <div style="margin-bottom: 10px;">
            <a href="{{ url('/company-settings/' . $company_id) }}#notifications">+ New recipient</a>
            </div>



    {{-- 	<div class="mb-4">
				<select class="form-control form-control-fancy m-select2" id="m_select2_11" multiple name="param">

				</select>
			</div> --}}


    <div class="mb-4">
        <input type="text" placeholder="Email Subject" name="subjects[{{ $email_counter + 1 }}]"
            value="{{ $email->subject ?? '' }}" class="form-control-fancy w-100 email_subject enteredtext"
            emailCounter="{{ $email_counter + 1 }}" id="email_subject{{ $email_counter + 1 }}" maxlength="50"
            required>
        <span class="text-muted float-right"><span
                class="subjectwordsCount-{{ $email_counter + 1 }}">0</span>/50</span>
    </div>

    <div class="mb-4">
        <textarea class="form-control-fancy w-100 hauto email_content enteredtext" style="height: 200px;"
            emailCounter="{{ $email_counter + 1 }}" id="email_content{{ $email_counter + 1 }}"
            name="contents[{{ $email_counter + 1 }}]" placeholder="Email Body" maxlength="1000" required>{{ $email->content ?? '' }}</textarea>
        <span class="text-muted float-right"><span class="wordsCount-{{ $email_counter + 1 }}">0</span>/1000</span>
    </div>

    <div>
        <label class="d-block">
            Use these placeholders to provide dynamic information
        </label>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$name">$name</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$connected_sensor">$connected_sensor</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$description">$description</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable celsius" id="celsius{{$email_counter + 1}}"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$celsius">$celsius</button>
        {{-- <button class="mr-2 mb-2 btn btn-sm btn-default shadow">$farenheight</button> --}}
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$deviceID">$deviceID</button>
        {{-- <button class="mr-2 mb-2 btn btn-sm btn-default shadow">$label.LabelKey</button> --}}
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$project_name">$project_name</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow email_variable"
            emailCounter="{{ $email_counter + 1 }}" emailVariable="$url">$url</button>

    </div>
    <div id="sugestions{{$email_counter+1}}" class="suggestions p-3">
        <div class="col-lg-4 col-md-4 p-2">
            <h4 class="m-subheader__title ">Text Suggestions</h4>
        </div>
        <button style="text-align: left" type="button"
            class="mr-2 mb-2 ml-1 btn btn-sm btn-default shadow email_variable_box" emailCounter="{{ $email_counter + 1 }}"
            emailVariable="
Type: Cooling
Project: $project_name
Equipment: $name
Connected Sensor: $connected_sensor
Status: $celsius
History: Not been below 4°C in the last 24 hours.

Details: $url

Recasoft Technologies
support@recasoft.no">
            Type: Cooling<br>
            Project: $project_name<br>
            Equipment: $name<br>
            Connected Sensor: $connected_sensor<br>
            Status: $celsius<br>
            History: Not been below 4°C in the last 24 hours.<br>

            <br>Details: $url<br>

            <br>Recasoft Technologies<br>
            support@recasoft.no<br>

        </button>

        <button style="text-align: left" type="button"
            class="mr-2 mb-2 ml-1 btn btn-sm btn-default shadow email_variable_box" emailCounter="{{ $email_counter + 1 }}"
            emailVariable="
Type: Cooling critical
Project: $project_name
Equipment: $name
Connected Sensor: $connected_sensor
Status: $celsius
History: Been above 10°C continuously for the last 5 hours.

Details: $url

Recasoft Technologies
support@recasoft.no">
            Type: Cooling critical<br>
            Project: $project name<br>
            Equipment: $name<br>
            Connected Sensor: $connected_sensor<br>
            Status: $celsius<br>
            History: Been above 10°C continuously for the last 5 hours.<br>
            <br>Details: $url<br>
            <br>Recasoft Technologies<br>
            support@recasoft.no<br>

        </button>


        <button style="text-align: left" type="button"
            class="mr-2 mb-2 ml-1 btn btn-sm btn-default shadow email_variable_box" emailCounter="{{ $email_counter + 1 }}"
            emailVariable="
Type: Freezer
Project: $project_name
Equipment: $name
Connected Sensor: $connected_sensor
Status: $celsius
History: Not been below -18°C in the last 24 hours.

Details: $url

Recasoft Technologies
support@recasoft.no">
            Type: Freezer<br>
            Project: $project name<br>
            Equipment: $name<br>
            Connected Sensor: $connected_sensor<br>
            Status: $celsius<br>
            History: Not been below -18°C in the last 24 hours.<br>
            <br>Details: $url<br>
            <br>Recasoft Technologies<br>
            support@recasoft.no<br>

        </button>


        <button style="text-align: left" type="button"
            class="mr-2 mb-2 ml-1 btn btn-sm btn-default shadow email_variable_box"
            emailCounter="{{ $email_counter + 1 }}"
            emailVariable="
Type: Freezer critical
Project: $project_name
Equipment: $name
Connected Sensor: $connected_sensor
Status: $celsius
History: Been warmer than -10°C continuously for the last 5 hours.

Details: $url

Recasoft Technologies
support@recasoft.no">
            Type: Freezer critical<br>
            Project: $project_name<br>
            Equipment: $name<br>
            Connected Sensor: $connected_sensor<br>
            Status: $celsius<br>
            History: Been warmer than -10°C continuously for the last 5 hours.<br>
            <br>Details: $url<br>
            <br>Recasoft Technologies<br>
            support@recasoft.no<br>
        </button>
    </div>

</div>

{{-- 	<link href="{{url('/tagsinput.css')}}" rel="stylesheet" type="text/css">
		<script src="{{url('/tagsinput.js')}}"></script> --}}


<script>
    $(document).ready(function() {
        $("#flip").click(function() {
            $('#panel').slideToggle("slow");
        });
        $('.m_select2_11').select2({
            multiple: true,
            tags: false,
            closeOnSelect: false
        });
    });
</script>
