@extends('layouts.app')

@section('content')
    <style>
        .element {
            width: 100%;
            /*background-color: skyblue;*/
            /*  text-align: center;
                      color: white;
                      font-size: 3em;*/
        }

        #fs-exit-doc-button {
            display: none;
        }

        #fs-doc-button {
            display: inline-block;
        }

        body.fullScreenClass #fs-doc-button {
            display: none;
        }

        body.fullScreenClass #fs-exit-doc-button {
            display: inline-block;
        }

    </style>

    <div class="m-grid__item m-grid__item--fluid m-wrapper">

        <!-- BEGIN: Subheader -->
        <div class="m-subheader">
            <div class="d-flex justify-content-between">
                <div>&nbsp;</div>
                <button class="btn btn-primary" data-target="#m_modal_3" data-toggle="modal"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 1C5.925 1 1 5.925 1 12s4.925 11 11 11s11-4.925 11-11S18.075 1 12 1Zm1 15a1 1 0 1 1-2 0v-3H8a1 1 0 1 1 0-2h3V8a1 1 0 1 1 2 0v3h3a1 1 0 1 1 0 2h-3v3Z" clip-rule="evenodd"></path></svg> New Account</button>
            </div>
        </div>
        <!-- END: Subheader -->
        <div class="m-content" style="width: 100%;">

            <!--Begin::Section-->

            <div class="m-portlet panel-has-radius mb-4 custom-p-5">
                <!-- Tags -->
                <div class="mb-1 d-flex align-items-center flex-wrap">


                    <!--begin: Datatable -->
                    <div class="table-responsive">
                        <table
                            class="table table-striped- table-borderless table-hover table-checkable has-valign-middle border-0">
                            <thead>

                                <tr>
                                    <th width="50%">Project Name</th>
                                    <th width="55%">Project Owner Name</th>
                                    <th width="20%">Sub Projects</th>
                                    <th width="20%" class="d-none d-sm-table-cell">Connectors</th>
                                    <th width="20%">Sensors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($array as $row)
                                    @php
                                        $projectsCount = 0;
                                        $projectsCount = App\Company::where('parent_id', $row->id)->count();
                                         $childCompanies = App\Company::where('parent_id', $row->id)->get();
                                    @endphp
                                    <tr class="company" company_id="{{$row->company_id}}">
                                        <td class="company-name" company_id="{{$row->company_id}}">{{ $row->name }}</td>
                                        <td class="company-email" company_id="{{$row->company_id}}">{{ $row->email }}</td>
                                        <td> @if ($projectsCount > 0)
                                            <a href="#" class="toggle-children" company_id="{{$row->company_id}}">{{ $projectsCount }} Sub Projects</a>
                                            @endif
                                        </td>
                                        <td>{{ $row->connTotal }}</td>
                                        <td>{{ $row->sensorTotal }}</td>
                                    </tr>
                                     @foreach ($childCompanies as $child)
                                     @php
                                     $subcompany = "select c.parent_id,c.name, c.company_id, d.device_id,
                                        count(IF(d.event_type='ccon',1,null)) as connTotal,
                                        count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                                        from companies c
                                        left join devices d on ( d.company_id = c.company_id AND d.device_status = 1)
                                        where c.is_active=1 AND c.company_id='$child->company_id'
                                        group by c.company_id";
                                        $subTotal= DB::select($subcompany);
                                     @endphp
                                        <tr class="sub-company" company_id="{{$child->company_id}}" style="background: gainsboro; " parent_id="{{ $row->company_id }}">
                                            <td class="company-name" company_id="{{$child->company_id}}">{{ $child->name }}</td>
                                            <td class="company-email" company_id="{{$child->company_id}}">{{ $child->email }}</td>
                                            <td>{{ $child->projectsCount }}</td>
                                            <td>{{ $subTotal[0]->connTotal }}</td>
                                            <td>{{ $subTotal[0]->sensorTotal }}</td>
                                        </tr>
                                        @endforeach
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
   



    <div class="modal fade" id="modal-support" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportModalLabel">Create Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>




            <div class="modal-body">
                @php
                    // $compID = isset($currentCompany->company_id) ? $currentCompany->company_id : '-';
                @endphp
                <form method="post" action="{{ route('companies.storeCompanyWithApi') }}">
                    @csrf
                    <div class="mb-3">
                        <label>
                           Account Name
                        </label>
                        <input type="text" class="form-control" name="company_name"
                            {{-- value="{{ $currentCompany->company_id ?? '' }}"  --}}
                            required>
                    </div>

                    <div class="mb-3">
                        <label>
                            Email
                        </label>
                        <input type="text" class="form-control" name="company_email"
                            {{-- value="{{ $currentCompany->name ?? '' }}"  --}}
                            required>
                    </div>
                    <div class="mb-3">
                        <label>
                            Phone Number
                        </label>
                        <input type="text" class="form-control" name="company_phone" required>
                    </div>
                   
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <button type="button" class="btn btn-secondary mx-1" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary mx-1">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@push('scripts')
    <script src="https://js.pusher.com/3.1/pusher.min.js"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.timeago.js') }}"></script>
    <script>
        $(document).ready(function() {
        $('.sub-company').hide();

            moment.tz.setDefault('Europe/Oslo');
        });
    // Add click event listener to main company rows
    $('.toggle-children').click(function() {
      const companyId = $(this).attr('company_id');
      // Toggle the visibility of child rows with the matching parent_id attribute
      $(`.sub-company[parent_id="${companyId}"]`).toggle();
    });
