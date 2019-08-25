
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{csrf_token()}}">
  <title>AdminLTE 3 | Starter</title>

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

    <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <ul class="navbar-nav ml-auto">
      {{-- <li class="nav-item dropdown booking-noti">
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="true">
          <i class="fas fa-bell fa-2x"></i>
          <span class="badge badge-danger navbar-badge">15</span>
        </a>
      </li> --}}
      <li class="nav-item booking-noti">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
          <i class="fas fa-bell fa-2x"></i>
          <span class="badge badge-danger navbar-badge">15</span>
        </a>
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



  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index3.html" class="brand-link">
      <img src="{{asset('/storage/images/icon/laptop.svg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('/storage/images/icon/boy.svg')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }} <span class="caret"></span></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                Bookings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Bookings</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/add-book-entry" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle"></i>
                  <p>Add</p>
                </router-link>
              </li>
            </ul>
          </li>

          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview {{Request::is('hotels', 'add-hotel', 'edit-hotel/*')?'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-hotel"></i>
              <p>
                Hotels
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/hotels" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Hotels</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/add-hotel" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle"></i>
                  <p>Add</p>
                </router-link>
              </li>
            </ul>
          </li>
          @endif   

          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview {{Request::is('rooms', 'add-room', 'edit-room/*')?'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-bed"></i>
              <p>
                Rooms
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/rooms" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Rooms</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/add-room" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle"></i>
                  <p>Add</p>
                </router-link>
              </li>
            </ul>
          </li>
          @endif

          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview {{Request::is('room-types', 'add-room-type', 'edit-room-type/*')?'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-door-closed"></i>
              <p>
                Room Types
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/room-types" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Room Types</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/add-room-type" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle"></i>
                  <p>Add</p>
                </router-link>
              </li>
            </ul>
          </li>
          @endif

          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview {{Request::is('users', 'add-user', 'edit-user/*')?'menu-open':''}}">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users-cog"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/users" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Users</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/add-user" class="nav-link">
                    <i class="nav-icon fas fa-plus-circle"></i>
                  <p>Add</p>
                </router-link>
              </li>
            </ul>
          </li>
          @endif

          <li class="nav-item">
            <router-link to="/profile" class="nav-link">
              <i class="fas fa-user nav-icon"></i>
              <p>
                Profile
              </p>
            </router-link>
          </li> 
        </ul>
      </nav>
      <div class="user-panel mt-1 pb-1 mb-1 d-flex"> </div>
      
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
          
          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview">
            <router-link to="/settings" class="nav-link">
              <i class="fas fa-sliders-h nav-icon"></i>
              <p>Settings</p>
            </router-link>
          </li>
          @endif

        </ul>
      </nav>

    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Starter Page</h1>
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

  <aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>

@auth
  <script>
    window.user = @json(auth()->user())
  </script>
@endauth

<script type="text/javascript" src="{{asset('/js/app.js')}}"></script>
</body>
</html>
