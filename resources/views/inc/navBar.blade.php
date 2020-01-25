<aside class="main-sidebar sidebar-light-success elevation-4">
    <router-link to="/"class="brand-link navbar-teal">
      <img id="logo-with-text" src="{{asset('/storage/images/pabook-logo.png')}}" alt="pabook" class="custom-logo">
      <img id="logo-without-text" src="{{asset('/storage/images/pabook-no-text.png')}}" width="50" height="50" alt="pabook" class="custom-logo">
      <!-- <span class="brand-text font-weight-light">pabook</span> -->
    </router-link>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <default-profile class="img-circle elevation-2" alt="User Image"></default-profile>
          <!-- <img src="{{asset('/storage/images/icon/boy.svg')}}" class="img-circle elevation-2" alt="User Image"> -->
        </div>
        <div class="info align-items-center">
          <router-link to="/profile" class="d-block">{{ Auth::user()->name }} <span class="caret"></span></router-link>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link nav-booking">
              <i class="nav-icon fas fa-calendar-alt"></i>
              <p>
                Bookings
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/" class="nav-link nav-new-booking">
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

          <div class="user-panel mt-1 pb-1 mb-1 d-flex"> </div>

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
                Account
              </p>
            </router-link>
          </li>
          
          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item">
            <router-link to="/profile" class="nav-link">
              <i class="fas fa-tags nav-icon"></i>
              <p>
                Subscription
              </p>
            </router-link>
          </li> 
          @endif

        </ul>
      </nav>
      <div class="user-panel mt-1 pb-1 mb-1 d-flex"> </div>
      
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
          
          @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
          <li class="nav-item has-treeview">
            <router-link to="/integration" class="nav-link">
              <i class="fas fa-network-wired"></i>
              <p>Channel manager</p>
            </router-link>
          </li>
          <li class="nav-item has-treeview">
            <router-link to="/integration" class="nav-link">
            <i class="fas fa-plug nav-icon"></i>
              <p>Integration</p>
            </router-link>
          </li>
          @endif
          <li class="nav-item has-treeview">
            <router-link to="/settings" class="nav-link">
              <i class="fas fa-sliders-h nav-icon"></i>
              <p>Settings</p>
            </router-link>
          </li>

        </ul>
      </nav>

    </div>
  </aside>