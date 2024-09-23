@extends('layouts.app')

@section('content')
    <style>
        .full-body {
            display: none
        }

        .showMore {
            color: blue !important;
        }

        .showLess {
            display: none;
            color: blue !important;
        }
        .pagination a {
    font-size: 14px; /* Adjust the size as needed */
}
.pagination .pagination-previous, .pagination .pagination-next {
    font-size: 16px; /* Adjust the size as needed */
}
        .limitBody {
           width: 240px;
           white-space: normal;
       }
       nav{
    align-items: center;
    text-align: center;

       }
       .pagination{
         display: -webkit-inline-box;
       }
      
    </style>
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <div class="m-subheader ">
            <div class="row">
            <div class="col-lg-4 col-md-4">
                </div>
                <div class="col-lg-8 col-md-8">
				  <div class="d-flex justify-content-end">
                        {{-- Buttons Group --}}
                        <div class="btn-group" role="group" aria-label="Large button group">
                            <button type="button" id="setup" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary">Setup</button>
                            <button type="button" id="history" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary active">History</button>
                        </div>
                        {{-- Buttons Group ends --}}
				 </div>
			  </div>
            </div>
        </div>

        <!-- END: Subheader -->
        <div class="m-content">

            <!--Begin::Section-->

            <div class="m-portlet panel-has-radius mb-4 custom-p-5">
            <div>
                    <h4 class="mb-3 font-weight-bold">Notifications History</h4>
                </div>
                <!-- Table Starts -->
                <table class="table has-valign-middle table-striped- table-borderless table-hover table-checkable has-valign-middle border-0 table-responsive">
                    <thead>
                        <tr>
                            <th width="15%">
                                Device Name
                            </th>
                            <th width="20%">
                                Email/Number
                            </th>
                            <th width="25%">
                                Body
                            </th>
                            <th width="8%">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alert_history as $history)
                            {{-- @php
                            $comp_id = App\company::where('company_id',$company_id)->first();
                            @endphp
                            @if ($notification->company_id != $comp_id->company_id)

                            @endif
                             @else --}}
                             @php
                             $device = \App\Device::where('device_id',$history->device_id)->first();
                            $device_name = isset($device->name)?$device->name:'';
                             @endphp
                            <tr>

                                <td>{{ !empty($device_name)?$device_name:$history->device_id }} </td>
                                <td>
                                    {{-- {{ $history->email }}  --}}
                                    {!! preg_replace('/,/', '<br/>', $history->email) !!}
                                </td>

                                <td>

                                    <div class="limitBody">
                                        <span class="full-body" id={{ 'showBody' . $history->id }}> {!! nl2br($history->body) !!}
                                        </span>

                                        <span class="long body" id={{ 'history-text' . $history->id }}>
                                            {!! substr($history->body, 0,  30)  !!}
                                        </span>
                                        <a data-id={{ $history->id }} class="showMore"
                                            id={{ 'showMore' . $history->id }}>More</a>
                                        <a data-id={{ $history->id }} class="showLess"
                                            id={{ 'showLess' . $history->id }}>Less</a>
                                    </div>
                                </td>
                                <td>{{ $history->updated_at }} </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- Table Ends -->
                {{ $alert_history->onEachSide(5) }}
            </div>

        </div>

    </div>
    <script>
        var company_id = "{{$company_id??''}}";
            $(document).on('click', '#setup', function() {
                $('#history').removeClass('active');
                  window.location.href = '{{url("notifications")}}'+'/'+company_id;
                  
            });
            $(document).on('click', '#history', function() {
                $('#setup').removeClass('active');
                  window.location.href = '{{url("notifications-alertHistory")}}'+'/'+company_id;

            });
        $('.showMore').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showBody" + id).attr("style", "display:block");
            $("#history-text" + id).attr("style", "display:none");
            $("#showMore" + id).attr("style", "display:none");
            $("#showLess" + id).attr("style", "display:block");
        });
        $('.showLess').on('click', function() {
            var id = $(this).attr('data-id');

            $("#showBody" + id).attr("style", "display:none");
            $("#history-text" + id).attr("style", "display:block");

            $('#showLess' + id).css('display', 'none');
            $('#showMore' + id).css('display', 'block');
        });
    </script>
@endsection
