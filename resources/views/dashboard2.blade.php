@extends('layouts.app2')
@section('content2')
<style>
.element {
  width: 100%;
  /*background-color: skyblue;*/
/*  text-align: center;
  color: white;
  font-size: 3em;*/
}
.element:-ms-fullscreen p {
  visibility: visible;
}
.m-footer{
    margin-left: 0% !important;
}
.element:fullscreen {
  /*background-color: #e4708a;*/
  background-color: #F1F3F7;
  width: 100vw;
  height: 100vh;
}
.app_reca_login{
    display: flex ;
    margin-left: 4px;
    border-radius: 0 !important;
}
#fs-exit-doc-button{
    display: none;
}
#fs-doc-button{
    display: inline-block;
}
body.fullScreenClass #fs-doc-button{
    display: none;
}

body.fullScreenClass #fs-exit-doc-button{
    display: inline-block;
}
@media screen and (max-width:425px){
    .app_reca_login{
    display: flex ;
    
}
.nav_parent{
    width: 100%;
    justify-content: space-between;
}
}
</style>
<div class="m-grid__item m-grid__item--fluid m-wrapper element" id="element">

    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">

        <div class="titleRow">
            <div class="titleRow_x">
                <h3 class="m-subheader__title subheader__title_avgscreen">
                    @if(isset($company_name) && $company_name!='')
                                     {{$company_name}}
                                @else

                                @endif
                         Dashboard
                </h3>
                <div class="m-subheader__title_fullscreen">
                    <h3 class="m-subheader__title ">
                                @if(isset($company_name) && $company_name!='')
                                     {{$company_name}}
                                @else

                                @endif
                         Dashboard
                     </h3>
                  <!--   <p>11:42:29 Monday, February 14 2022</p> -->
                </div>
            </div>
            <div class="titleRow_y">
                <img alt="" src="{{url('public/assets/demo/default/media/img/logo/logo-dark.svg')}}"
                    class="img-fluid logo" />
            </div>
            <div class="titleRow_z">
                <div class="d-flex align-items-center nav_parent">
                    <div class="btn-group m-btn-group mr-2 bg-light-grey p-2 border-radius-1" role="group"
                        aria-label="...">
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment" data-value="1">Day</button>
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment radio-active" data-value="2">Week</button>
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment" data-value="3">Month</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" id="fs-doc-button" onclick="var el = document.getElementById('element'); el.requestFullscreen();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><path fill="currentColor" d="M4 3.5a.5.5 0 0 0-.5.5v1.614a.75.75 0 0 1-1.5 0V4a2 2 0 0 1 2-2h1.614a.75.75 0 0 1 0 1.5H4Zm5.636-.75a.75.75 0 0 1 .75-.75H12a2 2 0 0 1 2 2v1.614a.75.75 0 0 1-1.5 0V4a.5.5 0 0 0-.5-.5h-1.614a.75.75 0 0 1-.75-.75ZM2.75 9.636a.75.75 0 0 1 .75.75V12a.5.5 0 0 0 .5.5h1.614a.75.75 0 0 1 0 1.5H4a2 2 0 0 1-2-2v-1.614a.75.75 0 0 1 .75-.75Zm10.5 0a.75.75 0 0 1 .75.75V12a2 2 0 0 1-2 2h-1.614a.75.75 0 1 1 0-1.5H12a.5.5 0 0 0 .5-.5v-1.614a.75.75 0 0 1 .75-.75Z"/></svg> Fullscreen
                        </button>
                     <!--    <button onclick="var el = document.getElementById('element'); el.requestFullscreen();">
					    Go Full Screen!
					  </button> -->
                      <button type="button" class="btn btn-default" id="fs-exit-doc-button" onclick="document.exitFullscreen()">
                        Exit Fullscreen
                    </button>
                    {{-- <button type="button" class="btn btn-default">
                        <i class="la la-plus-circle"></i> Create new card
                    </button> --}}
                </div>
                <div>

                    <li class="m-nav__item app_reca_login">
                        <a href="{{url('https://app.recasoft.com/login')}}" class="btn btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder" style="font-family: 'Open Sans';">Login</a>
                    </li>
                </div>
                </div>
            </div>
        </div>
        <!-- ends titleRow -->

    </div>

    <!-- END: Subheader -->
    <div class="m-content" style="padding: 30px;">

        <!--Begin::Section-->

        <div class="row" id="m_sortable_portlets">

            @if(isset($sensors) && count($sensors)>0)
            @foreach($sensors as $row)
            @php 
            $connected_equipment = App\Device::where('sensor_id',$row->device_id)->first();
            @endphp
            @if(($connected_equipment !='' || $connected_equipment!=null))
            <div class="col-md-6 connectedSortable" id="{{$row->id}}">
                <div class="@if(isset($row->is_active) && $row->is_active==0) {{-- m-portlet-offline --}} @endif m-portlet--sortable m-portlet panel-has-radius mb-4 relative hastransition">

                    <div class="m-portlet__head m-portlet__head_sm p-2 header">
                        <div class="c-card-caption">
                            <figure>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"/></svg>
                            </figure>
                            <figcaption>
                                <span class="fw-500">
                                    {{!empty($connected_equipment->name) ? $connected_equipment->name :$connected_equipment->device_id}}
                                </span>
                                <br />
                                <small id="toolTipValue-{{$row->device_id}}">
                                    Temperature Sensor
                                </small>
                            </figcaption>
                        </div>
                        <div>
                            <!--begin: Dropdown-->
                            
                            <!--end: Dropdown-->
                        </div>
                    </div>
                    <div class="p-2 d-flex">
                        <div class="graph_unit graph-container" id="graph-container-{{$row->id ? :'0'}}">
                            <div class="isloading">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg>
                            </div>
                            {{-- <img src="{{asset('assets/demo/default/media/img/misc/graph-1.jpg')}}" alt="Graph"
                                class="img-fluid"> --}}
                        </div>
                        <div class="graph_info_unit relative">
                            <div class="graph_info_unit_x">
                                <ul>
                                    <li>
                                        <label>
                                            Max
                                        </label>
                                        <span id="max_value-{{$row->id ? :'0'}}">26.4</span>
                                    </li>
                                    <li>
                                        <label>
                                            Avg
                                        </label>
                                        <span id="average-{{$row->id ? :'0'}}" >18.5</span>
                                    </li>
                                    <li>
                                        <label>
                                            Min
                                        </label>
                                        <span id="min_value-{{$row->id ? :'0'}}">14.7</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="graph_info_unit_y">
                                <h6 class="temperature" >
                                    <span id="temperature-{{$row->id ? :'0'}}"></span>°C
                                </h6>
                                <p class="m-0 temeprature_last_updated" id="temeprature_last_updated-{{$row->id ? :'0'}}">
                                    <time class="timeago-{{$row->id}}" datetime="{{$row->temeprature_last_updated}}"></time>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>
        <!-- ends row -->

    </div>
