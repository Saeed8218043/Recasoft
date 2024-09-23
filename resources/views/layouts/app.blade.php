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
    <meta name="theme-color" content="#000000">
    <link rel="apple-touch-icon" href="{{ asset('public/cloud.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <style type="text/css">
    .btn-companyselect {
	background: rgba(255,255,255,0.2);
	border-color: rgba(255,255,255,0.2);
	color: #fff;
        }
        #set-name,#detail,#cancel,.save_name{
            color:black;
        }
        .dce-sel-resolution {
            display:none !important;
        }
        .dbr-msg-poweredby{
            display:none !important;
        }
        th {
            text-transform: uppercase;
            font-weight:bold !important;
        }
        
        .btn-companyselect span {
            opacity: .5;
            margin-right: 1rem;
        }
        .dce-scanarea{
            display:none !important;
        }




        .m-aside-menu.m-aside-menu--skin-light{display:flex;flex-direction:column}.sidebar_bottom_area{margin:auto .5rem 1rem;display:none}.sidebar_bottom_area .sidebar_bottom_area_link a{display:flex;align-items:center;line-height:1;padding:12px 30px;border-radius:6px;color:#fff;text-decoration:none;background-color:#3e4a4f}.sidebar_bottom_area .sidebar_bottom_area_link a:hover{opacity:.9}.sidebar_bottom_area .sidebar_bottom_area_link a figure{width:20px;height:20px;margin:0 10px 0 0}.sidebar_bottom_area .sidebar_bottom_area_link a figure svg{font-size:20px}.sidebar_bottom_area .sidebar_bottom_area_link a figcaption{color:inherit}.ui-sortable .m-portlet--sortable{min-height:185px}.ui-sortable .m-portlet--sortable .graph_info_unit{min-height:120px}@media screen and (max-width:991px){.m-aside-menu.m-aside-menu--skin-light{height:100%!important}}
    </style>
    <!-- Putting Iconify at top -->
    <!--<script src="{{asset('assets/iconify/iconify.min.js')}}"></script>-->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('public/jsQR.js')}}"></script>
    <script src="{{asset('assets/vendors/base/vendors.bundle.js')}}" type="text/javascript"></script>
        <script src="{{asset('assets/demo/default/base/scripts.bundle.js')}}" type="text/javascript"></script>
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    
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
    <link href="{{asset('assets/vendors/base/vendors.bundle.css')}}" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <link href="{{asset('assets/demo/default/base/style.bundle.css')}}" rel="stylesheet" type="text/css" />

    <!-- Custom Styling -->
    <link href="{{asset('assets/demo/default/base/custom_style.css?v=55')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/demo/default/base/responsive.css')}}" rel="stylesheet" type="text/css" />

@include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{asset('public/assets/demo/default/media/img/logo/favicon.ico')}}" />
<style type="text/css">
.error {
	color: #be1717;
}

.btn-companyselect {
    padding: 10px;
}
@media screen and (max-width: 767px) {
    .dce-scanarea{
        left: 0 !important;
        width: 100% !important;
    }
    .new-class video {
        object-fit: cover  !important;
    }
}

@media screen and (max-width: 1024px) {
    .m-header-menu.m-header-menu-custom ul ,
    .m-header-menu.m-header-menu-custom ul li {
        display: flex;
        align-items: center;
    }
    .scan_button_wrap {
        margin-left: 10px !important;
    }
}
body.showFullScreen #m_header,
body.showFullScreen footer.m-footer,
body.showFullScreen #m_aside_left{
    display: none !important;
}
body.showFullScreen div.m-body{
    padding-left: 0 !important;
    padding-top: 0 !important;
    width: 100%;
}
#page-loader {
  position: fixed;
  z-index: 9999;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: white;
}

#page-loader img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
  .dashboardModal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
  }

  .dashboardModal .modal-content {
    position: relative;
    margin: 0 auto;
    padding: 39px;
    max-width: 100%;
    background-color: #fff;
  }

  .dashboardModal .close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 24px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
  }

  .dashboardModal iframe {
    width: 100%;
    height: 100%;
    border: none;
  }



</style>
</head>
@php
    $company_id = isset($company_id) ? $company_id : '';
    $dashboard_url = url('iframeDashboard').'/'.$company_id;
@endphp

