<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">

  <meta name="description" content="Entreprise Artisanale de Maçonnerie & Taille de Pierre. L'entreprise évolue dans le domaine de la restauration du bati ancien.">
  <meta name="keywords" content="Taille de pierre, Tailleur de pierre, Pierreux ,Bonneuil-en-Valois ,Crépy-en-Valois ,Maçon ,Maçonnerie ,Vallée de l'automne ,Restauration du bati ancien ,Rénovation de batiment ,Pierre dure ,Carrière ,Artisan ,savoir faire ,Tradition ,Pavés ,moëllons ,Dallage ,pavage ,paveur ,carreleur ,carrelage">
  <meta name="author" content="John Doe">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('pageTitle')</title>

  <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}"/>

  {{-- CSS --}}
  <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <link href="{{ asset('css/normalize.css') }}" rel="stylesheet">
  <link href="{{ asset('css/lightgallery.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">

  @yield('style')

  <script src='https://www.google.com/recaptcha/api.js'></script>

</head>
<body>

  <div id="app">
    @include('layouts.header')

    @yield('content')

    @include('layouts.footer')
  </div>



  {{-- Scripts --}}
  <script src="{{ asset('js/jquery-3.2.1.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/lightgallery.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/lg-hash.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/lazysizes.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/jquery.form.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/collapse.js') }}" type="text/javascript"></script>

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
