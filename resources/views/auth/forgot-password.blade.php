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
                                    <a href="{{url('login')}}">
                                        <img src="{{url('public/assets/app/media/img/logos/logo.png')}}" style="width:200px;">
                                    </a>
                                </div>
                                <div class="m-login__signin">
                                    <div class="m-login__head">
                                        <h3 class="m-login__title">Forgot Password</h3>
                                    </div>
                                    <form class="m-login__form m-form" action="{{ route('send-forgot-password') }}" method="post">
                                        @if(\Session::has('message'))
                                        <div class="alert alert-success">{{\Session::get('message')}}</div>
                                        @endif
                                        @if(\Session::has('error'))
                                        <div class="alert alert-danger">{{\Session::get('error')}}</div>
                                        @endif
                                        @csrf
                                        <div class="form-group m-form__group @error('email') has-danger @enderror">
                                            <input class="form-control m-input" type="email" placeholder="Enter email address" name="email" autocomplete="off" value="{{ old('email') }}" required  autofocus>
                                            @error('email')
                                             <div id="email-error" class="form-control-feedback">{{ $message }}</div>   
                                            @enderror
                                        </div>
                                        <div class="m-login__form-action">
                                            <button type="submit" id="m_login_signin_submit2" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">Reset Password</button>
                                        </div>
                                    </form>

                                    <div class="text-center">
                                        <a href="{{url('login')}}" class="m-link">
                                            Back to Login
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="m-stack__item m-stack__item--center d-none">
                            <div class="m-login__account">
                                <span class="m-login__account-msg">
                                    Don't have an account yet ?
                                </span>&nbsp;&nbsp;
                                <a href="javascript:;" id="m_login_signup" class="m-link m-link--focus m-login__account-link">Sign Up</a>
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
