<div id="m_aside_left" class="m-grid__item  m-aside-left  m-aside-left--skin-light ">
    @php
    
        $is_valid = 0;
        $currentRouteName = Request::route()->getName();
        $company = \App\Company::where(['company_id' => $company_id])->first();
        if (isset($company->id)) {
            $is_valid = 1;
        }

          $user_ID = \Auth::user()->id;
            $user_Role = '';
            if ($company_id != '') {
                $user_Role = \App\CompanyMembers::where([
                    'company_id' => $company_id,
                    'user_id' => $user_ID,
                    // , 'company_name' => $company_name
                ])
                    ->select('role')
                    ->first();
            }

            if( isset($company) && $company->parent_id !=0){
                $child_company = \App\Company::where(['company_id' => $company_id])->first();
            }

            if(isset($child_company) && $child_company->parent_id !=0  ){
                $role2 = 'valid';
            }
    @endphp
    <!-- BEGIN: Aside Menu -->
    <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-light m-aside-menu--submenu-skin-light "
        m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
        <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">

            <li class="m-menu__item sidebar_logo">
                <a href="{{ url('dashboard') }}/{{ $company_id ?? '' }}">
                    <img alt="" src="{{ url('public/assets/demo/default/media/img/logo/logo.png') }}"
                        class="img-fluid" />
                </a>
            </li>
            {{-- @if (\Auth::user()->id == 1) --}}
            {{-- <li class="m-menu__item  {{$currentRouteName=='home' ? 'm-menu__item--active' : '' }}" aria-haspopup="true"><a href="{{url('dashboard')}}/{{$company_id}}" class="m-menu__link "><i class="m-menu__link-icon flaticon-line-graph"></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span class="m-menu__link-text">Recasoft Admin</span>
                <span class="m-menu__link-badge"> </span> </span></span></a>
            </li> --}}

            {{-- @else --}}
                    @if ((isset($role2) && $role2=='valid' ) || $user_ID == 1)

            <li class="m-menu__item  {{ $currentRouteName == 'home' ? 'm-menu__item--active' : '' }}"
                aria-haspopup="true"><a href="{{ url('dashboard') }}/{{ $company_id }}" class="m-menu__link "><i
                        class="m-menu__link-icon flaticon-line-graph"></i><span class="m-menu__link-title"> <span
                            class="m-menu__link-wrap"> <span class="m-menu__link-text">Dashboard</span>
                            <span class="m-menu__link-badge"> {{-- <span class="m-badge m-badge--danger">2</span> --}}</span> </span></span></a>
            </li>
            @endif
            {{-- @endif --}}
            @if ($is_valid == 1)
             <li class="m-menu__item {{ $currentRouteName == 'equipments' || $currentRouteName == 'equipment-details' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('equipments') }}/{{ $company_id }}" class="m-menu__link loader-btn"><i
                            class="m-menu__link-icon">
                            <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 12.8799V11.1199C2 10.0799 2.85 9.21994 3.9 9.21994C5.71 9.21994 6.45 7.93994 5.54 6.36994C5.02 5.46994 5.33 4.29994 6.24 3.77994L7.97 2.78994C8.76 2.31994 9.78 2.59994 10.25 3.38994L10.36 3.57994C11.26 5.14994 12.74 5.14994 13.65 3.57994L13.76 3.38994C14.23 2.59994 15.25 2.31994 16.04 2.78994L17.77 3.77994C18.68 4.29994 18.99 5.46994 18.47 6.36994C17.56 7.93994 18.3 9.21994 20.11 9.21994C21.15 9.21994 22.01 10.0699 22.01 11.1199V12.8799C22.01 13.9199 21.16 14.7799 20.11 14.7799C18.3 14.7799 17.56 16.0599 18.47 17.6299C18.99 18.5399 18.68 19.6999 17.77 20.2199L16.04 21.2099C15.25 21.6799 14.23 21.3999 13.76 20.6099L13.65 20.4199C12.75 18.8499 11.27 18.8499 10.36 20.4199L10.25 20.6099C9.78 21.3999 8.76 21.6799 7.97 21.2099L6.24 20.2199C5.33 19.6999 5.02 18.5299 5.54 17.6299C6.45 16.0599 5.71 14.7799 3.9 14.7799C2.85 14.7799 2 13.9199 2 12.8799Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Equipment</span></span></span></a>
                </li>
                <li class="m-menu__item {{ $currentRouteName == 'sensors' || $currentRouteName == 'sensor-details' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('sensors') }}/{{ $company_id }}" class="m-menu__link loader-btn"><i
                            class="m-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M7.76 16.24C6.67 15.16 6 13.66 6 12s.67-3.16 1.76-4.24l1.42 1.42C8.45 9.9 8 10.9 8 12c0 1.1.45 2.1 1.17 2.83l-1.41 1.41zm8.48 0C17.33 15.16 18 13.66 18 12s-.67-3.16-1.76-4.24l-1.42 1.42C15.55 9.9 16 10.9 16 12c0 1.1-.45 2.1-1.17 2.83l1.41 1.41zM12 10c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2zm8 2c0 2.21-.9 4.21-2.35 5.65l1.42 1.42C20.88 17.26 22 14.76 22 12s-1.12-5.26-2.93-7.07l-1.42 1.42A7.94 7.94 0 0 1 20 12zM6.35 6.35L4.93 4.93C3.12 6.74 2 9.24 2 12s1.12 5.26 2.93 7.07l1.42-1.42C4.9 16.21 4 14.21 4 12s.9-4.21 2.35-5.65z" />
                            </svg></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Sensors</span></span></span></a>
                </li>
                
                    @if ((isset($role2) && $role2=='valid' ) || $user_ID == 1)
                <li class="m-menu__item hide-tab {{ $currentRouteName == 'notifications' || $currentRouteName == 'create.notification' || $currentRouteName == 'notifications.alertHistory' || $currentRouteName == 'notification.detail' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('notifications') }}/{{ $company_id }}"
                        class="m-menu__link loader-btn"><i class="m-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                <path fill="currentColor"
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742c-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
                            </svg></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Notifications </span></span></span></a>
                </li>


                {{-- <li class="m-menu__item hide-tab {{ $currentRouteName == 'notifications.alertHistory' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('notifications-alertHistory') }}/{{ $company_id }}"
                        class="m-menu__link loader-btn"><i class="m-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 256 256"><path fill="currentColor" d="M207.8 112a79.7 79.7 0 0 0-79.2-80h-.6a79.9 79.9 0 0 0-79.8 80c0 34.3-7.1 53.7-13 63.9a16.2 16.2 0 0 0-.1 16.1a15.9 15.9 0 0 0 13.9 8h39a40 40 0 0 0 80 0h39a15.9 15.9 0 0 0 13.9-8a16.2 16.2 0 0 0-.1-16.1c-5.9-10.2-13-29.6-13-63.9ZM128 224a24.1 24.1 0 0 1-24-24h48a24.1 24.1 0 0 1-24 24Zm-79-40c6.9-11.9 15.2-34.1 15.2-72A63.8 63.8 0 0 1 128 48h.5a62.9 62.9 0 0 1 44.8 18.9a63.6 63.6 0 0 1 18.5 45.1c0 37.9 8.3 60.1 15.2 72ZM224.9 73.3a9.3 9.3 0 0 1-3.5.8a7.9 7.9 0 0 1-7.2-4.5a97 97 0 0 0-35-38.8a8 8 0 0 1 8.5-13.6a111.7 111.7 0 0 1 40.8 45.4a8 8 0 0 1-3.6 10.7Zm-190.3.8a9.3 9.3 0 0 1-3.5-.8a8 8 0 0 1-3.6-10.7a111.7 111.7 0 0 1 40.8-45.4a8 8 0 0 1 8.5 13.6a97 97 0 0 0-35 38.8a7.9 7.9 0 0 1-7.2 4.5Z"></path></svg>


                        </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Alert History</span></span></span></a>
                </li> --}}


                <li class="m-menu__item hide-tab {{ $currentRouteName == 'export' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('export') }}/{{ $company_id }}" class="m-menu__link loader-btn"><i
                            class="m-menu__link-icon flaticon-download"></i><span class="m-menu__link-title"> <span
                                class="m-menu__link-wrap"> <span class="m-menu__link-text">Reports</span></span></span></a>
                </li>

                 {{-- <li class="m-menu__item hide-tab {{ $currentRouteName == 'deviations' ? 'm-menu__item--active' : '' }}"
                aria-haspopup="true"><a href="{{ url('deviations') }}/{{ $company_id }}"
                    class="m-menu__link loader-btn"><i class="m-menu__link-icon">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            width="1.2em" height="1.2em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="M14 11c0 .55-.45 1-1 1H4c-.55 0-1-.45-1-1s.45-1 1-1h9c.55 0 1 .45 1 1zM3 7c0 .55.45 1 1 1h9c.55 0 1-.45 1-1s-.45-1-1-1H4c-.55 0-1 .45-1 1zm7 8c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1s.45 1 1 1h5c.55 0 1-.45 1-1zm8.01-2.13l.71-.71a.996.996 0 0 1 1.41 0l.71.71c.39.39.39 1.02 0 1.41l-.71.71l-2.12-2.12zm-.71.71l-5.16 5.16c-.09.09-.14.21-.14.35v1.41c0 .28.22.5.5.5h1.41c.13 0 .26-.05.35-.15l5.16-5.16l-2.12-2.11z"/></svg>
                        </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                class="m-menu__link-text">Errors </span></span></span></a>
            </li> --}}
           
            <!-- Service Link -->
            <li class="d-none m-menu__item {{ $currentRouteName == 'sendOrder.service' || $currentRouteName == 'sendOrder.logs' ? 'm-menu__item--active' : '' }}"
                aria-haspopup="true">
                    <a href="{{ url('sendOrder-service') }}/{{ $company_id }}"
                        class="m-menu__link loader-btn"><i class="m-menu__link-icon sendOrder">
                            <svg version="1.0" xmlns:xlink="http://www.w3.org/1999/xlink" width="1.2em" height="1.2em" viewBox="0 0 24 24" xml:space="preserve" fill="currentColor">

                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>

                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>

                                <g id="SVGRepo_iconCarrier"> <g id="Guides"> <g id="_x32_0_px_2_"> </g> <g id="_x32_0px"> </g> <g id="_x34_0px"> </g> <g id="_x34_4_px"> </g> <g id="_x34_8px"> <g id="_x31_6px"> </g> <g id="square_4px"> <g id="_x32_8_px"> <g id="square_4px_2_"> </g> <g id="square_4px_3_"> </g> <g id="square_4px_1_"> </g> <g id="_x32_4_px_2_"> </g> <g id="_x31_2_px"> </g> </g> </g> </g> <g id="Icons"> </g> <g id="_x32_0_px"> </g> <g id="square_6px"> <g id="_x31_2_PX"> </g> </g> <g id="_x33_6_px"> <g id="_x33_2_px"> <g id="_x32_8_px_1_"> <g id="square_6px_1_"> </g> <g id="_x32_0_px_1_"> <g id="_x31_2_PX_2_"> </g> <g id="_x34_8_px"> <g id="_x32_4_px"> </g> <g id="_x32_4_px_1_"> </g> </g> </g> </g> </g> </g> <g id="_x32_0_px_3_"> </g> <g id="_x32_0_px_4_"> </g> <g id="New_Symbol_8"> <g id="_x32_4_px_3_"> </g> </g> </g> <g id="Artboard"> </g> <g id="Free_Icons"> <g> <line style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="0.5" y1="9.5" x2="7.5" y2="9.5"></line> <line style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="3" y1="12.5" x2="7.5" y2="12.5"></line> <line style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="5.5" y1="15.5" x2="7.5" y2="15.5"></line> <rect x="9.5" y="7.5" style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" width="14" height="11"></rect> <polyline style="fill:none;stroke:currentColor;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" points="9.5,7.5 16.5,13.5 23.5,7.5 "></polyline> </g> </g> </g>

                                </svg>
                            </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Order Service</span></span></span></a>
            </li>
            <!-- Service Link ends -->

                    @endif
                <li class="m-menu__item {{ $currentRouteName == 'documents' || $currentRouteName == 'documents.subfolders' ? 'm-menu__item--active' : '' }}"
                    aria-haspopup="true"><a href="{{ url('documents') }}/{{ $company_id }}" class="m-menu__link loader-btn"><i
                            class="m-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M203.3 195.4a28 28 0 1 0-30.6 0a36.4 36.4 0 0 0-19.6 23.6a4.2 4.2 0 0 0 2.9 4.9h1a4.1 4.1 0 0 0 3.9-3a28 28 0 0 1 54.2 0a4 4 0 0 0 7.8-2a36.4 36.4 0 0 0-19.6-23.5ZM168 172a20 20 0 1 1 20 20a20.1 20.1 0 0 1-20-20Zm48-96h-86.3l-28.5-28.5a11.9 11.9 0 0 0-8.5-3.5H40a12 12 0 0 0-12 12v144.6A11.4 11.4 0 0 0 39.4 212h81.2a4 4 0 1 0 0-8H39.4a3.4 3.4 0 0 1-3.4-3.4V84h180a4 4 0 0 1 4 4v32a4 4 0 0 0 8 0V88a12 12 0 0 0-12-12ZM40 52h52.7a3.6 3.6 0 0 1 2.8 1.2L118.3 76H36V56a4 4 0 0 1 4-4Z"></path></svg>
                        </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Files</span></span></span></a>
                </li>

            <li class="m-menu__item {{ $currentRouteName == 'company-settings' ? 'm-menu__item--active' : '' }}"
                aria-haspopup="true">
                    <a href="{{ url('company-settings') }}/{{ $company_id }}"
                        class="m-menu__link loader-btn"><i class="m-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 32 32">
                                <path fill="currentColor"
                                    d="M12 4a5 5 0 1 1-5 5a5 5 0 0 1 5-5m0-2a7 7 0 1 0 7 7a7 7 0 0 0-7-7zm10 28h-2v-5a5 5 0 0 0-5-5H9a5 5 0 0 0-5 5v5H2v-5a7 7 0 0 1 7-7h6a7 7 0 0 1 7 7zm0-26h10v2H22zm0 5h10v2H22zm0 5h7v2h-7z" />
                            </svg></i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Project Settings</span></span></span></a>
            </li>
            
            @if($user_ID ==1)
             <li class="m-menu__item hide-tab {{ $currentRouteName == 'system.log' ? 'm-menu__item--active' : '' }}"
                aria-haspopup="true"><a href="{{ url('system-log') }}/{{ $company_id }}"
                    class="m-menu__link loader-btn"><i class="m-menu__link-icon">
                        <svg viewBox="0 0 496 494" xmlns="http://www.w3.org/2000/svg" fill="currentcolor" height="1em" width="1.3em"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="currentcolor" stroke-width="31.744"></g><g id="SVGRepo_iconCarrier"> <defs> <style>.cls-1{fill:none;stroke:currentcolor;stroke-linecap:round;stroke-linejoin:round;stroke-width:17.408;}</style> </defs> <g data-name="Layer 2" id="Layer_2"> <g data-name="E425, History, log, manuscript" id="E425_History_log_manuscript"> <path class="cls-1" d="M75.11,117h0A21.34,21.34,0,0,1,53.83,95.57V31.39A21.34,21.34,0,0,1,75.11,10h0A21.34,21.34,0,0,1,96.39,31.39V95.57A21.34,21.34,0,0,1,75.11,117Z"></path> <rect class="cls-1" height="64.17" width="319.22" x="96.39" y="31.39"></rect> <rect class="cls-1" height="320.87" width="319.22" x="96.39" y="95.57"></rect> <path class="cls-1" d="M34.34,39.08H53.83a0,0,0,0,1,0,0v48.8a0,0,0,0,1,0,0H34.34A24.34,24.34,0,0,1,10,63.54v-.13A24.34,24.34,0,0,1,34.34,39.08Z"></path> <path class="cls-1" d="M436.89,117h0a21.34,21.34,0,0,0,21.28-21.39V31.39A21.34,21.34,0,0,0,436.89,10h0a21.34,21.34,0,0,0-21.28,21.39V95.57A21.34,21.34,0,0,0,436.89,117Z"></path> <path class="cls-1" d="M482.51,39.08H502a0,0,0,0,1,0,0v48.8a0,0,0,0,1,0,0H482.51a24.34,24.34,0,0,1-24.34-24.34v-.13a24.34,24.34,0,0,1,24.34-24.34Z" transform="translate(960.17 126.96) rotate(-180)"></path> <path class="cls-1" d="M75.11,395h0a21.34,21.34,0,0,0-21.28,21.39v64.18A21.34,21.34,0,0,0,75.11,502h0a21.34,21.34,0,0,0,21.28-21.39V416.43A21.34,21.34,0,0,0,75.11,395Z"></path> <rect class="cls-1" height="64.17" width="319.22" x="96.39" y="416.43"></rect> <path class="cls-1" d="M34.34,424.12H53.83a0,0,0,0,1,0,0v48.8a0,0,0,0,1,0,0H34.34A24.34,24.34,0,0,1,10,448.58v-.13A24.34,24.34,0,0,1,34.34,424.12Z"></path> <path class="cls-1" d="M436.89,395h0a21.34,21.34,0,0,1,21.28,21.39v64.18A21.34,21.34,0,0,1,436.89,502h0a21.34,21.34,0,0,1-21.28-21.39V416.43A21.34,21.34,0,0,1,436.89,395Z"></path> <path class="cls-1" d="M482.51,424.12H502a0,0,0,0,1,0,0v48.8a0,0,0,0,1,0,0H482.51a24.34,24.34,0,0,1-24.34-24.34v-.13a24.34,24.34,0,0,1,24.34-24.34Z" transform="translate(960.17 897.04) rotate(-180)"></path> <line class="cls-1" x1="143.41" x2="256" y1="140.11" y2="140.11"></line> <line class="cls-1" x1="143.41" x2="371.26" y1="186.47" y2="186.47"></line> <line class="cls-1" x1="143.41" x2="371.26" y1="232.82" y2="232.82"></line> <line class="cls-1" x1="143.41" x2="371.26" y1="279.18" y2="279.18"></line> <line class="cls-1" x1="143.41" x2="371.26" y1="325.53" y2="325.53"></line> <line class="cls-1" x1="256" x2="371.26" y1="371.89" y2="371.89"></line> </g> </g> </g></svg>

                        </i><span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                class="m-menu__link-text">System Log </span></span></span></a>
            </li>
            @endif
            @endif

            <li class="m-menu__item d-none" aria-haspopup="true"><a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                    class="m-menu__link loader-btn"><i class="m-menu__link-icon flaticon-logout"></i><span
                        class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                class="m-menu__link-text">Logout</span></span></span></a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>




