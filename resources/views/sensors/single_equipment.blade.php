 <div class="p-3">
										<div class="mb-3">
											<div class="row gutter-10">
												<div class="col-sm-8 mb-3 mb-sm-0 col-8">
													<div class="d-inline-flex align-items-center">
														<figure class="fig-60 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative">
															<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"/></svg>
														</figure>
														<figcaption class="m-0">
															<p class="m-0 fw-500 text-muted fs-12 device_name">
															@if($sensorData->event_type =='temperature')
																@if($sensorData->name == '')
																	No name | Temperature Sensor
																@else
															 		{{$sensorData->name }} | Temperature Sensor
																	@endif
																@else
																@if($sensorData->name == '')
																	No name | Connector
																@else
															 		{{$sensorData->name }} | Connector
																	@endif
																@endif
															</p>
													<p class="m-0 main_value_1">	
														{{$sensorData->temperature ?? ''}}
															</p>
															<p class="m-0 fw-500 text-muted fs-12">
															
																Project: <span class="fw-600">{{$company->name ?? $sensorData->company_id}}</span>
															</p>			
														</figcaption>
													</div>
												</div>
												<div class="col-sm-4 col-4 d-flex align-items-center justify-content-end">

													<div class="justify-content-end d-flex align-items-center">
														<div class="signal-indicator-icon-wrap">
															 <ul class="signal-indicator-bar-list">
                                                @php
                                                $active='active';
                                                if(isset($sensorData->is_active) && $sensorData->is_active==0){
                                                    $active='';
                                                }
                                                $signal_div='';
                                                if($sensorData->signal_strength<=20){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensorData->signal_strength>20 && $sensorData->signal_strength<=40) {
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensorData->signal_strength>40 && $sensorData->signal_strength<=60){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($sensorData->signal_strength>60 && $sensorData->signal_strength<=80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>';
                                                }elseif($sensorData->signal_strength>80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>';
                                                }
                                                @endphp
                                                {!!$signal_div!!}
                                               {{--  <li class="active"></li>
                                                <li class="active"></li>
                                                <li class="active"></li>
                                                <li class="active"></li>
                                                <li class=""></li> --}}
                                            </ul>
														</div>
													</div>	

												</div>
											</div>
										<!-- ends row -->										
										</div>
										<div class="row">
											<div class="col-sm-6 mb-2 mb-sm-0">
												<button type="submit" id="set-name" class="btn btn-default btn-block">Set Sensor name</button>
											</div>
											<div class="col-sm-6">
               								 <form method="GET" action="{{ route('sensor-details',['company_id'=>$sensorData->company_id,'device_id'=>$sensorData->device_id]) }}">
											@csrf
												<button type="submit" id ="detail" class="btn btn-default btn-block">Open Sensor details &#10230;</button>
												</form>
											</div>
										</div>
									</div>
									</div>
									<div class="d-none" id="set_name_form">
										<div>
											<div style="text-align: center;">
											<label><strong>Device Name</strong></label>
											<input type="text" name="sensor_description" placeholder="Enter device name" class="form-control" id="set_sensor_name" style="width: 94%;display: inline-block; margin: 5px;">
											</div>
												<div class="col-sm-6 mb-2 mb-sm-0" style="display: inline-block;">
													<button type="button" id="cancel" class="btn btn-default btn-block">Cancel</button>
												</div>
												<div class="col-sm-6" style="display: inline-block;float: right">
													<button type="submit" class="btn btn-default btn-block save_name">Save Name</button>
												</div>
											</div>
										</div>
									</div>
<script type="text/javascript">
            $(document).ready(function(){

$('#set-name').on('click',function(){
    $('#set_name_form').removeClass('d-none');
   // $('#sensor-info').addClass('d-none');

});
$('#cancel').on('click',function(){
    $('#set_name_form').addClass('d-none');
   // $('#sensor-info').addClass('d-none');

});
$('.save_name').on('click',function(){
  var device_id = "{{$sensorData->device_id??''}}";
  var company_id = "{{$sensorData->company_id??''}}";
        var name = $('#set_sensor_name').val();
		console.log(name);
		  if(name!=''){

            $.ajax({
                url:'{{url("updateSensorDetails")}}',
                method:'post',
                data:{device_id:device_id,name:name,company_id:company_id,'_token':'{{csrf_token()}}'},
                success:function(data){
					$('.device_name').text(name+' | Temperature Sensor');
    				$('#set_name_form').addClass('d-none');

                }
            });
        }
});
			});
</script>