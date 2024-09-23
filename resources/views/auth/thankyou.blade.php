@extends('layouts.guest')

@section('content')
<!-- begin:: Page -->
        <div class="m-grid m-grid--hor m-grid--root m-page">
            <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-grid--tablet-and-mobile m-grid--hor-tablet-and-mobile m-login m-login--1 m-login--signin login-custom" id="m_login">
                <div class="m-grid__item m-grid__item--order-tablet-and-mobile-2 m-login__aside">
                    <div class="m-stack m-stack--hor m-stack--desktop">
                        <div class="m-stack__item m-stack__item--fluid">
                            <div class="m-login__wrapper m-login__wrapper-custom">
                                <div class="m-login__logo mb-5" style="margin-bottom:0;">
                                    <a href="#">
                                        <img src="{{url('public/assets/app/media/img/logos/logo.png')}}" style="width:200px;">
                                    </a>
                                </div>
                                <div class="m-login__signin">
                                    <div class="m-login__head">
                                        <h3 class="m-login__title">Reset Password Confirmation</h3>
                                    </div>
                                    <br>
                                    @if(isset($success))
                                    <div class="alert alert-success">{{$success}}</div>
                                    @endif
                                    <div class="text-center">
                                        <a href="{{url('login')}}" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air" style="font-family: 'Open Sans'">Go to login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- end:: Page -->

        <!--begin::Global Theme Bundle -->
        <script src="{{url('assets/vendors/base/vendors.bundle.js')}}" type="text/javascript"></script>
        <script src="{{url('assets/demo/default/base/scripts.bundle.js')}}" type="text/javascript"></script>

        <!--end::Global Theme Bundle -->

        <!--begin::Page Scripts -->
        <script src="{{url('assets/snippets/custom/pages/user/login.js')}}" type="text/javascript"></script>

        <!--end::Page Scripts -->
@endsection
