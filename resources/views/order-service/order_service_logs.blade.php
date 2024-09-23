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
                    <h3 class="m-subheader__title "> </h3>
                </div>
                <div class="col-lg-8 col-md-8">
				  <div class="d-flex justify-content-end">
                        {{-- Buttons Group --}}
                        <div class="btn-group" role="group" aria-label="Large button group">
                             <div class="btn-group" role="group" aria-label="Large button group">
                            <button type="button" id="Order_Service" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary ">Order Service</button>
                            <button type="button" id="log" data-toggle="modal" data-target="#m_modal_5" class="m-btn btn btn-outline-primary active">Log</button>
                        </div>
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
            <h4 class="font-weight-bold">Order Service logs </h4>
                <!-- Table Starts -->
                <table class="table has-valign-middle table-striped- table-hover table-checkable has-valign-middle table-responsive table-borderless">
                    <thead>
                        <tr>
                            <th width="15%">
                                Company Name
                            </th>
                            <th width="25%">
                               Request email
                            </th>
                            <th >
                               Phone
                            </th>
                            <th width="20%">
                               Devices
                            </th>
                            <th width="25%">
                                Body
                            </th>
                            <th >
                                Urgent
                            </th>
                            <th width="8%">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $history)
                            
                            <tr>

                                <td>{{ $history->company_name }} </td>
                                <td>
                                  
                                    {!! preg_replace('/,/', '<br/>', $history->request_email) !!}
                                </td>
                                <td>
                                    {{$history->phone }}
                                </td>
                                <td>
                                    {!! preg_replace('/,/', '<br/>', $history->devices) !!}
                                </td>

                                <td>

                                    <div class="limitBody">
                                        <span class="full-body" id={{ 'showBody' . $history->id }}> {!! nl2br($history->comments) !!}
                                        </span>

                                        <span class="long body" id={{ 'history-text' . $history->id }}>
                                            {!! substr($history->comments, 0,  30)  !!}
                                        </span>
                                        @if (strlen($history->comments) > 20)
                                        <a data-id={{ $history->id }} class="showMore"
                                            id={{ 'showMore' . $history->id }}>More</a>
                                        <a data-id={{ $history->id }} class="showLess"
                                            id={{ 'showLess' . $history->id }}>Less</a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                {{ $history->urgent }}
                                </td>
                                <td>{{ $history->updated_at }} </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- Table Ends -->
                {{ $logs->onEachSide(5)->links() }}
            </div>

        </div>

    </div>
    <script>
        var company_id = "{{$company_id??''}}";
            $(document).on('click', '#Order_Service', function() {
                $('#log').removeClass('active');
                  window.location.href = '{{url("sendOrder-service")}}'+'/'+company_id;
                  
            });
            $(document).on('click', '#log', function() {
                $('#Order_Service').removeClass('active');
                  window.location.href = '{{url("order-service-logs")}}'+'/'+company_id;

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
