<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('pageTitle')</title>

  <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}"/>

  <!-- Styles -->
  <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>

  <div id="app">
    @include('admin.layouts.header')

    @yield('content')
  </div>

  <!-- Scripts -->
  <script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>

  @yield('script')

  <script>
    // dropdown menu on hover
    $('li.autodropdown').hover(function() {
      $(this).find('.dropdown-menu').stop(true, true).fadeIn(200);
    }, function() {
      $(this).find('.dropdown-menu').stop(true, true).fadeOut(200);
    });

  </script>

</body>
</html>
