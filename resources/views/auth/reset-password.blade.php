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
                                        <h3 class="m-login__title">Reset Password</h3>
                                    </div>
                                    <form class="m-login__form m-form" action="{{ route('reset-password-update') }}" method="post">
                                        @if(\Session::has('error'))
                                        <div class="alert alert-danger">{{\Session::get('error')}}</div>
                                        @endif
                                        @if(\Session::has('success'))
                                        <div class="alert alert-success">{{\Session::get('success')}} <a href="{{url('login')}}" style="color:#fff;text-decoration:underline;">Click here to Login</a></div>
                                        @endif
                                        @csrf
                                        <input type="hidden" name="id" value="{{$id??0}}">
                                        <div class="form-group m-form__group @error('email') has-danger @enderror">
                                            <input class="form-control m-input" type="text" placeholder="Email" name="email" autocomplete="off" value="{{$user[0]->email??''}}" required  autofocus readonly="">
                                            @error('email')
                                             <div id="email-error" class="form-control-feedback">{{ $message }}</div>   
                                            @enderror
                                        </div>
                                        <div class="form-group m-form__group @error('password') has-danger @enderror">
                                            <input class="form-control m-input" type="password" placeholder="Password" name="password" autocomplete="off" value="{{ old('password') }}" required  autofocus>
                                            @error('password')
                                             <div id="password-error" class="form-control-feedback">{{ $message }}</div>   
                                            @enderror
                                        </div>
                                        <div class="form-group m-form__group @error('confirm_password') has-danger @enderror">
                                            <input class="form-control m-input" type="password" placeholder="Confirm Password" name="confirm_password" autocomplete="off" value="{{ old('confirm_password') }}" required  autofocus>
                                            @error('password')
                                             <div id="confirm-password-error" class="form-control-feedback">{{ $message }}</div>   
                                            @enderror
                                        </div>
                                        <div class="m-login__form-action">
                                            <button type="submit" id="m_login_signin_submit2" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">Reset</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="m-login__forget-password">
                                    <div class="m-login__head">
                                        <h3 class="m-login__title">Forgotten Password ?</h3>
                                        <div class="m-login__desc">Enter your email to reset your password:</div>
                                    </div>
                                    <form class="m-login__form m-form" action="">
                                        <div class="form-group m-form__group">
                                            <input class="form-control m-input" type="text" placeholder="Email" name="email" id="m_email" autocomplete="off">
                                        </div>
                                        <div class="m-login__form-action">
                                            <button id="m_login_forget_password_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">Request</button>
                                            <button id="m_login_forget_password_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom">Cancel</button>
                                        </div>
                                    </form>
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
