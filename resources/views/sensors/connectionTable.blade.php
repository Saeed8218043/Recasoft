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

<div class="m-portlet panel-has-radius mb-4 p-4">
 
<!--begin: Datatable -->
<div class="table-responsive">
        <table class="table table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-borderless" id="m_table_1">
                    <thead >
                        <tr>
                            <th width="3%">#</th>
                            <th width="5%" style="text-align: center">TYPE</th>
                            <th width="30%" >Equipment name</th>
                            <th width="30%" >Description</th>
                            <th width="30%" class="d-none d-sm-table-cell">Specification</th>
                            {{-- <th width="5%" style="text-align: center">Actions</th> --}}
                           
                        </tr>
                    </thead>
                    <tbody>
                        
                       
                            @foreach($equipments as $row)

                            <tr @if($row->sensor_id==0) class="sensorTable" @endif @if($row->sensor_id!=0) class="is_disabled" @endif data-url="{{url('sensor-details')}}/{{$company_id}}/{{$row->device_id}}" data-device="{{$row->device_id}}" data-milliseconds="{{$row->milliseconds}}" >

                            <td align="center"><input type="radio" id="{{$row->device_id}}" name="sensor_id" value="{{$row->device_id}}"  ></td>
                            <td align="center">

                                <a id="{{$row->device_id}}" class="iconHolder mx-auto" style="color:#212529;text-decoration:none;display:block;">

                              

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
                                   c16.517,0,29.918-13.401,29.918-29.926C371.978,311.044,358.577,297.644,342.06,297.644z"/>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           <g>
                           </g>
                           </svg>

                                </a>
                            </td>

                            <td class="fw-500"><a style="color:#212529;text-decoration:none;display:block;">{{(isset($row->name) && $row->name!='')?$row->name:$row->device_id}}</a>
                            </td>

                            <td class="d-none d-sm-table-cell"><a  style="color:#212529;text-decoration:none;display:block;">{{(isset($row->description) && $row->description!='')?$row->description:'---'}}</a>
                            </td>

                            <td>
                                <a  style="color:#212529;text-decoration:none;display:inline-block;">  {{(isset($row->specification) && $row->specification!='')?$row->specification:'---'}}</a>
                            </td>
                            {{-- <td>
                                <span class="miniIcon">
                                    <i data-url="{{route('equipment.delete',['id'=>$row->id])}}" data-id="{{$row->id}}" class="deleteEquipment la la-trash"></i>
                                </span>
                            </td> --}}
                        </tr>
                        {{-- @php
                        $counter++;
                        @endphp --}}
                        @endforeach
                        {{-- @endif --}}
                    </tbody>
                </table>      
</div>

</div>
