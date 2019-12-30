/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import vue from 'vue'
window.Vue = vue//require('vue/dist/vue.min.js');

import moment from 'moment'
window.moment = moment

import { Form, HasError, AlertError } from 'vform'

import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);

window.form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

import VueRouter from 'vue-router'
Vue.use(VueRouter)

let routes = [

    /**
     * Integration
     */
    {path: '/integration', name:'Integration', component: require('./pages/integration-page/integration.vue').default},
    
    /**
     * Setting
     */
    {path: '/settings', name:'Settings', component: require('./pages/setting-page/settings.vue').default},

    /**
     * Booking
     */
    {path: '/', name:'Bookings', component: require('./pages/booking-page/booking.vue').default},
    {path: '/add-book-entry', name:'New Reservation', component: require('./pages/booking-page/add-entry.vue').default},
    {path: '/walk-in-payment', name:'Payment', component: require('./pages/booking-page/payment-method.vue').default},

    /**
     * RoomType
     */
    {path: '/room-types', name:'Room Types', component: require('./pages/room-type-page/room-types.vue').default},
    {path: '/add-room-type', name:'New Room Type', component: require('./pages/room-type-page/room-type.vue').default},
    {path: '/edit-room-type/:typeId', name:'Edit Room Type', component: require('./pages/room-type-page/room-type.vue').default},

    /**
     * Room
     */
    {path: '/rooms', name:'Rooms', component: require('./pages/room-page/rooms.vue').default},
    {path: '/add-room', name:'New Room', component: require('./pages/room-page/room.vue').default},
    {path: '/edit-room/:roomId', name:'Edit Room', component: require('./pages/room-page/room.vue').default},
    
    /**
     * Hotel
     */
    {path: '/hotels', name:'Hotels', component: require('./pages/hotel-page/hotels.vue').default},
    {path: '/add-hotel', name:'New Hotel', component: require('./pages/hotel-page/hotel.vue').default},
    {path: '/edit-hotel/:hotelId', name:'Edit Hotel', component: require('./pages/hotel-page/hotel.vue').default},

    /**
     * User
     */
    {path: '/profile', name:'Account', component: require('./pages/user-page/profile.vue').default},
    {path: '/users', name:'Users', component: require('./pages/user-page/users.vue').default},
    {path: '/add-user', name:'New User', component: require('./pages/user-page/user.vue').default},
    {path: '/edit-user/:userId', name:'Edit User', component: require('./pages/user-page/user.vue').default},
    {path: '/users-capability', name:'User Capability', component: require('./pages/user-page/capability.vue').default},

    /**
     * 404
     */
     {path: '*', name:'Not found', component: require('./pages/404.vue').default}
]

const router = new VueRouter({
    mode: 'history',
    routes
})


/**
* Temporary Holder components
*/
Vue.component('temporaryhold', require('./components/temporaryHoldComponent.vue').default);

/**
* Global components
*/
Vue.component('notifycount', require('./components/notifyCountComponent.vue').default);
Vue.component('notifybar', require('./components/notificationBarComponent.vue').default);
Vue.component(require('./components/autoCurrencyComponent').default);

/**
* Page icon components
*/
Vue.component('not-found-page-icon', require('./page-icon/404Component.vue').default);
Vue.component('booking-page-icon', require('./page-icon/bookingComponent.vue').default);
Vue.component('traveler-page-icon', require('./page-icon/travelerComponent.vue').default);
Vue.component('payment-page-icon', require('./page-icon/paymentComponent.vue').default);
Vue.component('add-user-page-icon', require('./page-icon/addUserComponent.vue').default);
Vue.component('result-page-icon', require('./page-icon/resultComponent.vue').default);
Vue.component('settings-page-icon', require('./page-icon/settingsComponent.vue').default);
Vue.component('profile-page-icon', require('./page-icon/profileComponent.vue').default);
Vue.component('collection-page-icon', require('./page-icon/collectionComponent.vue').default);
Vue.component('create-page-icon', require('./page-icon/createComponent.vue').default);
Vue.component('design-page-icon', require('./page-icon/designComponent.vue').default);
Vue.component('default-profile', require('./page-icon/defaultProfileComponent.vue').default);
Vue.component('integrate', require('./page-icon/IntegrateComponent.vue').default);
Vue.component('verification', require('./page-icon/verificationComponent.vue').default);
Vue.component('email-icon', require('./page-icon/emailImageComponent.vue').default);

Vue.filter('upWord', function(text){
    return text.toLowerCase()
    .split(' ')
    .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
    .join(' ');
})

Vue.filter('formatDate', function(created){
    return moment(created).format('MMMM Do YYYY');
})

import swal from 'sweetalert2'
window.swal = swal

const toast = swal.mixin({
  toast: true,
  position: 'top',
  showConfirmButton: false,
  timer: 900,
  allowOutsideClick:	false,
  allowEscapeKey:	false,
  allowEnterKey: false
});
window.toast = toast

const approve = swal.mixin({
  customClass: {
    confirmButton: 'btn btn-outline-primary btn-flat',
    cancelButton: 'btn btn-outline-secondary btn-flat'
  },
  buttonsStyling: false,
  allowOutsideClick:	false,
  allowEscapeKey:	false,
  allowEnterKey: false
})
window.approve = approve

const sure = swal.mixin({
  customClass: {
    confirmButton: 'btn btn-outline-danger btn-flat',
    cancelButton: 'btn btn-outline-primary btn-flat'
  },
  buttonsStyling: false,
  allowOutsideClick:	false,
  allowEscapeKey:	false,
  allowEnterKey: false
})
window.sure = sure

const paynow = swal.mixin({
  customClass: {
    confirmButton: 'btn btn-outline-primary btn-flat',
    cancelButton: 'btn btn-outline-danger btn-flat'
  },
  buttonsStyling: false,
})
window.paynow = paynow

window.guestAction = swal
window.fire = new Vue();

const app = new Vue({
  el: '#app',
  router,
  methods:{
    twoFactorCheck() {
      if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
        var timeOut;
        axios.get('/api/check-two-factor-if-expired').then((response) => {
          if (response.data=='reload') {
            window.clearTimeout(timeOut);
            window.location.reload();
          }
        });
        timeOut = window.setTimeout(this.twoFactorCheck, 5000);
      }
    }
  },
  created(){
    this.twoFactorCheck();
  }
});


