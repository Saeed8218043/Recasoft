  
   @if(isset($sensors) && count($sensors)>0)
   @if($type !='Temperature')
   <tr>
    <td colspan="6">
        <h5 class="m-0 p-0 text-nowrap bg-light-grey-2 p-2 p-sm-3 my-1 rounded d-inline-block">Connected</h5>
    </td>  
    </tr>
    @endif
                        @php
                        $counter=1;
                        @endphp
                        @foreach($sensors as $row)

                            @php
                        $sensor = \App\Device::where('device_id',$row->sensor_id)->first();
                            @endphp
                        <tr data-url="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->sensor_id}}" class="@if(isset($row->is_active) && $row->is_active==0) is_disabled @endif" data-device="{{$row->sensor_id}}" data-milliseconds="{{$row->milliseconds}}" deviceId="{{$sensor->id}}" >
                            {{-- <td><a href="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}

                            
                              @php
                              $exist=false;
                              	if(isset($ids) && count($ids)>0){
                              		foreach ($ids as $id) {
                              			if($id==$sensor->id){
                              				$exist=true;
                              				break;
                              			}
                              		}
                              	}
                              @endphp
                            
                            
                           <td>
                           	@if($exist==false)
								<svg class="text-primary fs-22 device_selector" deviceid="{{$sensor->id}}" company_id="{{$row->company_id}}" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"/></svg>
							@endif
							</td>
						{{-- {{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}} --}}
                            <td align="center">
                                <a class="iconHolder mx-auto" href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                @if($row->event_type=='temperature')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                                {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                @elseif($row->event_type=='ccon')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em;transform: rotate(360deg);">
                                    <path fill="#4A4A4A" fill-rule="evenodd" d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z"></path>
                                </svg>
                                @else
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
                                   c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                           </svg>
                                @endif
                                </a>
                            </td>
                            <td class="fw-500"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}</a></td>


                            @if($row->event_type=='temperature')
                            <td>
                                @if(isset($row->is_active) && $row->is_active==1)
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($row->temperature)?@number_format($row->temperature,2):0}}°C</a>
                                @else
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                @endif

                            </td>
                            @else
                            <td><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($sensor->temperature)?$sensor->temperature:''}}</a></td>
                            @endif
                            <td class="d-none d-sm-table-cell"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;"><time class="timeago-{{$row->device_id}}" datetime="{{$row->temeprature_last_updated??''}}">{{ isset($row->time_ago) && $row->time_ago!=''?$row->time_ago:'---' }}</time></a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                <div class="signal-indicator-icon-wrap">
                                    @if(isset($row->is_active) && $row->is_active==0 && $row->event_type!='ccon' && $row->event_type!='equipment')
                                    <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endif
                                    <ul class="signal-indicator-bar-list">
                                        {!! isset($row->signal)?$row->signal:'' !!}
                                    </ul>
                                </div>
                                </a>
                            </td>
                        </tr>
                        @php
                        $counter++;
                        @endphp
                        @endforeach
                        @endif

   @if(isset($not_connected) && count($not_connected)>0)

                            <tr>
                                <td colspan="6">
                                    <h5 class="m-0 p-0 text-nowrap bg-light-grey-2 p-2 p-sm-3 my-1 rounded d-inline-block">Not Connected</h5>
                                </td>  
                            </tr>


                        @php
                        $counter=1;
                        @endphp
                        @foreach($not_connected as $row)


                        <tr data-url="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}}" class="@if(isset($row->is_active) && $row->is_active==0) is_disabled @endif" data-device="{{$row->device_id}}" data-milliseconds="{{$row->milliseconds}}" deviceId="{{$row->id}}" >
                            {{-- <td><a href="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}

                            
                              @php
                              $exist=false;
                              	if(isset($ids) && count($ids)>0){
                              		foreach ($ids as $id) {
                              			if($id==$sensor->id){
                              				$exist=true;
                              				break;
                              			}
                              		}

                              	}
                              @endphp
                            
                            
                           <td>
                           	@if($exist==false)
								<svg class="text-primary fs-22 device_selector" deviceid="{{$row->id}}" company_id="{{$row->company_id}}" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"/></svg>
							@endif
							</td>
						{{-- {{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}} --}}
                            <td align="center">
                                <a class="iconHolder mx-auto" href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                @if($row->event_type=='temperature')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                                {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                @elseif($row->event_type=='ccon')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em;transform: rotate(360deg);">
                                    <path fill="#4A4A4A" fill-rule="evenodd" d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z"></path>
                                </svg>
                                @else
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
                                   c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                           </svg>
                                @endif
                                </a>
                            </td>
                            <td class="fw-500"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}</a></td>


                            @if($row->event_type=='temperature')
                            <td>
                                @if(isset($row->is_active) && $row->is_active==1)
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($row->temperature)?@number_format($row->temperature,2):0}}°C</a>
                                @else
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                @endif

                            </td>
                            @else
                            <td><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($row->temperature)?$row->temperature:''}}</a></td>
                            @endif
                            <td class="d-none d-sm-table-cell"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;"><time class="timeago-{{$row->device_id}}" datetime="{{$row->temeprature_last_updated??''}}">{{ isset($row->time_ago) && $row->time_ago!=''?$row->time_ago:'---' }}</time></a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                <div class="signal-indicator-icon-wrap">
                                    @if(isset($row->is_active) && $row->is_active==0 && $row->event_type!='ccon' && $row->event_type!='equipment')
                                    <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endif
                                    <ul class="signal-indicator-bar-list">
                                        {!! isset($row->signal)?$row->signal:'' !!}
                                    </ul>
                                </div>
                                </a>
                            </td>
                        </tr>
                        @php
                        $counter++;
                        @endphp
                        @endforeach
                        @endif




   @if(isset($gateways) && count($gateways)>0)

                            <tr>
                                <td colspan="6">
                                    <h5 class="m-0 p-0 text-nowrap bg-light-grey-2 p-2 p-sm-3 my-1 rounded d-inline-block">Geteways</h5>
                                </td>  
                            </tr>
                        @php
                        $counter=1;
                        @endphp
                        @foreach($gateways as $row)


                        <tr data-url="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}}" class="@if(isset($row->is_active) && $row->is_active==0) is_disabled @endif" data-device="{{$row->device_id}}" data-milliseconds="{{$row->milliseconds}}" deviceId="{{$row->id}}" >
                            {{-- <td><a href="{{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}

                            
                              @php
                              $exist=false;
                              	if(isset($ids) && count($ids)>0){
                              		foreach ($ids as $id) {
                              			if($id==$row->id){
                              				$exist=true;
                              				break;
                              			}
                              		}

                              	}
                              @endphp
                            
                            
                           <td>
                           	@if($exist==false)
								<svg class="text-primary fs-22 device_selector" deviceid="{{$row->id}}" company_id="{{$row->company_id}}" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"/></svg>
							@endif
							</td>
						{{-- {{url('sensor-details')}}/{{$row->company_id}}/{{$row->device_id}} --}}
                            <td align="center">
                                <a class="iconHolder mx-auto" href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                @if($row->event_type=='temperature')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                                {{-- <span class="iconify fs-22" data-icon="carbon:temperature"></span> --}}
                                @elseif($row->event_type=='ccon')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em;transform: rotate(360deg);">
                                    <path fill="#4A4A4A" fill-rule="evenodd" d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z"></path>
                                </svg>
                                @else
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
                                   c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                           </svg>
                                @endif
                                </a>
                            </td>
                            <td class="fw-500"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}</a></td>


                            @if($row->event_type=='temperature')
                            <td>
                                @if(isset($row->is_active) && $row->is_active==1)
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($row->temperature)?@number_format($row->temperature,2):0}}°C</a>
                                @else
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">-- --</a>
                                @endif

                            </td>
                            @else
                            <td><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">{{isset($row->signal_strength)?$row->signal_strength.'%':''}}</a></td>
                            @endif
                            <td class="d-none d-sm-table-cell"><a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;"><time class="timeago-{{$row->device_id}}" datetime="{{$row->temeprature_last_updated??''}}">{{ isset($row->time_ago) && $row->time_ago!=''?$row->time_ago:'---' }}</time></a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" style="color:#212529;text-decoration:none;display:block;">
                                <div class="signal-indicator-icon-wrap">
                                    @if(isset($row->is_active) && $row->is_active==0 && $row->event_type!='ccon' && $row->event_type!='equipment')
                                    <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endif
                                    <ul class="signal-indicator-bar-list">
                                        {!! isset($row->signal)?$row->signal:'' !!}
                                    </ul>
                                </div>
                                </a>
                            </td>
                        </tr>
                        @php
                        $counter++;
                        @endphp
                        @endforeach
                        @endif