<style type="text/css">
body.m-brand--minimize .m-aside-menu .m-menu__nav>.m-menu__item button.m-menu__link .m-menu__link-icon  {
    padding-left: 20px;
}
body.m-brand--minimize.m-aside-left-minimize-hover .m-aside-menu .m-menu__nav>.m-menu__item button.m-menu__link .m-menu__link-icon {
    padding-left: 0;
}
</style>
 @php
             $currentComp= \App\Company::where('company_id',$company_id)->first();
             if($currentComp!=null){
                $par_comp= \App\Company::where('id',$currentComp->parent_id)->first();
             }
                if(isset($par_comp)){
                $setting = \App\CompanySetting::where('company_id',$par_comp->company_id)->where('meta_key','email')->first();
                $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
                }else{
                $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
                $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
                }

            @endphp

@if (!empty($CompanyAdminEmail))
        <!-- Service Link New -->
        @if ((isset($role2) && $role2=='valid' ))
        <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow m-menu__nav__middle ">
            <li class="m-menu__item__primary m-menu__item {{ $currentRouteName == 'sendOrder.service' || $currentRouteName == 'sendOrder.logs' ? '' : '' }}"
                aria-haspopup="true">
                    <button  href="" class="m-menu__link loader-btn border-0" onclick="sendOrder()">
                        <i class="m-menu__link-icon">
                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 26 26"><path fill="currentColor" d="M1.313 0L0 1.313l2.313 4l1.5-.22l9.156 9.157l-.781.75c-.4.4-.4 1.006 0 1.406l.406.407c.4.4 1.012.4 1.312 0L15.094 18c-.1.6 0 1.313.5 1.813L21 25.188c1.1 1.1 2.9 1.1 4 0c1.3-1.2 1.288-2.994.188-4.094l-5.375-5.407c-.5-.5-1.213-.7-1.813-.5L16.687 14c.3-.4.3-1.012 0-1.313l-.375-.374a.974.974 0 0 0-1.406 0l-.656.656l-9.156-9.156l.218-1.5l-4-2.313zm19.5.031C18.84-.133 16.224 1.175 15 2.312c-1.506 1.506-1.26 3.475-.063 5.376l-2.124 2.125l1.5 1.687c.8-.7 1.98-.7 2.78 0l.407.406l.094.094l.875-.875c1.808 1.063 3.69 1.216 5.125-.219c1.4-1.3 2.918-4.506 2.218-6.406L23 7.406c-.4.4-1.006.4-1.406 0L18.687 4.5a.974.974 0 0 1 0-1.406L21.595.188c-.25-.088-.5-.133-.782-.157zm-11 12.469l-3.626 3.625A5.26 5.26 0 0 0 5 16c-2.8 0-5 2.2-5 5s2.2 5 5 5s5-2.2 5-5c0-.513-.081-1.006-.219-1.469l2.125-2.125l-.312-.406c-.8-.8-.794-2.012-.094-2.813L9.812 12.5zm7.75 4.563c.125 0 .243.024.343.125l5.907 5.906c.2.2.2.518 0 .718c-.2.2-.52.2-.72 0l-5.905-5.906c-.2-.2-.2-.518 0-.718c.1-.1.25-.125.375-.125zM5.688 18.405l1.906 1.907l-.688 2.593l-2.593.688l-1.907-1.907l.688-2.593l2.594-.688z"/></svg>
                            </i>
                            <span class="m-menu__link-title"> <span class="m-menu__link-wrap"> <span
                                    class="m-menu__link-text">Order Service</span></span></span>
                    </button>
            </li>
        </ul>
        @endif
        <!-- Service Link New ends -->
   @endif
