<style>
    /* Style the list */
    ul.breadcrumb {
        padding: 10px 16px;
        list-style: none;
        background-color: #eee;
    }
label { cursor: pointer; }
    /* Display list items side by side */
    ul.breadcrumb li {
        display: inline;
        font-size: 18px;
    }

    /* Add a slash symbol (/) before/behind each list item */
    ul.breadcrumb li+li:before {
        padding: 8px;
        color: black;
        content: "/\00a0";
    }

    /* Add a color to all links inside the list */
    ul.breadcrumb li a {
        color: #0275d8;
        text-decoration: none;
    }

    /* Add a color on mouse-over */
    ul.breadcrumb li a:hover {
        color: #01447e;
        text-decoration: underline;
    }

    .role {
        padding: 7px;
        width: 110px
    }
</style>

<div class="m-portlet panel-has-radius mb-4 p-2">
 
<!--begin: Datatable -->
<div class="table-responsive">
        <table class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless" id="m_table_1">
                    <thead >
                        <tr>
                            <th width="3%">#</th>
                            <th width="5%">TYPE</th>
                            <th width="55%" >NAME</th>
                            <th width="15%" >STATE</th>
                            {{-- <th width="15%" class="d-none d-sm-table-cell">LAST SEEN</th>
                            <th width="10%">SIGNAL</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        
                        @if(isset($sensors) && count($sensors)>0)
                        @php
                        $counter=1;
                        @endphp

                        @foreach($sensors as $row)
                        @php
                        $sensor_data = \App\Device::where('sensor_id',$row->device_id)->first();
                        @endphp
                        <tr @if(isset($sensor_data) && $sensor_data->sensor_id!=0) class="is_disabled" @else class="sensorTable" @endif device_id="{{$row->device_id}}" data-url="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" data-milliseconds="{{$row->milliseconds}}">
                            {{-- <td><a href="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" style="color:#212529;text-decoration:none;display:block;">{{$counter}}</a></td> --}}
                            <td align="center"><input type="radio" id="{{$row->device_id}}" name="sensor_id" value="{{$row->device_id}}" ></td>

                            <td align="center">

                              

                                @if($row->event_type=='temperature')
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em; transform: rotate(360deg);"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"></path></svg>
                                
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" class="iconify fs-22" data-icon="carbon:temperature" style="vertical-align: -0.125em;transform: rotate(360deg);">
                                    <g fill="#4A4A4A" fill-rule="nonzero">
                                        <path d="M7 22H5.5a.5.5 0 0 1-.5-.5V.5a.5.5 0 0 1 .5-.5h14a.5.5 0 0 1 .5.5V21a.5.5 0 0 1-.398.49l-12 2.5A.5.5 0 0 1 7 23.5V22zm0-1V3a.5.5 0 0 1 .432-.495l11-1.5.136.99L8 3.436v19.45l11-2.293V1H6v20h1z"></path>
                                        <path d="M9.75 14.5c-.727 0-1.25-.698-1.25-1.5s.523-1.5 1.25-1.5S11 12.198 11 13s-.523 1.5-1.25 1.5zm0-1c.102 0 .25-.198.25-.5s-.148-.5-.25-.5c-.102 0-.25.198-.25.5s.148.5.25.5z"></path>
                                    </g>
                                </svg>
                                @endif
                            </td>
                            <td class="fw-500">{{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}</td>


                            @if($row->event_type=='temperature')
                            <td>
                                @if(isset($row->is_active) && $row->is_active==1)
                                {{isset($row->temperature)?@number_format($row->temperature,2):0}}°C
                                @else
                                -- --
                                @endif

                            </td>
                            @else
                            <td style="text-align: center;">{{isset($row->temperature)?$row->temperature:''}}</td>
                            @endif
                            {{-- <td class="d-none d-sm-table-cell"><time class="timeago-{{$row->device_id}}" datetime="{{$row->temeprature_last_updated??''}}">{{ isset($row->time_ago) && $row->time_ago!=''?$row->time_ago:'---' }}</time>
                            </td>
                            <td style="text-align: center;">
                               
                                <div class="signal-indicator-icon-wrap">
                                    
                                    @if(isset($row->is_active) && $row->is_active==0 && $row->event_type!='ccon')
                                    <span class="signal-indicator-icon-small" style="color: #ed1c24;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 6a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0V7Zm0 9.5a1 1 0 1 0-2 0v.5a1 1 0 1 0 2 0v-.5Z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endif
                                    <ul class="signal-indicator-bar-list">
                                        
                                        {!! isset($row->signal)?$row->signal:'' !!}
                                        @if (isset($row->is_active) && $row->is_active==0 && $row->event_type=='ccon')

                                       <button class="btn btn-default bg-light btn-sm fw-500"><i class="fa fa-info-circle text-danger mr-2" ></i> Offline</button>
                                        @endif
                                        
                                    </ul>
                                </div>
                                
                            </td> --}}
                        </tr>
                        @php
                        $counter++;
                        @endphp
                        @endforeach
                        @endif
                    </tbody>
                </table>     
</div>

</div>
