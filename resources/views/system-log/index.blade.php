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

        .role {
            padding: 7px;
            width: 110px
        }
    
      
    </style>
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <div class="m-subheader ">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <h3 class="m-subheader__title ">System Logs</h3>
                </div>
                
            </div>
        </div>

        <!-- END: Subheader -->
        <div class="m-content">

            <!--Begin::Section-->

            <div class="m-portlet panel-has-radius mb-4 p-2">
                <!-- Table Starts -->
                <table class="table has-valign-middle table-striped- table-bordered table-hover table-checkable has-valign-middle border-0 table-responsive">
                    <thead>
                        <tr>
                            <th width="10%" style="text-align: center;">
                                Actions
                            </th>
                            <th width="20%">
                                User name
                            </th>
                            <th width="45%">
                                Body
                            </th>
                            <th width="8%">
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($system_logs as $history)
                          
                                <td style="text-align: center;">
                                @if($history->actions == "Create" || $history->actions == "Claimed" || $history->actions == "Download")
                                <span class="badge badge-pill badge-success role">{{ $history->actions }} </span>
                                @endif
                                
                                @if($history->actions == "Delete")
                                <span class="badge badge-pill badge-danger role">{{ $history->actions }} </span>
                                @endif
                                
                                @if($history->actions == "Update")
                                <span class="badge badge-pill badge-info role">{{ $history->actions }} </span>
                                @endif

                                @if($history->actions == "Moved")
                                <span class="badge badge-pill badge-dark role">{{ $history->actions }} </span>
                                @endif

                                @if($history->actions == "Order Service")
                                <span class="badge badge-pill badge-warning role">{{ $history->actions }} </span>
                                @endif
                                
                                @if($history->actions == "Csv export")
                                <span class="badge badge-pill badge-secondary role">{{ $history->actions }} </span>
                                @endif
                                
                                </td>
                                <td>
                                    {{-- {{ $history->email }}  --}}
                                    {{ $history->user }}
                                </td>

                                <td>
                                    {{ $history->log_message }}
                                    {{-- <div class="limitBody">
                                        <span class="full-body" id={{ 'showBody' . $history->id }}> {!! nl2br($history->log_message) !!}
                                        </span>

                                        <span class="long body" id={{ 'history-text' . $history->id }}>
                                            {!! substr($history->log_message, 0,  40)  !!}
                                        </span>
                                        <a data-id={{ $history->id }} class="showMore"
                                            id={{ 'showMore' . $history->id }}>More</a>
                                        <a data-id={{ $history->id }} class="showLess"
                                            id={{ 'showLess' . $history->id }}>Less</a>
                                    </div> --}}
                                </td>
                                <td>{{ $history->updated_at }} </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- Table Ends -->
                {{ $system_logs->onEachSide(5)->links() }}
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
