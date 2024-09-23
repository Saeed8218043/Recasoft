<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

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
    <link href="{{url('public/assets/vendors/base/vendors.bundle.css')}}" rel="stylesheet" type="text/css" />


    <link href="{{url('public/assets/demo/default/base/style.bundle.css')}}" rel="stylesheet" type="text/css" />

    <!-- Custom Styling -->
    <link href="{{url('public/assets/demo/default/base/custom_style.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('public/assets/demo/default/base/responsive.css')}}" rel="stylesheet" type="text/css" />

    

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{url('public/assets/demo/default/media/img/logo/favicon.ico')}}" />

    
    
</head>
<!-- begin::Body -->
    <body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

        @yield('content')
    </body>

    <!-- end::Body -->
</html>
