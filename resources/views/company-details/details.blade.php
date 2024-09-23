@extends('layouts.app')

@section('content')

<div class="m-grid__item m-grid__item--fluid m-wrapper">

    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <h3 class="m-subheader__title ">Information about your organization</h3>
       
    </div>

    <!-- END: Subheader -->
    <div class="m-content">

        <!--Begin::Section-->

        <div class="m-portlet panel-has-radius mb-4 p-4">
            <h5 class="mb-3">
                Organization Details
            </h5>

            <div class="row mb-3">
                <div class="col-lg-4 mb-2">
                    <label>
                        Organization Name
                    </label>
                    <div class="d-flex align-items-center">
                        <span id="p2" class="copyable mr-2">
                            @if(isset($company_name) && $company_name!='')
                            {{$company_name}}
                            @else
                            Tecasoft Technologies
                            @endif
                        </span>
                        <button onclick="copyToClipboard(this)" type="button" class="btn btn-sm btn-default copyBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"/><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"/></svg> Copy
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                    <div class="d-flex align-items-center">
                        <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fit="" preserveAspectRatio="xMidYMid meet" focusable="false">
                                <path fill="#17384c" fill-rule="evenodd" d="M3.943,21 C3.423,21 3,20.5775294 3,20.0576471 L3,3.94235294 C3,3.42352941 3.422,3 3.943,3 L20.057,3 C20.577,3 21,3.42247059 21,3.94235294 L21,20.0576471 C21,20.5775294 20.578,21 20.057,21 L3.943,21 Z M7,6 C7,5.44771525 6.55228475,5 6,5 C5.44771525,5 5,5.44771525 5,6 C5,6.55228475 5.44771525,7 6,7 C6.55228475,7 7,6.55228475 7,6 Z"></path>
                            </svg>
                        </figure>
                        <figcaption>
                            <label class="d-block m-0">
                                Sensors
                            </label>
                            <strong class="fs-18">{{$company[0]->sensorTotal??0}}</strong>
                        </figcaption>
                    </div>
                </div>
                @php
                    $projectsCount = 0;
                    $projectsCount = App\Company::where('parent_id', $company[0]->id)->count();
                @endphp
                <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                    <div class="d-flex align-items-center">
                        <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 48 48"><path fill="#17384c" d="M17.75 6c-.69 0-1.25.56-1.25 1.25V14h-4.25A6.25 6.25 0 0 0 6 20.25v13.5A6.25 6.25 0 0 0 12.25 40h23.5A6.25 6.25 0 0 0 42 33.75v-13.5A6.25 6.25 0 0 0 35.75 14H31.5V7.25c0-.69-.56-1.25-1.25-1.25h-12.5ZM29 14H19V8.5h10V14Z"/></svg>
                        </figure>
                        <figcaption>
                            <label class="d-block m-0">
                                Projects 
                                
                            </label>
                            <strong class="fs-18">{{$projectsCount}}</strong>
                        </figcaption>
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <label>
                        Organization ID
                    </label>
                    <div class="d-flex align-items-center">
                        <span id="p2" class="copyable mr-2">
                            @if(isset($company_id) && $company_id!='')
                            {{$company_id}}
                            @else
                            Tecasoft Technologies
                            @endif
                        </span>
                        <button onclick="copyToClipboard(this)" type="button" class="btn btn-sm btn-default copyBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"/><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"/></svg> Copy
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                    <div class="d-flex align-items-center">
                        <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24" fit="" preserveAspectRatio="xMidYMid meet" focusable="false">
                                <path fill="#17384c" fill-rule="evenodd" d="M3.943,21 C3.423,21 3,20.5775294 3,20.0576471 L3,3.94235294 C3,3.42352941 3.422,3 3.943,3 L20.057,3 C20.577,3 21,3.42247059 21,3.94235294 L21,20.0576471 C21,20.5775294 20.578,21 20.057,21 L3.943,21 Z M7,6 C7,5.44771525 6.55228475,5 6,5 C5.44771525,5 5,5.44771525 5,6 C5,6.55228475 5.44771525,7 6,7 C6.55228475,7 7,6.55228475 7,6 Z"></path>
                            </svg>
                        </figure>
                        <figcaption>
                            <label class="d-block m-0">
                                Cloud Connectors
                            </label>
                            <strong class="fs-18">{{$company[0]->connTotal??0}}</strong>
                        </figcaption>
                    </div>
                </div>
                @php
                    $user_Email = \Auth::user()->email;
                    $companyMemberID = App\CompanyMembers::select('id')->where(array('company_name'=>$company[0]->name??''))->first();
                    if($companyMemberID != '' || $companyMemberID != null){
                        $total_subs = App\CompanyMembers::where(array('company_id'=>$company[0]->company_id, 'role'=>2))->count();
                    }
				@endphp	
                <div class="col-lg-4 col-md-6 col-sm-6 mb-2">
                    <div class="d-flex align-items-center">
                        <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 48 48"><path fill="#17384c" d="M17.75 6c-.69 0-1.25.56-1.25 1.25V14h-4.25A6.25 6.25 0 0 0 6 20.25v13.5A6.25 6.25 0 0 0 12.25 40h23.5A6.25 6.25 0 0 0 42 33.75v-13.5A6.25 6.25 0 0 0 35.75 14H31.5V7.25c0-.69-.56-1.25-1.25-1.25h-12.5ZM29 14H19V8.5h10V14Z"/></svg>
                        </figure>
                        <figcaption>
                            <label class="d-block m-0">
                                Organization Admins
                            </label>
                            <strong class="fs-18">
                                {{$total_subs??0}}
                            </strong>
                        </figcaption>
                    </div>
                </div>
            </div>
            <!-- ends row -->

        </div>
        @if(isset($company_id))
        <div class="m-portlet panel-has-radius mb-4 p-3">
            <h4 class="mb-4">
                Email for service requests
            </h4>
            <p>This email address will recieve the service request from your projects</p>
            @php
            $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
            $settingEmail = isset($setting->meta_value)?$setting->meta_value:'';
            @endphp
            <div class="row">
                <div class="col-lg-5">
                    <form method="post" action="{{route('updateSettings',['company_id'=>$company_id])}}">
                        @csrf
                        <div class="mb-3">
                        <input type="email" name="setting_email" value="{{$settingEmail}}" class="form-control" placeholder="Email" style="font-family: 'Open Sans';color: #000000; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <input type="submit" name="" class="btn btn-primary" value="Save">
                    </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection