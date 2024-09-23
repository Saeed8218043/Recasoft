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
.highcharts-scrollbar {
  display: none;
}
.lists{
    display:none;
} 
.buttons{
    display: inline-block;
}

@media screen and (max-width: 767px) {
  .buttons {
    display: none;
  }
  .lists{
    display:block;
}
}

.element:-ms-fullscreen p {
  visibility: visible !important;
}
.element:fullscreen {
  /*background-color: #e4708a;*/
  background-color: #F1F3F7;
  width: 100vw;
  height: 100vh;
}

#fs-exit-doc-button{
    display: none;
}
#fs-doc-button{
    display: inline-block;
}
body.fullScreenClass #fs-doc-button{
    display: none !important;
}

body.fullScreenClass #fs-exit-doc-button{
    display: inline-block !important;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-css/1.4.6/select2-bootstrap.css" integrity="sha512-7/BfnxW2AdsFxJpEdHdLPL7YofVQbCL4IVI4vsf9Th3k6/1pu4+bmvQWQljJwZENDsWePEP8gBkDKTsRzc5uVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <div class="d-flex align-items-center">
                    <div class="btn-group m-btn-group mr-2 bg-light-grey p-2 border-radius-1" role="group"
                        aria-label="...">
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment radio-active" data-value="1">Day</button>
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment " data-value="2">Week</button>
                        <button type="button" class="btn btn-secondary btn-sm fw-400 btn_segment" data-value="3">Month</button>
                    </div>
                    
                </div>
            </div>
        </div>
        <!-- ends titleRow -->

    </div>

    <!-- END: Subheader -->
    <div class="m-content" style="height: 100%;">

        <!--Begin::Section-->

          <div class="row" id="m_sortable_portlets">

            @if(isset($sensors) && count($sensors)>0)
            @foreach($sensors as $row)
            @php 
            $connected_equipment = App\Device::where('sensor_id',$row->device_id)->first();
            @endphp
            @if(($connected_equipment !='' || $connected_equipment!=null) && $currentCompany->parent_id!=0)
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
                            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                m-dropdown-toggle="click" aria-expanded="true">
                                <div class="buttons" >
                                
                                
                                
                                        @if (!empty($CompanyAdminEmail))


                                        <button type="button" class="btn btn-light order-service"  company_id="{{$row->company_id}}" style="border: #bfbfbf 1px solid;" device_id="{{$row->device_id}}" data-toggle="modal" data-target="#modal-support" >
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"/></svg> Service
                                        </button>
                                        @endif
                                       
                                        </div>

                                <a href="#" class="m-portlet__nav-link m-dropdown__toggle btn m-btn m-btn--link lists">
                                    <i class="la la-ellipsis-v"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span
                                        class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">

                                                    <li class="m-nav__item sensor-detail">
                                                        <a href="{{url('sensor-details/'.$row->company_id.'/'.$row->device_id)}}" class="m-nav__link">
                                                            <i class="m-nav__link-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 512 512"><path fill="currentColor" d="M136 24H16v120h120Zm-32 88H48V56h56Zm32 88H16v120h120Zm-32 88H48v-56h56Zm32 88H16v120h120Zm-32 88H48v-56h56Zm72-440.002h320v32H176zm0 88h256v32H176zm0 88h320v32H176zm0 88h256v32H176zm0 176h256v32H176zm0-88h320v32H176z"/></svg>
                                                            </i>
                                                            <span class="m-nav__link-text">Details</span>
                                                        </a>
                                                    </li>

                                                        @if (!empty($CompanyAdminEmail))
                                                <li class="m-nav__item order-service" company_id="{{$row->company_id}}" device_id="{{$row->device_id}}">
                                                        <a href="#" data-toggle="modal" data-target="#modal-support" class="m-nav__link ">

                                                <i class="m-nav__link-icon">
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"/></svg></i>
                                                            <span class="m-nav__link-text">Order Service</span>
                                        </button>
                                        </a>
                                                    </li>
                                        @endif
                                                    {{-- <li class="m-nav__item mt-3">
                                                        <a href="#"
                                                            class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><span
                                                                class="iconify" data-icon="carbon:delete"></span> Remove
                                                            Card</a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            @elseif($currentCompany->parent_id==0)
            <div class="col-md-6 connectedSortable" id="{{$row->id}}">
                <div class="@if(isset($row->is_active) && $row->is_active==0) {{-- m-portlet-offline --}} @endif m-portlet--sortable m-portlet panel-has-radius mb-4 relative hastransition">

                    <div class="m-portlet__head m-portlet__head_sm p-2 header">
                        <div class="c-card-caption">
                            <figure>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32"><path fill="currentColor" d="M13 17.26V6a4 4 0 0 0-8 0v11.26a7 7 0 1 0 8 0zM9 4a2 2 0 0 1 2 2v7H7V6a2 2 0 0 1 2-2zm0 24a5 5 0 0 1-2.5-9.33l.5-.28V15h4v3.39l.5.28A5 5 0 0 1 9 28zM20 4h10v2H20zm0 6h7v2h-7zm0 6h10v2H20zm0 6h7v2h-7z"/></svg>
                            </figure>
                            <figcaption>
                                <span class="fw-500">
                                    {{!empty($row->name) ? $row->name :$row->device_id}}
                                </span>
                                <br />
                                <small id="toolTipValue-{{$row->device_id}}">
                                    Temperature Sensor
                                </small>
                            </figcaption>
                        </div>
                        <div>
                            <!--begin: Dropdown-->
                            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
                                m-dropdown-toggle="click" aria-expanded="true">
                                <div class="buttons" >
                                
                                
                                
                                        @if (!empty($CompanyAdminEmail))


                                        <button type="button" class="btn btn-light order-service"  company_id="{{$row->company_id}}" style="border: #bfbfbf 1px solid;" device_id="{{$row->device_id}}" data-toggle="modal" data-target="#modal-support" >
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"/></svg> Service
                                        </button>
                                        @endif
                                        <button class="btn btn-light detail-page" company_id="{{$row->company_id}}" device_id="{{$row->device_id}}" style="border: #bfbfbf 1px solid;">
                                                 <i class="m-nav__link-icon">
                                                    <svg fill="currentColor"  width="1em" height="1em" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M960 0c530.193 0 960 429.807 960 960s-429.807 960-960 960S0 1490.193 0 960 429.807 0 960 0Zm0 101.053c-474.384 0-858.947 384.563-858.947 858.947S485.616 1818.947 960 1818.947 1818.947 1434.384 1818.947 960 1434.384 101.053 960 101.053Zm-42.074 626.795c-85.075 39.632-157.432 107.975-229.844 207.898-10.327 14.249-10.744 22.907-.135 30.565 7.458 5.384 11.792 3.662 22.656-7.928 1.453-1.562 1.453-1.562 2.94-3.174 9.391-10.17 16.956-18.8 33.115-37.565 53.392-62.005 79.472-87.526 120.003-110.867 35.075-20.198 65.9 9.485 60.03 47.471-1.647 10.664-4.483 18.534-11.791 35.432-2.907 6.722-4.133 9.646-5.496 13.23-13.173 34.63-24.269 63.518-47.519 123.85l-1.112 2.886c-7.03 18.242-7.03 18.242-14.053 36.48-30.45 79.138-48.927 127.666-67.991 178.988l-1.118 3.008a10180.575 10180.575 0 0 0-10.189 27.469c-21.844 59.238-34.337 97.729-43.838 138.668-1.484 6.37-1.484 6.37-2.988 12.845-5.353 23.158-8.218 38.081-9.82 53.42-2.77 26.522-.543 48.24 7.792 66.493 9.432 20.655 29.697 35.43 52.819 38.786 38.518 5.592 75.683 5.194 107.515-2.048 17.914-4.073 35.638-9.405 53.03-15.942 50.352-18.932 98.861-48.472 145.846-87.52 41.11-34.26 80.008-76 120.788-127.872 3.555-4.492 3.555-4.492 7.098-8.976 12.318-15.707 18.352-25.908 20.605-36.683 2.45-11.698-7.439-23.554-15.343-19.587-3.907 1.96-7.993 6.018-14.22 13.872-4.454 5.715-6.875 8.77-9.298 11.514-9.671 10.95-19.883 22.157-30.947 33.998-18.241 19.513-36.775 38.608-63.656 65.789-13.69 13.844-30.908 25.947-49.42 35.046-29.63 14.559-56.358-3.792-53.148-36.635 2.118-21.681 7.37-44.096 15.224-65.767 17.156-47.367 31.183-85.659 62.216-170.048 13.459-36.6 19.27-52.41 26.528-72.201 21.518-58.652 38.696-105.868 55.04-151.425 20.19-56.275 31.596-98.224 36.877-141.543 3.987-32.673-5.103-63.922-25.834-85.405-22.986-23.816-55.68-34.787-96.399-34.305-45.053.535-97.607 15.256-145.963 37.783Zm308.381-388.422c-80.963-31.5-178.114 22.616-194.382 108.33-11.795 62.124 11.412 115.76 58.78 138.225 93.898 44.531 206.587-26.823 206.592-130.826.005-57.855-24.705-97.718-70.99-115.729Z" fill-rule="evenodd"></path> </g></svg>
                                                            </i>
                                                            <span class="m-nav__link-text"></span>
                                                        </button>
                                        </div>

                                <a href="#" class="m-portlet__nav-link m-dropdown__toggle btn m-btn m-btn--link lists">
                                    <i class="la la-ellipsis-v"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span
                                        class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav">

                                                    <li class="m-nav__item sensor-detail">
                                                        <a href="{{url('sensor-details/'.$row->company_id.'/'.$row->device_id)}}" class="m-nav__link">
                                                            <i class="m-nav__link-icon">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 512 512"><path fill="currentColor" d="M136 24H16v120h120Zm-32 88H48V56h56Zm32 88H16v120h120Zm-32 88H48v-56h56Zm32 88H16v120h120Zm-32 88H48v-56h56Zm72-440.002h320v32H176zm0 88h256v32H176zm0 88h320v32H176zm0 88h256v32H176zm0 176h256v32H176zm0-88h320v32H176z"/></svg>
                                                            </i>
                                                            <span class="m-nav__link-text">Details</span>
                                                        </a>
                                                    </li>

                                                        @if (!empty($CompanyAdminEmail))
                                                <li class="m-nav__item order-service" company_id="{{$row->company_id}}" device_id="{{$row->device_id}}">
                                                        <a href="#" data-toggle="modal" data-target="#modal-support" class="m-nav__link ">

                                                <i class="m-nav__link-icon">
                                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"/></svg></i>
                                                            <span class="m-nav__link-text">Order Service</span>
                                        </button>
                                        </a>
                                                    </li>
                                        @endif
                                                    {{-- <li class="m-nav__item mt-3">
                                                        <a href="#"
                                                            class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><span
                                                                class="iconify" data-icon="carbon:delete"></span> Remove
                                                            Card</a>
                                                    </li> --}}
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

<!--begin::Modal-->
<div class="modal fade" id="modal-support" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportModalLabel">Order Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                $compID = isset($currentCompany->company_id)?$currentCompany->company_id:'-';
                @endphp
                <form method="post" action="{{route('sendOrderService',['company_id'=>$compID])}}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>
                            Project ID
                        </label>
                        <input type="text" class="form-control" name="company_id" value="{{$currentCompany->company_id??''}}" readonly>
                    </div>

                    <div class="mb-3">
                        <label>
                            Project Name
                        </label>
                        <input type="text" class="form-control" name="company_name" value="{{$currentCompany->name??''}}" readonly>
                    </div>
                    <div class="mb-3">
                        <label>
                            Phone Number
                        </label>
                        <input type="text" class="form-control" name="phone_number" required>
                    </div>

                    <!--Bootstrap Select-->
                    <div class="mb-3" >
                        <label>
                            Device
                        </label>
                         <select name="devices[]" id="chooseFileInput" class="form-control m-bootstrap-select m_selectpicker" style="font-weight:bold;" multiple>
                             @if(isset($sensors) && count($sensors)>0)
                              @foreach($sensors as $row)
                                @php 
                                $equipment = App\Device::where('sensor_id',$row->device_id)->first();
                                @endphp 
                                @if($equipment != null)
                                @if(isset($equipment->name) && $equipment->name !='')
                                    <option class="select_device" value="{{ $row->device_id }}">{{ $equipment->name }}</option>
                                    @else
                                    <option class="select_device" value="{{ $row->device_id }}">{{ $equipment->device_id }}</option>
                                @endif
                        @endif
                        @endforeach
                        @endif
                        </select>
                    </div>
                    <!--Bootstrap Select ends-->
                    <div class="input-group mb-3">
                        <input type="file" class="form-control" name="order_service_file" style="width: 100%; height: auto;">
                    </div>
                    
                    <div class="mb-3">
                        <label>
                            Comments
                        </label>
                        <textarea name="description" class="form-control" style="height: 140px;font-weight:600; font-family: 'Open Sans';"></textarea>
                    </div>
                    <div>
                        <input type="checkbox" id="urgent" name="urgent" value="Yes">
                        <label for="urgent">Urgent</label>
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
<!--end::Modal-->
@endsection
@push('scripts')
<script src="https://js.pusher.com/3.1/pusher.min.js"></script>
<script type="text/javascript" src="{{asset('assets/js/jquery.timeago.js')}}"></script>
<script>
    $(document).ready(function(){
        $('body').addClass('showFullScreen').addClass('fullScreenClass');
        $('.detail-page').on('click',function(){
           var device_id = $(this).attr('device_id');
           var company_id = $(this).attr('company_id');
           let url ='{{url("sensor-details")}}'+'/'+company_id+'/'+device_id;
           window.location.href = url;
        });
        $('.order-service').on('click',function(){
           var device_id = $(this).attr('device_id');
            //var option = $('.selectpicker').find('option span.text:contains("' + device_id + '")').parent();
                var option = $('.m_selectpicker').find('option[value="'+device_id+'"]');
                option.prop('selected', true);
            //$('.selectpicker').selectpicker('refresh');

           $('.m_selectpicker').selectpicker('refresh');
           //$('.m_selectpicker').selectpicker('show');
        });
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
        loadGraps(1);
        $( ".btn_segment" ).on( "click", function() {
            console.log( $(this).data('value') );
            $('.btn_segment').removeClass('radio-active');
            setTimeout(() => {
			 $(this).addClass('radio-active');
			}, 100)

            loadGraps($(this).data('value'));
         });
    });
    var chartAr = [];
        var intervalAr = [];
function requestData(id,device_id,event_id) {

    	 Highcharts.getJSON('{{url("events")}}'+'/'+device_id+'/'+event_id, function (data) {
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
                    allowDecimals: false,
                    labels: {
                        formatter: function() {
                            return this.value+'°C';
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
            url:'{{url("update-order")}}',
            type:'post',
            data:{position:data},
            success: function(data){
            }
        });
    }

</script>
@endpush