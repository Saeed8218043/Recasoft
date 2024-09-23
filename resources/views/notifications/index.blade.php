@extends('layouts.app')

@section('content')
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
        <!-- BEGIN: Subheader -->
        <div class="m-subheader ">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="d-flex justify-content-end">
                        {{-- Buttons Group --}}
                        <div class="btn-group" role="group" aria-label="Large button group">
                            <button type="button" id="setup" data-toggle="modal" data-target="#m_modal_5"
                                class="m-btn btn btn-outline-primary active">Setup</button>
                            <button type="button" id="history" data-toggle="modal" data-target="#m_modal_5"
                                class="m-btn btn btn-outline-primary">History</button>
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
                @if (\Session::has('message'))
                    {{-- <div class="alert alert-success">{{\Session::get('message')}}</div> --}}

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ \Session::get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <!-- Tags -->
                <div class="mt-1 mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3 font-weight-bold">Notifications</h4>
                        </div>
                        <div class="col-md-6 d-flex justify-content-md-end">
                            <a href="{{ url('create-notification') }}/{{ $company_id }} " class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"
                                    style="margin-top: -4px; margin-right: 4px;">
                                    <path fill="#ffffff" fill-rule="evenodd"
                                        d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z"
                                        clip-rule="evenodd"></path>
                                </svg> Create new Notification
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Tags end -->

                <!-- Table Starts -->
                <table class="table has-valign-middle table-responsive table-hover table-borderless">
                    <thead>
                        <tr>
                            <th width="65%">
                                NAME
                            </th>
                            <th width="15%" class="text-left">
                                TRIGGER
                            </th>
                            <th width="10%" class="text-center">
                                DEVICES
                            </th>
                            <th width="10%">
                                RECIPIENTS
                            </th>
                            <th width="8%" class="text-center">
                                REMOVE
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            {{-- @php
									$comp_id = App\company::where('company_id',$company_id)->first();
									@endphp
									@if ($notification->company_id != $comp_id->company_id)

									@endif
 									@else --}}
                            <tr
                                data-url="{{ route('notification.detail', ['company_id' => $company_id, 'id' => $notification->id]) }}">
                                <td > 
                                    <a href="{{ route('notification.detail', ['company_id' => $company_id, 'id' => $notification->id]) }}" class="font-weight-bold"
                                        style="text-decoration: none; color: black">{{ $notification->name }} @if (isset($notification->isActive) && $notification->isActive == 0)
                                            (Disabled)
                                        @endif
                                    </a>
                                </td>
                                <td class="text-left">
                                    <a href="{{ route('notification.detail', ['company_id' => $company_id, 'id' => $notification->id]) }}"
                                        style="text-decoration: none; color: black">{{ $notification->alert_type }}</a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('notification.detail', ['company_id' => $company_id, 'id' => $notification->id]) }}"
                                        style="text-decoration: none; color: black">{{ count($notification->devices) }}</a>
                                </td>

                                @php
                                    $totalmails = App\NotificationEmail::where('notification_id', $notification->id)
                                        ->where('notification_type', 0)
                                        ->get();
                                    $totalEmails = 0;
                                    
                                    foreach ($totalmails as $email) {
                                        $string = $email->email;
                                        $string = preg_replace('/\.$/', '', $string); //Remove dot at end if exists
                                        $array = explode(',', $string); //split string into array seperated by ', '
                                        foreach (
                                            $array
                                            as $value //loop over values
                                        ) {
                                            $totalEmails++;
                                        }
                                    }
                                    
                                    $SMSES = App\NotificationEmail::where('notification_id', $notification->id)
                                        ->where('notification_type', 1)
                                        ->get();
                                    $totalSMS = 0;
                                    
                                    foreach ($SMSES as $sms) {
                                        $stringg = $sms->email;
                                        $stringg = preg_replace('/\.$/', '', $stringg); //Remove dot at end if exists
                                        $arrayy = explode(',', $stringg); //split string into array seperated by ', '
                                    
                                        foreach (
                                            $arrayy
                                            as $valuee //loop over values
                                        ) {
                                            $totalSMS++;
                                        }
                                    }
                                @endphp
                                <td>
                                    <a href="{{ route('notification.detail', ['company_id' => $company_id, 'id' => $notification->id]) }}"
                                        style="text-decoration: none; color: black">
                                        @if ($totalEmails > 0)
                                            Emails {{ $totalEmails }}
                                        @endif
                                        @if ($totalSMS > 0)
                                            SMS {{ $totalSMS }}
                                        @endif
                                        <svg class="fs-18" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                            viewBox="0 0 24 24" fit="" preserveAspectRatio="xMidYMid meet"
                                            focusable="false">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                            </path>
                                        </svg>
                                    </a>
                                </td>


                                <td class="text-center">
                                    <svg class="fs-18 deleteNotification " xmlns="http://www.w3.org/2000/svg" width="1em"
                                        height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36"
                                        url="{{ url('delete/notification', $notification->id) }}" data-toggle="modal"
                                        data-target="#m_modal_3">
                                        <path fill="#207da9"
                                            d="M18 2a16 16 0 1 0 16 16A16 16 0 0 0 18 2Zm8 22.1a1.4 1.4 0 0 1-2 2l-6-6l-6 6.02a1.4 1.4 0 1 1-2-2l6-6.04l-6.17-6.22a1.4 1.4 0 1 1 2-2L18 16.1l6.17-6.17a1.4 1.4 0 1 1 2 2L20 18.08Z"
                                            class="clr-i-solid clr-i-solid-path-1"></path>
                                        <path fill="none" d="M0 0h36v36H0z"></path>
                                    </svg>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                <!-- Table Ends -->
            </div>

        </div>
    </div>

    <!-- BEGIN: Subheader -->








    <!--begin::Modal-->
    <div class="modal fade" id="m_modal_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="delete_notification_form" method="GET">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to delete Notificaiton?</p>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" onclick="DeleteCommentFunction()">Yes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            var company_id = "{{ $company_id ?? '' }}";
            $(document).on('click', '#setup', function() {
                $('#history').removeClass('active');
                window.location.href = '{{ url('notifications') }}' + '/' + company_id;

            });
            $(document).on('click', '#history', function() {
                $('#setup').removeClass('active');
                window.location.href = '{{ url('notifications-alertHistory') }}' + '/' + company_id;

            });
            $(document).on('click', '.deleteNotification', function() {
                let url = $(this).attr('url');
                $('.delete_notification_form').attr('action', url);

            });
        });
    </script>
@endpush
