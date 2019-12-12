
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{csrf_token()}}">
  <title>Pabook</title>

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.default.css">
  <link rel="stylesheet" type="text/css" href="{{asset('/css/app.css')}}">

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item booking-noti">
        <notifycount></notifycount>
      </li>
      <li class="nav-item logout">
        <a class="nav-link" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
             <i class="fas fa-power-off nav-icon fa-1x"></i>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </li>
    </ul>
  </nav>
  
  <notifybar></notifybar>

  @include('./inc/navBar')

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">@{{$route.name}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Starter Page</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <router-view></router-view>

      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Pabook
    </div>
    <strong>Copyright &copy; <?php echo date('Y');?> All rights reserved, Designed & Developed by<a href="https://www.facebook.com/ayophanz" target="_blank"> Jford Ayop</a></strong>
  </footer>
</div>

@auth
  <script>
    window.user = @json(auth()->user()->load('notifications'))
  </script>
@endauth

<script type="text/javascript" src="{{asset('/js/app.js')}}"></script>
</body>
</html>
