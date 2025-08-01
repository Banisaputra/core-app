<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>Tiny Dashboard - A Bootstrap Dashboard Template</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/feather.css') }}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/daterangepicker') }}.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/app-light.css') }}" id="lightTheme" disabled>
    <link rel="stylesheet" href="{{ asset('fedash/css/app-dark.css') }}" id="darkTheme" >
  </head>
  <body class="dark ">
    
    @yield('content')


    {{-- script --}}
    <script src="{{ asset('fedash/js/jquery.min.js') }}"></script>
    <script src="{{ asset('fedash/js/popper.min.js') }}"></script>
    <script src="{{ asset('fedash/js/moment.min.js') }}"></script>
    <script src="{{ asset('fedash/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('fedash/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('fedash/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.stickOnScroll.js') }}"></script>
    <script src="{{ asset('fedash/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('fedash/js/config.js') }}"></script>
    <script src="{{ asset('fedash/js/apps.js') }}"></script>

    @yield('page_script')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag()
      {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-56159088-1');
    </script>
  </body>
</html>
</body>
</html>