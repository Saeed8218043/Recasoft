<div class="bg-light-grey-2 p-3 mb-3">
    <div class="row d-flex align-items-center">
        <div class="col-sm-8 col-10">
            <div class="d-inline-flex align-items-center">
                <figure class="fig-80 bg-white shadow panel-has-radius c-border-1 mb-0 mr-3 relative">
                    <span class="sensor_icon_mini" style="background-color: red;"></span>
                    @if(isset($device->event_type) && $device->event_type=='ccon')
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="-3 0 32 32" class="iconify" data-icon="carbon:temperature" style="vertical-align: -0.125em;transform: rotate(360deg);">
                                            <path fill="#4A4A4A" fill-rule="evenodd" d="M11.995 0l-.633.012-1.526.123L8.95.27l-.755.18-.373.146-.277.153-.256.203-.215.248-.165.278-.118.286-.152.585-.21 1.756-.198 2.511-.167 3.778L6 14.684l.051 5.298.026.497.063.504.114.493.102.292.13.284.162.269.194.249.222.223.245.194.345.214.362.168.374.137.649.172.987.18.986.107.997.035 1.002-.037.986-.109.964-.176.489-.124.468-.158.45-.208.26-.158.247-.186.228-.214.2-.241.17-.266.14-.283.139-.389.094-.397.059-.396.095-5.804-.006-1.283-.078-3.856-.168-3.435-.134-1.719-.177-1.712-.14-.8-.128-.395-.14-.291-.187-.272-.234-.23-.264-.181-.28-.136L15.49.36 14.415.168 12.891.023 11.995 0zm-.86 22.967l-.862-.097-.862-.165-.552-.154-.303-.117-.289-.143-.27-.176-.346-.325-.142-.192-.22-.44-.17-.656-.052-.439L7 14.567l.03-2.886.114-3.598.143-2.393.217-2.364.066-.462.05-.246.152-.456.11-.201.14-.175.176-.144.196-.116.29-.119.31-.089 1.1-.192.788-.083L11.994 1l.79.022.791.063 1.101.163.616.153.426.187.182.128.156.161.125.193.1.217.098.325.067.346.207 1.977.12 1.631.087 1.631.097 2.86L17 14.73l-.027 3.665-.03 1.52-.074.65-.074.332-.107.32-.23.428-.147.19-.357.316-.563.306-.387.139-1.257.271-.862.1-.877.034-.872-.033z"></path>
                                        </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                    @endif

                    <!--Door Icon -->
                   {{--  <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><g transform="translate(16 0) scale(-1 1)"><g fill="currentColor"><path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1s.5.448.5 1s-.224 1-.5 1z"/><path d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117zM11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5zM4 1.934V15h6V1.077l-6 .857z"/></g></g></svg> --}}

                </figure>
                <figcaption class="m-0">
                    <p class="m-0 fw-500 text-muted">

                        {{-- Temperature Sensor --}}
                        {{isset($device->name)?$device->name:''}}
                    </p>
                    <p class="m-0 fs-28 fw-600">
                        @if(isset($device->is_active) && $device->is_active==0)
                        Offline
                        @else
                       {{isset($device->temperature)?$device->temperature:''}}
                       {{(isset($device->event_type) && $device->event_type!='ccon')?'°C':''}}
                       @endif
                    </p>
                    <p class="m-0 fw-500 text-muted">Company: <strong>{{isset($device->company->name)?$device->company->name:''}}</strong></p>
                    {{-- <p class="m-0 fw-500 text-muted">
                        Today at {{ isset($device->temeprature_last_updated) && $device->temeprature_last_updated!=''?$device->temeprature_last_updated:'---' }}
                    </p>        --}}
                </figcaption>
            </div>
        </div>
        <div class="col-sm-4 col-2">
            <div class="d-flex flex-column align-items-sm-end">
                @if(isset($device->event_type) && $device->event_type!='ccon')
                <div class="signal-indicator-icon-wrap">
                    @if(isset($device->is_active) && $device->is_active==0 && $device->event_type!='ccon')
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="color: rgb(237, 28, 36); vertical-align: -0.125em; transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify signal-indicator-icon-small" data-icon="akar-icons:circle-alert-fill"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"></path></svg>
                                        
                                        @endif
                    <ul class="signal-indicator-bar-list">
                        @php
                        $signal_div='';
                         $active='active';
                                            if(isset($device->is_active) && $device->is_active==0){
                                                $active='';
                                            }
                        if($device->signal_strength<=20){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($device->signal_strength>20 && $device->signal_strength<=40) {
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($device->signal_strength>40 && $device->signal_strength<=60){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>
                                                                   <li class=""></li>';
                                                }elseif($device->signal_strength>60 && $device->signal_strength<=80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class=""></li>';
                                                }elseif($device->signal_strength>80){
                                                    $signal_div = '<li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>
                                                                   <li class="'.$active.'"></li>';                       
                                                }
                        @endphp
                        {!!$signal_div!!}
                        {{-- <li class="active"></li>
                        <li class=""></li>
                        <li class=""></li>
                        <li class=""></li>
                        <li class=""></li> --}}
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div>
    <a href="{{isset($url)?$url:'javascript:;'}}" class="btn btn-block btn-secondary">
        @if(isset($device->event_type) && $device->event_type!='ccon')
        Go to Temperature Sensor 
        @else
        Go to Cloud Connector 
        @endif
        <svg class="fs-18" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="M21.188 9.281L19.78 10.72L24.063 15H4v2h20.063l-4.282 4.281l1.407 1.438L27.905 16z"/></svg>
    </a>
</div>