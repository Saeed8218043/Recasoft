@extends('layouts.app')

@section('content')

<style type="text/css">
	.form-control-fancy + .select2-container--default .select2-selection--multiple {
		border-left: none;
		border-top: none;
		border-right: none;
		border-radius: 0;
	}
</style>


{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"> --}}
	{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" crossorigin="anonymous"> --}}
<div class="m-grid__item m-grid__item--fluid m-wrapper">

			<!-- BEGIN: Subheader -->
			<div class="m-subheader ">
				<div class="row">
					<div class="col-lg-4 col-md-4">
						<h4 class="mb-3 font-weight-bold">Notifications</h4>
						<p class="m-0 text-muted">
							Define sensor triggers and get notified
						</p>
					</div>						
				</div>
			</div>

			<!-- END: Subheader -->
		<div class="m-content">
					<form action="{{url('store/notification')}}" method="POST">
						@csrf
						<!--Begin::Section-->
						<div class="details_wrap">
							<div class="details_wrap_x">
								<div class="m-portlet panel-has-radius mb-4 p-4 portlet-height-1">
									<div class="mb-4">
										<a href="{{url('create-notification')}}/{{$company_id}} "  class="btn btn-primary btn-block">
											<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"></path></svg> Create new Notification
										</a>
									</div>
									<input type="hidden" name="company_id" value="{{$company_id}}">
									
									<h6 class="text-uppercase fw-500 p-3 m-0">
										Name
									</h6>
									<ul class="device_caption_list_minimal">
									@foreach(App\Notification::where('company_id',$company_id)->get() as $notificationData)
										{{-- @if($notificationn->id!=$notification->id) --}}
										<li class=" @if (isset($notificationData->isActive) && $notificationData->isActive == 0) is_disabled @endif">
																							
												<figcaption>
													<a href="{{route('notification.detail',['company_id'=>$company_id,'id'=>$notificationData->id])}}" style="text-decoration: none; color: black">{{$notificationData->name}}
													@if (isset($notificationData->isActive) && $notificationData->isActive == 0)
                                                    (Disabled)
                                                @endif</a>
												</figcaption>
											
										</li>
										{{-- @endif --}}
										@endforeach
									</ul>

								</div>
							</div>
							<div class="details_wrap_y">
								<p class="mb-2 px-3 px-md-0">
									<a href="{{url('notifications')}}/{{$company_id}}">
										<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M21 11H6.414l5.293-5.293l-1.414-1.414L2.586 12l7.707 7.707l1.414-1.414L6.414 13H21z"></path></svg> Back to full list
									</a>
								</p>
								@if(\Session::has('message'))
								{{-- <div class="alert alert-success">{{\Session::get('message')}}</div> --}}

								<div class="alert alert-success alert-dismissible fade show" role="alert">
								  {{\Session::get('message')}}
								  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
								    <span aria-hidden="true">&times;</span>
								  </button>
								</div>
								@endif

								

								<div class="m-portlet panel-has-radius mb-4 p-0">
									<div class="d-flex">
										<div class="col-9 border-right p-4">
											<input type="text" name="name" class="form-control is-invalid notification_name" placeholder="Notification Name" required>
										</div>
										<div class="col-3 p-4 text-center d-flex align-items-center justify-content-center">
											<div class="d-flex flex-column flex-sm-row align-items-center">
												<span class="mr-2">Enabled</span>
												<label class="switch m-0">
													<input type="checkbox" name="isActive" checked="">
													<span class="slider round"></span>
												</label>
											</div>
										</div>
									</div>
								</div>

								<div class="m-portlet panel-has-radius mb-4 p-4">
									<h5 class="mb-3">
										What should trigger a notification?
									</h5>
									<div class="row">
										<div class="col-lg-4 mb-3">
											<label class="d-block">
												When
											</label>
											<select class="form-control fw-600 " id="notification_type" name="alert_type">
												<option value="Temperature">Temperature</option>
												{{-- <option value="Proximity Status">Proximity Status</option>
												<option value="Touched">Touched</option>
												<option value="Humidity">Humidity</option>
												<option value="Water Detected">Water Detected</option>
												<option value="Motion">Motion</option>
												<option value="CO2">CO2</option>
												<option value="Desk Occupancy">Desk Occupancy</option> --}}
												<option value="Device Monitoring (Beta)">Device Monitoring (Beta)</option>
												<option value="Maintenance">Maintenance</option>
												
											</select>
										</div>
										<div class="col-lg-8">
											<div class="row isDefault">
												<div class="col-md-6 mb-3 temp_range_container">
													<label class="d-block">
														is
													</label>
													<select class="form-control fw-600 " id="range" name="temp_range" required>
														<option value="above">Above</option>
														<option value="below">Below</option>
													</select>
												</div>
												<div class="col-md-6 mb-3 date_container d-none">
													<label class="d-block">
														Select date
													</label>
													<input type="date" name="repeat_date" id="repeat_date" class="form-control fw-600" placeholder="select date">

												</div>
												<div class="col-md-3 mb-3 temp_upper_container">
													<label class="d-block">
														Upper (°C)
													</label>
													<input type="number" name="upper_celcius" id="upper_celcius_field" class="form-control fw-600" placeholder="Upper" required>
												</div>
												<div class="col-md-3 mb-3 d-none temp_lower_container">
													<label class="d-block">
														Lower (°C)
													</label>
													<input type="number" name="lower_celcius" id="lower_celcius_field" class="form-control fw-600" placeholder="Lower" >
												</div>
											</div>
										</div>
									</div>
									<!-- ends row -->

									<div class="row">
										<div class="col-lg-4 mb-3 delay_time">
											<label class="d-block">
												Send Notification After Delay <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 6a3.939 3.939 0 0 0-3.934 3.934h2C10.066 8.867 10.934 8 12 8s1.934.867 1.934 1.934c0 .598-.481 1.032-1.216 1.626a9.208 9.208 0 0 0-.691.599c-.998.997-1.027 2.056-1.027 2.174V15h2l-.001-.633c.001-.016.033-.386.441-.793c.15-.15.339-.3.535-.458c.779-.631 1.958-1.584 1.958-3.182A3.937 3.937 0 0 0 12 6zm-1 10h2v2h-2z"></path><path fill="currentColor" d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10s10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8s8 3.589 8 8s-3.589 8-8 8z"></path></svg>
											</label>
											{{-- <select class="form-control fw-600 send_alert_select" name="send_alert">
												<option value="Immediately">Immediately</option>
												<option value="After a delay">After a delay</option>
											</select> --}}

											<label class="d-block">
														Hours
											</label>
											<input type="number" name="delay_time" value="1" id="delay_time" class="form-control fw-600" placeholder="Enter hours" required>
										</div>

										<div class="col-lg-4 mb-3 repeater d-none">
										<label class="d-block">
														Repeat Every
													</label>
													<select class="form-control fw-600" id="repeater" name="repeater" required>
														<option value="7">Week</option>
														<option value="30">Month</option>
														<option value="365">Year</option>
														<option value="0">Do not repeat</option>

													</select>
										</div>
										{{-- <div class="col-lg-8">
											<div class="row isDefault">
												<div class="col-md-6 mb-3">
													<label class="d-block">
														Hours
													</label>
													<input type="number" name="delay_time" class="form-control fw-600" placeholder="0" >
												</div>
											</div>
										</div> --}}
									</div>
									<!-- ends row -->

									<div class="text-muted ">Notifications will only be sent if the trigger remains active for the given delay.</div>
									
 								
                                <div class="row mt-3">
                                    <div class="col-lg-4 mb-3">
                                        <div class="reminder">
                                            <div class="">
                                                <label class="d-block">
                                                    Reminder
                                                </label>
                                                <select class="form-control" id="reminder" name="reminder">
                                                    <option value="">No reminder</option>
                                                    <option value="3">
                                                        3 days</option>
                                                    <option value="7">
                                                        7 days</option>
                                                </select>
                                            </div>
                                            <div class="text-muted">Note! The reminders will only be sent once on emails.</div>
                                        </div>
                                    </div>
                                </div>
									 <div class="resolved d-none" style="margin-bottom: 10px;margin-top: 15px;margin-left: -13px;">
                                <div class="notification-option" style="display: inline-block;float: left; margin: 20px;">

                                    <label class="switch m-0">
                                        <input type="checkbox" name="isResolved"
                                            class="notification-option"
                                            style="display: inline-block;float: left; margin: 20px;">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="option-text" style="display: inline-block;margin-top: 11px;">
                                    <strong class="option-title" style="margin-bottom: 1px;">Resolved
                                        Notification</strong>
                                    <p class="option-description text-muted" style="margin-bottom: 2px;">Receive a
                                        notification when the trigger is resolved.</p>
                                </div>
                            </div>

								</div>

								<div class="mb-5 text-center">
									<button type="button" class="btn btn-default shadow addDevice first-device-button" companyId="{{$company_id??'0'}}" data-toggle="modal" data-target="#m_modal_1">
										<svg class="text-primary fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"></path></svg> Add equipment
									</button>
								</div>

								<div class="m-portlet panel-has-radius mb-4 p-4 devices_container d-none">
									<div class="d-flex align-items-center mb-3">
										<h5 class="mb-0 mr-3 d-inline-block">
											Devices
										</h5>
										<button type="button" class="btn btn-default shadow addDevice ml-auto" companyId="{{$company_id??'0'}}" data-toggle="modal" data-target="#m_modal_1">
											<svg class="text-primary fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"></path></svg> Add equipment
										</button>
									</div>

									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th>
														Name	
													</th>
													 <th>
                                                CONNECTED SENSOR
                                            		</th>
													<th class="text-center">
														Trigger
													</th>
													<th width="10%" class="text-center">
														Remove
													</th>
												</tr>
											</thead>
											<tbody id="added_devices_container">



												{{-- <tr>
													<td>
														<a href="#">
															02/Pizza fridge
														</a>
													</td>
													<td>
														Not Triggering
													</td>
													<td class="text-center">
														---
													</td>
													<td class="text-center">
														<svg class="fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36"><path fill="#207da9" d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z" class="clr-i-solid clr-i-solid-path-1"></path><path fill="none" d="M0 0h36v36H0z"></path></svg>
													</td>
												</tr> --}}

											</tbody>
										</table>
									</div>
								</div>

								<div class="email_container">
									<input type="hidden" id="email_counter" value="0">
									

								</div>
								<div class="sms_container">
									<input type="hidden" id="sms_counter" value="0">
									

								</div>

								


								<div class="text-center addAction_mb">
									<div class="btn-group">
										<button type="button" class="btn btn-default shadow dropdown-toggle add_action" disabled data-toggle="dropdown" aria-expanded="false">
											<svg class="text-primary fs-18 mr-1" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"></path></svg> Add Action
										</button>
										<div class="dropdown-menu dropdown-menu-right">
											<button class="dropdown-item btn sendEmail" type="button"><svg class="mr-3" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><path fill="currentColor" d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414L.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"></path></svg>Send Email</button>

											<button class="dropdown-item btn sendSMS" type="button"><svg class="mr-3 fs-16" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="m8.5 18l3.5 4l3.5-4H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h3.5zM7 7h10v2H7V7zm0 4h7v2H7v-2z"></path></svg>Send SMS</button>
										</div>
									</div>
								</div>

								

								<!-- Submission Box -->
								<div class="notification_submission_box m-portlet panel-has-radius shadow d-flex align-items-center">
									<div class="border-right p-3">
										<button class="btn btn-default save_notification" Style="color:black !important" disabled type="submit">
											Save Notification
										</button>
									</div>
									<div class="p-3">
										<button type="button" class="btn btn-light d-flex align-items-center cancel_btn">
											<svg class="text-primary fs-18 mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M19.1 4.9C15.2 1 8.8 1 4.9 4.9S1 15.2 4.9 19.1s10.2 3.9 14.1 0s4-10.3.1-14.2zm-4.3 11.3L12 13.4l-2.8 2.8l-1.4-1.4l2.8-2.8l-2.8-2.8l1.4-1.4l2.8 2.8l2.8-2.8l1.4 1.4l-2.8 2.8l2.8 2.8l-1.4 1.4z"></path></svg> Cancel
										</button>
									</div>
								</div>
								<!-- Submission Box ends -->
							</div>
							<!-- ends .gotLine -->
						</div>
					</form>
					</div>
		</div>

                
			<!--begin::Modal-->
		{{-- <div class="modal fade" id="m_modal_4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Companies</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>

							<div class="d-flex">
								<div class="input-group mb-3 mr-3">
								  <div class="input-group-prepend">
								    <button class="btn btn-outline-secondary dropdown-toggle color-7" type="button" data-toggle="dropdown" aria-expanded="false">Company</button>
								    <div class="dropdown-menu">
								      <a class="dropdown-item" href="#">Dropdown item</a>
								      <a class="dropdown-item" href="#">Dropdown item</a>
								      <a class="dropdown-item" href="#">Dropdown item</a>
								      <a class="dropdown-item" href="#">Dropdown item</a>
								    </div>
								  </div>
								  <input type="text" class="form-control" aria-label="Search" placeholder="Search">
								</div>
								<div class="mb-3">
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#m_modal_3" data-dismiss="modal">
										<i class="la la-plus-circle"></i>	New Company
									</button>
								</div>
							</div>

							<table class="table sensors-popup-table">
								<thead>
									<tr>
										<th>#</th>
										<th>Company Name</th>
										<th class="text-center">Cloud Connectors</th>
										<th class="text-center">Sensors</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th scope="row">1</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
									<tr class="m--bg-danger bg-op-15">
										<th scope="row">2</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
									<tr>
										<th scope="row">3</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
									<tr>
										<th scope="row">4</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
									<tr>
										<th scope="row">5</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
									<tr>
										<th scope="row">6</th>
										<td>Lorem Ipsum<br/><small>React technologies</small></td>
										<td align="center">4</td>
										<td align="center">8</td>
									</tr>
								</tbody>
							</table>

						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div> --}}


		<!--begin::Modal-->
		<div class="modal fade" id="m_modal_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Create New Company</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="mb-3">
								<input type="text" name="" class="form-control" placeholder=" Company Name">
							</div>
							<div class="mb-3">
								<input type="email" name="" class="form-control" placeholder=" Company Email Address">
							</div>
							<div class="mb-3">
								<input type="text" name="" class="form-control" placeholder=" Company Phone">
							</div>
							<div class="mb-3">
								<textarea class="form-control" placeholder="About Company" style="height: 100px;"></textarea>
							</div>
							<div class="mb-4 d-flex align-items-center">
								<figure class="user-thumb-1 user-thumb mb-0 mr-3">
									<img class="img-fluid" src="assets/app/media/img/users/c.jpg" alt="">
								</figure>
								<input type="file" name="" class="form-control ml-3 height-auto">
							</div>
							<div class="d-flex">
								<input type="submit" name="" class="btn btn-primary mr-3" value="Create">
								<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


		<!--begin::Modal-->
		<div class="modal fade" id="m_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header flex-column relative bg-light p-4">
						<h5 class="mb-3 eeeeeeeeeeee" id="exampleModalLabel">Add Devices</h5>
						<button type="button" class="close position-absolute top-0 end-0 m-0 lh-1" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<div class="input-group">
							<input type="text" class="form-control search_devices search_devices_input" companyId="{{$company_id}}" placeholder="Search for Sensors of Cloud Connectors" aria-label="sensor-search" aria-describedby="basic-addon2">
							<div class="input-group-append">
								{{-- search_devices --}}
								<button type="submit" class="input-group-text btn " companyId="{{$company_id}}" id="basic-addon2">
									<i class="fl flaticon-search"></i>
								</button>
							</div>
						</div>
					</div>
					<div class="modal-body p-3 connectroListTableWrap">
						<div class="table-responsive">
							<table class="table table-borderless table-hover">
								<thead>
									<tr>
										<th width="5%">
											Add
										</th>
										<th width="8%">
											Type
										</th>
										<th  width="65%">
											Name
										</th>
										<th width="6%">
											State
										</th>
										<th width="10%" class="d-none d-sm-table-cell">
											Last Seen
										</th>
										<th width="6%">
											Signal
										</th>
									</tr>
								</thead>
								<tbody id="notificationsContianerTable">
									
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
						<button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">Done</button>
					</div>
				</div>
			</div>
		</div>

@endsection
@push('scripts')
	<script type="text/javascript" src="{{asset('assets/js/jquery.timeago.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        moment.tz.setDefault('Europe/Oslo');
        $('#m_table_1 tbody tr').on('click',function(){
            window.location.href=$(this).attr('data-url');
        }) ;
    });
    $('#reset-filter').on('click',function(){
        window.location.href = '{{url("sensors/".$company_id)}}';
    });

    $('table tr').each(function(){
        var device_id = $(this).attr('data-device');
        var milliseconds = $(this).attr('data-milliseconds');
        if(typeof device_id !== "undefined" && typeof milliseconds !== "undefined"){
            var now = new Date();
            var UTC_DIFFERENCE = now.getTimezoneOffset()*60;
            var newTime = parseInt(milliseconds)+(UTC_DIFFERENCE);
            var newTime2 = new Date(newTime);
             setInterval(function(){
                $("time.timeago-"+device_id).timeago('update',newTime2);
            },1000);
        }
    });