</div>
<footer class="m-grid__item m-footer">
    <div class="m-container m-container--fluid m-container--full-height m-page__container">
        <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
            <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last" style="text-align: center;">
                <span class="m-footer__copyright">
                    Powered by <br>
                    Recasoft Technologies ©
                </span>
            </div>
            <div class="m-stack__item m-stack__item--right m-stack__item--middle m-stack__item--first d-none">
                <ul class="m-footer__nav m-nav m-nav--inline m--pull-right">
                    <li class="m-nav__item">
                        <a href="#" class="m-nav__link">
                            <span class="m-nav__link-text">About</span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="#" class="m-nav__link">
                            <span class="m-nav__link-text">Privacy</span>
                        </a>
                    </li>
                    <li class="m-nav__item">
                        <a href="#" class="m-nav__link">
                            <span class="m-nav__link-text">T&amp;C</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
@endsection
@push('scripts')
<script src="https://js.pusher.com/3.1/pusher.min.js"></script>
<script type="text/javascript" src="{{asset('assets/js/jquery.timeago.js')}}"></script>
<script>
    $(document).ready(function(){
        moment.tz.setDefault('Europe/Oslo');
    });
    $.ajaxSetup({
        headers: {'X-CSRF-Token': '{{csrf_token()}}'}
    }); 
    
    var sensors_list=  @json($sensors);
     var pusher = new Pusher('ece81d906376bc8c0bab', {
                    cluster: 'ap2',
                    encrypted: true
                  });
    function loadGraps(event_id){
        for(let i=0;i< sensors_list.length; i++){
            console.log(sensors_list[i].device_id);
            setTimeout(() => {
                requestData(sensors_list[i].id,sensors_list[i].device_id,event_id);
                // Initiate the Pusher JS library
                 

                  // Subscribe to the channel we specified in our Laravel Event
                  var channel = pusher.subscribe('my-channel.'+sensors_list[i].device_id);

                  // Bind a function to a Event (the full Laravel class)
                  channel.bind('App\\Events\\HelloPusherEvent', function(data) {
                    if(data){
                        requestData(sensors_list[i].id,sensors_list[i].device_id,event_id);
                        console.log('Pusher = ',data);
                    }
                  });
            }, 500);
        }
    }
    $(function() {
        loadGraps(2);
        $( ".btn_segment" ).on( "click", function() {
            console.log( $(this).data('value') );
            $('.btn_segment').removeClass('radio-active');
            setTimeout(() => {
			 $(this).addClass('radio-active');
			}, 100)

            loadGraps($(this).data('value'));
         });
    });
    var chartAr=[];
    var intervalAr=[];
    function requestData(id,device_id,event_id) {

    	 Highcharts.getJSON('{{url("events2")}}'+'/'+device_id+'/'+event_id, function (data) {
            if(data.device_status && data.device_status==1){
                $('#temperature-'+id).html(data.temperature);
                $('#temeprature_last_updated-'+id).find('.timeago-'+id).html(data.temeprature_last_updated);
                
            }else{
                $('#temperature-'+id).closest('.temperature').html('Offline');
                $('#temeprature_last_updated-'+id).find('.timeago-'+id).html(data.temperature+'°C | '+data.temeprature_last_updated);
                
            }
                
             $('#average-'+id).html(data.average);  
             $('#min_value-'+id).html(data.min_value);  
             $('#max_value-'+id).html(data.max_value); 

             /*var millis = new Date(data.temeprature_last_updated);
             var milliseconds = millis.getTime();*/
             var now = new Date();
             var UTC_DIFFERENCE = now.getTimezoneOffset()*60;
             var newTime = (data.milliseconds)+(UTC_DIFFERENCE);
             var newTime2 = new Date(newTime);

             if(intervalAr[id]){
                clearInterval(intervalAr[id]);
                console.log('Clear Interval');
            }
             intervalAr[id] = setInterval(function(){
                $("time.timeago-"+id).timeago('update',newTime2);
             },1000);

            /*var millis = new Date(data.temeprature_last_updated);
            var milliseconds2 = millis.getTime();
            var now2 = new Date();
            var UTC_DIFFERENCE2 = now2.getTimezoneOffset()*60;
            var newTime2 = (milliseconds2*1000)+(UTC_DIFFERENCE2);
            var newTime22 = new Date(newTime2);
            var interval = setInterval(function(){
                $("time.timeago-"+id).timeago('update',newTime22);
            },1000);*/

            if(chartAr[id]){
                
                console.log('before Destroy()');
                chartAr[id].destroy();
            }
             var minTemperature = Math.min(...data.data.map(point => point[1])); // Minimum temperature from data
            var maxTemperature = Math.max(...data.data.map(point => point[1])); // Maximum temperature from data
            var temperatureDifference = maxTemperature - minTemperature;
            var integerDifference = Math.floor(temperatureDifference);
                // Define the tickInterval based on the temperatureDifference
                var tickInterval = 2;
                if (integerDifference <=3) {
                    tickInterval = 1;
                } else if (integerDifference ===4 || integerDifference ===5 || integerDifference ===6 || integerDifference ===8 || integerDifference === 10) {
                    tickInterval = 2;
                } else if (integerDifference ===7 || integerDifference ===9) {
                    tickInterval = 3;
                }else if (integerDifference > 10 && integerDifference < 20) {
                    tickInterval = 5;
                }else if(integerDifference > 20){
                    tickInterval = 10;
                }

            /*const chart =*/chartAr[id]= Highcharts.stockChart('graph-container-'+id, {
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
                    min: minTemperature,
                    allowDecimals:false,
                    tickInterval: tickInterval,
                    showLastLabel: true,
                    labels: {
                    formatter: function () {
                        return this.value + '°C'; // Display the maximum temperature on the right side
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
                        enabled:false
                        // selected: 1,
                    },

                    series: [{
                        name: 'Temperature',
                        color: '#3e4a4f',
                        lineColor : '#3e4a4f',
                        data: data.data,
                        type: 'spline',
                        step: true,
                        /*tooltip: {
                            valueDecimals: 1,
                            valueSuffix: '°C',
                        }*/
                    }],

                    tooltip:{
                        formatter : function(){
                            var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M', this.x);
                            var html = this.y.toFixed(2)+'°C on '+dateVl;
                            $("#toolTipValue-"+device_id).html(html);
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
          opacity:0.7,
          disabled: false,
          scroll: true,
          items: '.connectedSortable',
          // containment: '#m_sortable_portlets',
          start: function (event, ui) 
          {
          },
          stop: function (event, ui) 
          {
            var selectedData2 = new Array();
            $('.connectedSortable').each(function() {
                selectedData2.push($(this).attr("id"));
            });
            updateOrderItem(selectedData2);
          }
    });

    function updateOrderItem(data){
        $.ajax({
            url:'{{url("update-order2")}}',
            type:'post',
            data:{position:data},
            success: function(data){
            }
        });
    }

</script>
@endpush