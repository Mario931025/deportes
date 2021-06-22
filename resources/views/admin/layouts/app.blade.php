<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title><!-- =============== VENDOR STYLES ===============-->
    <!-- FONT AWESOME-->
    <link rel="stylesheet" href="{{ asset('angle/vendor/@fortawesome/fontawesome-free/css/brands.css') }}">
    <link rel="stylesheet" href="{{ asset('angle/vendor/@fortawesome/fontawesome-free/css/regular.css') }}">
    <link rel="stylesheet" href="{{ asset('angle/vendor/@fortawesome/fontawesome-free/css/solid.css') }}">
    <link rel="stylesheet" href="{{ asset('angle/vendor/@fortawesome/fontawesome-free/css/fontawesome.css') }}"><!-- SIMPLE LINE ICONS-->
    <link rel="stylesheet" href="{{ asset('angle/vendor/simple-line-icons/css/simple-line-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('angle/vendor/animate.css/animate.css') }}"><!-- WHIRL (spinners)-->
	<link rel="stylesheet" href="{{ asset('angle/vendor/whirl/dist/whirl.css') }}"><!-- =============== PAGE VENDOR STYLES ===============-->	
	@stack('vendor-styles')
	<!-- =============== BOOTSTRAP STYLES ===============-->
	<link rel="stylesheet" href="{{ asset('angle/css/bootstrap.css') }}" id="bscss"><!-- =============== APP STYLES ===============-->
	<link rel="stylesheet" href="{{ asset('angle/css/app.css') }}" id="maincss">
	<link rel="stylesheet" href="{{ asset('angle/css/theme-kravmaga.css') }}" id="maincss">
	@stack('styles')
</head>
<body>
    <div class="wrapper">
		@include('admin.navbar')
		@include('admin.sidebar')
		<section class="section-container">
			<!-- Page content-->
			@yield('content')
		</section>
		<!-- Page footer-->
		<footer class="footer-container"><span>&copy; 2020 - {{ config('app.name', 'Laravel') }}</span></footer>
    </div><!-- =============== VENDOR SCRIPTS ===============-->
    <!-- MODERNIZR-->
    <script src="{{ asset('angle/vendor/modernizr/modernizr.custom.js') }}"></script><!-- STORAGE API-->
    <script src="{{ asset('angle/vendor/js-storage/js.storage.js') }}"></script><!-- SCREENFULL-->
	<script src="{{ asset('angle/vendor/screenfull/dist/screenfull.js') }}"></script><!-- i18next-->
    <script src="{{ asset('angle/vendor/i18next/i18next.js') }}"></script>
    <script src="{{ asset('angle/vendor/i18next-xhr-backend/i18nextXHRBackend.js') }}"></script><!-- JQUERY-->
    <script src="{{ asset('angle/vendor/jquery/dist/jquery.js') }}"></script><!-- BOOTSTRAP-->
    <script src="{{ asset('angle/vendor/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('angle/vendor/bootstrap/dist/js/bootstrap.js') }}"></script><!-- PARSLEY-->
	@stack('vendor-scripts')
	<script src="{{ asset('angle/js/app.js') }}"></script>
	
	@stack('scripts')
</body>
</html>