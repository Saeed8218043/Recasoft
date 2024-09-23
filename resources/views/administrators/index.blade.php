@extends('layouts.app')

@section('content')   

<!-- END: Left Aside -->
<div class="m-grid__item m-grid__item--fluid m-wrapper">

    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <h3 class="m-subheader__title ">@if(isset($company_name) && $company_name!='')
                            {{$company_name}}
                            @else
                            Tecasoft Technologies
                            @endif | Organization Admins</h3>
        <p class="m-0 text-muted">
            Manage access to your organization
        </p>
    </div>

    <!-- END: Subheader -->
    <div class="m-content">

        <!--Begin::Section-->

        <div class="m-portlet panel-has-radius mb-4 p-4">
            <h5 class="mb-3">
                Organization Administrator
            </h5>

            <div class="panel bg-light-grey-2 border mb-4 p-3 border-radius-1">
                <h5 class="mb-2">
                    Invite administrator
                </h5>			
                <p>
                    Administrator users/service accounts can <span class="fw-600">create new projects</span> and have <span class="fw-600">administrator access in all the projects</span> of the organization.
                </p>
                @if(\Session::has('message'))
				<div class="alert alert-success">{{\Session::get('message')}}</div>
				@endif
                @if(\Session::has('error'))
                    <div class="alert alert-danger">{{\Session::get('error')}}</div>
                    @endif
				@php
					$companyMemberID = App\CompanyMembers::select('id')->where(array('email'=>$user_Email??'','company_name'=>$company[0]->name??''))->first();
				@endphp
                <form action="{{route('invite-admin')}}" method="post">
                    @csrf
                    <div class="row d-flex gutter-10 align-items-end">
                    
                        <input type="hidden" name="company_id" value="{{$company[0]->company_id??0}}">
                        <input type="hidden" name="compMemberId" value="@if($companyMemberID != '')
								{{$companyMemberID->id??'abd'}}
							@endif
							">
                        <div class="col-lg-5 col-xl-5 col-md-8 col-sm-7">
                            <label>
                                Email address of user/service account
                            </label>
                            <input type="text" name="email" class="form-control" placeholder="Email address of user/service account" required>
                        </div> 
                        <div class="col-lg-4 col-xl-3 col-md-4 col-sm-5 mt-2 mt-sm-0">
                            <button type="submit" class="btn btn-default border btn-block" style="color: black !important;"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"/></svg> Invite Administrator</button>
                        </div> 
                    </div> 
                </form>
            </div>

            <div class="table-responsive">
                <table class="table has-valign-middle">
                    <thead>
                        <tr>
                            <th width="3%">
                                Type
                            </th>
                            <th width="88%">
                                Email/User name
                            </th>
                            <th width="10%" class="text-center">
                                Added
                            </th>
                            <th width="10%" class="text-center">
                                Remove
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($members) && count($members)>0)
						@foreach($members as $member)
                            @if($member->role==2)
                            <tr>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="fs-22" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M21 10.5h-1v-1a1 1 0 0 0-2 0v1h-1a1 1 0 0 0 0 2h1v1a1 1 0 0 0 2 0v-1h1a1 1 0 0 0 0-2Zm-7.7 1.72A4.92 4.92 0 0 0 15 8.5a5 5 0 0 0-10 0a4.92 4.92 0 0 0 1.7 3.72A8 8 0 0 0 2 19.5a1 1 0 0 0 2 0a6 6 0 0 1 12 0a1 1 0 0 0 2 0a8 8 0 0 0-4.7-7.28ZM10 11.5a3 3 0 1 1 3-3a3 3 0 0 1-3 3Z"/></svg>
                                </td>
                                <td>
                                <a href="mailto:{{$member->email}}">{{$member->email??''}}</a><!-- <span class="badge badge-primary">You</span> -->
                                </td>
                                <td>
                                    <span>{{ date('F d, Y', strtotime($member->created_at)) }}</span>
                                    </td>
                                <td align="center">
                                    <a href="{{url('delete-from-company/'.$member->email.'/'.$member->company_id)}}">
                                        <span class="fs-18" title="Delete" style="color: #f4516c;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36"><path fill="currentColor" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"/><path fill="none" d="M0 0h36v36H0z"/></svg>
                                        </span>
                                </a>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        @else
						<tr class="text-center">
							<td colspan="3">No record found</td>
						</tr>
						@endif                  
                    </tbody>
                </table>
            </div>


        </div>

    </div>
</div>
@endsection