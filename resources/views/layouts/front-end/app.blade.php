<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180"
          href="{{asset('storage/company')}}/{{$web_config['fav_icon']->value}}">
    <link rel="icon" type="image/png" sizes="32x32"
          href="{{asset('storage/company')}}/{{$web_config['fav_icon']->value}}">

    <link rel="stylesheet" media="screen"
          href="{{asset('assets/front-end')}}/vendor/simplebar/dist/simplebar.min.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('assets/front-end')}}/vendor/tiny-slider/dist/tiny-slider.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('assets/front-end')}}/vendor/drift-zoom/dist/drift-basic.min.css"/>
    <link rel="stylesheet" media="screen"
          href="{{asset('assets/front-end')}}/vendor/lightgallery.js/dist/css/lightgallery.min.css"/>
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/css/toastr.css"/>
    <!-- Main Theme Styles + Bootstrap-->
    <link rel="stylesheet" media="screen" href="{{asset('assets/front-end')}}/css/theme.min.css">
    <link rel="stylesheet" media="screen" href="{{asset('assets/front-end')}}/css/slick.css">
    <link rel="stylesheet" media="screen" href="{{asset('assets/front-end')}}/css/font-awesome.min.css">
    <!--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/css/toastr.css"/>
    <link rel="stylesheet" href="{{asset('assets/front-end')}}/css/master.css"/>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Titillium+Web:wght@400;600;700&display=swap"
        rel="stylesheet">

    @stack('css_or_js')

    <link rel="stylesheet" href="{{asset('assets/front-end')}}/css/home.css"/>
    <link rel="stylesheet" href="{{asset('assets/front-end')}}/css/responsive1.css"/>
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    {{--dont touch this--}}
    <meta name="_token" content="{{csrf_token()}}">
    {{--dont touch this--}}
    <!--to make http ajax request to https-->
    <!--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">-->
    <style>
        body {
            background-color: #f7f8fa94;
        }

        .rtl {
            direction: {{ Session::get('direction') }};
        }

        .password-toggle-btn .password-toggle-indicator:hover {
            color: {{$web_config['primary_color']}};
        }

        .password-toggle-btn .custom-control-input:checked ~ .password-toggle-indicator {
            color: {{$web_config['secondary_color']}};
        }

        .dropdown-item:hover, .dropdown-item:focus {
            color: {{$web_config['primary_color']}};
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0)
        }

        .dropdown-item.active, .dropdown-item:active {
            color: {{$web_config['secondary_color']}};
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0)
        }

        .topbar a {
            color: black !important;
        }

        .navbar-light .navbar-tool-icon-box {
            color: {{$web_config['primary_color']}};
        }

        .search_button {
            background-color: {{$web_config['primary_color']}};
            border: none;
        }

        .nav-link {
            color: white !important;
        }

        .navbar-stuck-menu {
            background-color: {{$web_config['primary_color']}};
            min-height: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        .mega-nav {
            background: white;
            position: relative;
            margin-top: 6px;
            line-height: 17px;
            width: 304px;
            border-radius: 3px;
        }

        .mega-nav .nav-item .nav-link {
            padding-top: 11px !important;
            color: {{$web_config['primary_color']}}                                    !important;
            font-size: 20px;
            font-weight: 600;
            padding-left: 20px !important;
        }

        .nav-item .dropdown-toggle::after {
            margin-left: 20px !important;
        }

        .navbar-tool-text {
            padding-left: 5px !important;
            font-size: 16px;
        }

        .navbar-tool-text > small {
            color: #4b566b !important;
        }

        .modal-header .nav-tabs .nav-item .nav-link {
            color: black !important;
            /*border: 1px solid #E2F0FF;*/
        }

        .checkbox-alphanumeric::after,
        .checkbox-alphanumeric::before {
            content: '';
            display: table;
        }

        .checkbox-alphanumeric::after {
            clear: both;
        }

        .checkbox-alphanumeric input {
            left: -9999px;
            position: absolute;
        }

        .checkbox-alphanumeric label {
            width: 2.25rem;
            height: 2.25rem;
            float: left;
            padding: 0.375rem 0;
            margin-right: 0.375rem;
            display: block;
            color: #818a91;
            font-size: 0.875rem;
            font-weight: 400;
            text-align: center;
            background: transparent;
            text-transform: uppercase;
            border: 1px solid #e6e6e6;
            border-radius: 2px;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            -ms-transition: all 0.3s ease;
            transition: all 0.3s ease;
            transform: scale(0.95);
        }

        .checkbox-alphanumeric-circle label {
            border-radius: 100%;
        }

        .checkbox-alphanumeric label > img {
            max-width: 100%;
        }

        .checkbox-alphanumeric label:hover {
            cursor: pointer;
            border-color: {{$web_config['primary_color']}};
        }

        .checkbox-alphanumeric input:checked ~ label {
            transform: scale(1.1);
            border-color: red !important;
        }

        .checkbox-alphanumeric--style-1 label {
            width: auto;
            padding-left: 1rem;
            padding-right: 1rem;
            border-radius: 2px;
        }

        .d-table.checkbox-alphanumeric--style-1 {
            width: 100%;
        }

        .d-table.checkbox-alphanumeric--style-1 label {
            width: 100%;
        }

        /* CUSTOM COLOR INPUT */
        .checkbox-color::after,
        .checkbox-color::before {
            content: '';
            display: table;
        }

        .checkbox-color::after {
            clear: both;
        }

        .checkbox-color input {
            left: -9999px;
            position: absolute;
        }

        .checkbox-color label {
            width: 2.25rem;
            height: 2.25rem;
            float: left;
            padding: 0.375rem;
            margin-right: 0.375rem;
            display: block;
            font-size: 0.875rem;
            text-align: center;
            opacity: 0.7;
            border: 2px solid #d3d3d3;
            border-radius: 50%;
            -webkit-transition: all 0.3s ease;
            -moz-transition: all 0.3s ease;
            -o-transition: all 0.3s ease;
            -ms-transition: all 0.3s ease;
            transition: all 0.3s ease;
            transform: scale(0.95);
        }

        .checkbox-color-circle label {
            border-radius: 100%;
        }

        .checkbox-color label:hover {
            cursor: pointer;
            opacity: 1;
        }

        .checkbox-color input:checked ~ label {
            transform: scale(1.1);
            opacity: 1;
            border-color: red !important;
        }

        .checkbox-color input:checked ~ label:after {
            content: "\f121";
            font-family: "Ionicons";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .card-img-top img, figure {
            max-width: 200px;
            max-height: 200px !important;
            vertical-align: middle;
        }

        .product-card {
            box-shadow: 1px 1px 6px #00000014;
            border-radius: 5px;
        }

        .product-card .card-header {
            text-align: center;
            background: white 0% 0% no-repeat padding-box;
            border-radius: 5px 5px 0px 0px;
            border-bottom: white !important;
        }

        .product-title {
            font-family: 'Roboto', sans-serif !important;
            font-weight: 400 !important;
            font-size: 22px !important;
            color: #000000 !important;
        }

        .feature_header span {
            font-weight: 700;
            font-size: 25px;
            text-transform: uppercase;
        }

        html[dir="ltr"] .feature_header span {
            padding-right: 15px;
        }

        html[dir="rtl"] .feature_header span {
            padding-left: 15px;
        }

        @media (max-width: 768px ) {
            .feature_header {
                margin-top: 0;
                display: flex;
                justify-content: flex-start !important;

            }

            .store-contents {
                justify-content: center;
            }

            .feature_header span {
                padding-right: 0;
                padding-left: 0;
                font-weight: 700;
                font-size: 25px;
                text-transform: uppercase;
            }

            .view_border {
                margin: 16px 0px;
                border-top: 2px solid #E2F0FF !important;
            }

        }

        .scroll-bar {
            max-height: calc(100vh - 100px);
            overflow-y: auto !important;
        }

        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px white;
            border-radius: 5px;
        }

        ::-webkit-scrollbar {
            width: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(194, 194, 194, 0.38) !important;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: {{$web_config['secondary_color']}}                 !important;
        }

        .mobileshow {
            display: none;
        }

        @media screen and (max-width: 500px) {
            .mobileshow {
                display: block;
            }
        }

        [type="radio"] {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        [type="radio"] + span:after {
            content: '';
            display: inline-block;
            width: 1.1em;
            height: 1.1em;
            vertical-align: -0.10em;
            border-radius: 1em;
            border: 0.35em solid #fff;
            box-shadow: 0 0 0 0.10em{{$web_config['secondary_color']}};
            margin-left: 0.75em;
            transition: 0.5s ease all;
        }

        [type="radio"]:checked + span:after {
            background: {{$web_config['secondary_color']}};
            box-shadow: 0 0 0 0.10em{{$web_config['secondary_color']}};
        }

        [type="radio"]:focus + span::before {
            font-size: 1.2em;
            line-height: 1;
            vertical-align: -0.125em;
        }


        .checkbox-color label {
            box-shadow: 0px 3px 6px #0000000D;
            border: none;
            border-radius: 3px !important;
            max-height: 35px;
        }

        .checkbox-color input:checked ~ label {
            transform: scale(1.1);
            opacity: 1;
            border: 1px solid #ffb943 !important;
        }

        .checkbox-color input:checked ~ label:after {
            font-family: "Ionicons", serif;
            position: absolute;
            content: "\2713" !important;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .navbar-tool .navbar-tool-label {
            position: absolute;
            top: -.3125rem;
            right: -.3125rem;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            background-color: {{$web_config['secondary_color']}}         !important;
            color: #fff;
            font-size: .75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.25rem;
        }

        .btn-primary {
            color: #fff;
            background-color: {{$web_config['primary_color']}}         !important;
            border-color: {{$web_config['primary_color']}}         !important;
        }

        .btn-primary:hover {
            color: #fff;
            background-color: {{$web_config['primary_color']}}         !important;
            border-color: {{$web_config['primary_color']}}         !important;
        }

        .btn-secondary {
            background-color: {{$web_config['secondary_color']}}         !important;
            border-color: {{$web_config['secondary_color']}}         !important;
        }

        .btn-outline-accent:hover {
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-accent {
            color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .text-accent {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 18px;
            color: {{$web_config['primary_color']}};
        }

        a:hover {
            color: {{$web_config['secondary_color']}};
            text-decoration: none
        }

        .active-menu {
            color: {{$web_config['secondary_color']}}         !important;
        }

        .page-item.active > .page-link {
            box-shadow: 0 0.5rem 1.125rem -0.425rem{{$web_config['primary_color']}}











        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: rgba(0, 0, 0, 0)
        }

        .btn-outline-accent:not(:disabled):not(.disabled):active, .btn-outline-accent:not(:disabled):not(.disabled).active, .show > .btn-outline-accent.dropdown-toggle {
            color: #fff;
            background-color: {{$web_config['secondary_color']}};
            border-color: {{$web_config['secondary_color']}};
        }

        .btn-outline-primary {
            color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-primary:hover {
            color: #fff;
            background-color: {{$web_config['secondary_color']}};
            border-color: {{$web_config['secondary_color']}};
        }

        .btn-outline-primary:focus, .btn-outline-primary.focus {
            box-shadow: 0 0 0 0{{$web_config['secondary_color']}};
        }

        .btn-outline-primary.disabled, .btn-outline-primary:disabled {
            color: #6f6f6f;
            background-color: transparent
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active, .btn-outline-primary:not(:disabled):not(.disabled).active, .show > .btn-outline-primary.dropdown-toggle {
            color: #fff;
            background-color: {{$web_config['primary_color']}};
            border-color: {{$web_config['primary_color']}};
        }

        .btn-outline-primary:not(:disabled):not(.disabled):active:focus, .btn-outline-primary:not(:disabled):not(.disabled).active:focus, .show > .btn-outline-primary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0{{$web_config['primary_color']}};
        }

        .feature_header span {
            background-color: #fafafc !important
        }

        .discount-top-f {
            position: absolute;
        }

        html[dir="ltr"] .discount-top-f {
            left: 0;
        }

        html[dir="rtl"] .discount-top-f {
            right: 0;
        }

        .for-discoutn-value {
            background: {{$web_config['primary_color']}};

        }

        .czi-star-filled {
            color: #fea569 !important;
        }

        .flex-start {
            display: flex;
            justify-content: flex-start;
        }

        .flex-center {
            display: flex;
            justify-content: center;
        }

        .flex-around {
            display: flex;
            justify-content: space-around;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
        }

        .row-reverse {
            display: flex;
            flex-direction: row-reverse;
        }

        .count-value {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.25rem;
        }
    </style>

    <!--for product-->
    <style>
        .stock-out {
            position: absolute;
            top: 40% !important;
            color: white !important;
            font-weight: 900;
            font-size: 15px;
        }

        html[dir="ltr"] .stock-out {
            left: 35% !important;
        }

        html[dir="rtl"] .stock-out {
            right: 35% !important;
        }

        .product-card {
            height: 100%;
        }

        .badge-style {
            left: 75% !important;
            margin-top: -2px !important;
            background: transparent !important;
            color: black !important;
        }

        html[dir="ltr"] .badge-style {
            right: 0 !important;
        }

        html[dir="rtl"] .badge-style {
            left: 0 !important;
        }
    </style>

    <style>
        .dropdown-menu {
            min-width: 304px !important;
            border-top-left-radius: 0px;
            border-top-right-radius: 0px;
        }


        .select2-selection__rendered {
            line-height: 44px !important;
        }

        .select2-container .select2-selection--single {
            height: 44px !important;
        }

        .select2-selection__arrow {
            height: 44px !important;
        }
    </style>
</head>
<!-- Body-->
<body class="toolbar-enabled">
<!-- Sign in / sign up modal-->
{{--@include('layouts.front-end.partials._modals')--}}
{{--<!-- Navbar-->--}}
{{--<!-- Quick View Modal-->--}}
{{--@include('layouts.front-end.partials._quick-view-modal')--}}
{{--<!-- Navbar Electronics Store-->--}}
{{--@include('layouts.front-end.partials._header')--}}
<!-- Page title-->

{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-12"
             style="width:85%;position: fixed;z-index: 9999;display: flex;align-items: center;justify-content: center;">
            <div id="loading" style="display: none">
                <img width="200"
                     src="{{storage_asset('company')}}/{{\App\Services\AdditionalServices::get_business_settings('loader_gif')}}"
                     onerror="this.src='{{asset('assets/front-end/img/loader.gif')}}'">
            </div>
        </div>
    </div>
</div>
{{--loader--}}

<!-- Page Content-->
@yield('content')

<!-- Footer-->
<!-- Footer-->
{{--@include('layouts.front-end.partials._footer')--}}
<!-- Toolbar for handheld devices-->
<!--<div class="cz-handheld-toolbar" id="toolbar">
    {{--@include('layouts.front-end.partials._toolbar')--}}
</div>-->

<!-- Back To Top Button-->
<a class="btn-scroll-top" href="#top" data-scroll>
    <span class="btn-scroll-top-tooltip text-muted font-size-sm mr-2">Top</span><i
        class="btn-scroll-top-icon czi-arrow-up"> </i>
</a>

<!-- Vendor scrits: js libraries and plugins-->
{{--<script src="{{asset('assets/front-end')}}/vendor/jquery/dist/jquery.slim.min.js"></script>--}}
<script src="{{asset('assets/front-end')}}/vendor/jquery/dist/jquery-2.2.4.min.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script
    src="{{asset('assets/front-end')}}/vendor/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/simplebar/dist/simplebar.min.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/tiny-slider/dist/min/tiny-slider.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<script src="{{asset('assets/front-end')}}/vendor/drift-zoom/dist/Drift.min.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/lightgallery.js/dist/js/lightgallery.min.js"></script>
<script src="{{asset('assets/front-end')}}/vendor/lg-video.js/dist/lg-video.min.js"></script>
{{--Toastr--}}
<script src={{asset("assets/back-end/js/toastr.js")}}></script>
<!-- Main theme script-->
<script src="{{asset('assets/front-end')}}/js/theme.min.js"></script>
<script src="{{asset('assets/front-end')}}/js/slick.min.js"></script>

<script src="{{asset('assets/front-end')}}/js/sweet_alert.js"></script>
{{--Toastr--}}
<script src={{asset("assets/back-end/js/toastr.js")}}></script>
<script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{!! Toastr::message() !!}


@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

@stack('script')

</body>
</html>
