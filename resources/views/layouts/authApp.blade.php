<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

         <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css') }}">


        <title>Fitwnata Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                    {{ $slot }}
            </div>
        </div>


        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
        <script src="{{ asset('assets/js/off-canvas.js')}}"></script>
        <script src="{{ asset('assets/js/hoverable-collapse.js')}}"></script>
        <script src="{{ asset('assets/js/template.js')}}"></script>
        <script src="{{ asset('assets/js/settings.js')}}"></script>
        <script src="{{ asset('assets/js/todolist.js')}}"></script>
    </body>
</html>
