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
    <style>
      /* Style untuk loading indicator */
      .loading-indicator {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 20px;
        border-radius: 5px;
        z-index: 9999;
      }

      .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
        margin: 0 auto;
      }

      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .overlay {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
      }
    </style>
  </head>
  <body class="vertical dark ">
    <div class="wrapper">
      
      {{-- navabar --}}
      @include('layouts.navbar')
      
      {{-- sidebar menu --}}
      @include('layouts.sidebar')

      <main role="main" class="main-content">
       
        @yield('content')
    
      </main> <!-- main -->
    </div> <!-- .wrapper -->

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="overlay"></div>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="loading-indicator">
        <div class="loading-spinner"></div>
        <p>Memproses data...</p>
    </div>
   
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
    <script>
      $(document).ready( function () {
        $('#loadingOverlay').hide();
      })
     
    </script>
  
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