<script>
    function sendOrder(){
        let company_id="{{$company_id??''}}";
        window.location.href = '{{url("sendOrder-service")}}'+'/'+company_id;
    }</script>
        <!-- Adding link at bottom of sidebar -->
        @if ($user_Role != '' || $user_ID == 1)
            @if ((isset($user_Role->role) && $user_Role->role == 2) || $user_ID == 1)
                <div class="sidebar_bottom_area">
                    <div class="sidebar_bottom_area_link">
                        <a href="{{ url('company-details') }}/{{ $company_id }}">
                            <figure>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 36 36">
                                    <path fill="currentColor" d="M31 8h-8v2h8v21h-8v2h10V10a2 2 0 0 0-2-2Z"
                                        class="clr-i-outline clr-i-outline-path-1" />
                                    <path fill="currentColor"
                                        d="M19.88 3H6.12A2.12 2.12 0 0 0 4 5.12V33h18V5.12A2.12 2.12 0 0 0 19.88 3ZM20 31h-3v-3H9v3H6V5.12A.12.12 0 0 1 6.12 5h13.76a.12.12 0 0 1 .12.12Z"
                                        class="clr-i-outline clr-i-outline-path-2" />
                                    <path fill="currentColor" d="M8 8h2v2H8z"
                                        class="clr-i-outline clr-i-outline-path-3" />
                                    <path fill="currentColor" d="M12 8h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-4" />
                                    <path fill="currentColor" d="M16 8h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-5" />
                                    <path fill="currentColor" d="M8 13h2v2H8z"
                                        class="clr-i-outline clr-i-outline-path-6" />
                                    <path fill="currentColor" d="M12 13h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-7" />
                                    <path fill="currentColor" d="M16 13h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-8" />
                                    <path fill="currentColor" d="M8 18h2v2H8z"
                                        class="clr-i-outline clr-i-outline-path-9" />
                                    <path fill="currentColor" d="M12 18h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-10" />
                                    <path fill="currentColor" d="M16 18h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-11" />
                                    <path fill="currentColor" d="M8 23h2v2H8z"
                                        class="clr-i-outline clr-i-outline-path-12" />
                                    <path fill="currentColor" d="M12 23h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-13" />
                                    <path fill="currentColor" d="M16 23h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-14" />
                                    <path fill="currentColor" d="M23 13h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-15" />
                                    <path fill="currentColor" d="M27 13h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-16" />
                                    <path fill="currentColor" d="M23 18h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-17" />
                                    <path fill="currentColor" d="M27 18h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-18" />
                                    <path fill="currentColor" d="M23 23h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-19" />
                                    <path fill="currentColor" d="M27 23h2v2h-2z"
                                        class="clr-i-outline clr-i-outline-path-20" />
                                    <path fill="none" d="M0 0h36v36H0z" />
                                </svg>
                            </figure>
                            <figcaption>
                                @if (isset($company_name) && $company_name != '')
                                    {{ $company_name }}
                                @else
                                    Recasoft Technologies
                                @endif

                            </figcaption>
                        </a>
                    </div>
                    <div class="sidebar_bottom_area_link mt-2">
                        <a href="{{ url('company-admins') }}/{{ $company_id }}">
                            <figure>
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="M21 10.5h-1v-1a1 1 0 0 0-2 0v1h-1a1 1 0 0 0 0 2h1v1a1 1 0 0 0 2 0v-1h1a1 1 0 0 0 0-2Zm-7.7 1.72A4.92 4.92 0 0 0 15 8.5a5 5 0 0 0-10 0a4.92 4.92 0 0 0 1.7 3.72A8 8 0 0 0 2 19.5a1 1 0 0 0 2 0a6 6 0 0 1 12 0a1 1 0 0 0 2 0a8 8 0 0 0-4.7-7.28ZM10 11.5a3 3 0 1 1 3-3a3 3 0 0 1-3 3Z" />
                                </svg>
                            </figure>
                            <figcaption>
                                Administrators
                            </figcaption>
                        </a>
                    </div>
                </div>
            @endif
        @endif


        <!-- Adding link at bottom of sidebar ends -->
    </div>

    <!-- END: Aside Menu -->
</div>
