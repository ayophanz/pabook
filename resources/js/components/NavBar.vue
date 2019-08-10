<template>
<nav class="mt-2">
  <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li :class="navOpen">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-calendar-alt"></i>
        <p>
          Bookings
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <router-link to="/bookings" class="nav-link">
            <i class="nav-icon fas fa-list"></i>
            <p>Bookings</p>
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

    @if(Gate::check('superAdmin') || Gate::check('hotelOwner'))
    <li :class="navOpen">
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
    <li :class="navOpen">
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
    <li :class="navOpen">
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
    <li :class="navOpen">
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
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
          onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
           <i class="fas fa-power-off nav-icon"></i>
        <p>  
           {{ __('Logout') }}
        </p>
      </a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
    </li>
  </ul>
</nav>
</template>
<script>
    export default {
      //this.$gate.superAdminOrhotelOwner()
        data() {
          return {
            navOpen: 'nav-item has-treeview '
          }
        },
        created() {
            console.log('Component mounted.')
        }
    }
</script>