@if ($company_id && request()->segment(1)!='dashboard' && request()->segment(1)!='iframeDashboard' && \Auth::user()->id!=1)
    <script>
        var url = '{{ $dashboard_url }}';
    </script>
{{-- <script src="{{ asset('public/inactivity.js') }}"></script> --}}
@endif
<!-- begin::Body -->
    <body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-light m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">
    @php 
    $currentRouteName = Request::route()->getName();
    @endphp
    @if($currentRouteName !='export' && $currentRouteName !='sensors' && $currentRouteName !='home')
        {{-- <div id="page-loader">
            <img src="{{ asset('public/Loader.gif') }}" alt="Loading...">
        </div> --}}
    @endif


        <!-- begin:: Page -->
        <div class="m-grid m-grid--hor m-grid--root m-page">

            <!-- BEGIN: Header -->
            <header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
                <div class="m-container m-container--fluid m-container--full-height">
                    <div class="m-stack m-stack--ver m-stack--desktop">

                        <!-- BEGIN: Brand -->
                        <div class="m-stack__item m-brand  m-brand--skin-light custom-m-brand-wrap">
                            <div class="m-stack m-stack--ver m-stack--general">
                                <div class="m-stack__item m-stack__item--middle m-brand__logo">
                                    <a href="{{url('dashboard')}}/{{$company_id??''}}" class="m-brand__logo-wrapper">
                                        <img alt="" src="{{asset('public/assets/demo/default/media/img/logo/logo-white.png')}}" class="img-fluid" />
                                    </a>
                                </div>
                                <div class="m-stack__item m-stack__item--middle m-brand__tools">

                                    <!-- BEGIN: Left Aside Minimize Toggle -->
                                    <a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block  ">
                                        <span></span>
                                    </a>

                                    <!-- END -->

                                    <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                                    <a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                                        <span></span>
                                    </a>

                                    <!-- END -->

                                    <!-- BEGIN: Responsive Header Menu Toggler -->
                                    <!--
                                    <a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                                        <span></span>
                                    </a>
                                    -->
                                    <!-- END -->

                                    <!-- BEGIN: Topbar Toggler -->
                                    <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                                        <i class="flaticon-more"></i>
                                    </a>

                                    <!-- BEGIN: Topbar Toggler -->


                                </div>
                            </div>
                        </div>

                        <!-- END: Brand -->
                        <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">

                            <!-- BEGIN: Horizontal Menu -->
                            <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-light " id="m_aside_header_menu_mobile_close_btn"><i class="la la-close"></i></button>

                            <!--
                            <div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-light m-aside-header-menu-mobile--submenu-skin-light">
                            -->
                            <div id="m_header_menu" class="m-header-menu m-header-menu-custom">
                                <ul class="m-menu__nav  m-menu__nav--submenu-arrow ">
                                    <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel"><a href="javascript:;" class="m-menu__link m-menu__toggle" title="{{$company_name??''}}">
                                        <div class="m-menu__link-text">
                                            <button type="button" class="btn btn-companyselect" data-toggle="modal" data-target="#m_modal_4">
                                            @php 
                                            $company =\App\Company::where('company_id',$company_id)->first();
                                            $parent_company = \App\Company::where('id',isset($company->parent_id)?$company->parent_id:'')->first();
                                            $parent_name = isset($parent_company)?$parent_company->name.' /' :'Recasoft Technologies /'
                                            @endphp
                                            <span>{{$parent_name}}</span>
                                            @if(isset($company_name) && $company_name!='')
                                            {{$company_name}}
                                            @else
                                            Select Company
                                            @endif
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="m16 28l-7-7l1.41-1.41L16 25.17l5.59-5.58L23 21l-7 7zm0-24l7 7l-1.41 1.41L16 6.83l-5.59 5.58L9 11l7-7z"/></svg>
                                            </button>
                                        </div>
                                        </a>
                                    </li>
                                    
                                    <li class="m-menu__item  m-menu__item--submenu m-menu__item--rel pl-0 scan_button_wrap">
                                    	<button  class="btn btn-companyselect" onclick="scn()" data-toggle="modal" data-target="#scanner_modal"  style="width: 40px; height: 36px;">
                                        <svg style="font-size: 18px; margin-top: -5px;" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M8 21H4a1 1 0 0 1-1-1v-4a1 1 0 0 0-2 0v4a3 3 0 0 0 3 3h4a1 1 0 0 0 0-2Zm14-6a1 1 0 0 0-1 1v4a1 1 0 0 1-1 1h-4a1 1 0 0 0 0 2h4a3 3 0 0 0 3-3v-4a1 1 0 0 0-1-1ZM20 1h-4a1 1 0 0 0 0 2h4a1 1 0 0 1 1 1v4a1 1 0 0 0 2 0V4a3 3 0 0 0-3-3ZM2 9a1 1 0 0 0 1-1V4a1 1 0 0 1 1-1h4a1 1 0 0 0 0-2H4a3 3 0 0 0-3 3v4a1 1 0 0 0 1 1Zm8-4H6a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM9 9H7V7h2Zm5 2h4a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1Zm1-4h2v2h-2Zm-5 6H6a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1Zm-1 4H7v-2h2Zm5-1a1 1 0 0 0 1-1a1 1 0 0 0 0-2h-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1Zm4-3a1 1 0 0 0-1 1v3a1 1 0 0 0 0 2h1a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1Zm-4 4a1 1 0 1 0 1 1a1 1 0 0 0-1-1Z"/></svg>

                                        </button>
                                    </li>
                                </ul>
                            </div>




                            <!-- END: Horizontal Menu -->

                            <!-- BEGIN: Topbar -->
                            <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
                                <div class="m-stack__item m-topbar__nav-wrapper">
                                    <ul class="m-topbar__nav m-nav m-nav--inline">
                                        <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
                                         m-dropdown-toggle="click">
                                            <a href="#" class="m-nav__link m-dropdown__toggle">
                                                <div class="userinfo">
                                                    <h6>
                                                        Hi, {{\Auth::user()->name}}
                                                    </h6>
                                                    <span>
                                                        {{\Auth::user()->email}}
                                                    </span>
                                                    <i class="la la-angle-down arrow"></i>
                                                </div>
                                            </a>
                                            <div class="m-dropdown__wrapper">
                                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="color: #fff;"></span>
                                                <div class="m-dropdown__inner">
                                                    <div class="m-dropdown__body">
                                                        <div class="m-dropdown__content">
                                                            <ul class="m-nav m-nav--skin-light">
                                                                <li class="m-nav__section m--hide">
                                                                    <span class="m-nav__section-text">Section</span>
                                                                </li>
                                                                <!-- <li class="m-nav__item">
                                                                    <?php $segment = request()->segment(2); ?>
                                                                    <a href="{{url('dashboard/'.$segment)}}" class="m-nav__link">
                                                                        <i class="m-nav__link-icon flaticon-dashboard"></i>
                                                                        <span class="m-nav__link-title">
                                                                            <span class="m-nav__link-wrap">
                                                                                <span class="m-nav__link-text">My Dashboard</span>
                                                                                <span class="m-nav__link-badge"><span class="m-badge m-badge--success">2</span></span>
                                                                            </span>
                                                                        </span>
                                                                    </a>
                                                                </li> -->
                                                                <li class="m-nav__separator m-nav__separator--fit">
                                                                </li>
                                                                <li class="m-nav__item">
                                                                    <a href="{{route('logout')}}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder" style="font-family: 'Open Sans';">Logout</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- END: Topbar -->

                        </div>
                    </div>
                </div>
            </header>

            <!-- END: Header -->

            <!-- begin::Body -->
            <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">

                <!-- BEGIN: Left Aside -->
                <button class="m-aside-left-close  m-aside-left-close--skin-light " id="m_aside_left_close_btn"><i class="la la-close"></i></button>
                @include('includes.sidebar')

                <!-- END: Left Aside -->
                @yield('content')
            </div>
            <!-- end:: Body -->

            <!-- begin::Footer -->
            <footer class="m-grid__item m-footer">
                <div class="m-container m-container--fluid m-container--full-height m-page__container">
                    <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                        <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last" style="text-align: center;">
                            <span class="m-footer__copyright">
                                2023 &copy; Recasoft AS
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
                                        <span class="m-nav__link-text">T&C</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end::Footer -->
        </div>

        <!-- end:: Page -->

        <!-- begin::Scroll Top -->
        <div id="m_scroll_top" class="m-scroll-top">
            <i class="la la-arrow-up"></i>
        </div>

        <!-- end::Scroll Top -->