</script>

<script >

	$(document).ready(function(){
	
			
		let ids=[],email_counter=0,devices_counter=0, sms_counter=0;
      var token =  $('input[name="_token"]').val();
		$('#notification_type').on('change',function(){
			
			var alert_type=$(this).val();
			if(alert_type=='Temperature'){
				$('.temp_range_container').removeClass('d-none');
                $('.celsius').removeClass('d-none');
                $('.suggestions').removeClass('d-none');
				$('.temp_upper_container').removeClass('d-none');
				$('.reminder').removeClass('d-none');
				$('.delay_time').removeClass('d-none');
                $('.resolved').addClass('d-none');
                $('.repeater').addClass('d-none');
                $('.date_container').addClass('d-none');
				$('#range').change();
				$('#repeat_date').prop('required',false);
				$('#range').prop('disabled',false);
			}
			if(alert_type=='Device Monitoring (Beta)'){
				$('.temp_range_container').addClass('d-none');
				$('.temp_upper_container').addClass('d-none');
                $('.celsius').addClass('d-none');
                $('.suggestions').addClass('d-none');
				$('.temp_lower_container').addClass('d-none');
				$('.repeater').addClass('d-none');
				$('.date_container').addClass('d-none');
				$('.reminder').addClass('d-none');
				$('.upper_celcius_field').val('');
                $('.resolved').removeClass('d-none');
                $('.delay_time').removeClass('d-none');
				$('.lower_celcius_field').val('');
				$('#upper_celcius_field').prop('required',false);
				$('#lower_celcius_field').prop('required',false);
				$('#repeat_date').prop('required',false);
				$('#range').prop('disabled',true);
			}
			if(alert_type=='Maintenance'){

				// Get the input element
				const repeatDateInput = document.getElementById('repeat_date');

				// Get the current date and format it as YYYY-MM-DD
				const currentDate = new Date().toISOString().split('T')[0];

				// Set the minimum date for the input element to tomorrow (one day after today)
				const nextDate = new Date();
				nextDate.setDate(nextDate.getDate() + 1);
				const nextDateFormatted = nextDate.toISOString().split('T')[0];
				repeatDateInput.min = nextDateFormatted;

				$('.temp_range_container').addClass('d-none');
				$('.temp_upper_container').addClass('d-none');
                $('.suggestions').addClass('d-none');
                $('.celsius').addClass('d-none');
				$('.temp_lower_container').addClass('d-none');
				$('.resolved').addClass('d-none');
				$('.delay_time').addClass('d-none');
				$('.reminder').addClass('d-none');
				$('.upper_celcius_field').val('');
                $('.date_container').removeClass('d-none');
                $('.repeater').removeClass('d-none');
				$('.lower_celcius_field').val('');
				$('#upper_celcius_field').prop('required',false);
				$('#lower_celcius_field').prop('required',false);
				$('#repeat_date').prop('required',true);
				$('#range').prop('disabled',true);
		
}
		});
		$('.notification_name').on('keyup',function(){
			let notification_name=$(this).val();
			if(notification_name!=''){
				$(this).removeClass('is-invalid');
			}else{
				$(this).addClass('is-invalid');
			}
		});

		$('#range').on('change',function(){
				
			var range=$(this).val();
			if(range=='above'){
				$('.temp_lower_container').addClass('d-none');
				$('.temp_upper_container').removeClass('d-none');
				$('#upper_celcius_field').prop('required',true);
				$('#lower_celcius_field').prop('required',false);
				$('#lower_celcius_field').val('');

			}
			if(range=='below'){
				// $('#upper_celcius_field').addClass('d-none');
				$('.temp_upper_container').addClass('d-none');
				$('.temp_lower_container').removeClass('d-none');
				$('#lower_celcius_field').prop('required',true);
				$('#upper_celcius_field').prop('required',false);
				$('#upper_celcius_field').val('');
			
			}
		});
		$('.addDevice').on('click',function(){
			var company_id=$(this).attr('companyId');
			var alert_type=$('#notification_type').val();
		

			$.ajax({

				      url: "{{url('get/devices')}}",
			           type: 'POST',
			           data: {
			              company_id: company_id,
			              alert_type:alert_type,
			              ids:ids
			           },
				      headers: {
				                    'X-CSRF-Token': token 
				               },
				      success: function (data) {
				      	console.log(data);
				      	$('#notificationsContianerTable').empty();
				      	$('#notificationsContianerTable').html(data.html);
				      	$('.devices_container').removeClass('d-none');
				      	$('.first-device-button').addClass('d-none');

				      },
				      error: function (data) { 
				      	console.log(data);
				      }
		     });
		});

		 // $(window).keydown(function(e){

		 //    if(e.which == 13) { e.preventDefault() }
		 // });
		var company_id ="{{$company_id}}";
		$('.search_devices').on('change',function(e){

		    // if(e.which == 13) {
		       var search=$('.search_devices_input').val();
		       
			var alert_type=$('#notification_type').val();
		
			$.ajax({
				      url: "{{url('search/devices')}}",
			           type: 'POST',
			           data: {
			              company_id: company_id,
			              alert_type:alert_type,
			              search:search,
			              ids:ids
			           },
				      headers: {
				                    'X-CSRF-Token': token 
				               },
				      success: function (data) {
				      	console.log(data);
				      	$('#notificationsContianerTable').empty();
				      	$('#notificationsContianerTable').html(data.html);

				      },
				      error: function (data) { 
				      	console.log(data);
				      }
		     });
		    // }
		});
		$(document).on('click','.device_selector',function(){
			let device_id=$(this).attr('deviceid');
			console.log(device_id);
		;

			$.ajax({

				      url: "{{url('get/single/device')}}",
			           type: 'POST',
			           data: {
			              device_id: device_id,

			           },
				      headers: {
				                    'X-CSRF-Token': token 
				               },
				      success: function (data) {
				      	console.log(data);
				      	ids.push(device_id);
				      	console.log(ids);
				      	devices_counter++;
				      
				      	$('#added_devices_container').append(data.html);
				      	$('.add_action').prop('disabled',false);
				      	if(email_counter!=0 || sms_counter!=0)
				      	 $('.save_notification').prop('disabled',false);
				      },
				      error: function (data) { 
				      	console.log(data);
				      }
		     });
			$(this).addClass('d-none');

		});
		$(document).on('click','.removeDevice',function(){
			let device_id=$(this).attr('deviceId');
			console.log(device_id);
			$('.list-'+device_id).remove();
			 let index = ids.indexOf(device_id);
			 delete ids[index];
			 console.log(ids.length);
			 devices_counter--;

			  $('.save_notification').prop('disabled',false);

			 if(devices_counter == 0){

			 	$('.add_action').prop('disabled',true);
			 	$('.save_notification').prop('disabled',true);


			 }
			
			 // if(devices_counter == 0){
			 // 	$('.add_action').prop('disabled',true);

			 // }
		});

			 console.log('ids '+ids);
		$('.sendEmail').on('click',function(){
			email_counter=$('#email_counter').val();
			let company_id = "{{$company_id}}";
			
			$.ajax({

				      url: "{{url('add/email')}}",
			           type: 'POST',
			          data: {
			              email_counter: email_counter,
						company_id:company_id
			           },
				      headers: {
				                    'X-CSRF-Token': token 
				               },
				      success: function (data) {
				      	console.log(data);
				      	email_counter=parseInt(email_counter)+1;
				      	$('.email_container').append(data.html);
				      	$('#email_counter').val(email_counter);
				      	$('.save_notification').prop('disabled',false);
						  var val =$('#notification_type').val();
						  if(val === "Temperature"){
							$('#email_subject'+email_counter).val('Temp. alert // $project_name');
							}else if(val === "Device Monitoring (Beta)" || val === "Maintenance"){
								if( val === "Maintenance"){
								$('#email_subject'+email_counter).val('Equipment Maintenance //$project_name');
								}else{

								$('#email_subject'+email_counter).val('Device offline //$project_name');
								}

								$('#celsius'+email_counter).css("display", "none");
								$('#sugestions'+email_counter).css("display", "none");

							}
				      	console.log(email_counter);
				      	
				      },
				      error: function (data) { 
				      	console.log(data);
				      }
		     });
		});
				
		$(document).on('keyup','.email_field',function(){
			email_counter=$(this).attr('emailCounter');
			let email=$(this).val();
		
			if(email!='')
			 $('.email_error'+email_counter).addClass('d-none');
			else
			 $('.email_error'+email_counter).removeClass('d-none');

		});
		$(document).on('click','.deleteEmailSection',function(){
			let counter=$(this).attr('emailCounter');
			console.log(counter);
			$('.email_div'+counter).remove();
			
			 $('#email_counter').val(parseInt(email_counter)-1);
			 email_counter=parseInt(email_counter)-1;
			  console.log(sms_counter);
			  console.log(email_counter);
			 if(email_counter==0 && sms_counter==0){
			 	$('.save_notification').prop('disabled',true);
			 }


		});
		$(document).on('click','.email_subject',function(){
			var emailCounter=$(this).attr('emailCounter');
			$("#email_content"+emailCounter).removeClass("body"+emailCounter);
			$("#email_subject"+emailCounter).addClass("subject"+emailCounter);


			
		});
		$(document).on('click','.email_content',function(){
			var emailCounter=$(this).attr('emailCounter');
			$("#email_subject"+emailCounter).removeClass("subject"+emailCounter);
			$("#email_content"+emailCounter).addClass("body"+emailCounter);
		});
		$(document).on('click','.email_variable',function(){

			let email_variable=$(this).attr('emailVariable');
			let emailCounter=$(this).attr('emailCounter');
			console.log(email_variable);

			var subject = $(".subject"+emailCounter).val();
			if(typeof(subject) != "undefined" && subject !== null) {
			var userInput = $(".subject"+emailCounter).val();
			userInput = userInput + " " + email_variable;
			var userInput = $(".subject"+emailCounter).val();
				userInput = userInput + " " + email_variable;
				var caretPos = $(".subject"+emailCounter)[0].selectionStart;
				var textAreaTxt = $(".subject"+emailCounter).val();
				var txtToAdd = email_variable;
				$(".subject"+emailCounter).val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
				$(".subject"+emailCounter).focus();
			}

			var body = $(".body"+emailCounter).val();
			if(typeof(body) != "undefined" && body !== null) {
    
				var userInput = $(".body"+emailCounter).val();
				userInput = userInput + " " + email_variable;
				var caretPos = $(".body"+emailCounter)[0].selectionStart;
				var textAreaTxt = $(".body"+emailCounter).val();
				var txtToAdd = email_variable;
				$(".body"+emailCounter).val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
				$(".body"+emailCounter).focus();
				}
			$('.save_notification').prop('disabled',false);
		});



		$(document).on('click','.email_variable_box',function(){
			var email_variable=$(this).attr('emailVariable');
			var emailCounter=$(this).attr('emailCounter');
			$('#email_content'+emailCounter).val(null);
			console.log(emailCounter);
		
			var userInput = $('#email_content'+emailCounter).val();
			userInput = userInput + " " + email_variable;
			$('#email_content'+emailCounter).val(userInput);
			$('#email_content'+emailCounter).focus();

		});
		$(document).on('keyup','.email_content',function(e){
	
			let emailCounter=$(this).attr('emailCounter');

			 var wordCounterwithSpace = 0;
             var wordCounterwithoutSpace = 0;
              var val =$('#email_content'+emailCounter).val();

             for (var i = 0; i < val.length; i++) {
		        if (val[i] == ' ') {
		            wordCounterwithSpace++;
		            continue;
		        } else {
		            wordCounterwithoutSpace++;
		            wordCounterwithSpace++;
		        }
            }
			console.log(wordCounterwithoutSpace);
			if(e.which!=32)
			 $('.wordsCount-'+emailCounter).text(wordCounterwithSpace);
		});

		$(document).on('keyup','.email_subject',function(e){
			let emailCounter=$(this).attr('emailCounter');

			 var wordCounterwithSpace = 0;
             var wordCounterwithoutSpace = 0;
              var val =$('#email_subject'+emailCounter).val();

             for (var i = 0; i < val.length; i++) {
		        if (val[i] == ' ') {
		            wordCounterwithSpace++;
		            continue;
		        } else {
		            wordCounterwithoutSpace++;
		            wordCounterwithSpace++;
		        }
            }
			console.log(wordCounterwithoutSpace);
			if(e.which!=32)
			 $('.subjectwordsCount-'+emailCounter).text(wordCounterwithSpace);
		});

		 $(document).on("keyup", '.enteredtext', function(e) {
           var c = $(this).val().length;

           if (c == 0)
              return e.which !== 32;
        });



		 $('.sendSMS').on('click',function(){
			sms_counter=$('#sms_counter').val();
			company_id = "{{$company_id}}"
			
			$.ajax({

				      url: "{{url('add/sms')}}",
			           type: 'POST',
			          data: {
			              sms_counter: sms_counter,
							company_id:company_id

			           },
				      headers: {
				                    'X-CSRF-Token': token 
				               },
				      success: function (data) {
				      	console.log(data);
				      	sms_counter=parseInt(sms_counter)+1;
				      	$('.sms_container').append(data.html);
				      	$('#sms_counter').val(sms_counter);
				      	$('.save_notification').prop('disabled',false);
						var val =$('#notification_type').val();
						   if(val === "Device Monitoring (Beta)"){
								$('#temp'+sms_counter).css("display", "none");
							}
				      	console.log(sms_counter);
				      	
				      },
				      error: function (data) { 
				      	console.log(data);
				      }
		     });
		});


		 $(document).on('keyup','.sms_field',function(){
			sms_counter=$(this).attr('smsCounter');
			let sms=$(this).val();
		
			if(sms!='')
			 $('.sms_error'+sms_counter).addClass('d-none');
			else
			 $('.sms_error'+sms_counter).removeClass('d-none');

		});
		$(document).on('click','.deleteSmsSection',function(){
			let counter=$(this).attr('smsCounter');
			console.log(counter);
			$('.sms_div'+counter).remove();
			
			 $('#sms_counter').val(parseInt(sms_counter)-1);
			 sms_counter=parseInt(sms_counter)-1;
			 console.log(sms_counter);
			  console.log(email_counter);
			 if(email_counter==0 && sms_counter==0){
			 	$('.save_notification').prop('disabled',true);
			 }


		});
		$(document).on('click', '.sms_content', function() {
                var emailCounter = $(this).attr('smsCounter');
                $("#sms_content" + emailCounter).addClass("sms_content" + emailCounter);
            });

            $(document).on('click', '.sms_variable', function() {
                let sms_variable = $(this).attr('smsVariable');
                let smsCounter = $(this).attr('smsCounter');
                console.log(sms_variable);

                var smsbody = $(".sms_content" + smsCounter).val();
                if (typeof(smsbody) != "undefined" && smsbody !== null) {

                    var userInput = $(".sms_content" + smsCounter).val();
                    userInput = userInput + " " + sms_variable;
                    var caretPos = $(".sms_content" + smsCounter)[0].selectionStart;
                    var textAreaTxt = $(".sms_content" + smsCounter).val();
                    var txtToAdd = sms_variable;
                    $(".sms_content" + smsCounter).val(textAreaTxt.substring(0, caretPos) + txtToAdd +
                        textAreaTxt.substring(caretPos));
                    $(".sms_content" + smsCounter).focus();
                }
            });
		$(document).on('keyup','.sms_content',function(e){
	
			let smsCounter=$(this).attr('smsCounter');

			 var smsWordCounterwithSpace = 0;
             var smsWordCounterwithoutSpace = 0;
              var val =$('#sms_content'+smsCounter).val();

             for (var i = 0; i < val.length; i++) {
		        if (val[i] == ' ') {
		            smsWordCounterwithSpace++;
		            continue;
		        } else {
		            smsWordCounterwithoutSpace++;
		            smsWordCounterwithSpace++;
		        }
            }
			console.log(smsWordCounterwithoutSpace);
			if(e.which!=32)
			 $('.smsWordsCount-'+smsCounter).text(smsWordCounterwithSpace);
		});

		


		 $(document).on('click','.cancel_btn',function(){
		 	window.location.href="{{url('notifications')}}/{{$company_id}}";
		 })
		  $(document).on('keyup','#delay_time',function(){
		 	let time=$(this).val();
		 	if(time<=0){
		 		$(this).val('1');
		 	}
		 	if(time>72){
		 		$(this).val(72);
		 	}
		 })

		  // $(document).on('change','.recipients',function(){
		  
		  // 	$('.select2-container').removeClass('select2-container--open');
		  // });

		  // select2-container select2-container--default select2-container--open

	});

</script>
{{-- <script src="tagsinput.js"></script> --}}

{{-- <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" crossorigin="anonymous"></script> --}}
{{-- <script src="../../../assets/demo/default/custom/crud/forms/widgets/select2.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
					integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
					crossorigin="anonymous"></script>
		
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
					integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
					crossorigin="anonymous"></script>
		
			<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script> --}}
			
{{-- @endpush

	});

</script> --}}
{{-- <script src="tagsinput.js"></script> --}}

{{-- <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js" crossorigin="anonymous"></script> --}}
{{-- <script src="../../../assets/demo/default/custom/crud/forms/widgets/select2.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
					integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
					crossorigin="anonymous"></script>
		
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
					integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
					crossorigin="anonymous"></script>
		
			<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script> --}}
			
@endpush