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

<div class="m-portlet panel-has-radius mb-4 custom-p-5">
    @php
        $myuser = Session::get('newArray');
        
    @endphp
    <!-- Tags -->
    @if(isset($myuser))
    <div class="mb-2">
        <ul class="breadcrumb">
            <li><a class="chooseFile btn" folder_id="{{$id}}">Home</a> </li>
            @foreach ($myuser['data'] as $data)
                @php
                    $folder = \App\Document::where('id', $data)->first();
                @endphp
                <li><a class="folders btn" folder_id="{{ $folder->id }}" slug="{{$folder->slug}}" >{{ $folder->name }} </a>
                </li>
                @if ($folder->slug == $slug)
                @break;
            @endif
        @endforeach
    </ul>
</div>
@endif
<!--begin: Datatable -->
<div class="table-responsive p-4">
    <table class="table table-striped- table-bordered table-hover table-checkable has-valign-middle table-borderless"
        id="m_table_1">
        <thead>
            <tr>
                <th width="1%" style="text-align: center">Choose</th>
                <th width="2%" style="text-align: center">TYPE</th>
                <th width="55%">NAME</th>
                <th>Created By</th>
                <th width="2%">Documents</th>
                

            </tr>
        </thead>
        <tbody>
            @if (isset($documents))
                @foreach ($documents as $row)
                    <tr id="row" file_name="{{$row->name}}" file_id="{{$row->id}}">

                        <td style="text-align: center">
                            @if($row->type==1)
                            <input type="radio" class="radio sensor_doc" id="{{$row->slug}}" name="sensor_doc" value="{{$row->file}}">

                            @endif
                        </td>
                        <td align="center">
                            @if ($row->type == 0)
                                <a class="folders iconHolder fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative"
                                    folder_id ="{{ $row->id }}" slug="{{$row->slug}}"
                                    style="color:#212529;text-decoration:none;display:block;">

                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20px" height="20px">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="32"
                                            d="M64 192v-72a40 40 0 0 1 40-40h75.89a40 40 0 0 1 22.19 6.72l27.84 18.56a40 40 0 0 0 22.19 6.72H408a40 40 0 0 1 40 40v40">
                                        </path>
                                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="32"
                                            d="M479.9 226.55L463.68 392a40 40 0 0 1-39.93 40H88.25a40 40 0 0 1-39.93-40L32.1 226.55A32 32 0 0 1 64 192h384.1a32 32 0 0 1 31.8 34.55Z">
                                        </path>
                                    </svg>
                            @endif
                            @if ($row->type == 1)
                                <a  class="iconHolder fig-40 sensor-icon-main bg-white shadow panel-has-radius c-border-1 mb-0 mr-2 relative" style="color:#212529;text-decoration:none;display:block;">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="20px"
                                        height="20px" viewBox="0 0 256 256" xml:space="preserve">

                                        <defs>
                                        </defs>
                                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;"
                                            transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                            <path
                                                d="M 77.474 17.28 L 61.526 1.332 C 60.668 0.473 59.525 0 58.311 0 H 15.742 c -2.508 0 -4.548 2.04 -4.548 4.548 v 80.904 c 0 2.508 2.04 4.548 4.548 4.548 h 58.516 c 2.508 0 4.549 -2.04 4.549 -4.548 V 20.496 C 78.807 19.281 78.333 18.138 77.474 17.28 z M 61.073 5.121 l 12.611 12.612 H 62.35 c -0.704 0 -1.276 -0.573 -1.276 -1.277 V 5.121 z M 74.258 87 H 15.742 c -0.854 0 -1.548 -0.694 -1.548 -1.548 V 4.548 C 14.194 3.694 14.888 3 15.742 3 h 42.332 v 13.456 c 0 2.358 1.918 4.277 4.276 4.277 h 13.457 v 64.719 C 75.807 86.306 75.112 87 74.258 87 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 68.193 33.319 H 41.808 c -0.829 0 -1.5 -0.671 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 26.385 c 0.828 0 1.5 0.671 1.5 1.5 S 69.021 33.319 68.193 33.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 34.456 33.319 H 21.807 c -0.829 0 -1.5 -0.671 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 12.649 c 0.829 0 1.5 0.671 1.5 1.5 S 35.285 33.319 34.456 33.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <linearGradient id="SVGID_1" gradientUnits="userSpaceOnUse"
                                                x1="21.8064" y1="19.2332" x2="42.2984" y2="19.2332">
                                                <stop offset="0%"
                                                    style="stop-color:rgb(255,255,255);stop-opacity: 1" />
                                                <stop offset="100%"
                                                    style="stop-color:rgb(0,0,0);stop-opacity: 1" />
                                            </linearGradient>
                                            <line x1="-10.246" y1="0" x2="10.246" y2="0"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: url(#SVGID_1); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " />
                                            <path
                                                d="M 42.298 20.733 H 21.807 c -0.829 0 -1.5 -0.671 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 20.492 c 0.829 0 1.5 0.671 1.5 1.5 S 43.127 20.733 42.298 20.733 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 68.193 44.319 H 21.807 c -0.829 0 -1.5 -0.671 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 46.387 c 0.828 0 1.5 0.671 1.5 1.5 S 69.021 44.319 68.193 44.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 48.191 55.319 H 21.807 c -0.829 0 -1.5 -0.672 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 26.385 c 0.828 0 1.5 0.672 1.5 1.5 S 49.02 55.319 48.191 55.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 68.193 55.319 H 55.544 c -0.828 0 -1.5 -0.672 -1.5 -1.5 s 0.672 -1.5 1.5 -1.5 h 12.649 c 0.828 0 1.5 0.672 1.5 1.5 S 69.021 55.319 68.193 55.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 68.193 66.319 H 21.807 c -0.829 0 -1.5 -0.672 -1.5 -1.5 s 0.671 -1.5 1.5 -1.5 h 46.387 c 0.828 0 1.5 0.672 1.5 1.5 S 69.021 66.319 68.193 66.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                            <path
                                                d="M 68.193 77.319 H 55.544 c -0.828 0 -1.5 -0.672 -1.5 -1.5 s 0.672 -1.5 1.5 -1.5 h 12.649 c 0.828 0 1.5 0.672 1.5 1.5 S 69.021 77.319 68.193 77.319 z"
                                                style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                        </g>
                                    </svg>
                                </a>
                            @endif
                        </td>
                        
                        <td  @if ($row->type == 0) class="folders" @endif folder_id ="{{ $row->id }}" slug="{{$row->slug}}">
                            <a id="{{ $row->id }}"
                                {{-- @if ($row->type == 0) href="{{ url('documents') }}/{{ $company_id }}/{{ $row->slug }}" @endif --}}
                                style="color:#212529;text-decoration:none;display:block;"><label for="{{$row->slug}}">{{ $row->name }}</label> </a>
                        </td>
                        @php
                            $folder = \App\Document::where('slug', $row->slug)->first();
                            $roles = \App\CompanyMembers::where('user_id', $folder->user_id)->first();
                            $role = isset($roles->role) ? $roles->role : '';
                            
                        @endphp
                        <td>
                            @if ($role == null)
                                <span class="badge badge-pill badge-dark role">Super Admin</span>
                            @endif
                            @if ($role == 1)
                                <span class="badge badge-pill badge-success role">User</span>
                            @endif
                            @if ($role == 2)
                                <span class="badge badge-pill badge-info role">Company Admin</span>
                            @endif
                        </td>

                        <td align="center">
                            {{ $row->children_count }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

</div>