$('.company-name,.company-email').on('click',function(){
            var company_id =$(this).attr('company_id');
            let url = "/dashboard/"+company_id;
            window.location.href = url;
        });
       /* $('.company').on('click',function(){
            var company_id =$(this).attr('company_id');
            let url = "/dashboard/"+company_id;
            window.location.href = url;
        }); */
    // Add click event listener to main company rows
  /*  $('.toggle-children').click(function() {
      const companyId = $(this).attr('company_id');
      $('.sub-company').addClass('less');
      $('.less').css('display','block');
      // Toggle the visibility of child rows with the matching parent_id attribute
      $(`.sub-company[parent_id="${companyId}"]`).toggle();
    }); */
    $('.less').on('click',function(){
        $('.sub-company').hide();
    });
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        });

        var sensors_list = @json($sensors);
        var pusher = new Pusher('ece81d906376bc8c0bab', {
            cluster: 'ap2',
            encrypted: true
        });

        function loadGraps(event_id) {
            for (let i = 0; i < sensors_list.length; i++) {
                console.log(sensors_list[i].device_id);
                setTimeout(() => {
                    requestData(sensors_list[i].id, sensors_list[i].device_id, event_id);
                    // Initiate the Pusher JS library


                    // Subscribe to the channel we specified in our Laravel Event
                    var channel = pusher.subscribe('my-channel.' + sensors_list[i].device_id);

                    // Bind a function to a Event (the full Laravel class)
                    channel.bind('App\\Events\\HelloPusherEvent', function(data) {
                        if (data) {
                            requestData(sensors_list[i].id, sensors_list[i].device_id, event_id);
                            console.log('Pusher = ', data);
                        }
                    });
                }, 500);
            }
        }
        $(function() {
            loadGraps(2);
            $(".btn_segment").on("click", function() {
                console.log($(this).data('value'));
                $('.btn_segment').removeClass('radio-active');
                setTimeout(() => {
                    $(this).addClass('radio-active');
                }, 100)

                loadGraps($(this).data('value'));
            });
        });
        var chartAr = [];
        var intervalAr = [];

        function requestData(id, device_id, event_id) {

            Highcharts.getJSON('{{ url('events') }}' + '/' + device_id + '/' + event_id, function(data) {
                if (data.device_status && data.device_status == 1) {
                    $('#temperature-' + id).html(data.temperature);
                    $('#temeprature_last_updated-' + id).find('.timeago-' + id).html(data.temeprature_last_updated);

                } else {
                    $('#temperature-' + id).closest('.temperature').html('Offline');
                    $('#temeprature_last_updated-' + id).find('.timeago-' + id).html(data.temperature + '°C | ' +
                        data.temeprature_last_updated);

                }

                $('#average-' + id).html(data.average);
                $('#min_value-' + id).html(data.min_value);
                $('#max_value-' + id).html(data.max_value);

                /*var millis = new Date(data.temeprature_last_updated);
                var milliseconds = millis.getTime();*/
                var now = new Date();
                var UTC_DIFFERENCE = now.getTimezoneOffset() * 60;
                var newTime = (data.milliseconds) + (UTC_DIFFERENCE);
                var newTime2 = new Date(newTime);

                if (intervalAr[id]) {
                    clearInterval(intervalAr[id]);
                    console.log('Clear Interval');
                }
                intervalAr[id] = setInterval(function() {
                    $("time.timeago-" + id).timeago('update', newTime2);
                }, 1000);

                /*var millis = new Date(data.temeprature_last_updated);
                var milliseconds2 = millis.getTime();
                var now2 = new Date();
                var UTC_DIFFERENCE2 = now2.getTimezoneOffset()*60;
                var newTime2 = (milliseconds2*1000)+(UTC_DIFFERENCE2);
                var newTime22 = new Date(newTime2);
                var interval = setInterval(function(){
                    $("time.timeago-"+id).timeago('update',newTime22);
                },1000);*/

                if (chartAr[id]) {

                    console.log('before Destroy()');
                    chartAr[id].destroy();
                }

                /*const chart =*/
                chartAr[id] = Highcharts.stockChart('graph-container-' + id, {
                    scrollbar: {
                        enabled: false
                    },
                    chart: {
                        height: 120
                    },

                    title: {
                        text: ''
                    },

                    subtitle: {
                        text: ''
                    },
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            format: '{value:%b %e}'
                        },
                        minRange: 3600 * 1000 // one hour
                    },

                    yAxis: {
                        opposite: false,
                        allowDecimals: false,
                        labels: {
                            formatter: function() {
                                return this.value + '°C';
                            }
                        }
                    },
                    rangeSelector: {
                        /*allButtonsEnabled: true,
                        buttons: [{
                            type: 'hour',
                            count: 1,
                            text: '1h'
                        }, {
                            type: 'day',
                            count: 1,
                            text: '1d'
                        }, {
                            type: 'week',
                            count: 1,
                            text: '1w'
                        }, {
                            type: 'month',
                            count: 1,
                            text: '1m'
                        }],*/
                        enabled: false
                        // selected: 1,
                    },

                    series: [{
                        name: 'Temperature',
                        color: '#3e4a4f',
                        lineColor: '#3e4a4f',
                        data: data.data,
                        type: 'spline',
                        step: true,
                        /*tooltip: {
                            valueDecimals: 1,
                            valueSuffix: '°C',
                        }*/
                    }],

                    tooltip: {
                        formatter: function() {
                            var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M', this.x);
                            var html = this.y.toFixed(2) + '°C on ' + dateVl;
                            $("#toolTipValue-" + device_id).html(html);
                            return false;
                        }
                    },

                    credits: {
                        enabled: false
                    },

                    exporting: {
                        enabled: false
                    },

                    navigator: {
                        enabled: false
                    }
                });

            });
        }

        /*setInterval(function(){
            $("time.timeago-89").timeago();
        },1000);*/

        $("#m_sortable_portlets").sortable({
            handle: ".header",
            revert: true,
            opacity: 0.7,
            disabled: false,
            scroll: true,
            items: '.connectedSortable',
            // containment: '#m_sortable_portlets',
            start: function(event, ui) {},
            stop: function(event, ui) {
                var selectedData2 = new Array();
                $('.connectedSortable').each(function() {
                    selectedData2.push($(this).attr("id"));
                });
                updateOrderItem(selectedData2);
            }
        });

        function updateOrderItem(data) {
            $.ajax({
                url: '{{ url('update-order') }}',
                type: 'post',
                data: {
                    position: data
                },
                success: function(data) {}
            });
        }
    </script>
@endpush
