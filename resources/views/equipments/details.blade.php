@extends('layouts.app')

@section('content')
<style type="text/css">
    .img-style {  
        flex-grow: 1;
        background-color: rgba(116,147,162,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 2px;
    }
 
/*.modal-backdrop{
    display:none !important;
}*/
    .btn-active {
        border-color: #ebedf2 !important;
        background-color: #f4f5f8 !important;
    }
        .sensor_icon_mini {
    position: absolute;
    left: 4px !important;
    top: 4px !important;
    }
    .filter-option{
        font-weight:bold;
    }
</style>
<style type="text/css">
    input {font-weight:600;
    color: #000000
    }
    .form-control {
    font-family: 'Open Sans';
    color: #000000;
}
@media screen and (max-width: 767px) {
  
    .m-subheader__title{
        display:none !important;
    }
}

 </style>
 <style>
    .sensorTable td:nth-child(3) a{
        font-family: 'Open Sans';
        font-weight: 600;
    }
     .sensorTable td:nth-child(4) a{
        font-family: 'Open Sans';
        font-weight: 600;
    }

     .input-group-text{
    background-color: white !important;
 }
.dropdown-menu li {
    display:block;
    padding: 10px;
    cursor: pointer;
  }

  .dropdown-menu li:hover {
    background-color: #eee;
  }

  .dropdown-menu li a {
    display:block;
    padding: 5px;
  }

/*for Suggestions  */
#search_suggestions {
    border: 1px solid #ebedf2;
}
#search_suggestions > .dropdown-item {
    padding: .65rem 1.5rem;
}
#search_suggestions > .dropdown-item:not(:last-child) {
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



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.css" integrity="sha512-7/BfnxW2AdsFxJpEdHdLPL7YofVQbCL4IVI4vsf9Th3k6/1pu4+bmvQWQljJwZENDsWePEP8gBkDKTsRzc5uVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 @php
        $is_valid = 0;
        $currentRouteName = Request::route()->getName();
        $company = \App\Company::where(['company_id' => $company_id])->first();
        if (isset($company->id)) {
            $is_valid = 1;
        }

          $user_ID = \Auth::user()->id;
            $user_Role = '';
            $role2 = '';
            if ($company_id != '') {
                $user_Role = \App\CompanyMembers::where([
                    'company_id' => $company_id,
                    'user_id' => $user_ID,
                    // , 'company_name' => $company_name
                ])
                    ->select('role')
                    ->first();
            }

            if( isset($company) && $company->parent_id !=0){
                $child_company = \App\Company::where(['company_id' => $company_id])->first();
            }

            if(isset($child_company) && $child_company->parent_id !=0 ){
                $role2 = 'valid';
            }
    @endphp

@if(isset($can_manage_users) && $can_manage_users>0)
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
                    <input type="hidden" name="sID" id="sIDS" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
                    <input type="hidden" name="comp_ID" id="comp_ID" value="{{isset($currentID[0]->id)?$currentID[0]->id:''}}">
                    <input type="hidden" name="transfer_sensor" id="selected_company_id" value="">

                  <div class="mb-4" id="custome-input">
                        <input type="search" name="" class="form-control" name="transfer_sensor1" id="transfer_sensor" placeholder="Start typing a project's name">
                        <div id="search_suggestions"></div>

                        <p id="company_select_error" class="text-danger d-none">Please select any project</p>
                    </div>
                </form>
                @else
                <form id="move_sensor_form">
                    <input type="hidden" name="sID" id="sIDS" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">

                    <input type="hidden" name="comp_ID" id="comp_ID" value="{{isset($currentID[0]->id)?$currentID[0]->id:''}}">
                    <input type="hidden" name="transfer_sensor" id="selected_company_id" value="">

                  <div class="mb-4" id="custome-input">
                        <input type="search" name="" class="form-control" name="transfer_sensor1" id="transfer_sensor" placeholder="Start typing a project's name">
                        <div id="search_suggestions"></div>

                        <p id="company_select_error" class="text-danger d-none">Please select any project</p>
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
@endif

<!--begin::Modal-->
<div class="modal fade" id="modal-device-docs" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h5 class="modal-title" id="supportModalLabel">Resources</h5>

                <button data-dismiss="modal" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-doc">Add Resource</button>

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
                        $id_device = $sensor->id??'';
                        $docs_devices = \App\DeviceDocument::where('device_id',$id_device)->get();
                    @endphp
                    <tbody>
                        @foreach ($docs_devices as $docs_device)
                        <tr>
                            <td>
                                <span class="fw-600">{{$docs_device->url}}</span>
                            </td>
                            <td>{{$docs_device->created_at}}</td>
                            <td class="text-right">
                                <!-- Actions dropdown -->
                                <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
                                    <a href="#" class="m-portlet__nav-link btn btn-lg btn-secondary  m-btn m-btn--outline-2x m-btn--air m-btn--icon m-btn--icon-only m-btn--pill  m-dropdown__toggle">
                                        <i class="la la-plus m--hide"></i>
                                        <i class="la la-ellipsis-h"></i>
                                    </a>
                                    <div class="m-dropdown__wrapper">
                                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
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
                                                                <i class="m-nav__link-icon la la-trash m--font-danger"></i>
                                                                <span class="m-nav__link-text m--font-danger">Delete</span>
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
<div class="modal fade" id="modal-add-doc" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocModalLabel">Upload file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.uploadSensorDoc')}}" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
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
                                <input type="file" class="form-control" name="sensor_doc" required="" style="width: 100%; height: auto;">
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



<div class="modal fade" id="chooseModals" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
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
                        <button data-dismiss="modal" class="btn btn-secondary mx-1" data-toggle="modal" data-target="#modal-add-doc">Your device</button>
                        <button type="button" class="btn btn-secondary mx-1 chooseFile" data-toggle="modal" data-target="#chooseFileModal">Document cloud</button>
                    </div>

                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-primary mx-1" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="chooseFileModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.uploadSensorDoc2')}}" enctype="multipart/form-data">
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">

                @csrf

                    <div id="folder-table">

                    </div>

                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                        <input type="button" class="btn btn-primary mx-1" data-dismiss="modal" data-toggle="modal" data-target="#upload-doc" value="Upload">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="connectionModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose Sensor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('sensor.connection')}}" enctype="multipart/form-data">
                    <input type="hidden" name="eID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
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


<div class="modal fade" id="connectionCloseModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disconnect Sensor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('sensor.disconnect')}}" enctype="multipart/form-data">
                    <input type="hidden" name="eID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
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




<div class="modal fade" id="upload-doc" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit file name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.uploadSensorDoc2')}}" enctype="multipart/form-data">
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
                    @csrf
                    <div class="mb-3">
                        <label>
                            Name
                        </label>
                        <input required type="text" class="form-control" name="sensor_file_name" id="sensor_file_name">
                    </div>
                    <input  type="hidden" name="sensor_file_id" id="sensor_file_id" required>
                    <input  type="hidden" name="sensor_doc" id="sensor_file" required>


               
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary mx-1" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!--begin::Modal-->
<div class="modal fade" id="modal-delete-doc" tabindex="-1" role="dialog" aria-labelledby="deleteDocModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDocModalLabel">Delete Resource</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.uploadSensorDoc')}}" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
                    <p>Are you sure you want to delete this?</p>

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



