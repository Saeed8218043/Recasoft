<div class="m-portlet--sortable m-portlet panel-has-radius mb-4 relative hastransition m-portlet-has-border border-warning">
    <div class="isloading d-none">
        <span class="iconify" data-icon="eos-icons:loading"></span>
    </div>
    <div class="m-portlet__head m-portlet__head_sm p-2">
        <div class="c-card-caption">
            <figure>
                <span class="iconify" data-icon="carbon:temperature"></span>
            </figure>
            <figcaption>
                <span class="fw-500">
                    {{$device->name??''}}
                </span>
                <br/>
                <small>
                    Temperature Sensor
                </small>
            </figcaption>
        </div>
        <div>
            <!--begin: Dropdown-->
            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="click" aria-expanded="true">
                <a href="#" class="m-portlet__nav-link m-dropdown__toggle btn m-btn m-btn--link">
                    <i class="la la-ellipsis-v"></i>
                </a>
                <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                                <ul class="m-nav">
                                    {{-- <li class="m-nav__item">
                                        <a href="" class="m-nav__link">
                                            <i class="m-nav__link-icon">
                                                <span class="iconify" data-icon="fluent:rename-20-regular"></span>
                                            </i>
                                            <span class="m-nav__link-text">Re-name Card</span>
                                        </a>
                                    </li> --}}
                                    <li class="m-nav__item">
                                        <a href="" class="m-nav__link">
                                            <i class="m-nav__link-icon">
                                                <span class="iconify" data-icon="cil:list-rich"></span>
                                            </i>
                                            <span class="m-nav__link-text">Sensor Details</span>
                                        </a>
                                    </li>
                                   {{--  <li class="m-nav__item mt-3">
                                        <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><span class="iconify" data-icon="carbon:delete"></span> Remove Card</a>
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
        <div class="graph_unit">
            <img src="assets/demo/default/media/img/misc/graph-1.jpg" alt="Graph" class="img-fluid">
        </div>
        <div class="graph_info_unit relative">
            <div class="graph_info_unit_x">
                <ul>
                    <li>
                        <label>
                            Max
                        </label>
                        <span>26.4</span>
                    </li>
                    <li>
                        <label>
                            Avg
                        </label>
                        <span>18.5</span>
                    </li>
                    <li>
                        <label>
                            Min
                        </label>
                        <span>14.7</span>
                    </li>
                </ul>
            </div>
            <div class="graph_info_unit_y">
                <h6>
                    {{$device->temperature??''}}°C
                </h6>
                <p class="m-0">
                    {{ $device->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
</div>