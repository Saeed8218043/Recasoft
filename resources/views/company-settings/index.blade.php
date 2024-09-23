@extends('layouts.app')

@section('content')

    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <style type="text/css">
            input {
                font-weight: 600;
            }

            .form-control {
                font-family: 'Open Sans';
                color: #000000;
            }
        </style>
        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: '{{ session('title') ?? '' }}',
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
        @php
            $is_valid = 0;
            $currentRouteName = Request::route()->getName();
            $company1 = \App\Company::where(['company_id' => $company_id])->first();
            if (isset($company->id)) {
                $is_valid = 1;
            }
            $user_ID = \Auth::user()->id;
            $Role = '';
            if ($company_id != '') {
                $Role = \App\CompanyMembers::where([
                    'company_id' => $company_id,
                    'user_id' => $user_ID,
                    // , 'company_name' => $company_name
                ])
                    ->select('role')
                    ->first();
            }
            
            if (isset($company1) && $company1->parent_id != 0) {
                $child_company = \App\Company::where(['company_id' => $company_id])->first();
            }
            
            if (isset($child_company) && $child_company->parent_id != 0) {
                $role2 = 'valid';
            }
            $user_Id = \Auth::user()->id;
            $user_Email = \Auth::user()->email;
            $company_Name_id = App\Company::select('parent_id')
                ->where(['user_id' => $user_Id, 'name' => $company[0]->name])
                ->first();
            $user_Role = App\CompanyMembers::where('company_id', $company_id)
                ->where('email', $user_Email)
                ->first();
            // if($company[0]->name != ''){
            // 	$user_Role = App\CompanyMembers::select('role', 'company_name')->where(array('email' => $user_Email, 'company_name' => $company[0]->name))->first();
            // }
            // $superCheck = false;
            
            // if($user_Company->name??'X' === $user_Role->company_name??'Y'){
            // 	$superCheck = true;
            // }
        @endphp

        <!-- BEGIN: Subheader -->
        <div class="m-subheader ">
            <div class="row">
                <div class="col-xl-10 col-lg-9 col-md-9 mb-3">
                    <h3 class="m-subheader__title font-weight-bold">Profile</h3>
                    <p class="m-0 text-muted">
                        You can update your profile here
                    </p>
                </div>
            </div>
        </div>

        <!-- END: Subheader -->
        <div class="m-content">
            <!--Begin::Section-->
            <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4">
                <h4 class="p-2 p-md-0 px-3 px-md-0 font-weight-bold">
                    Project Details
                </h4>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>
                                Project Name
                            </label>
                            <input type="text" name="" class="form-control" placeholder="Company Name"
                                value="{{ $company[0]->name ?? '' }}"
                                style="font-family: 'Open Sans';
										color: #000000; font-weight:600" readonly>
                        </div>
                        <div class="mb-3">
                            <label>
                                Project ID
                            </label>
                            <div class="d-flex align-items-center">
                                <span id="p1" class="copyable mr-2">
                                    {{ $company[0]->company_id ?? '' }}
                                </span>
                                <button onclick="copyToClipboard(this)" type="button"
                                    class="btn btn-sm btn-default copyBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32">
                                        <path fill="currentColor"
                                            d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z" />
                                        <path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z" />
                                    </svg> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-2">
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"
                                        viewBox="0 0 24 24" fit="" preserveAspectRatio="xMidYMid meet"
                                        focusable="false">
                                        <path fill="#17384c" fill-rule="evenodd"
                                            d="M3.943,21 C3.423,21 3,20.5775294 3,20.0576471 L3,3.94235294 C3,3.42352941 3.422,3 3.943,3 L20.057,3 C20.577,3 21,3.42247059 21,3.94235294 L21,20.0576471 C21,20.5775294 20.578,21 20.057,21 L3.943,21 Z M7,6 C7,5.44771525 6.55228475,5 6,5 C5.44771525,5 5,5.44771525 5,6 C5,6.55228475 5.44771525,7 6,7 C6.55228475,7 7,6.55228475 7,6 Z">
                                        </path>
                                    </svg>
                                </figure>
                                <figcaption>
                                    <label class="d-block m-0">
                                        Sensors
                                    </label>
                                    <strong class="fs-18">{{ $company[0]->sensorTotal ?? 0 }}</strong>
                                </figcaption>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <figure class="bg-light mb-0 mr-3 border radius fig-50 radius-4">
                                    <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" fit=""
                                        preserveAspectRatio="xMidYMid meet" focusable="false">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11.995 0L11.362 0.012L9.836 0.135L8.95 0.27L8.195 0.45L7.822 0.596L7.545 0.749L7.289 0.952L7.074 1.2L6.909 1.478L6.791 1.764L6.639 2.349L6.429 4.105L6.231 6.616L6.064 10.394L6 14.684L6.051 19.982L6.077 20.479L6.14 20.983L6.254 21.476L6.356 21.768L6.486 22.052L6.648 22.321L6.842 22.57L7.064 22.793L7.309 22.987L7.654 23.201L8.016 23.369L8.39 23.506L9.039 23.678L10.026 23.858L11.012 23.965L12.009 24L13.011 23.963L13.997 23.854L14.961 23.678L15.45 23.554L15.918 23.396L16.368 23.188L16.628 23.03L16.875 22.844L17.103 22.63L17.303 22.389L17.473 22.123L17.613 21.84L17.752 21.451L17.846 21.054L17.905 20.658L18 14.854L17.994 13.571L17.916 9.715L17.748 6.28L17.614 4.561L17.437 2.849L17.297 2.049L17.169 1.654L17.029 1.363L16.842 1.091L16.608 0.861L16.344 0.68L16.064 0.544L15.49 0.36L14.415 0.168L12.891 0.023L11.995 0Z"
                                            fill="#4A4A4A"></path>
                                    </svg>
                                </figure>
                                <figcaption>
                                    <label class="d-block m-0">
                                        Cloud Connectors
                                    </label>
                                    <strong class="fs-18">{{ $company[0]->connTotal ?? 0 }}</strong>
                                </figcaption>
                            </div>
                        </div>
                    </div>
                    @if ($company[0]->sensorTotal == 0 && $company[0]->connTotal == 0)
                        @if ($company[0]->parent_id != 0 || $user_ID == 1)
                            <div class="col-md-6">
                                <button class="btn btn-default" data-toggle="modal" data-target="#m_modal_10"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 22A9.99 9.99 0 0 1 2 12v-.2a10.005 10.005 0 0 1 17.074-6.874A10 10 0 0 1 12 22Zm0-8.59L14.59 16L16 14.59L13.41 12L16 9.41L14.59 8L12 10.59L9.41 8L8 9.41L10.59 12L8 14.59L9.41 16L12 13.411v-.001Z" />
                                    </svg> Delete Project...</button>
                            </div>
                        @else
                            <div class="col-md-6">
                                <button class="btn btn-light" disabled><svg xmlns="http://www.w3.org/2000/svg"
                                        width="1em" height="1em" preserveAspectRatio="xMidYMid meet"
                                        viewBox="0 0 24 24">
                                        <path fill="currentColor"
                                            d="M12 22A9.99 9.99 0 0 1 2 12v-.2a10.005 10.005 0 0 1 17.074-6.874A10 10 0 0 1 12 22Zm0-8.59L14.59 16L16 14.59L13.41 12L16 9.41L14.59 8L12 10.59L9.41 8L8 9.41L10.59 12L8 14.59L9.41 16L12 13.411v-.001Z" />
                                    </svg>You cannot delete inventory Project</button>
                            </div>
                        @endif
                    @else
                        <div class="col-md-6">
                            <button class="btn btn-light" disabled><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                    height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M12 22A9.99 9.99 0 0 1 2 12v-.2a10.005 10.005 0 0 1 17.074-6.874A10 10 0 0 1 12 22Zm0-8.59L14.59 16L16 14.59L13.41 12L16 9.41L14.59 8L12 10.59L9.41 8L8 9.41L10.59 12L8 14.59L9.41 16L12 13.411v-.001Z" />
                                </svg> Cannot delete Project while it contains devices</button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4">

                <div class="">
                    {{-- <div class="col-xl-3 col-lg-4">
									<div class="mb-4">
										<div class="userinfo_area">
											<figure class="can-change-thumb">
												@php
												if(isset($company[0]->image_url) && $company[0]->image_url!=''){
													$image_url = asset('public/uploads/company_images/'.$company[0]->image_url);
												}else{
													$image_url = asset('public/assets/app/media/img/users/c-big.jpg');
												}
												@endphp
												<img src="{{$image_url}}" alt="Thumb" class="img-fluid" id="load-image">
												<div class="buttons-control">
													<button type="button" class="btn mx-1 btn-danger btn-sm m-btn--pill px-4" data-toggle="modal" data-target="#imageModal">
														<i class="la la-edit"></i> Change
													</button>
													<button type="button" class="btn mx-1 btn-danger btn-sm m-btn--pill px-4" data-toggle="modal" data-target="#imageDeleteModal">
														<i class="la la-trash"></i> Remove
													</button>
												</div>
											</figure>
											<figcaption>
												<h4>
													{{$company[0]->name??''}}
												</h4>
												<span class="text-muted fw-500">
													<a href="mailto:the-c-company@gmail.com">{{$company[0]->email??''}}</a>
												</span>
												<div class="d-flex justify-content-center my-3">
													<button type="button" class="btn mx-1 btn-info btn-sm m-btn--pill">
														Follow
													</button>
													<button type="button" class="btn mx-1 btn-danger btn-sm m-btn--pill">
														Message
													</button>
												</div>
											</figcaption>
										</div>
									</div>
								</div> --}}
                    <div class="">
                        <form action="{{ url('update-company-settings') }}" method="post" id="form-validate">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $company[0]->company_id ?? '' }}">
                            <div class="row py-3 py-md-0">
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        Project Name*
                                    </label>
                                    <input type="text" name="company_name" class="form-control"
                                        placeholder="Company Name" value="{{ $company[0]->name ?? '' }}" required="">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        Organization Name*
                                    </label>
                                    <input type="text" name="organization_name" class="form-control"
                                        placeholder="Organization Name"
                                        value="{{ $company[0]->organization_name ?? '' }}" required="">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        Organization Number
                                    </label>
                                    <input type="text" name="organization_no" class="form-control"
                                        placeholder="Organization Number"
                                        value="{{ $company[0]->organization_no ?? '' }}">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        Project Email*
                                    </label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Company Email" value="{{ $company[0]->email ?? '' }}"
                                        required="">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        Project Phone*
                                    </label>
                                    <input type="text" name="phone" class="form-control"
                                        placeholder="Company Phone" value="{{ $company[0]->phone ?? '' }}"
                                        required="">
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <label>
                                        About Project
                                    </label>
                                    <textarea class="form-control"
                                        name="description"style="font-family: 'Open Sans';color: #000000; font-weight:600;height: 140px;">{{ $company[0]->description ?? '' }}</textarea>
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <input type="submit" name="" class="btn btn-primary px-4 mr-3"
                                        value="Save">
                                    {{-- <button type="button" class="btn btn-outline-danger px-4">
													Cancel
												</button> --}}
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- ends row -->

            </div>

            @if (isset($parent_id) && $parent_id > 0 && $can_manage_users >= 0)

                <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4 table-responsive">

                    @if (\Session::has('message'))
                        <div class="alert alert-success">{{ \Session::get('message') }}</div>
                    @endif

                    @php
                        $companyMemberID = App\CompanyMembers::select('id')
                            ->where(['email' => $user_Email ?? '', 'company_id' => $company[0]->company_id ?? ''])
                            ->first();
                    @endphp

                    <h4 class="p-2 p-md-0 px-3 px-md-0 font-weight-bold">Project Members</h4>
                    <form action="{{ route('invite-member') }}" method="post">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $company[0]->company_id ?? 0 }}">
                        <input type="hidden" name="compMemberId"
                            value="@if ($companyMemberID != '') {{ $companyMemberID->id ?? 'abd' }} @endif ">
                        <div class="row mt-3">
                            <div class="col-lg-5 col-md-4">
                                <div class="mb-3">
                                    <label>
                                        Email address
                                    </label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Email address" required="">
                                </div>
                                <input type="hidden" name="role" value="1">
                            </div>

                            <div class="col-lg-5 col-md-4" id="invited_company_name">
                                <div class="mb-3">
                                    <label>
                                        Project Name
                                    </label>
                                    <input type="name" class="form-control" value="{{ $company[0]->name ?? '' }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4" id="project_id">
                                <div class="mb-3">
                                    <label>
                                        Project ID
                                    </label>
                                    <input type="text" class="form-control"
                                        value="{{ $company[0]->company_id ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4">
                                <div class="mb-3">
                                    <label class="splabel" style="visibility:hidden;">
                                        Button
                                    </label>
                                    <button type="submit" class="btn btn-primary px-4 mr-3">Invite Member</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div style="margin-top: -12px;" class="mt-5">
                        <!--begin: Datatable -->
                        <table class="table has-valign-middle table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        EMAIL
                                    </th>
                                    <th>
                                        STATUS
                                    </th>
                                    <th width="10%">
                                        ADDED
                                    </th>
                                    <th class="text-center">
                                        ACTIONS
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($members) && count($members) > 0)
                                    @foreach ($members as $member)
                                        <tr>
                                            <td>
                                                <a href="mailto:{{ $member->email }}">{{ $member->email ?? '' }}</a>
                                            </td>
                                            @php
                                                $members_invites = DB::table('company_members_invite')
                                                    ->where('email', $member->email)
                                                    ->where('company_id', $member->company_id)
                                                    ->first();
                                                $accepted = isset($members_invites) ? $members_invites->accepted : 0;
                                            @endphp
                                            @if ($accepted == 0)
                                                <td>
                                                    Pending
                                                </td>
                                            @else
                                                <td>
                                                    Accepted
                                                </td>
                                            @endif
                                            <td>
                                                <span>{{ date('F d, Y', strtotime($member->created_at)) }}</span>
                                            </td>
                                            <td align="center">
                                                <a href="{{ url('delete-from-company/' . $member->email . '/' . $member->company_id) }}"
                                                    class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32">
                                                        <path fill="currentColor" d="M12 12h2v12h-2zm6 0h2v12h-2z" />
                                                        <path fill="currentColor"
                                                            d="M4 6v2h2v20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8h2V6zm4 22V8h16v20zm4-26h8v2h-8z" />
                                                    </svg> Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="6">No record found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ((isset($role2) && $role2 == 'valid') || $user_ID == 1)

                <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4 table-responsive" id="notifications">


                    <h4 class="p-2 p-md-0 px-3 px-md-0 font-weight-bold">
                        Notification recipients
                    </h4>
                    <form action="{{ url('add-notification-email') }}" method="post">
                        @csrf
                        <input type="name" name="company_name" class="form-control"
                            value="{{ $company[0]->name ?? '' }}" hidden readonly>
                        <input type="text" name="company_id" class="form-control"
                            value="{{ $company[0]->company_id ?? '' }}" hidden readonly>

                        <div class="row mt-3">
                            <div class="col-lg-5 col-md-4">
                                <div class="mb-3">
                                    <label>
                                        Name
                                    </label>
                                    <input required type="text" class="form-control"
                                    name="name" placeholder="Name"
                                    title="Please enter alphabetic characters and spaces only." />
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Email address
                                    </label>
                                    <input required type="email" class="form-control" placeholder="Email" name="email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?<![0-9])$" title="Please enter a valid email address." />
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Phone
                                    </label>
                                  <input type="tel" name="phone"  class="form-control" pattern="^\+\d{1,3}\s?\d{1,14}$" placeholder="e.g. +47 452 32 346" title="Please enter a valid phone number with country code (e.g., +47 452 32 346)." required />

                                </div>
                                <input type="hidden" name="role" value="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="splabel" style="visibility:hidden;">
                                Button
                            </label>
                            <button type="submit" class="btn btn-primary px-4 mr-3">Add</button>
                        </div>
                    </form>
                    <!--begin: Datatable -->
                    <div class="mt-5">
                        <table class="table has-valign-middle table-hover table-borderless">
                            <thead>
                                <tr>
                                    <th>
                                        NAME
                                    </th>
                                    <th>
                                        EMAIL
                                    </th>

                                    <th>
                                        PHONE
                                    </th>

                                    <th class="text-center">
                                        ACTIONS
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($notification_emails) && count($notification_emails) > 0)
                                    @foreach ($notification_emails as $row)
                                        <tr>
                                            <td class="font-weight-bold">
                                                {{ $row->name }}
                                            </td>
                                            <td>
                                                {{ $row->email }}
                                            </td>

                                            <td>
                                                {{ $row->phone }}
                                            </td>

                                            <td align="center">
                                                @php
                                                    $query = "SELECT *
                                                            FROM notification_emails
                                                            INNER JOIN notifications
                                                            ON notifications.id = notification_emails.notification_id
                                                            WHERE notifications.company_id='$company_id' AND email LIKE '%$row->phone%'";
                                                    $Numberexist = DB::select($query);
                                                    $query2 = "SELECT *
                                                            FROM notification_emails
                                                            INNER JOIN notifications
                                                            ON notifications.id = notification_emails.notification_id
                                                            WHERE notifications.company_id='$company_id' AND email LIKE '%$row->email%'";
                                                    $Numberexist = DB::select($query);
                                                    $Emailexist = DB::select($query2);
                                                    //  $Numberexist = App\NotificationEmail::where('email', 'LIKE', '%' . $row->phone . '%')->first();
                                                    // $Emailexist = App\NotificationEmail::where('email', 'LIKE', '%' . $row->email . '%')->first();
                                                @endphp
                                                <a href="#"
                                                    class="btn btn-outline-success m-btn m-btn--pill m-btn--wide btn-sm update_recipient"
                                                    r-name="{{ $row->name }}" r-email="{{ $row->email }}"
                                                    r-phone="{{ $row->phone }}" r-id="{{ $row->id }}"
                                                    data-toggle="modal" data-target="#edit-recipient">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                        preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32">
                                                        <path fill="currentColor" d="M12 12h2v12h-2zm6 0h2v12h-2z" />
                                                        <path fill="currentColor"
                                                            d="M4 6v2h2v20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8h2V6zm4 22V8h16v20zm4-26h8v2h-8z" />
                                                    </svg> Update
                                                </a>
                                                @if ($Emailexist == null && $Numberexist == null)
                                                    <a href="{{ url('delete-email/' . $row->email . '/' . $row->company_id) }}"
                                                        class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="1em"
                                                            height="1em" preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 32 32">
                                                            <path fill="currentColor" d="M12 12h2v12h-2zm6 0h2v12h-2z" />
                                                            <path fill="currentColor"
                                                                d="M4 6v2h2v20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8h2V6zm4 22V8h16v20zm4-26h8v2h-8z" />
                                                        </svg> Delete</a>
                                                @elseif($Emailexist != null || $Numberexist != null)
                                                    <a data-toggle="modal" data-target="#m_modal_3"
                                                        class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="1em"
                                                            height="1em" preserveAspectRatio="xMidYMid meet"
                                                            viewBox="0 0 32 32">
                                                            <path fill="currentColor" d="M12 12h2v12h-2zm6 0h2v12h-2z" />
                                                            <path fill="currentColor"
                                                                d="M4 6v2h2v20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8h2V6zm4 22V8h16v20zm4-26h8v2h-8z" />
                                                        </svg> Delete</a>
                                                @endif

                                            </td>
                                    @endforeach
                                @else
                                    <tr class="text-center">
                                        <td colspan="6">No record found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                    </div>

                </div>
                {{-- <iframe id='bigFrame' src='{{url("/dashboard2/$company_id")}}' frameBorder="0" style="min-width: -webkit-fill-available;min-height: 1000px;"></iframe> --}}
						
                @if ($user_ID != 1)
                    <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4 table-responsive">
                        <div class="m-portlet panel-has-radius mb-4 custom-p-5 p-md-4" style="border: none;">
                            <h4 class="font-weight-bold">
                                iFrame
                            </h4>
                            <div>
                                <label><b>URL :</b></label>
                                <a href='{{ url("/dashboard2/$company_id") }}'> {{ url("/dashboard2/$company_id") }}</a>

                            </div>
                            <div>

                                <label><b>IFRAME DASHBOARD :</b></label>
                                &lt;iframe id='bigFrame' src='{{ url("/dashboard2/$company_id") }}' frameBorder="0"
                                style="min-width: -webkit-fill-available;min-height: 1000px;"&gt;&lt;/iframe&gt;
                            </div>
                        </div>
                    </div>
                @endif
        </div>
        @endif
    </div>
    </div>
    <!-- Delete Button Modal -->
    <div class="modal fade" id="m_modal_10" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Delete Project?
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete Project "{{ $company[0]->name ?? '' }}" ?</p>
                </div>
                <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button> -->
                    <button class="btn btn-default" data-dismiss="modal">Cancel</button>

                    <form action="{{ route('company-settings.deleteCompany', $company[0]->company_id) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit-recipient" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocModalLabel2">Update notification recipient</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('editRecipient') }}" enctype="multipart/form-data">
                        <input type="text" name="company_id" id="r-company" hidden>
                        <input type="text" name="r_id" id="r-id" hidden>
                        @csrf
                        <div id="modal-edit">
                            <div class="mb-3">
                                <label for="recipient_name">Name</label>
                                <input required type="text" class="form-control" placeholder="Name"
                                    name="name" id="recipient_name"
                                    title="Please enter alphabetic characters and spaces only." />
                            </div>

                            <div class="mb-3">
                                <label for="recipient_email">Email</label>
                                <input required type="email" class="form-control" placeholder="Email" name="email" id="recipient_email" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?![0-9])$" title="Please enter a valid email address." />
                         </div>

                              <div class="mb-3">
                                    <label for="recipient_phone">Phone</label>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="tel" name="phone" id="recipient_phone" class="form-control" pattern="^\+\d{1,3}\s?\d{1,14}$" placeholder="e.g. +47 452 32 346" title="Please enter a valid phone number with country code (e.g., +47 452 32 346)." required />
                                        </div>
                                    </div>
                                


                            </div>
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

    <!--begin::Modal-->
    <div class="modal fade" id="m_modal_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete recipient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="delete_notification_form">
                    @csrf
                    <div class="modal-body">
                        <button class="btn btn-light" style="text-wrap: inherit;" disabled=""><svg
                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M12 22A9.99 9.99 0 0 1 2 12v-.2a10.005 10.005 0 0 1 17.074-6.874A10 10 0 0 1 12 22Zm0-8.59L14.59 16L16 14.59L13.41 12L16 9.41L14.59 8L12 10.59L9.41 8L8 9.41L10.59 12L8 14.59L9.41 16L12 13.411v-.001Z">
                                </path>
                            </svg> Cannot delete recipient that are active in notifications. Remove recipient from all
                            notifications.</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('public/assets/js/jquery.validate.js') }}"></script>
    <script type="text/javascript">
        $('.update_recipient').on('click', function() {
            var name = $(this).attr('r-name');
            var company_id = "{{ $company_id }}";
            var email = $(this).attr('r-email');
            var phone = $(this).attr('r-phone');
            var recipient_id = $(this).attr('r-id');
            $('#recipient_name').val(name);
            $('#recipient_email').val(email);
            $('#recipient_phone').val(phone);
            $('#r-company').val(company_id);
            $('#r-id').val(recipient_id);
        });
        $(window).on('load', function() {
            <?php
		if(\Session::has('message')){
			?>
            $("html, body").animate({
                scrollTop: $(document).height()
            }, 1000);
            <?php
		}
		?>
        });

  


        $('#invited_company_name').hide();
        $('#project_id').hide();

        $('#role_access_level').on('change', function() {
            if (this.value == 2) {

                $('#invited_company_name').show();
                $('#project_id').show();
            } else {
                $('#invited_company_name').hide();
                $('#project_id').hide();
            }

        });
    </script>
@endpush
