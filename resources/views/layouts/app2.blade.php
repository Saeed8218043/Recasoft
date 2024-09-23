<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <script src="{{url('assets/vendors/base/vendors.bundle.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/demo/default/base/scripts.bundle.js')}}" type="text/javascript"></script>
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script src="{{url('public/assets/iconify/iconify.min.js')}}"></script>
    <script>
        WebFont.load({
            // google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            google: {"families":["Open+Sans:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    <link href="{{url('public/assets/vendors/base/vendors.bundle.css')}}" rel="stylesheet" type="text/css" />


    <link href="{{url('public/assets/demo/default/base/style.bundle.css')}}" rel="stylesheet" type="text/css" />

    <!-- Custom Styling -->
    <link href="{{url('public/assets/demo/default/base/custom_style.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('public/assets/demo/default/base/responsive.css')}}" rel="stylesheet" type="text/css" />

    

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{url('public/assets/demo/default/media/img/logo/favicon.ico')}}" />
    <style type="text/css">
    	.error {
    		color: #be1717;
    	}
    </style>
</head>
<!-- begin::Body -->
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-light m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

        <!-- begin:: Page -->
        <div class="m-grid m-grid--hor m-grid--root m-page">

            <!-- begin::Body -->
            {{-- <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body"> --}}

                
                @yield('content2')
            {{-- </div> --}}
            <!-- end:: Body -->

            
        </div>

        <!-- end:: Page -->

        <!-- begin::Scroll Top -->
        <div id="m_scroll_top" class="m-scroll-top">
            <i class="la la-arrow-up"></i>
        </div>

        <!-- end::Scroll Top -->

        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/stock/modules/data.js"></script>
        {{-- <script src="https://code.highcharts.com/stock/modules/exporting.js"></script> --}}
        <script type="text/javascript" src="{{ asset('public/assets/js/jquery.validate.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone-with-data-2012-2022.min.js"></script>
        <?php if(request()->segment(1)=='sensor-details'){ ?>
            <script src="https://js.pusher.com/3.1/pusher.min.js"></script>
        <?php } ?>
        <script type="text/javascript">
            Highcharts.setOptions({
                time: {
                    timezone: 'Europe/Oslo'
                }
            });
            function exportTasks(_this){
                var _url = $(_this).data('href');
                window.location.href = _url;
            }
            $(document).ready(function(){
                var isSidebar = localStorage.getItem('isSidebar');
                if(isSidebar==1){
                    $('#m_aside_left_minimize_toggle').addClass('m-brand__toggler--active');                    
                    $('body').addClass('m-aside-left--minimize m-brand--minimize');                    
                }

                $('#m_aside_left_minimize_toggle').on('click',function(){
                    if(!$(this).hasClass('m-brand__toggler--active')){
                        
                        localStorage.removeItem('isSidebar');
                    }else{
                        
                        localStorage.setItem('isSidebar',1);
                    }
                });
                $('#company-create-btn').on('click',function(){
                    $('#create-company').validate({
                        rules: {
                        },
                        messages: {
                        },
                        submitHandler: function(form){
                            $.ajax({
                                url: '{{route("companies.store")}}',
                                method: 'post',
                                data: $('#create-company').serialize(),
                                dataType: 'json',
                                beforeSend: function(){
                                    $('#loader').show();
                                    $('#company-create-btn').prop('disabled',true);
                                },
                                complete: function(){
                                    $('#loader').hide();
                                    $('#company-create-btn').prop('disabled',false);
                                },
                                success: function(data){
                                    $('#create-company-msg').addClass(data.class).text(data.message).show();
                                    if(data.status=='true'){
                                        loadCompaniesList();
                                        $('#create-company')[0].reset();
                                        setTimeout(function(){
                                            $('#create-company-msg').removeClass(data.class).text('').hide();
                                            $('#m_modal_3').modal('hide');
                                            location.reload();
                                        },1000);
                                    }
                                }
                            });
                        }
                    });
                });

                $('#company-upload-image-btn').on('click',function(){
                    $('#upload-company-image').validate({
                        rules: {
                        },
                        messages: {
                        },
                        submitHandler: function(form){
                            var fd = new FormData(form);
                            $.ajax({
                                url: '{{url("update-company-image")}}',
                                method: 'post',
                                data  : fd,
                                cache:false,
                                contentType: false,
                                processData: false,
                                dataType: 'json',
                                beforeSend: function(){
                                    $('#image-loader').show();
                                    $('#company-upload-image-btn').prop('disabled',true);
                                },
                                complete: function(){
                                    $('#image-loader').hide();
                                    $('#company-upload-image-btn').prop('disabled',false);
                                },
                                success: function(data){
                                    $('#upload-image-msg').text(data.message).show();
                                    $('#load-image').attr('src',data.url);
                                    $('#upload-company-image')[0].reset();
                                    $('#imageModal').modal('hide');
                                }
                            });
                        }
                    });
                });
                $('#searchType').on('change',function(){
                    $('#search-company').val('');
                    if($(this).val()==1){
                        $('#projectList').show();
                        $('#searchDevice').hide();
                         loadCompaniesList('');  
                    }else{
                        $('#projectList').hide();
                        
                        var srh=$('#search-company').val();
                        loadDeviceURL(srh);  
                    }
                }); 
                $('#search-company').on('keyup',function(e){
                    var search = $(this).val();
                    var searchType = $('#searchType').val();
                    
                    if(searchType==2){
                        loadDeviceURL(search);  
                    }else{
                        loadCompaniesList(search);  
                    }
                    
                });

                var currentURL = window.location.href;
                var url = currentURL;
                var parts = url.split('/');
                if(parts.length==6){
                    var last_part = parts[parts.length-2];
                }else{
                    var last_part = parts[parts.length-1];
                }

                var last_part2 = parts[parts.length-1];

                loadCompaniesList();

                function loadDeviceURL(search=''){
                    $.ajax({
                        url: '{{route("companies.loadDeviceURL")}}',
                        data: {search:search},
                        dataType: 'JSON',
                        beforeSend:function(){
                            $('#search-loader').show();
                        },
                        complete: function(){
                            $('#search-loader').hide();
                        },
                        success: function(data){
                            if(data && data.url){
                                $('#gotoDetails').attr('href',data.url);
                                $('#searchDevice').show().html(data.html);
                            }else if(data.status){
                                $('#searchDevice').show().html('<div class="text-center mt-3">\
                                    <p>Unable to locate device within your Projects.</p>\
                                    <p>Make sure the ID is entered correctly.</p>\
                                    </div>');
                            }

                            // $('#loadCompaniesList').html('');
                            // $('#loadCompaniesList').append(data);
                            // $('#search-loader').hide();
                        }
                    });
                }
                $('#m_modal_4').on('hidden.bs.modal', function () {
                    loadCompaniesList('');
                    $('#search-company').val('');
                     $('#projectList').show();
                        $('#searchDevice').hide();
                        $('#searchType').val(1);
                        

                });

                $('#m_modal_3').on('hidden.bs.modal', function () {
                    $(this).find('form')[0].reset();
                    $("form").each(function(){
                        $(this).validate().resetForm();
                    });
                });

                function loadCompaniesList(search=''){
                    $.ajax({
                        url: '{{route("companies.companiesList")}}',
                        data: {company_id:last_part,search:search},
                        beforeSend:function(){
                            $('#search-loader').show();
                        },
                        success: function(data){
                            $('#loadCompaniesList').html('');
                            $('#loadCompaniesList').append(data);
                            $('#search-loader').hide();
                        }
                    });
                }

                $(document).on('click','.listRow',function(){
                    var company_id = $(this).attr('data-id');
                    var currentURL = window.location.href;
                    currentURL = currentURL.slice(0, currentURL.lastIndexOf('/'));
                    var url = currentURL+'/'+company_id;
                    var parts = url.split('/');
                    if(parts.length==8){
                        var last_part = parts[parts.length-1];
                    }else{
                        var last_part = parts[parts.length-2];
                    }
                    if(last_part==company_id){
                        return false;
                    }
                    var segment = "{{request()->segment(1)}}";
                    var segment2 = "{{request()->segment(2)}}";
                    var segment3 = "{{request()->segment(3)}}";
                    if(segment=='sensors'){
                        window.location.href = '{{url("sensors")}}'+'/'+company_id;
                    }else if(segment=='sensor-details'){
                        window.location.href = '{{url("sensor-details")}}'+'/'+company_id+'/'+$(this).attr('data-device');
                    }else if(segment=='export'){
                        window.location.href = '{{url("export")}}'+'/'+company_id;
                    }else if(segment=='company-settings'){
                        window.location.href = '{{url("company-settings")}}'+'/'+company_id;
                    }else{
                        window.location.href = url;
                    }
                });

                <?php if(request()->segment(1)=='sensor-details'){ ?>
                function DrawContainerJson(val){
                    if(val!=''){
                        val = '/'+val;
                    }
                    Highcharts.getJSON('{{url("history-details")}}'+'/'+last_part2+val, function (data) {

                        // Create the chart
                        const chart = Highcharts.stockChart('master-container', {

                            chart: {
                                height: 220,
                                zoomType: 'x'
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
                                        return this.value+'°C';
                                    }
                                }
                            },
                            rangeSelector: {
                                    enabled:false
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
                                    }],
                                    selected: 1 // all*/
                                },

                                series: [{
                                     color: '#3e4a4f',
                                    lineColor : '#3e4a4f',
                                    name: 'Temperature',
                                    data: data,
                                    // type: 'spline',
                                    step: false,
                                    /*tooltip: {
                                        valueDecimals: 1,
                                        valueSuffix: '°C'
                                    }*/
                                }],

                                tooltip:{
                                    formatter : function(){
                                        var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M', this.x);
                                        var html = this.y.toFixed(2)+'°C on '+dateVl;
                                        $("#toolTipValue").html(html);
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
                                 },
                                  scrollbar: {
                                    enabled: false
                                },
                        });
                    });
                }

                DrawContainerJson('week');

                $('.range-btn').on('click',function(){
                    $('.range-btn').removeClass('radio-active');
                    $(this).addClass('radio-active');
                    var val = $(this).attr('data-val');
                    DrawContainerJson(val);
                });

              var deviceId = "{{request()->segment(3)}}";
                // Initiate the Pusher JS library
              var pusher = new Pusher('ece81d906376bc8c0bab', {
                cluster: 'ap2',
                encrypted: true
              });

              // Subscribe to the channel we specified in our Laravel Event
              var channel = pusher.subscribe('my-channel.'+deviceId);

              // Bind a function to a Event (the full Laravel class)
              channel.bind('App\\Events\\HelloPusherEvent', function(data) {
                if(data){
                    DrawContainerJson('week');
                    console.log('Pusher = ',data);
                }
              });
                <?php } ?>
            });
            
            var currentURL = window.location.href;
            if((currentURL.split('/').length)==4){
                var device_id = '{{isset($device_id)?$device_id:''}}';
                if(device_id!=''){
                    var url = '{{url("dashboard")}}'+'/'+device_id;
                    $('<a href="' + url + '"></a>')[0].click();
                }
            }
        </script>
        

        <!--begin::Page Vendors -->
        <script src="{{url('public/assets/vendors/custom/jquery-ui/jquery-ui.bundle.js')}}" type="text/javascript"></script>

        <!--end::Page Vendors -->

        <!--begin::Page Scripts -->
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

        <!--end::Page Scripts -->


        <!--end::Page Scripts -->
    </body>

    <!-- end::Body -->

    <script type="text/javascript">
        var requestFullscreen = function (ele) {
            if (ele.requestFullscreen) {
                ele.requestFullscreen();
            } else if (ele.webkitRequestFullscreen) {
                $('body').addClass('fullScreenClass');
                ele.webkitRequestFullscreen();
            } else if (ele.mozRequestFullScreen) {
                ele.mozRequestFullScreen();
            } else if (ele.msRequestFullscreen) {
                ele.msRequestFullscreen();
            } else {
                console.log('Fullscreen API is not supported.');
            }
        };

        var exitFullscreen = function () {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
                // $('body').removeClass('fullScreenClass');
            } else if (document.mozCancelFullScreen) {
                
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else {
                console.log('Fullscreen API is not supported.');
            }
        };

        var fsDocButton = document.getElementById('fs-doc-button');
        var fsExitDocButton = document.getElementById('fs-exit-doc-button');

        fsDocButton.addEventListener('click', function(e) {
            e.preventDefault();
             requestFullscreen(document.getElementById('element'));
            // $('body').addClass('isfullscreen');
        });

        fsExitDocButton.addEventListener('click', function(e) {
            e.preventDefault();
             exitFullscreen();
            // $('body').removeClass('isfullscreen');
        });

        $(document).keyup(function(e) {
		     if (e.key === "Escape") {
		        $('body').removeClass('isfullscreen');
		    }
		});
         // document.addEventListener('fullscreenchange', exitHandler);
        document.addEventListener('webkitfullscreenchange', exitHandler222);
        // document.addEventListener('mozfullscreenchange', exitHandler);
        // document.addEventListener('MSFullscreenChange', exitHandler);

        function exitHandler222() {
            if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                $('body').removeClass('fullScreenClass');
            }
        
    }

function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text().trim()).select();
        document.execCommand("copy");
        $temp.remove();
        alert('ID is copied to clipboard.');
    }
    </script>
    @stack('scripts')
</html>