<!--begin::Modal-->
<div class="modal fade" id="modal-support" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="supportModalLabel"><b>Order Service </b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                $compID = isset($currentCompany->company_id)?$currentCompany->company_id:'-';
                @endphp
                <form method="post" action="{{route('sendOrderService',['company_id'=>$compID])}}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>
                            Project ID
                        </label>
                        <input type="text" class="form-control" name="company_id" value="{{$currentCompany->company_id??''}}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>
                            Project Name
                        </label>
                        <input type="text" class="form-control" name="company_name" value="{{$currentCompany->name??''}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label>
                            Phone Number
                        </label>
                        <input type="text" class="form-control" name="phone_number" required>
                    </div>

                    <!--Bootstrap Select-->
                    <div class="mb-3" >
                        <label>
                            Select Device(s)
                        </label>
                        <select name="devices[]" id="chooseFileInput" class="form-control m-bootstrap-select m_selectpicker" style="font-weight:bold;" multiple>
                            @php
                            $currentDevice = isset($sensor->device_id)?$sensor->device_id:'';
                            @endphp
                            @foreach ($equipments as $item)
                            @if($item->event_type=='equipment')
                                   
                            <option class="select_device" value="{{ $item->device_id }}" @if($currentDevice==$item->device_id) selected @endif >{{(isset($item->name) && $item->name!='')?$item->name:$item->device_id}}</option>
                            @endif
                            @endforeach

                        {{-- <optgroup label="Not connected" >
                        @foreach ($other_equipments as $item)
                            @if ( $item->event_type=='equipment')
                            <option class="select_device" value="{{ $item->device_id }}" @if($currentDevice==$item->device_id) selected @endif >{{(isset($item->name) && $item->name!='')?$item->name:$item->device_id}}</option>
                            @endif
                            @endforeach
                        </optgroup> --}}
                        </select>
                    </div>
                    <!--Bootstrap Select ends-->
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" name="order_service_file" style="width: 100%; height: auto;">
                    </div>
                    <div class="mb-3 d-none">
                        <label>
                            Device
                        </label>
                        <select class="form-control">
                            <option>-Select Device-</option>
                            @foreach ($sensors as $item)
                            <option>{{(isset($item->name) && $item->name!='')?$item->name:$item->device_id}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>
                            Comments
                        </label>
                        <textarea name="description" class="form-control" style="height: 140px;font-weight:600; font-family: 'Open Sans';"></textarea>
                    </div>
                    <div>
                        <input type="checkbox" id="urgent" name="urgent" value="Yes">
                        <label for="urgent">Urgent</label>
                    </div>
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary mx-1">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->


	<div class="modal fade" id="m_modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body bg-light">
						<div class="text-center">
							<h4 style="line-height: 1.6;">
								<small class="text-muted d-block">
									Listening to:
								</small>
								All sensors in {{isset($currentCompany->name)?$currentCompany->name:''}}
							</h4>

							<div class="text-center my-4 touch_header">
								<figure class="m-0">
									<img src="{{asset('public/assets/app/media/img/misc/sensor-animation.svg')}}" alt="Sensor" class="img-fluid" />
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
                <h3 class="m-subheader__title ">
                    Equipment Details
                </h3>
            </div>
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

        @if(\Session::has('message'))
		<div class="alert alert-success alert-dismissible fade show" role="alert">
		  <strong>Success Message!</strong> {{\Session::get('message')}}
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		  </button>
		</div>
		 @endif

    </div>

    <!-- END: Subheader -->
    @if($sensor != '' || $equipment !='')
    <div class="m-content">

        <!--Begin::Section-->

        <div class="details_wrap">
            <div class="details_wrap_x">
                <div class="m-portlet panel-has-radius mb-4 p-4 portlet-height-1">
                     <div class="mb-3">
        @if ((isset($role2) && $role2=='valid' ))
                    @if(count($connected_equipments)>0)
                       <div class="bg-light-grey-2 p-2 p-sm-3 my-1" style="border-radius: 8px;">
                                <h4 class="m-0 p-0">Connected</h4>
                            </div>
                        <ul class="device_caption_list_minimal">
                            @foreach($connected_equipments as $row)
                            @php
                            $selected='';
                            if($row->device_id==$sensor->device_id){
                                $selected='ribbon-left ribbon-danger';
                            }
                            $sensor_data = App\Device::where('device_id', $row->sensor_id)->first();
                            @endphp
                                @if($row->event_type=='equipment')
                            <li id="{{$row->device_id}}" class="{{$selected}} @if(isset($sensor_data->is_active) && $sensor_data->is_active==0) is_disabled @endif">
                                <a  href="{{url('equipment-details')}}/{{$company_id}}/{{$row->device_id}}">
                                    <figure id="sensor_{{$row->device_id}}" class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                    <span class="sensor_icon_mini" style="background-color: green;"></span>
                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 459.359 459.359" style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                   <g>
                                       <path style="fill:#020202;" d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362
                                           c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912
                                           c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z"/>
                                       <path style="fill:#020202;" d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67
                                           c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774
                                           c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z"/>
                                       <path style="fill:#020202;" d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z
                                            M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93
                                           C153.145,312.05,157.593,307.619,163.061,307.619z"/>
                                       <path style="fill:#020202;" d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473
                                           c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294
                                           C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143
                                           c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026
                                           H23.075V236.214h65.787V361.026z"/>
                                       <path style="fill:#020202;" d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814
                                           c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8
                                           c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058
                                           c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03
                                           c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8
                                           c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814
                                           c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064
                                           c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738
                                           c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372
                                           l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113
                                           h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366
                                           l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801
                                           c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926
                                           C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337
                                           c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z"/>
                                       <path style="fill:#020202;" d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926
                                           c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>

                                    </figure>
                                    <figcaption>
                                        {{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}
                                    </figcaption>
                                </a>
                            </li>
                            @endif
                            @endforeach
                            {{-- <li>
                                <a href="#" class="is_disabled">
                                    <figure>
                                        <span class="iconify fs-22" data-icon="carbon:temperature"></span>
                                    </figure>
                                    <figcaption>
                                        03/Fridge
                                    </figcaption>
                                </a>
                            </li> --}}
                        </ul>
                    @endif
                    @if ((isset($role2) && $role2=='valid' ))
                    @if(count($other_equipments)>0)
                          <div class="bg-light-grey-2 p-2 p-sm-3 my-4" style="border-radius: 8px;">
                                <h4 class="m-0 p-0">Not connected</h4>
                            </div>
                        <ul class="device_caption_list_minimal">
                            @foreach($other_equipments as $row)
                            @php
                            $selected='';
                            if($row->device_id==$sensor->device_id){
                                $selected='ribbon-left ribbon-danger';
                            }
                            @endphp
                                @if($row->event_type=='equipment')
                            <li id="{{$row->device_id}}" class="{{$selected}} @if(isset($row->is_active) && $row->is_active==0) is_disabled @endif">
                                <a  href="{{url('equipment-details')}}/{{$company_id}}/{{$row->device_id}}">
                                    <figure id="sensor_{{$row->device_id}}" class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 459.359 459.359" style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                   <g>
                                       <path style="fill:#020202;" d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362
                                           c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912
                                           c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z"/>
                                       <path style="fill:#020202;" d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67
                                           c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774
                                           c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z"/>
                                       <path style="fill:#020202;" d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z
                                            M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93
                                           C153.145,312.05,157.593,307.619,163.061,307.619z"/>
                                       <path style="fill:#020202;" d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473
                                           c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294
                                           C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143
                                           c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026
                                           H23.075V236.214h65.787V361.026z"/>
                                       <path style="fill:#020202;" d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814
                                           c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8
                                           c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058
                                           c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03
                                           c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8
                                           c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814
                                           c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064
                                           c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738
                                           c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372
                                           l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113
                                           h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366
                                           l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801
                                           c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926
                                           C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337
                                           c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z"/>
                                       <path style="fill:#020202;" d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926
                                           c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>

                                    </figure>
                                    <figcaption>
                                        {{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}
                                    </figcaption>
                                </a>
                            </li>
                            @endif
                            @endforeach
                            {{-- <li>
                                <a href="#" class="is_disabled">
                                    <figure>
                                        <span class="iconify fs-22" data-icon="carbon:temperature"></span>
                                    </figure>
                                    <figcaption>
                                        03/Fridge
                                    </figcaption>
                                </a>
                            </li> --}}
                        </ul>
                    @endif
                @endif
        @endif
        @if ( $role2!='valid')

                          <div class="bg-light-grey-2 p-2 p-sm-3 my-4" style="border-radius: 8px;">
                                <h4 class="m-0 p-0">Inventory</h4>
                            </div>
                        <ul class="device_caption_list_minimal">
                            @foreach($inventory_equipments as $row)
                            @php
                            $selected='';
                            if($row->device_id==$sensor->device_id){
                                $selected='ribbon-left ribbon-danger';
                            }
                            @endphp
                                @if($row->event_type=='inventory')
                            <li id="{{$row->device_id}}" class="{{$selected}} @if(isset($row->is_active) && $row->is_active==0) is_disabled @endif">
                                <a  href="{{url('equipment-details')}}/{{$company_id}}/{{$row->device_id}}">
                                    <figure id="sensor_{{$row->device_id}}" class="fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                        viewBox="0 0 459.359 459.359" style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                   <g>
                                       <path style="fill:#020202;" d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362
                                           c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912
                                           c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z"/>
                                       <path style="fill:#020202;" d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67
                                           c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774
                                           c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z"/>
                                       <path style="fill:#020202;" d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z
                                            M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93
                                           C153.145,312.05,157.593,307.619,163.061,307.619z"/>
                                       <path style="fill:#020202;" d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473
                                           c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294
                                           C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143
                                           c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026
                                           H23.075V236.214h65.787V361.026z"/>
                                       <path style="fill:#020202;" d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814
                                           c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8
                                           c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058
                                           c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03
                                           c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8
                                           c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814
                                           c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064
                                           c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738
                                           c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372
                                           l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113
                                           h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366
                                           l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801
                                           c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926
                                           C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337
                                           c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z"/>
                                       <path style="fill:#020202;" d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926
                                           c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>

                                    </figure>
                                    <figcaption>
                                        {{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}
                                    </figcaption>
                                </a>
                            </li>
                            @endif
                            @endforeach
                            {{-- <li>
                                <a href="#" class="is_disabled">
                                    <figure>
                                        <span class="iconify fs-22" data-icon="carbon:temperature"></span>
                                    </figure>
                                    <figcaption>
                                        03/Fridge
                                    </figcaption>
                                </a>
                            </li> --}}
                        </ul>
        @endif
                    </div>
                </div>
            </div>

            <div class="details_wrap_y">
                <div class="m-portlet panel-has-radius mb-4 p-4">
                    <div class="mb-2">
                        <p class="m-0">
                            <a href="{{url('equipments')}}/{{$sensor->company_id}}" class="no-decoration">
                                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M21 11H6.414l5.293-5.293l-1.414-1.414L2.586 12l7.707 7.707l1.414-1.414L6.414 13H21z"/></svg>
                                Back to full list
                            </a>
                        </p>
                    </div>
                    {{-- <video id="previeww" width="500px" height="400px"></video> --}}
                   {{--  <div class="card-body">
		                <figure>
						 <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->errorCorrection('H')->size(50)->generate($sensor->device_id)) !!}">
						  <figcaption>
		                <a href="data:image/png;base64, {!! base64_encode(QrCode::format('png')->errorCorrection('H')->size(50)->generate($sensor->device_id)) !!}" download>Dwonload</a></figcaption>
						</figure>
                    </div> --}}

                     <div class="bg-light-grey-2 p-2 p-sm-3 mb-4 rounded">
                        <div class="d-inline-flex align-items-center">

                                    <figure id="single_sensor_{{$sensor->device_id}}" class="fig-60 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                        <span class="sensor_icon_mini" style="background-color: green;"></span>
                                        
                                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 459.359 459.359" style="enable-background:new 0 0 459.359 459.359;" xml:space="preserve">
                                        <g>
                                            <path style="fill:#020202;" d="M162.152,45.256h242.922v155.985c1.209-0.106,2.381-0.362,3.605-0.362
                                                c10.193,0,19.748,3.938,27.026,10.998c0.023-0.437,0.135-0.857,0.135-1.308V44.01c0-16.285-13.243-29.521-29.527-29.521H160.912
                                                c-16.285,0-29.527,13.235-29.527,29.521v51.618h30.768V45.256z"/>
                                            <path style="fill:#020202;" d="M226.788,233.405l21.16-21.137c0.052-0.06,0.12-0.091,0.174-0.135v-61.67
                                                c0-17.518-14.249-31.76-31.767-31.76H109.767c-17.518,0-31.766,14.242-31.766,31.76v43.446h14.774
                                                c6.009,0,11.621,1.503,16.691,3.981l0.3-48.42h107.588v99.904C219.224,243.424,222.334,237.851,226.788,233.405z"/>
                                            <path style="fill:#020202;" d="M131.167,330.469v11.057h62.827v-11.057v-17.863c0-5.018,1.052-9.779,2.793-14.182h-65.62V330.469z
                                                M163.061,307.619c5.467,0,9.915,4.432,9.915,9.915c0,5.484-4.447,9.93-9.915,9.93c-5.469,0-9.916-4.445-9.916-9.93
                                                C153.145,312.05,157.593,307.619,163.061,307.619z"/>
                                            <path style="fill:#020202;" d="M92.775,213.139H19.164C8.593,213.139,0,221.732,0,232.294v139.473
                                                c0,10.561,8.593,19.154,19.164,19.154h73.612c10.568,0,19.162-8.593,19.162-19.154V232.294
                                                C111.937,221.732,103.344,213.139,92.775,213.139z M55.977,384.101c-4.506,0-8.143-3.65-8.143-8.143
                                                c0-4.492,3.637-8.127,8.143-8.127c4.476,0,8.113,3.635,8.113,8.127C64.09,380.451,60.453,384.101,55.977,384.101z M88.862,361.026
                                                H23.075V236.214h65.787V361.026z"/>
                                            <path style="fill:#020202;" d="M451.24,304.495h-18.065c-2.261-8.924-5.784-17.322-10.396-25.029l12.807-12.814
                                                c3.155-3.14,3.169-8.308,0-11.478l-21.168-21.152c-3.29-3.32-8.593-2.914-11.478,0c-13.07,13.069,0.271-0.271-12.799,12.8
                                                c-0.023,0-0.039-0.015-0.06-0.03c-7.655-4.567-16-8.052-24.855-10.321c-0.031-0.015-0.06-0.015-0.091-0.03v-18.058
                                                c0-4.477-3.635-8.111-8.12-8.111h-29.912c-4.484,0-8.12,3.635-8.12,8.111v18.058c-0.031,0.016-0.06,0.016-0.091,0.03
                                                c-8.854,2.27-17.2,5.754-24.855,10.321c-0.022,0.016-0.037,0.03-0.06,0.03c-13.017-13.011,0.323,0.33-12.799-12.8
                                                c-2.336-2.358-6.211-3.335-9.982-0.991c-2.05,1.262-20.957,20.446-22.663,22.144c-3.169,3.17-3.155,8.338,0,11.478l12.807,12.814
                                                c-4.612,7.707-8.135,16.105-10.396,25.029H232.88c-4.484,0-8.12,3.635-8.12,8.111v29.926c0,4.477,3.635,8.113,8.12,8.113h18.064
                                                c2.262,8.924,5.777,17.307,10.383,25.014c0,0,0.006,0,0.006,0.014l-12.799,12.801c-1.524,1.517-2.374,3.591-2.374,5.738
                                                c0,2.148,0.857,4.221,2.374,5.739l21.167,21.152c1.585,1.577,3.666,2.372,5.738,2.372c2.074,0,4.155-0.795,5.739-2.372
                                                l12.792-12.801c7.677,4.597,16.045,8.099,24.923,10.366c0.031,0.015,0.06,0.015,0.091,0.03v18.058c0,4.477,3.635,8.113,8.12,8.113
                                                h29.912c4.484,0,8.12-3.637,8.12-8.113v-18.058c0.031-0.016,0.06-0.016,0.091-0.03c8.878-2.268,17.247-5.77,24.923-10.366
                                                l12.792,12.801c3.178,3.153,8.301,3.153,11.478,0l21.168-21.152c3.169-3.171,3.155-8.338,0-11.478l-12.799-12.801
                                                c0-0.014,0.006-0.014,0.006-0.014c4.605-7.707,8.121-16.09,10.382-25.014h18.065c4.484,0,8.119-3.637,8.119-8.113v-29.926
                                                C459.359,308.129,455.725,304.495,451.24,304.495z M342.06,390.907c-34.921,0-63.33-28.409-63.33-63.337
                                                c0-34.929,28.409-63.337,63.33-63.337c34.92,0,63.33,28.408,63.33,63.337C405.39,362.498,376.98,390.907,342.06,390.907z"/>
                                            <path style="fill:#020202;" d="M342.06,297.644c-16.518,0-29.918,13.4-29.918,29.926c0,16.525,13.401,29.926,29.918,29.926
                                                c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                                    </figure>
                                    <figcaption class="m-0">
                                        @if($sensor->event_type=='equipment' || $sensor->event_type=='inventory' )
                                        <p class="m-0 main_value_1">
                                            <strong>{{$sensor->name}}</strong>
                                        </p>
                                        @elseif($sensor->event_type=='ccon')
                                        <p class="m-0 fw-500 text-muted fs-12">
                                            Network Connection
                                        </p>
                                        @endif
                                        @if($sensor->event_type=='temperature')
                                        <p class="m-0 main_value_1">
                                            @if(isset($sensor->is_active) && $sensor->is_active==1)
                                            {{isset($sensor->temperature)?@number_format($sensor->temperature,2):0}}°C
                                            @else
                                            Offline
                                            @endif
                                        </p>
                                        @else
                                        <p class="m-0 fs-22 fw-600">
                                            @if(isset($sensor->temperature) && $sensor->temperature!='')
                                            {{$sensor->temperature}}
                                            @endif
                                        </p>
                                        @endif
                                        <p class="m-0 fw-500 text-muted fs-12">
                                            @if(isset($sensor->is_active) && $sensor->is_active==1)
                                                @if($sensor->event_type=='ccon')
                                                Online
                                                @elseif($sensor->event_type!='equipment' &&$sensor->event_type!='inventory' )
                                                Today at {{date('H:i:s',strtotime($sensor->temeprature_last_updated))}}
                                                @endif

                                            @else
                                                @if($sensor->event_type!='temperature')
                                                Offline
                                                @else
                                                Last seen {{$sensor->temeprature_last_updated->diffForHumans()}}
                                                @endif
                                            @endif
                                        </p>
                                    </figcaption>
                                </div>
                     </div>

                    @if($sensor->event_type=='ccon')
                    <div class="row p-2 p-sm-3">
                        <div class="col-md-6">
                    @endif

                            <div class="mb-3">
                                <div class="mb-3">
                                    @if($sensor->event_type==='temperature')
                                    <label>
                                        Device Name
                                    </label>
                                    @elseif($sensor->event_type==='ccon')
                                    <label>
                                    Cloud Connector Name
                                    </label>
                                    @elseif($sensor->event_type==='equipment')
                                    <label>
                                    Equipment Name
                                    </label>
                                    @endif
                                    <input type="text" name="sensor_name" class="form-control" placeholder="Enter equipment name" value="{{$sensor->name}}" id="sensor_name"  >
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Description
                                    </label>
                                    <input type="text" name="sensor_description" class="form-control" value="{{$sensor->description}}" placeholder="Enter equipment description" id="sensor_description">
                                </div>
                                <div class="mb-3">
                                    <label>
                                        Specification
                                    </label>
                                    <input type="text" name="sensor_specification" class="form-control" value="{{$sensor->specification??''}}" placeholder="Enter equipment specification" id="sensor_specification">
                                </div>
                                <div class="mb-3">
                                    <div class=" mb-3">
                                        @if($sensor->event_type=='temperature')
                                        <label>
                                            Sensor ID
                                        </label>
                                        @elseif($sensor->event_type=='ccon')
                                        <label>
                                            Cloud Connector ID
                                        </label>
                                        @else
                                        <label>
                                            Equipment ID
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
                                    @if($sensor->event_type=='temperature')
                                    <div class=" mb-3">
                                        <label>
                                            Battery level – Updated {{isset($battery_updated_datetime)?$battery_updated_datetime:''}}
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <figure class="fig-30 mb-0 mr-3">
                                                <span class="fs-24">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="18" height="12" x="2" y="6" rx="2"/><path d="M7 10v4m4-4v4m4-4v4m5-4h1.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H20v-4Z"/></g></svg>
                                                </span>
                                            </figure>
                                            <span class="fw-500">{{$sensor->battery_level}}%</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                       


                                    <div class="mb-3">
                                        @if ($sensor->event_type!="inventory")
                                            
                                        <div class="mb-2">
                                            <a type="button" href="data:image/png;base64, {!! base64_encode(QrCode::format('png')->errorCorrection('H')->generate($sensor->device_id)) !!}" class="btn btn-default btn-qr" style="width: 204px;" download>
                                                <svg class="mr-2 fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M6 20q-.825 0-1.412-.587Q4 18.825 4 18v-3h2v3h12v-3h2v3q0 .825-.587 1.413Q18.825 20 18 20Zm6-4l-5-5l1.4-1.45l2.6 2.6V4h2v8.15l2.6-2.6L17 11Z"/></svg>Download QR Code
                                            </a>
                                        </div>
                                        @endif
                                        @if (!empty($CompanyAdminEmail))
                            @if ( $role2=='valid' )

                                    <div class="mb-2">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-support" style="width: 204px;">
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"></path></svg> Order Service
                                        </button>
                                        </div>
                            @endif
                        @endif
                                    </div>
                                    
                                    
                                </div>
                                <div id="load_message"></div>

                                @if(\Session::has('messageUpload'))
                                <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                    <strong>Success!</strong> {{\Session::get('messageUpload')}}</div>
                                @endif

                            </div>
                        </div>
                        @if($sensor->event_type=='ccon')
                        <div class="col-md-6">
                        </div>

                        @endif
                @if($sensor->event_type=='ccon')
                    </div>
                </div>
                @endif
                @endif
                @if($sensor->event_type!='inventory' )
                <div class="m-portlet panel-has-radius mb-4 p-4">
                <div class="row align-items-center">
                        
                    <div class="col-6">
                            <h4 class="m-0 fw-700">
                                Temperature Data
                            </h4>
                        </div>
                    <div class="col-6 d-flex justify-content-end">
                    @if(\Auth::user()->id==1 || (isset($role2) && $role2=='valid'))
                        @if($sensor->sensor_id ==0)
                        <button data-dismiss="modal" id="connection" class="btn btn-primary" data-toggle="modal" data-target="#connectionModal">Connect with Sensor</button>
                        @else
                        <button data-dismiss="modal" id="connection" class="btn btn-default" data-toggle="modal" data-target="#connectionCloseModal">Disconnect</button>
                        @endif
                        @endif
                    </div>
                </div>
                @if($sensor != '' && $sensor->sensor_id!=0  && $sensor->event_type!='ccon' && $connected_sensor!=null)

                <div class="bg-light-grey-2 p-2 p-sm-3 mb-4 rounded" style="margin-top: 10px;">
                        <div class="row d-flex align-items-center gutter-10">
                            <div class="col-sm-8 mb-3 mb-sm-0 col-7">
                                <div class="d-inline-flex align-items-center">

                                    <figure id="single_sensor_{{$connected_sensor->device_id}}" class="fig-60 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
                                        <span class="sensor_icon_mini" style="background-color: red;"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                                    </figure>
                                    <figcaption class="m-0">
                                        @if($connected_sensor->event_type=='temperature')
                                        <p class="m-0 fw-500 text-muted fs-12">
                                            Temperature Sensor | <strong>{{!empty($connected_sensor->name)?$connected_sensor->name:$connected_sensor->device_id}}</strong>
                                        </p>
                                        @elseif($connected_sensor->event_type=='ccon')
                                        <p class="m-0 fw-500 text-muted fs-12">
                                            Network Connection
                                        </p>
                                        @endif
                                        @if($connected_sensor->event_type=='temperature')
                                        <p class="m-0 main_value_1">
                                            @if(isset($connected_sensor->is_active) && $connected_sensor->is_active==1)
                                            {{isset($connected_sensor->temperature)?@number_format($connected_sensor->temperature,2):0}}°C
                                            @else
                                            Offline
                                            @endif
                                        </p>
                                        @else
                                        <p class="m-0 fs-22 fw-600">
                                            @if(isset($connected_sensor->temperature) && $connected_sensor->temperature!='')
                                            {{$connected_sensor->temperature}}
                                            @endif
                                        </p>
                                        @endif
                                        <p class="m-0 fw-500 text-muted fs-12">
                                            @if(isset($connected_sensor->is_active) && $connected_sensor->is_active==1)
                                                @if($connected_sensor->event_type=='ccon')
                                                Online
                                                @elseif($connected_sensor->event_type!='equipment')
                                                Today at {{date('H:i:s',strtotime($connected_sensor->temeprature_last_updated))}}
                                                @endif

                                            @else
                                                @if($connected_sensor->event_type!='temperature')
                                                Offline
                                                @else
                                                @php
                                                $temperatureLastUpdated = Carbon\Carbon::parse($connected_sensor->temeprature_last_updated);

                                                    // Now you can use the diffForHumans() method on the date/time object
                                                    $lastSeen = $temperatureLastUpdated->diffForHumans();
                                                @endphp
                                                Last seen {{$lastSeen}}
                                                @endif
                                            @endif
                                        </p>
                                    </figcaption>
                                </div>
                            </div>

                            @if($sensor->event_type=='equipment')
                            <div class="col-sm-4 col-5">
                             <div class="justify-content-between justify-content-sm-end d-flex align-items-center">
                           



							<div class="d-flex justify-content-end flex-column align-items-end" style="margin-left: auto;">
                                {{-- <div class="d-flex flex-column align-items-end"> --}}
                                    <div class="signal-indicator-icon-wrap">
                                        @if(isset($connected_sensor->is_active) && $connected_sensor->is_active==0 && $connected_sensor->event_type!='ccon')
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="color: rgb(237, 28, 36); vertical-align: -0.125em; transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify signal-indicator-icon-small" data-icon="akar-icons:circle-alert-fill"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"></path></svg>
                                       
                                        @endif
                                        <ul class="signal-indicator-bar-list">
                                            <?php
                                            $signal_div='';
                                            $active='active';
                                            if(isset($connected_sensor->is_active) && $connected_sensor->is_active==0){
                                                $active='';
                                            }
                                            if($connected_sensor->event_type=='ccon'){
                                                if($connected_sensor->is_active==1){
                                                ?><img src="../../public/assets/img_cellular.svg" alt=""> {{isset($connected_sensor->temperature)?$connected_sensor->temperature:''}}<?php
                                            }else{
                                                echo 'Offline';
                                            }

                                            }else{
                                                if($connected_sensor->signal_strength<=20){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connected_sensor->signal_strength>20 && $connected_sensor->signal_strength<=40) {
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connected_sensor->signal_strength>40 && $connected_sensor->signal_strength<=60){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connected_sensor->signal_strength>60 && $connected_sensor->signal_strength<=80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>';
                                                }elseif($connected_sensor->signal_strength>80){
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
                                    @if($sensor->event_type=='ccon' || $sensor->event_type=='temperature' || $sensor->event_type=='equipment')
                                    <div class="mt-2 fs-13 fw-600 text-uppercase connectivity-collapse theme-blue nowrap" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" style="cursor:pointer;">
                                        Connectivity <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1024"><path fill="currentColor" d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z"/></svg>
                                    </div>
                                    @endif
                                </div>
                                </div>
                            </div>
                            @endif
                        </div>
                           
                        @if($sensor->event_type=='ccon' || $sensor->event_type=='temperature' || $sensor->event_type=='equipment')
                        <div class="collapse" id="collapseExample">
                            <div class="mt-4">
                               
                                @if($sensor->event_type=='temperature' || $sensor->event_type=='equipment')
                                <h5 class="mt-3 mb-3">
                                    Seen by these Cloud Connectors
                                </h5>
                                @endif
                                 @if(isset($connectors) && count($connectors)>0)
                                    @foreach($connectors as $connector)
                                    @php
                                    /*if(isset($connector->is_active) && $connector->is_active==0){
                                        continue;
                                    }*/
                                    @endphp
                                <div class="mb-4 d-flex align-items-center connID" id="connID2-{{isset($connector->device_id)?$connector->device_id:''}}">

                                    <div class="mr-3 d-inline-flex">
                                        <img src="{{asset('public/assets/demo/default/media/img/misc/img_tiny_sensor.svg')}}" alt="Icon" class="img-fluid mr-2">
                                        <div class="signal-indicator-icon-wrap">
                                            <ul class="signal-indicator-bar-list">
                                                @php
                                                $active='active';
                                                if(isset($connector->is_active) && $connector->is_active==0){
                                                    $active='';
                                                }
                                                $signal_div='';
                                                if($connector->signal_strength<=20){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connector->signal_strength>20 && $connector->signal_strength<=40) {
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connector->signal_strength>40 && $connector->signal_strength<=60){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($connector->signal_strength>60 && $connector->signal_strength<=80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>';
                                                }elseif($connector->signal_strength>80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>';
                                                }
                                                @endphp
                                                {!!$signal_div!!}
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="d-inline-flex align-items-center">
                                        <figure class="m-0 mr-2">
                                            <img src="{{asset('public/assets/demo/default/media/img/misc/img_ccon.svg')}}" alt="Icon" class="img-fluid">
                                        </figure>
                                        <div>
                                            <div class="">
                                                <a href="{{url('sensor-details/'.$company_id.'/'.$connector->device_id)}}" class="no-decoration">{{$connector->name??''}}</a> {{-- <span class="iconify ml-2" data-icon="zondicons:travel-case"></span> --}}
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span id="p_{{$connector->device_id??''}}" class="copyable mr-2">
                                                    {{$connector->device_id??''}}
                                                </span>
                                                <button onclick="copyToClipboard('#p_{{$connector->device_id??''}}')" type="button" class="btn btn-sm btn-default">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"/><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"/></svg> Copy
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
                                $colors=['#2B4B5D','#74A7C6','#8B635C','#DB624D'];
                                $lp=0;
                                $key =0;
                                @endphp
                                    @if(isset($available_ccon) && count($available_ccon)>0)

                                    @foreach($available_ccon as $connector)
                                    @if(isset($key) && $key >5)
                                    @php
                                    $lp=0;
                                    @endphp
                                    @endif
                                    @php
                                    $uniqueName = $colors[$lp];
                                    $uniqueName = str_replace('#','',$uniqueName);
                                   
                                    @endphp
                                <div class="mb-2 d-flex align-items-center connID" style="display: none;" id="connID-{{$connector}}">
                                @php
                                $device = \App\Device::where('device_id',$connector)->first();
                                @endphp
                                    <span class="signal-dot mr-2" style="background-color: {{$colors[$lp]}};"></span>
                                    <a href="#" class="no-decoration">{{isset($device)?$device->name:$connector}}</a>

                                    <span class="fs-16 fw-600 mx-3 g-value" id="signal-{{$uniqueName}}">--</span>
                                    <div class="signal-indicator-icon-wrap indicator-small">
                                        <ul class="signal-indicator-bar-list" id="signal-indicator-bar-list-{{$uniqueName}}">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="btn-group bg-light-grey p-2 border-radius-1 d-flex align-items-center flex-wrap" role="group" aria-label="Basic example">
                                        <label class="mb-0 mr-3 ml-2 fw-400">
                                            Zoom range
                                        </label>
                                      <button type="button" class="btn btn-default btn-sm range-btn-ccon" data-val="hour">Hour</button>
                                      <button type="button" class="btn btn-default btn-sm range-btn-ccon" data-val="day">Day</button>
                                      <button type="button" class="btn btn-default btn-sm range-btn-ccon radio-active" data-val="week">Week</button>
                                      <button type="button" class="btn btn-default btn-sm range-btn-ccon" data-val="month">Month</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                <div class="p-2 p-sm-3 mb-4 ">
                        <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        @if($connected_sensor->event_type=='temperature')
                                        <label>
                                            Sensor ID
                                        </label>
                                        @elseif($connected_sensor->event_type=='ccon')
                                        <label>
                                            Cloud Connector ID
                                        </label>
                                        @else
                                        <label>
                                            Equipment ID
                                        </label>
                                        @endif
                                        <div class="d-flex align-items-center">
                                         <span id="p2" class="copyable mr-2">
                                                {{$connected_sensor->device_id??''}}
                                            </span>
                                            <button onclick="copyToClipboard(this)" type="button" class="btn btn-sm btn-default copy-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"></path><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"></path></svg> Copy
                                            </button>
                                        </div>
                                    </div>
                                    @if($connected_sensor->event_type=='temperature')
                                    <div class="col-sm-6 mb-3">
                                        <label>
                                            Battery level – Updated {{isset($battery_updated_datetime)?$battery_updated_datetime:''}}
                                        </label>
                                        <div class="d-flex align-items-center">
                                            <figure class="fig-30 mb-0 mr-3">
                                                <span class="fs-24">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect width="18" height="12" x="2" y="6" rx="2"/><path d="M7 10v4m4-4v4m4-4v4m5-4h1.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H20v-4Z"/></g></svg>
                                                </span>
                                            </figure>
                                            <span class="fw-500">{{$connected_sensor->battery_level}}%</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                    @if($sensor->device_id!='c3mj37j8crq000984bf0')
                    <p id="toolTipValue"></p>
                    <div class="my-3" id="master-container">
                        <div class="isloading" style="position:relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                        </div>
                    </div>
                    @endif
                    </div>
                    @if($sensor->event_type=='temperature' && $sensor->device_id=='c3mj37j8crq000984bf0')
                    <p id="toolTipValue2"></p>
                    <div class="my-3" id="Gen2-container">
                        <div class="isloading" style="position:relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                        </div>
                    </div>
                    @endif
                    <div class="d-flex justify-content-end">
                        <div class="btn-group bg-light-grey p-2 border-radius-1 d-flex align-items-center flex-wrap" role="group" aria-label="Basic example">
                            <label class="mb-0 mr-3 ml-2 fw-400">
                                Zoom range
                            </label>
                         <!--  <button type="button" class="btn btn-default btn-sm range-btn" data-val="5min">5 min</button> -->
                          <button type="button" class="btn btn-default btn-sm range-btn" data-val="hour">Hour</button>
                          <button type="button" class="btn btn-default btn-sm range-btn" data-val="day">Day</button>
                          <button type="button" class="btn btn-default btn-sm range-btn radio-active" data-val="week">Week</button>
                          <button type="button" class="btn btn-default btn-sm range-btn" data-val="month">Month</button>
                        </div>
                    </div>

                @endif
                </div>
             @endif                    

                <!--Ends Temperature Data-->



                <div class="m-portlet panel-has-radius mb-4 custom-p-5">

                    @if(\Session::has('resourceMessage'))
                                <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                                    <strong>Success!</strong> {{\Session::get('resourceMessage')}}</div>
                                @endif
                    <div class="row align-items-center mb-3 isDefault">
                        <div class="col-6">
                            <h4 class="m-0 fw-700">
                               <strong> Resources </strong>
                            </h4>
                        </div>
                        
                        <div class="col-6 d-flex justify-content-end">
                            <button data-dismiss="modal" id="resources" class="btn btn-primary" data-toggle="modal" data-target="#chooseModals">Add Resource</button>
                        </div>
                        
                    </div>
                    <div class="table-responsive table-borderless">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="60%">Name</th>
                                    <th width="20%">Added Date</th>
                                    <th width="20%" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            @php
                                $id_device = $sensor->id??'';
                                $docs_devices = \App\DeviceDocument::where('device_id',$id_device)->get();
                            @endphp
                            <tbody>
                                @if(isset($docs_devices) && count($docs_devices)>0)
                                @foreach ($docs_devices as $docs_device)
                                <tr>
                                    <td>
                                    @php
                                            $fileExtension = pathinfo($docs_device->url, PATHINFO_EXTENSION);
                                        @endphp
                                        @if ($fileExtension === 'pdf')

                                            <a href="#" onclick='openModal("{{ asset("storage/app/public/$docs_device->url") }}", "pdf")'><span class="fw-600">{{(isset($docs_device->name) && $docs_device->name!='')?$docs_device->name:$docs_device->url}}</span></a>

                                        @elseif (in_array($fileExtension, ['png', 'jpg', 'jpeg', 'gif']))
                                            <a href="#" onclick='openModal("{{ asset("storage/app/public/$docs_device->url") }}", "image")'><span class="fw-600">{{(isset($docs_device->name) && $docs_device->name!='')?$docs_device->name:$docs_device->url}}</span></a>
                                        @else
                                         <a href="#" onclick='openModal("{{ asset("storage/app/public/$docs_device->url") }}", "pdf")'><span class="fw-600">{{(isset($docs_device->name) && $docs_device->name!='')?$docs_device->name:$docs_device->url}}</span></a>
                                        @endif
                                    </td>
                                    <td>{{ date('F d, Y', strtotime($docs_device->created_at)) }}</td>
                                    <td class="text-right">
                                        
                                        <span class="miniIcon">
                                            <i data-url="{{route('companies.deleteDoc',['id'=>$docs_device->id])}}" data-id="{{$docs_device->id}}" class="deleteDoc la la-trash"></i>
                                        </span>
                                      
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="3" style="text-align: center;">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
        <div class="modal fade" id="ImageModal" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2" aria-hidden="true">
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
                
                <div class="m-portlet panel-has-radius mb-4 custom-p-5">
                        @if(\Session::has('noteSaved'))
                        <div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
                            <strong>Success!</strong> {{\Session::get('noteSaved')}}</div>
                        @endif
                    <div class="row isDefault align-items-center mb-3">
                        <div class="col-6">
                            <h4 class="m-0 fw-700">
                               <strong> Notes </strong>
                            </h4>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <button data-dismiss="modal" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-doc2">Add Notes</button>
                        </div>
                    </div>
                    
                   <div class="table-responsive table-borderless">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="60%">Name</th>
                                    <th width="20%">Added Date</th>
                                    <th width="20%" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            @php
                                $id_device = $sensor->id??'';
                                $devices_notes = \App\Note::where('device_id',$id_device)->get();
                            @endphp
                            <tbody>
                                @if(isset($devices_notes) && count($devices_notes)>0)
                                @foreach ($devices_notes as $note)
                                <tr>
                                    <td>
                                        <a href="#" onclick="openModal('{{ route('companies.ViewNote',[$company_name, $note->id]) }}', 'pdf')" style="color: black;">
                                            <span class="fw-600">{{(isset($note->name) && $note->name!='')?$note->name:$note->url}}</span>
                                        </a>
                                    </td>
                                    <td>{{  date('F d, Y', strtotime($note->created_at))  }}</td>
                                    <td class="text-right">
                                        @if(\Auth::user()->id!=1)
                                         <span class="miniIcon">
                                         <a href="#" onclick="openModal('{{ route('companies.ViewNote',[$company_name, $note->id]) }}', 'pdf')" style="color: black;">
                                            <i class="la la-eye" ></i>
                                        </span>
                                        </a>
                                        @endif
                                        @if(\Auth::user()->id==1 || \Auth::user())
                                         <span class="miniIcon">
                                            <i data-url="{{route('companies.deleteNote',['id'=>$note->id])}}" data-id="{{$note->id}}" class="deletenote la la-trash"></i>
                                        </span>
                                        <span class="miniIcon">
                                            <i data-dismiss="modal" class="editnote la la-edit" data-toggle="modal" data-id="{{$note->id}}"  data-target="#modal-edit-note"></i>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                 @endforeach
                                 @else
                                <tr>
                                    <td colspan="3" style="text-align: center;">No record found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

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
.scannerBody #camera-select2:focus ,
.scannerBody #camera-select2:focus-visible {
    outline: none;
}    
</style>

                <!--begin2::Modal-->
<div class="modal fade" id="modal-add-doc2" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocModalLabel2">Add Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.uploadSensorNotes')}}" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
                    <input type="hidden" name="company_id" value="{{isset($sensor->company_id)?$sensor->company_id:''}}">
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

<div class="modal fade" id="modal-edit-note" tabindex="-1" role="dialog" aria-labelledby="addDocModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocModalLabel2">Update Notes</h5>
        
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('companies.editNote')}}" enctype="multipart/form-data">
                <input type="text" name="note_id" id="note_id" hidden>
                    @csrf
                    <div id="modal-edit">
                    <div class="mb-3">
                        <label>
                            Name
                        </label>
                        <input required type="text" class="form-control" name="name" id='note-name' >
                    </div>
                    <div class="mb-3">
                        @if (\Auth::user()->id==1)
                        <label>
                            Write Notes
                        </label> 
                        @else
                        <label>
                            Notes
                            @endif
                            </label> 

                        <div class="form-group">
                            <div class="input-group mb-3">
                                <textarea class="form-control" name="notes" id="notes-area" cols="30" rows="10"></textarea>
                            </div>
                        </div>

                    </div>
                    </div>
                    {{-- @if (\Auth::user()->id==1) --}}
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
<div class="modal fade" id="modal-delete-note" tabindex="-1" role="dialog" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNoteModalLabel">Delete Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('companies.deleteNote')}}" enctype="multipart/form-data">
                @csrf
                    <input type="hidden" name="sID" value="{{isset($sensor->device_id)?$sensor->device_id:''}}">
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
    box-shadow: 2px 2px 4px rgba(0,0,0,.1);
    background-color: #207da9;
    color: #fff;
}
</style>
                                <?php

                                $labels_json = isset($sensor->labels_json)?$sensor->labels_json:'';
                                if($labels_json!=''){
                                    $jsonDecode = json_decode($labels_json, true);
                                }
                                ?>
                                @if(isset($jsonDecode['Email']))
                                <tr>
                                    <td>
                                        Email
                                    </td>
                                    <td>
                                        <input type="text" name="email" class="form-control" value="{{$jsonDecode['Email']}}">
                                    </td>
                                    <td align="center">
                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                    </td>
                                </tr>
                                @endif
                                @if(isset($jsonDecode['MaxTempTrigger']))
                                <tr>
                                    <td>
                                        MaxTempTrigger
                                    </td>
                                    <td>
                                        <input type="text" name="email" class="form-control" value="{{$jsonDecode['MaxTempTrigger']}}">
                                    </td>
                                    <td align="center">
                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                    </td>
                                </tr>
                                @endif
                                @if(isset($jsonDecode['MinTempTrigger']))
                                <tr>
                                    <td>
                                        MinTempTrigger
                                    </td>
                                    <td>
                                        <input type="text" name="email" class="form-control" value="{{$jsonDecode['MinTempTrigger']}}">
                                    </td>
                                    <td align="center">
                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                    </td>
                                </tr>
                                @endif
                                @if(isset($jsonDecode['Notify']))
                                <tr>
                                    <td>
                                        Notify
                                    </td>
                                    <td>
                                        <input type="text" name="email" class="form-control" value="{{$jsonDecode['Notify']}}">
                                    </td>
                                    <td align="center">
                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                    </td>
                                </tr>
                                @endif
                                @if(isset($jsonDecode['kit']))
                                <tr>
                                    <td>
                                        kit
                                    </td>
                                    <td>
                                        <input type="text" name="email" class="form-control" value="{{$jsonDecode['kit']}}">
                                    </td>
                                    <td align="center">
                                        <span class="iconify fs-18" data-icon="clarity:remove-solid" style="color: #f4516c;"></span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!--End::Section-->

    </div>
</div>


@endsection
@push('scripts')
<script type="text/javascript" src="{{ asset('public/crud/forms/widgets/bootstrap-select.js') }}"></script>



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
</script>
<script>


     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    $('.move_sensor_button').on('click', function() {
                let company_name = $('#transfer_sensor').val();
                let comp_ID = $('#comp_ID').val();
                let company_id = $('#selected_company_id').val();

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
                        // beforeSend:function(){
                        //     $('#search-loader').show();
                        // },
                        success: function(data) {
                            console.log(data);
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

    $(document).on('click','#move_sensor_button2',function(){
       $.ajax({

        url: '{{route("companies.moveSensor2")}}',
        type: 'POST',
            dataType: 'JSON',
        data: $('#move_sensor_form2').serialize(),
        beforeSend:function(){
            $('#search-loader').show();
        },
        success: function(data){
            if(data.success && data.success==true){
                window.location.href = '{{route('sensors',['company_id'=>$company_id])}}';
                // window.location.href = data.url;
            }
            /*$('#loadCompaniesList').html('');
            $('#loadCompaniesList').append(data);*/
            $('#search-loader').hide();
        }
    });
    });



    var device_id = "{{$connected_sensor->device_id??''}}";
    var company_id = "{{$parent_company??''}}";
    $('.range-btn-ccon').on('click',function(){
        $('.range-btn-ccon').removeClass('radio-active');
        $(this).addClass('radio-active');
        var val = $(this).attr('data-val');
        console.log('val'+val);
        loadGraph(val);
    });
    loadGraph('week');
    // Initiate the Pusher JS library
      var pusher = new Pusher('ece81d906376bc8c0bab', {
        cluster: 'ap2',
        encrypted: true
      });


      // var company_id = '{{$parent_company??''}}';
      var current_company = '{{$currentCompany->company_id??''}}';
      // Subscribe to the channel we specified in our Laravel Event
      var channel = pusher.subscribe('my-channel.'+device_id);

      // Bind a function to a Event (the full Laravel class)
      channel.bind('App\\Events\\HelloPusherEvent', function(data) {
        if(data.data){
            loadGraph('week');
            console.log('Pusher = ',data.data);
        }
      });





      var channel2 = pusher.subscribe('my-channel-project.'+current_company);

      // Bind a function to a Event (the full Laravel class)
      channel2.bind('App\\Events\\TouchEvent', function(data) {
      	console.log(data);
        if(data.data && data.data.event_type && data.data.event_type=='touch'){
        	console.log(data.data.deviceId);
            console.log('inside ', $('#sensor_'+data.data.deviceId));
            $('#sensor_'+data.data.deviceId).addClass('pulse-button');
            $('#single_sensor_'+data.data.deviceId).addClass('pulse-button');
            setTimeout(function(){
                $('#sensor_'+data.data.deviceId).removeClass('pulse-button');
                 $('#single_sensor_'+data.data.deviceId).removeClass('pulse-button');
            },2000);
              let device_exist=false;
            @foreach($sensors as $sensorr)
            var device_id = "{{$sensorr->device_id??''}}";
             if(data.data.deviceId==device_id){
             	device_exist=true;
             }
            @endforeach
            console.log(device_exist);
            if($('#m_modal_2').is(':visible')== true && device_exist==true){
            	window.location.href = '{{url('sensor-details')}}/'+current_company+'/'+data.data.deviceId;
            }
            console.log('Pusher = ',data.data);
        }
      });
    function loadGraph(val){
        if(val!=''){
            val = '/'+val;
        }
        @php
        $dataURL = url("history-details-ccon2");
        if($sensor != ''){
        if($sensor->event_type=='ccon'){
            $dataURL = url("history-details-ccon2");
        }else{
            $dataURL = url("history-details-ccon2");
        }}

        @endphp
        Highcharts.getJSON('{{$dataURL}}'+'/'+device_id+val, function (data) {
            $('.connID').attr('style','display:none !important');
            
            if(data && data.availed){
                      
                    $.each(data.availed, function(index, el) {
                        
                        $('#connID-' + el).removeAttr('style');
                        $('#connID2-' + el).removeAttr('style');
                        
                    });
            }
             if(data && data.availedColors){
                $.each(data.availedColors,function(index,el){
                    $('#connID-'+el.id).find('span.signal-dot').css('background-color',el.color);
                    // $('#connID2-'+el).removeAttr('style');
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
                                showLastLabel:true,
                                labels: {
                                    formatter: function() {
                                        return this.value+'%';
                                    }
                                }
                            },
                rangeSelector: {
                        enabled:false
                },
                series: data.data,
                tooltip:{
                    formatter : function(abc){
                        // console.log('abc',abc);
                        var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);
                        var html = dateVl;
                        $("#signal_strength").html(parseInt(this.y)+'%');
                         // var html = this.y.toFixed(0)+'% on '+dateVl;
                         var html = dateVl;
                        $("#toolTipValueCcon").html(html);
                        var s='';
                        $("#signal-2B4B5D,#signal-74A7C6,#signal-8B635C,#signal-DB624D").html('----');

                        this.points.forEach((el, index) => {
                            console.log('el',el);
                            // console.log('el_x',el.plotX);
                            // console.log('el_y',el.plotY);
                            var uniqueName = el.color.replace('#','');
                            var signal = get_signal(el.y.toFixed(0));
                            $("#signal-"+uniqueName).html(el.y.toFixed(0)+'%');
                            $('#signal-indicator-bar-list-'+uniqueName).html(signal);
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

    function get_signal(signal){
        var signal_div='';
        var active = 'active';
        if(signal<=20){
            signal_div = '<li class="'+active+'"></li>\
                           <li class=""></li>\
                           <li class=""></li>\
                           <li class=""></li>\
                           <li class=""></li>';
        }else if (signal>20 && signal<=40) {
            signal_div = '<li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class=""></li>\
                           <li class=""></li>\
                           <li class=""></li>';
        }else if(signal>40 && signal<=60){
            signal_div = '<li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class=""></li>\
                           <li class=""></li>';
        }else if(signal>60 && signal<=80){
            signal_div = '<li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class=""></li>';
        }else if(signal>80){
            signal_div = '<li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>\
                           <li class="'+active+'"></li>';
        }else{
            signal_div='';
        }

        return signal_div;
    }
    


    
    
   $(document).on('click','#identify_touch_sensor',function(){
    		 $('.touch_header').removeClass('d-none');
    		 $('.sensor_touch_info').addClass('d-none');
    		 $('.try_again').addClass('d-none');
    		 // isSearching=true;
    		 setTimeout(function(){
                $('.sensor_touch_info').removeClass('d-none');
                $('.touch_header').addClass('d-none');
                $('.try_again').removeClass('d-none');
                // isSearching=false;
            },15000);
    	});

    	$(document).on('click','.try_again',function(){
    		 $('.sensor_touch_info').addClass('d-none');
    		 $('.try_again').addClass('d-none');
    		  $('.touch_header').removeClass('d-none');
    		  // isSearching=true;
    		   setTimeout(function(){
                $('.sensor_touch_info').removeClass('d-none');
                $('.touch_header').addClass('d-none');
                $('.try_again').removeClass('d-none');
                // isSearching=false;
            },15000);
    	});
</script>

<script >
	let claim_devices_list=[],device_counter=0;
	const videoo = document.getElementById('qr-video');
	let alreadyClaimed=1;

	function setResultt(label, result) {
        console.log(result.data);


        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        // clearTimeout(label.highlightTimeout);
        // label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);

       deviceid=  result.data;

	let company_id="{{$company_id??''}}";

	setTimeout(function(){
                claimSensor(deviceid);
            },2000);
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
        QrScanner.scanImage(file, { returnDetailedScanResult: true })
            .then(result => setResult(fileQrResult, result))
            .catch(e => setResult(fileQrResult, { data: e || 'No QR code found.' }));
    });

    $(document).on('click','.q_r_scan_btn',function(){
    	$('.q_r_scan_table').removeClass('d-none');
    	$('.claim_qr_scanner').removeClass('d-none');
    	$('.q_r_scanner_header').addClass('d-none');
    	$('.q_r_scan_image').addClass('d-none');
        $('.claim_input').val('');


    });
    $(document).on('click','.close_scanner ',function(){
    	if(device_counter==0){
    		$('.q_r_scan_table').addClass('d-none');
    		$('.q_r_scan_image').removeClass('d-none');
    	}

    	$('.claim_qr_scanner').addClass('d-none');
    	$('.q_r_scanner_header').removeClass('d-none');

    	scannerr.stop();
        $('.kit_id').click();
    });

    $(document).on('click','.kit_id',function(){
    	$(this).addClass('active');
    	$('.devi_id').removeClass('active');
    	$('.devi_id').removeClass('btn-primary btn-default');
    	$('.claim_input').attr('placeholder','E.g. ABC-42-DEF');

    	$(this).addClass('btn-primary btn-default');
    	$('.addToList').text('Add Kit to List');
    });
     $(document).on('click','.devi_id',function(){
    	$(this).addClass('active');
    	$(this).addClass('btn-primary btn-default');

    	$('.claim_input').attr('placeholder','E.g. b6sfpst7rihg0dm4v01g');
    	$('.kit_id').removeClass('active');
    	$('.kit_id').removeClass('btn-primary btn-default');

    	$('.addToList').text('Add Device to List');

    });

     $(document).on('click','.addToList',function(){

     	let devi_id=$('.claim_input').val();


     	claimSensor(devi_id);
        $('.claim_input').val('');
     });

     $(document).on('blur','.claim_input',function(){
     	let val=$(this).val();

     	if(val!=''){


     	let firtst_dash = val.charAt(3);
		let second_dash = val.charAt(6);
		if(firtst_dash=='-' && second_dash=='-'){
			$('.kit_id').addClass('active');
    	$('.devi_id').removeClass('active');
    	$('.devi_id').removeClass('btn-primary btn-default');

    	$('.kit_id').addClass('btn-primary btn-default');
    	$('.addToList').text('Add Kit to List');
		}
		else{

		$('.devi_id').addClass('active');
    	$('.devi_id').addClass('btn-primary btn-default');
    	$('.kit_id').removeClass('active');
    	$('.kit_id').removeClass('btn-primary btn-default');

    	$('.addToList').text('Add Device to List');
		}
	   }
     });


    $(document).on('click','.removeDevice',function(){
			let device_id=$(this).attr('deviceId');
			console.log(device_id);
            $('.claim_input').val('');

			$('.list-'+device_id).remove();
			console.log(claim_devices_list.length);
			 let index = claim_devices_list.indexOf(device_id);
			 delete claim_devices_list[index];
			 device_counter--;
			 console.log(claim_devices_list.length);
			 if(device_counter==0){

			 	$('.q_r_scan_table').addClass('d-none');
				$('.q_r_scan_image').removeClass('d-none');
				$('.claimSubmitBtn').prop('disabled',true);
                alreadyClaimed=1;
			 }
			 console.log(claim_devices_list);
		});


            function claimSensor(deviceid){

            	 $.ajax({

			        url: '{{route("claim.sensor")}}',
			        type: 'POST',
			            dataType: 'JSON',
			        data: {
			        	device_id:deviceid,

			        },

			        success: function(data){
			        	console.log(data);
			        	console.log($.inArray(deviceid, claim_devices_list));
						if(data.error!=true){
							$('.q_r_scan_table').removeClass('d-none');
							$('.q_r_scan_image').addClass('d-none');
							if($.inArray(deviceid, claim_devices_list)==-1){

    							claim_devices_list.push(deviceid);
    							device_counter++;
    							console.log(claim_devices_list);
    			        	    $('.claim_sensor_list').append(data.claim_html);
                                if(data.response.type=='DEVICE'){
                                    if(data.isClaimed!=true){

                                        alreadyClaimed=0;
                                    }
                                }else{
                                    if(data.claimedDevices!=data.response.kit.devices.length){
                                        alreadyClaimed=0;
                                    }
                                }

    						}

						}

                    if(data.error==true){
                        alert('Data Not Found');
                        $('.claimSubmitBtn').prop('disabled',true);
                    }
					if(alreadyClaimed==0){
						console.log(alreadyClaimed);
							$('.claimSubmitBtn').prop('disabled',false);

					}

			        },error: function(data) {
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
             suggestions += ' <li class="dropdown-item"><a class="dropdown-item" id="select_company" href="#" company-id="' + companies[i].company_id + '" role="option">' + companies[i].name + '</a></li>';
        }
      }

      $("#search_suggestions").html(suggestions);
    });

     $(document).ready(function(){
        $(document).on('click','.deleteDoc',function(e){
        var id = $(this).attr('data-id');
        $('#modal-delete-doc').modal('show');
        $('#modal-delete-doc form').attr('action',$(this).attr('data-url'));
    });

    $(document).on('click','.deletenote',function(e){
        var id = $(this).attr('data-id');
        $('#modal-delete-note').modal('show');
        $('#modal-delete-note form').attr('action',$(this).attr('data-url'));
    });
    $(document).on('click','#modal-edit-note',function(e){
        console.log(e);
        var id = $(this).attr('data-id');
    });
$(document).on('click', '.editnote', function(e) {
        console.log(e);
        var id = $(this).attr('data-id');
        console.log(id);
        $('#note_id').val(id);

                $.ajax({
                    type: 'get',
                    data: {
                        id: id,
                        
                    },
                    url: "{{ route('companies.viewNoteValue') }}",
                     success: function(response) {
                    var data = response.data;
                    $('#note-name').val(data.name);
                    $('#notes-area').val(data.notes);
                    console.log(data);

                    }
                });
                $('#modal-edit-note').modal('show');
            });

        var sensor_name = $('#sensor_name').val();
    var sensor_description = $('#sensor_description').val();
    var sensor_specification = $('#sensor_specification').val();
    
    $('#sensor_name').blur(function(){
        console.log('jihuihiuhiuhuih');
        var company_id ="{{$sensor->company_id??''}}"
        var device_id = "{{$sensor->device_id??''}}";
        var name = $('#sensor_name').val();
        var description = $('#sensor_description').val();
        if(sensor_name!=name && name!=''){
                   
            $.ajax({
                url:'{{url("updateSensorDetails")}}',
                method:'post',
                data:{device_id:device_id,name:name,company_id:company_id,'_token':'{{csrf_token()}}'},
                success:function(data){
                    $("#load_message").show();
                        $('#load_message').html('<div class="alert alert-success">Updated successfully</div>');

                    setTimeout(function() {
                    $("#load_message").hide('blind', {}, 500)
                    }, 2000);
                     if(name ==''){
                    $('#'+device_id).find('figcaption').text(device_id);
                    }else{
                    $('#'+device_id).find('figcaption').text(name);
                    }
                    $('.m_selectpicker').selectpicker('destroy');
                    var device = $('#chooseFileInput option:selected').text(name);
                    $('.m_selectpicker').selectpicker('show');

                }
            });
    
    }else{
      //  $("#load_message").show();
                       // $('#load_message').html('<div class="alert alert-danger">Equipment name can not be empty.</div>');
    }
    });
        $('#sensor_description').blur(function(){
        var company_id ="{{$sensor->company_id??''}}"
        var device_id = "{{$sensor->device_id??''}}";
        var description = $('#sensor_description').val();
        if(sensor_description!=description ){
                $.ajax({
                url:'{{url("updateSensorDetails")}}',
                method:'post',
                data:{device_id:device_id,description:description,company_id:company_id,'_token':'{{csrf_token()}}'},
                success:function(data){
                    $("#load_message").show();
                        $('#load_message').html('<div class="alert alert-success">Updated successfully</div>');

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

         $('#sensor_specification').blur(function(){
        var company_id ="{{$sensor->company_id??''}}";
        var device_id = "{{$sensor->device_id??''}}";
        var specification = $('#sensor_specification').val();
        if(sensor_specification!=specification ){

                $.ajax({
                url:'{{url("updateSensorDetails")}}',
                method:'post',
                data:{device_id:device_id,specification:specification,company_id:company_id,'_token':'{{csrf_token()}}'},
                success:function(data){
                    $("#load_message").show();
                        $('#load_message').html('<div class="alert alert-success">Updated successfully</div>');

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

$('#resources').on('click',function(){
$('.modal-backdrop').addClass('d-none');
});
            $(document).ready(function(){
                    $('.chooseFile').on('click', function(e) {
        console.log(e);
                var company_id ="{{$company_id}}";
                console.log(company_id);
                // e.preventDefault();

                $.ajax({
                    type: 'get',
                    data: {
                        company_id: company_id,
                    },
                    url: "{{ route('documents.in.resource') }}",
                    success: function(data) {
                        console.log(data);
                        $('#folder-table').html(data.html);
                    }
                });
            });
            });

   $('#connection').on('click', function(e) {
        console.log(e);
                var company_id ="{{$company_id}}";
                // e.preventDefault();

                $.ajax({
                    type: 'get',
                    data: {
                        company_id: company_id,
                    },
                    url: "{{ route('connect.with.sensor') }}",
                    success: function(data) {
                        console.log(data);
                        $('#connect-sensor-table').html(data.html);
                    }
                });
            });
               $(document).on('click', '.folders', function(e) {
                // alert('ihi');
                id = $(this).attr('folder_id');
                slug = $(this).attr('slug');
                name = $(this).attr('folder_name');
                $.ajax({
                    type: 'get',
                    data: {
                        id: id,
                        slug:slug
                    },
                    url: "{{ route('sub-documents.in.resource') }}",
                    success: function(data) {
                        console.log(data);
                        $('#folder-table').html(data.html);
                    }
                });
            });
             $(document).on('click', '#row', function(e) {
                name = $(this).attr('file_name');
                file = $(this).find(".sensor_doc").val();
                $('#sensor_file_name').val(name);
                id = $(this).attr('file_id');
                $('#sensor_file_id').val(id);
                $('#sensor_file').val(file);


            });
          $(document).on('click', '.sensorTable', function(e) {
            var radio = $(this).find('input[type="radio"]');
            var deviceId = $(this).attr('device_id');
            console.log(deviceId);
            radio.val(deviceId);
            radio.prop('checked', true);
            radio.trigger('change');
        });


         $('#transfer_sensor').on('focus',function(){
     
    $('#search_suggestions').css('display','block');
        
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
    $('#search_suggestions').css('display','none');

});

});

    </script>
@endpush
