<head>
    <meta charset="utf-8" />
    <meta name="description" content="Select2 dropdown examples">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">


    {{-- <link href="{{ url('public/assets/vendors/base/tagsinput.css') }}" rel="stylesheet" type="text/css"> --}}
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script src="../../../assets/demo/default/custom/crud/forms/widgets/select2.js" type="text/javascript"></script>
    
    <!--end::Web font -->

    <!--RTL version:<link href="../../../assets/demo/default/base/style.bundle.rtl.css" rel="stylesheet" type="text/css" />-->

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="../../../assets/demo/default/media/img/logo/favicon.ico" />
</head>

<div class="m-portlet panel-has-radius mb-4 p-4 sms_div{{ $sms_counter + 1 }}">
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
                Send SMS
            </h5>
        </div>

        <div>
            <i class="fl flaticon-delete-1 fs-16 text-muted deleteSmsSection btn p-0"
                smsCounter="{{ $sms_counter + 1 }}"></i>
        </div>
    </div>


    <label for="">Phone Number</label>
    <div class="mb-4 sms_div">
        {{-- <input type="text" placeholder="+4712345678"  smsCounter="{{$sms_counter+1}}" value="{{$sms->email??''}}" class="form-control-fancy w-100 sms_field" name="sms[]" required> --}}
        <select class="form-control form-control-fancy num_field  m-select2  m_select2_11"
            name="sms[{{ $sms_counter }}][]" id="num_field{{ $sms_counter }}" sms_counter="{{ $sms_counter+1 }}" placeholder="Recipients" multiple required>
            <option></option>

                {{-- @if (isset($seperated_numbers))
                    @foreach ($seperated_numbers as $unumbers)
                        <option value="{{ $unumbers }}" selected>{{ $unumbers }}</option>
                    @endforeach
                @endif --}}

                @if (isset($notification_numbers))
                    @foreach ($notification_numbers as $key => $numbers)
                            <option value="{{ $numbers->phone }}">{{ $numbers->name }}</option>
                    @endforeach
                @endif

        </select>
        <!-- Your anchor tag -->
    <!-- Your anchor tag with the smooth scroll transition -->
    



        {{-- <input type="text" name="smss[]" class="form-control-fancy w-100"  data-role="tagsinput" placeholder="Recipients"> --}}
    </div>
            <div style="margin-bottom: 10px;">
            <a href="{{ url('/company-settings/' . $company_id) }}#notifications">+ New recipient</a>
            </div>

    {{-- 	<div class="mb-4">
				<select class="form-control form-control-fancy m-select2" id="m_select2_11" multiple name="param">
				
				</select>
			</div> --}}


    {{-- <div class="mb-4">
				<input type="text" placeholder="sms Subject" name="subjects[]" value="{{$sms->subject??''}}" class="form-control-fancy w-100 sms_subject enteredtext" smsCounter="{{$sms_counter+1}}" id="sms_subject{{$sms_counter+1}}" maxlength="50" required>
				<span class="text-muted float-right"><span class="subjectwordsCount-{{$sms_counter+1}}">0</span>/50</span>
			</div> --}}

    <div class="mb-4">
        <textarea class="form-control-fancy w-100 hauto sms_content enteredtext" smsCounter="{{ $sms_counter + 1 }}"
            id="sms_content{{ $sms_counter + 1 }}" name="smscontents[{{ $sms_counter + 1 }}]" placeholder="SMS Body"
            maxlength="300" required>{{ $sms->content ?? '' }}</textarea>
        <span class="text-muted float-right"><span class="smsWordsCount-{{ $sms_counter + 1 }}">0</span>/300</span>
    </div>

    <div>
        <label class="d-block">
            Use these placeholders to provide dynamic information
        </label>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow sms_variable"
            smsCounter="{{ $sms_counter + 1 }}" smsVariable="$name">$name</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow sms_variable"
            smsCounter="{{ $sms_counter + 1 }}" smsVariable="$description">$description</button>
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow celsius sms_variable" id="temp{{$sms_counter + 1}}"
            smsCounter="{{ $sms_counter + 1 }}" smsVariable="$celsius">$celsius</button>
        {{-- <button class="mr-2 mb-2 btn btn-sm btn-default shadow">$farenheight</button> --}}
        <button type="button" class="mr-2 mb-2 btn btn-sm btn-default shadow sms_variable"
            smsCounter="{{ $sms_counter + 1 }}" smsVariable="$deviceID">$deviceID</button>
        {{-- <button class="mr-2 mb-2 btn btn-sm btn-default shadow">$label.LabelKey</button> --}}
    </div>

</div>
<script>
    $(document).ready(function() {
        $('.m_select2_11').select2({
            multiple: true,
            tags: false,
            closeOnSelect: false
        });
    });
    </script>
{{-- <script>
    var token = $('input[name="_token"]').val();
    $(document).on('change', '.num_field', function() {
        let number = $(this).val();
        var company_id = "{{ $company_id }}";
        console.log(company_id);
        $.ajax({
            url: "{{ url('add/addSMSInField') }}",
            type: 'POST',
            dataType: "json",
            data: {
                number: number,
                company_id: company_id
            },
            headers: {
                'X-CSRF-Token': token
            },
            success: function(res) {
                console.log(res);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
 
</script> --}}
{{-- <script>
				$(document).ready(function(){
		$(".num_field  + .select2 .select2-search__field").prop('placeholder','+047')

	});
		</script> --}}
{{-- <script src="{{ url('assets/vendors/base/tagsinput.js') }}" type="text/javascript"></script> --}}