<style type="text/css">
.qrscanner {
    position: relative;
    background-color: #fff;
    padding: 0;
}    
 .dce-sel-camera {
    position: absolute;
    left: 1.5rem;
    fill: currentColor;
    top: 1.5rem;
    width: 140px;
    background-color: transparent;
    color: #fff;
    border-radius: 0.375rem;
    line-height: 1;
    display:inline-block;
}
.dce-btn-close{
    position: absolute !important;
    right: 2.5rem !important;
    fill: currentColor !important;
    top: 1.5rem !important;
    background-color: transparent !important;
    line-height: 2 !important;
    border: none !important;
}
.scannerBody {
    position: relative;
}
.scannerBody #camera-select {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    z-index: 10;
    width: auto;
    border-radius: 0.375rem;
    padding-left: 1rem;
    padding-right: 1rem;
    background-color: transparent;
    color: #000;
    border-color: #000;
    line-height: 1.4;
    padding-right: 30px;
}
.scannerBody #camera-select:focus ,
.scannerBody #camera-select:focus-visible {
    outline: none;
}
</style>

<div class="modal fade" id="scanner_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body bg-light p-0 position-relative scannerBody">
						<div class="text-center">

							<div class="text-center mb-4 touch_header">
                                <div>
                                <select id="camera-select">
                                </select>
                                </div>
								<div id="video-container" class="qrscanner" >

								    <video id="qr-videos" style="width: 100% !important;"></video>
								</div>
								<div class="d-none">
								    <label>
								        Highlight Style
								        <select id="scan-region-highlight-style-select">
								            <option value="default-style">Default style</option>
								            <option value="example-style-1">Example custom style 1</option>
								            <option value="example-style-2">Example custom style 2</option>
								        </select>
								    </label>
								    <label>
								        <input id="show-scan-region" type="checkbox">
								        Show scan region canvas
								    </label>
								</div>
								<div class="d-none">
								    <select id="inversion-mode-select">
								        <option value="original">Scan original (dark QR code on bright background)</option>
								        <option value="invert">Scan with inverted colors (bright QR code on dark background)</option>
								        <option value="both">Scan both</option>
								    </select>
								    {{-- <br> --}}
								</div>
								<b class="d-none">Device has camera: </b>
								<span id="cam-has-camera" class="d-none"></span>
								{{-- <br> --}}
								<div class="d-none">
								    <b>Preferred camera:</b>
								    <select id="cam-list">
								        <option value="environment" selected>Environment Facing (default)</option>
								        <option value="user">User Facing</option>
								    </select>
								</div>
								<b class="d-none">Camera has flash: </b>
								<span class="d-none" id="cam-has-flash"></span>
								<div class="d-none">
								    <button id="flash-toggle">📸 Flash: <span id="flash-state">off</span></button>
								</div>
								{{-- <br> --}}
								<b class="d-none">Detected QR code: </b>
								<span class="d-none" id="cam-qr-result">None</span>
								{{-- <br> --}}
								<b class="d-none">Last detected at: </b>
								<span class="d-none" id="cam-qr-result-timestamp"></span>
								{{-- <br> --}}
								<button id="start-button" class="d-none">Start</button>
								<button id="stop-button" class="d-none">Stop</button>
								{{-- <hr> --}}

								<h1 class="d-none">Scan from File:</h1>
								<input class="d-none" type="file" id="file-selector">
								<b class="d-none">Detected QR code: </b>
								<span class="d-none" id="file-qr-result">None</span>
							</div>

							<!-- if Sensor not found -->
							<div class="text-left sensor_touch_info ">
								<p class="text-dark h5 text-center mt-3 mb-4 fw-500">
									Scan device QR Code
								</p>
								<p class="text-center">Scan device QR codes to identify Sensors & Cloud Connectors</p>

								<p class="scansensor_error text-danger h5 d-none">You don't have access to this device</p>

							</div>
                             <div id="sensor-info"></div>
							<!-- if Sensor not found ends -->

							{{-- <div class="bg-white border rounded p-3 text-left shadow">
								Tip: Search for the Kit ID on your box (e.g. abc-00-abc) to only listen to sensors in that box.
							</div> --}}

						</div>
					</div>
					<div class="modal-footer p-3">

						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

        {{-- <div class="modal fade" id="scanner_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-body bg-light p-0">
						<div class="">

							<div class="text-center touch_header">
                                <div id="video-container" class="qrscanner d-none">
                                    <video id="qr-videos" style="width: 100% !important;display: block;"></video>
                                    <select id="cam-list" class="form-select form-control"></select> <!-- Move cam-list select into video-container -->
                                </div>
                                <div class="d-none">
                                    <label>
                                        Highlight Style
                                        <select id="scan-region-highlight-style-select">
                                            <option value="default-style">Default style</option>
                                            <option value="example-style-1">Example custom style 1</option>
                                            <option value="example-style-2" selected>Example custom style 2</option>
                                        </select>
                                    </label>
                                    <label>
                                        <input id="show-scan-region" type="checkbox">
                                        Show scan region canvas
                                    </label>
                                </div>

								<div class="d-none">
								    <select id="inversion-mode-select">
								        <option value="original">Scan original (dark QR code on bright background)</option>
								        <option value="invert">Scan with inverted colors (bright QR code on dark background)</option>
								        <option value="both">Scan both</option>
								    </select>
								</div>
								<b class="d-none">Device has camera: </b>
								<span id="cam-has-camera" class="d-none"></span>
								<div class="d-none">
								    <b>Preferred camera:</b>
								    <select id="cam-list2">
								        <option value="environment" selected>Environment Facing (default)</option>
								        <option value="user">User Facing</option>
								    </select>
								</div>
								<b class="d-none">Camera has flash: </b>
								<span class="d-none" id="cam-has-flash"></span>
								<div class="d-none">
								    <button id="flash-toggle">📸 Flash: <span id="flash-state">off</span></button>
								</div>
								<b class="d-none">Detected QR code: </b>
								<span class="d-none" id="cam-qr-result">None</span>
								<b class="d-none">Last detected at: </b>
								<span class="d-none" id="cam-qr-result-timestamp"></span>
								<button id="start-button" class="d-none">Start</button>
								<button id="stop-button" class="d-none">Stop</button>

								<h1 class="d-none">Scan from File:</h1>
								<input class="d-none" type="file" id="file-selector">
								<b class="d-none">Detected QR code: </b>
								<span class="d-none" id="file-qr-result">None</span>


                                       
							</div>

							<!-- if Sensor not found -->
								<p class="scansensor_error text-danger h5 d-none">You don't have access to this device</p>

						</div>
					</div>
							<div class="text-left sensor_touch_info ">
								<p class="text-dark h5 text-center mt-3 mb-4 fw-500">
									Scan device QR Code
								</p>
								<p class="text-center">Scan device QR codes to identify Sensors & Cloud Connectors</p>


							</div>
                            <div id="sensor-info"></div>
					<div class="modal-footer p-3">

						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div> --}}


        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker.controller) {
                navigator.serviceWorker.register("/sw.js").then(function(registration) {
                console.log('Service worker registration successful with scope: ', registration.scope);
                }).catch(function(error) {
                    console.error('Service worker registration failed: ', error);
                    });
            }

        </script>

        @include('templates.modal')

        <script src="https://code.highcharts.com/stock/highstock.js"></script>
        <script src="https://code.highcharts.com/stock/modules/data.js"></script>
        {{-- <script src="https://code.highcharts.com/stock/modules/exporting.js"></script> --}}
        {{-- <script src="https://code.highcharts.com/stock/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script> --}}
        <script type="text/javascript" src="{{ asset('public/assets/js/jquery.validate.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
        {{-- <script type="text/javascript" src="{{asset('assets/js/instascan.min.js')}}"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.13/moment-timezone-with-data-2012-2022.min.js"></script>
{{-- <script type="text/javascript" src="{{ asset('js/jsqrscanner.nocache.js') }}"></script> --}}

<script src="{{asset('public/qr-scanner.umd.min.js')}}"></script>
<!--<script src="../qr-scanner.legacy.min.js"></script>-->
{{-- <script src="{{asset('qr-scanner.min.js')}}"></script> --}}
<script type="text/javascript">
//window.location.reload(true);
    // import QrScanner from "../qr-scanner.min.js";
    const video = document.getElementById('qr-videos');
    const videoContainer = document.getElementById('video-container');
    const camHasCamera = document.getElementById('cam-has-camera');
    const camList = document.getElementById('cam-list');
    const camHasFlash = document.getElementById('cam-has-flash');
    const flashToggle = document.getElementById('flash-toggle');
    const flashState = document.getElementById('flash-state');
    const camQrResult = document.getElementById('cam-qr-result');
    const camQrResultTimestamp = document.getElementById('cam-qr-result-timestamp');
    const fileSelector = document.getElementById('file-selector');
    const fileQrResult = document.getElementById('file-qr-result');

    function setResult(label, result) {
        console.log(result.data);

        label.textContent = result.data;
        camQrResultTimestamp.textContent = new Date().toString();
        label.style.color = 'teal';
        clearTimeout(label.highlightTimeout);
        label.highlightTimeout = setTimeout(() => label.style.color = 'inherit', 100);

       deviceid=  result.data;

	let company_id="{{$company_id??''}}";

	checkSensor(deviceid,company_id);
    }

     const scanner = new QrScanner(video, result => setResult(camQrResult, result), {
	        onDecodeError: error => {
	            camQrResult.textContent = error;
	            camQrResult.style.color = 'inherit';
	        },
	        highlightScanRegion: true,
	        highlightCodeOutline: true,
	      });

 let activeCamera = null; // Keep track of the active camera

function scn() {
  if (activeCamera) {
    // Stop the scanner and remove the active camera
    scanner.stop();
    //scanner.destroy();
  }

  const cameraSelect = document.getElementById('camera-select');
  const selectedCamera = cameraSelect.value;

  cameraSelect.innerHTML = ''; // Remove all options

  const updateFlashAvailability = () => {
    scanner.hasFlash().then(hasFlash => {
      camHasFlash.textContent = hasFlash;
      flashToggle.style.display = hasFlash ? 'inline-block' : 'none';
    });
  };

  // Add a default option as the first option
  const defaultOption = document.createElement('option');
  defaultOption.text = 'Default Camera';
  defaultOption.selected = true;
  defaultOption.value = "environment";
  cameraSelect.add(defaultOption);

  scanner.start(selectedCamera).then(() => {
    activeCamera = selectedCamera; // Set the active camera
    updateFlashAvailability();
    QrScanner.listCameras(true).then(cameras => {
      cameras.forEach(camera => {
        const option = document.createElement('option');
        option.value = camera.id;
        option.text = camera.label;
        cameraSelect.add(option);
      });

      // Update the selected option based on the initially selected camera
      cameraSelect.value = selectedCamera || defaultOption.value;
    });
  });

  cameraSelect.addEventListener('change', event => {
    scanner.setCamera(event.target.value).then(updateFlashAvailability);
  });
}


    QrScanner.hasCamera().then(hasCamera => camHasCamera.textContent = hasCamera);

    // for debugging
    window.scanner = scanner;

    document.getElementById('scan-region-highlight-style-select').addEventListener('change', (e) => {
        videoContainer.className = e.target.value;
        scanner._updateOverlay(); // reposition the highlight because style 2 sets position: relative
    });

    document.getElementById('show-scan-region').addEventListener('change', (e) => {
        const input = e.target;
        const label = input.parentNode;
        label.parentNode.insertBefore(scanner.$canvas, label.nextSibling);
        scanner.$canvas.style.display = input.checked ? 'block' : 'none';
    });

    document.getElementById('inversion-mode-select').addEventListener('change', event => {
        scanner.setInversionMode(event.target.value);
    });

    camList.addEventListener('change', event => {
        scanner.setCamera(event.target.value).then(updateFlashAvailability);
    });

    flashToggle.addEventListener('click', () => {
        scanner.toggleFlash().then(() => flashState.textContent = scanner.isFlashOn() ? 'on' : 'off');
    });

    document.getElementById('start-button').addEventListener('click', () => {
        scanner.start();
    });

    document.getElementById('stop-button').addEventListener('click', () => {
        scanner.stop();
    });

    // ####### File Scanning #######

    fileSelector.addEventListener('change', event => {
        const file = fileSelector.files[0];
        if (!file) {
            return;
        }
        QrScanner.scanImage(file, { returnDetailedScanResult: true })
            .then(result => setResult(fileQrResult, result))
            .catch(e => setResult(fileQrResult, { data: e || 'No QR code found.' }));
    });


    $('#scanner_modal').on('hidden.bs.modal', function () {

    	 scanner.stop();

});

</script>
<script>
        // Get all menu items
        var menuItems = $('.m-menu__item');

        // Click event handler for menu items
        menuItems.click(function() {
            // Remove active class from all menu items
            menuItems.removeClass('m-menu__item--active');

            // Add active class to the clicked menu item
            $(this).addClass('m-menu__item--active');
        });
 
    </script>
<style>


    #video-container {
        line-height: 0;
    }

    #video-container.example-style-1 .scan-region-highlight-svg,
    #video-container.example-style-1 .code-outline-highlight {
        stroke: #64a2f3 !important;
    }

    #video-container.example-style-2 {
        position: relative;
        /*width: max-content;*/
        /*height: max-content;*/
        overflow: hidden;
    }
    #video-container.example-style-2 .scan-region-highlight {
        border-radius: 30px;
        outline: rgba(0, 0, 0, .25) solid 50vmax;
    }
    #video-container.example-style-2 .scan-region-highlight-svg {
        display: none;
    }
    #video-container.example-style-2 .code-outline-highlight {
        stroke: rgba(255, 255, 255, .5) !important;
        stroke-width: 15 !important;
        stroke-dasharray: none !important;
    }

    #flash-toggle {
        display: none;
    }

   /* hr {
        margin-top: 32px;
    }*/
 /*   input[type="file"] {
        display: block;
        margin-bottom: 16px;
    }*/
