<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('favicon.ico')}}">
    @yield('title')
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/uppy.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/jquery.steps.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('fedash/css/quill.snow.css') }}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/daterangepicker.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('fedash/css/app-light.css') }}" id="lightTheme" disabled>
    <link rel="stylesheet" href="{{ asset('fedash/css/app-dark.css') }}" id="darkTheme">
    {{-- page css --}}
    @yield('page_css')
  </head>
  <body class="vertical dark ">
    <div class="wrapper">
      
      {{-- navabar --}}
      <nav class="topnav navbar navbar-light" style="margin-left:0">
        <a href="{{ url('/')}}"><button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 backDashboard">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button></a>
        <ul class="nav">

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-muted pr-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="avatar avatar-sm mt-2">
                <img src="{{ asset('storage/'.session('user_image')) }}" alt="..." class="avatar-img rounded-circle">
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="{{ route('setting.profile') }}">Profile</a>
              <hr>
              <a class="dropdown-item" href="{{ url('/logout')}}">Logout</a>
            </div>
          </li>
        </ul>
      </nav>
      
      <main role="main" class="main-content" style="margin-left:0">
       
        @yield('content')
    
      </main> <!-- main -->
    </div> <!-- .wrapper -->
    
    <script src="{{ asset('fedash/js/jquery.min.js') }}"></script>
    <script src="{{ asset('fedash/js/popper.min.js') }}"></script>
    <script src="{{ asset('fedash/js/moment.min.js') }}"></script>
    <script src="{{ asset('fedash/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('fedash/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('fedash/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.stickOnScroll.js') }}"></script>
    <script src="{{ asset('fedash/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('fedash/js/config.js') }}"></script>
    <script src="{{ asset('fedash/js/d3.min.js') }}"></script>
    <script src="{{ asset('fedash/js/topojson.min.js') }}"></script>
    <script src="{{ asset('fedash/js/datamaps.all.min.js') }}"></script>
    <script src="{{ asset('fedash/js/datamaps-zoomto.js') }}"></script>
    <script src="{{ asset('fedash/js/datamaps.custom.js') }}"></script>
    <script src="{{ asset('fedash/js/Chart.min.js') }}"></script>
    <script>
      /* defind global options */
      Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
      Chart.defaults.global.defaultFontColor = colors.mutedColor;
    </script>
    <script src="{{ asset('fedash/js/gauge.min.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('fedash/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('fedash/js/apexcharts.custom.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('fedash/js/select2.min.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('fedash/js/jquery.timepicker.js') }}"></script>
    <script src="{{ asset('fedash/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('fedash/js/uppy.min.js') }}"></script>
    <script src="{{ asset('fedash/js/quill.min.js') }}"></script>
    
    {{-- page script --}}
    @yield('page_script')
    
    <script src="{{ asset('fedash/js/apps.js') }}"></script>
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