</style>





        <?php if(request()->segment(1)=='sensor-details' || request()->segment(1)=='sensors' || request()->segment(1)=='equipments'){ ?>
            <script src="https://js.pusher.com/3.1/pusher.min.js"></script>
        <?php } ?>
        <script type="text/javascript">
        	 $.ajaxSetup({
        headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
     });
            Highcharts.setOptions({
                time: {
                    timezone: 'Europe/Oslo'
                }
            });
            function exportTasks(_this){
                var _url = $(_this).data('href');
                window.location.href = _url;
            }

            $(document).ajaxStop(function() {
            // Hide the loader when all AJAX requests have completed
            $('#page-loader').hide();
            });
                $('.loader-btn').on('click', function() {
                    $('#loader').show();
                    });
            if (document.cookie.indexOf("reloaded=true") === -1) {
                // Set a cookie to indicate the page has been reloaded
                document.cookie = "reloaded=true";

                // Perform a hard reload
                location.reload(true);
            }
            $(document).ready(function(){
                        
                  $('form').submit(function() {
                  location.reload(true);
                        // Otherwise, show the loader
                        $('#page-loader').show();
                                    });

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
                    var segment = "{{request()->segment(1)}}";
                    $.ajax({
                        url: '{{route("companies.companiesList")}}',
                        data: {company_id:last_part,search:search,segment:segment},
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
                    var parent_id = $(this).attr('parent-id');
                    let company ="{{isset($company->parent_id)?$company->parent_id:''}}";
                    console.log(company);
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
                    }
                    else if(segment=='dashboard' && parent_id==0){
                        window.location.href = '{{url("equipments")}}'+'/'+company_id;
                    }
                    else if(segment=='sensor-details'){
                        window.location.href = '{{url("sensor-details")}}'+'/'+company_id+'/'+$(this).attr('data-device');
                    }
                    else if(segment=='equipment-details'){
                        window.location.href = '{{url("equipments")}}'+'/'+company_id;
                    }
                    else if(segment=='company-settings'){
                        window.location.href = '{{url("company-settings")}}'+'/'+company_id;
                    }
                    else if(segment=='notification-detail'){
                        window.location.href = '{{url("notifications")}}'+'/'+company_id;
                    }
                    else if((segment=='notifications' || segment=='export' || segment=='deviations') && parent_id!=0){
                        window.location.href = '{{url("dashboard")}}'+'/'+company_id;
                    }
                    else if((segment=='notifications' || segment=='export' || segment=='deviations') && parent_id==0){
                        window.location.href = '{{url("equipments")}}'+'/'+company_id;
                    }
                    else if(segment=='documents'){
                        window.location.href = '{{url("documents")}}'+'/'+company_id;
                    }
                    else if(company != 0 || company != ''){
                    if(segment=='company-details' || segment=='company-admins' ){
                        window.location.href = '{{url("sensors")}}'+'/'+company_id;
                    }              
                    window.location.href = url;      
                    } 
                    else{
                        window.location.href = url;
                    }
                });

                <?php if(request()->segment(1)=='sensor-details' || request()->segment(1)=='equipment-details' ){ ?>
                function DrawContainerJson(val){
                    var device_id ="{{isset($connected_sensor)?$connected_sensor->device_id:''}}";
                        if(val!=''){
                            val = '/'+val;
                        }
                     @if(request()->segment(1)=='equipment-details' )
                    last_part2 =device_id;
                    @endif
                      Highcharts.getJSON('{{url("history-details")}}'+'/'+last_part2+val, function (data) {

    // Find null values and add plot bands
    const xAxis = {
        type: 'datetime',
        labels: {
            format: '{value:%b %e}'
        },
        minRange: 3600 * 1000 // one hour
    };
    const series = [{
        color: '#3e4a4f',
        lineColor : '#3e4a4f',
        name: 'Temperature',
        data: data,
        step: false,
        events: {

            
            // Add plot bands for null values
           afterAnimate: function () {

            // Add triangle design to plot bands


    const series = this.chart.series[0];
    const data = series.options.data;
    const plotBands = [];
    let nullDataStart = null;
    let lastNonNullValue = null;
    for (let i = 0; i < data.length; i++) {
        const point = data[i];
        if (point[1] === null) {
            if (nullDataStart === null) {
                nullDataStart = point[0];
            }
        } else {
            if (nullDataStart !== null) {
                plotBands.push({
                    from: lastNonNullValue,
                    to: point[0],
                    color: '#CCCCCC',
                    zIndex: 1,
                    
                });
                nullDataStart = null;
            }
            lastNonNullValue = point[0];
        }
    }
    if (nullDataStart !== null) {
        plotBands.push({
            from: lastNonNullValue,
            to: data[data.length - 1][0],
            color: '#CCCCCC',
            zIndex: 1
        });
    }
    xAxis.plotBands = plotBands;
    this.chart.update({ xAxis });
}

        }
    }];

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
        xAxis,
        yAxis: {
            opposite: false,
            allowDecimals: false,
            showLastLabel: true,
            labels: {
                formatter: function() {
                    return this.value+'°C';
                }
            }
        },
        rangeSelector: {
            enabled:false
        },
        series,
        tooltip:{
            formatter : function(){
                var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);
                 /*var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);
                */
                var originalTimestamp = this.x; // Assuming this.x contains the original timestamp

                        // Create a new Date object based on the original timestamp
                        var originalDate = new Date(originalTimestamp);

                        // Add one hour to the original date
                        var newDate = new Date(originalDate.getTime()); // Add 1 hour (1 * 60 * 60 * 1000 milliseconds)

                        // Format the new date as a string using Highcharts.dateFormat
                        var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', newDate.getTime());
                        var html = '<b>' + this.y.toFixed(2) + '°C</b> on ' + dateVl;

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
        }
    });
});


                    /*    Highcharts.getJSON('{{url("history-details")}}'+'/'+last_part2+val, function (data) {

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
                                },

                                series: [{
                                     color: '#3e4a4f',
                                    lineColor : '#3e4a4f',
                                    name: 'Temperature',
                                    data: data,
                                    step: false,
                                }],

                                tooltip:{
                                    formatter : function(){
                                        var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);
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
                    }); */
                   Highcharts.getJSON('{{url("history-details")}}'+'/'+last_part2+val, function (data) {

    // Find null values and add plot bands
    const xAxis = {
        type: 'datetime',
        labels: {
            format: '{value:%b %e}'
        },
        minRange: 3600 * 1000 // one hour
    };
    const series = [{
        color: '#3e4a4f',
        lineColor : '#3e4a4f',
        name: 'Temperature',
        data: data,
        step: false,
        events: {
            // Add plot bands for null values
            afterAnimate: function () {
                // Add triangle design to plot bands
                const series = this.chart.series[0];
                const data = series.options.data;
                const plotBands = [];
                let nullDataStart = null;
                let lastNonNullValue = null;
                for (let i = 0; i < data.length; i++) {
                    const point = data[i];
                    if (point[1] === null) {
                        if (nullDataStart === null) {
                            nullDataStart = point[0];
                        }
                    } else {
                        if (nullDataStart !== null) {
                            plotBands.push({
                                from: lastNonNullValue,
                                to: point[0],
                                color: '#EF6161',
                                zIndex: 1,
                            });
                            nullDataStart = null;
                        }
                        lastNonNullValue = point[0];
                    }
                }
                if (nullDataStart !== null) {
                    plotBands.push({
                        from: lastNonNullValue,
                        to: data[data.length - 1][0],
                        color: 'rgba(255, 165, 0, 0.2)',
                        zIndex: 3,
                        svg: {
                            path: 'M 964.5 40 L 964.5 185 975.5 185 975.5 40 z',
                            zIndex: 4
                        }
                    });
                }
                xAxis.plotBands = plotBands;
                this.chart.update({ xAxis });
            }
        }
    }];

    // Create the chart
    Highcharts.stockChart('Gen2-container', {
        chart: {
            height: 200,
            zoomType: 'x',
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis,
        yAxis: {
            opposite: false,
            allowDecimals: false,
            showLastLabel:true,
            labels: {
                formatter: function() {
                    return this.value+'°C';
                }
            }
        },
        rangeSelector: {
            enabled:false,
            selected: 1
        },
         series: [{
                                     color: '#3e4a4f',
                                    lineColor : '#3e4a4f',
                                    name: 'Temperature',
                                    data: data,
                                    step: false,
                                    marker: {
                                        enabled: true,
                                        radius: 2,
                                        // lineHeight: 0.2,

                                    },
                                    lineWidth: 1
                                }],
        tooltip:{
             
            valueDecimals: 2,
            formatter : function(){
                /*var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', this.x);
                */
                var originalTimestamp = this.x; // Assuming this.x contains the original timestamp

                        // Create a new Date object based on the original timestamp
                        var originalDate = new Date(originalTimestamp);

                        // Add one hour to the original date
                        var newDate = new Date(originalDate.getTime()); // Add 1 hour (1 * 60 * 60 * 1000 milliseconds)

                        // Format the new date as a string using Highcharts.dateFormat
                        var dateVl = Highcharts.dateFormat('%A, %b %e, %H:%M:%S', newDate.getTime());
                        var html = this.y.toFixed(2)+'°C on '+dateVl;
                $("#toolTipValue2").html(html);
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

        <script>
        setTimeout(function(){
           $('.sidebar_bottom_area').show();
        }, 1500);
    </script>


        <!--end::Page Scripts -->


        <!--end::Page Scripts -->

    </body>

    <!-- end::Body -->
{{-- <script src="https://cdn.jsdelivr.net/npm/screenfull@5.0.2/dist/screenfull.min.js"></script> --}}
<script src="{{ asset('public/jquery.fullscreen.js') }}"></script>

    <script type="text/javascript">
        var requestFullscreen = function (ele) {
            if (ele.webkitRequestFullscreen) {
                
                    $('body').addClass('showFullScreen').addClass('fullScreenClass');
            }else{
                    $('body').addClass('showFullScreen').addClass('fullScreenClass');
                ele.requestFullscreen();

            }
{{-- 
            if (ele.requestFullscreen) {
                $('body').addClass('showFullScreen').addClass('fullScreenClass');

            } else if (ele.webkitRequestFullscreen) {
                 $('body').addClass('fullScreenClass');
                ele.webkitRequestFullscreen();
                ele.requestFullscreen().catch((err) => {
      alert(`Error attempting to enable fullscreen mode: ${err.message} (${err.name})`);
    });;
            } else if (ele.mozRequestFullScreen) {
                alert('moz fullscreen');
                ele.mozRequestFullScreen();
            } else if (ele.msRequestFullscreen) {
                alert('ms fullscreen');
                ele.msRequestFullscreen();
            } else { --}}
                 
                console.log('Fullscreen API is not supported.');
            {{-- } --}}
        };

       

        var exitFullscreen = function () {
          if (document.webkitExitFullscreen) {
                $('body').removeClass('showFullScreen').removeClass('fullScreenClass');
                 {{-- $('body').removeClass('fullScreenClass'); --}}

          }else{
                $('body').removeClass('showFullScreen').removeClass('fullScreenClass');
                document.exitFullscreen();

          }

            {{-- if (document.exitFullscreen) {
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
                // $('body').removeClass('fullScreenClass');
            } else if (document.mozCancelFullScreen) {

                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else { --}}
                console.log('Fullscreen API is not supported.');
            {{-- } --}}
        };

        var fsDocButton = document.getElementById('fs-doc-button');
        var fsExitDocButton = document.getElementById('fs-exit-doc-button');

        fsDocButton.addEventListener('click', function(e) {
             e.preventDefault();
        requestFullscreen(document.getElementById('element'));
            // if (screenfull.isEnabled) {
            // alert('asdsdfds');
            // $('#element').fullScreen(true);
                /*screenfull.toggle(document.getElementById('element'));
                screenfull.onerror(function(err){
                    alert(err);
                });*/
            // }else{
            //     alert('not enabled');
            //  // requestFullscreen(document.getElementById('element'));
            // }
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

    function copyToClipboard(button) {
    var parent = button.previousElementSibling; // Get the <span> element before the button
    var copyText = parent.textContent.trim(); // Trim leading and trailing whitespace

    // Create a temporary input element to copy the text
    var tempInput = document.createElement('input');
    tempInput.value = copyText;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    // Change the button text to indicate successful copy
    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="green" d="M24.6 11.4l-8.6 8.6-4.6-4.6-2.8 2.8 7.4 7.4 11-11-2.8-2.8z"></path></svg> Copied';

    // Restore the original button state after 5 seconds
    setTimeout(function () {
        button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="M27.4 14.7l-6.1-6.1C21 8.2 20.5 8 20 8h-8c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V16.1c0-.5-.2-1-.6-1.4zM20 10l5.9 6H20v-6zm-8 18V10h6v6c0 1.1.9 2 2 2h6v10H12z"></path><path fill="currentColor" d="M6 18H4V4c0-1.1.9-2 2-2h14v2H6v14z"></path></svg> Copy';
        button.style.backgroundColor = ''; // Clear the background color
    }, 5000); // 5000 milliseconds (5 seconds)
}
    </script>

    <script>



           function checkSensor(deviceid, company_id) {
  $.ajax({
    url: '{{route("check.sensor")}}',
    type: 'GET',
    dataType: 'JSON',
    data: {
      device_id: deviceid,
      company_id: company_id
    },
    success: function(data) {
      {{-- console.log(data); --}}
      if (data.isCheck == true) {
        console.log('in succes function');
        $('.scansensor_error').addClass('d-none');
        $('.sensor_touch_info').addClass('d-none');
        // Fetch the page content using another AJAX request
        
            $('#sensor-info').html(data.html);
          
      } else {
        $('.scansensor_error').removeClass('d-none');
        setTimeout(function() {
          $('.scansensor_error').addClass('d-none');
        }, 8000);
      }
    },
    error: function(data) {
      console.log(data);
    }
  });
}

        </script>
    @stack('scripts')
</html>
