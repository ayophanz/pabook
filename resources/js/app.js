/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//window.Vue = require('vue');
window.Vue = require('vue/dist/vue.min.js');

import moment from 'moment'

window.moment = moment

import { Form, HasError, AlertError } from 'vform'

import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);

window.form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

// import VueRouter from 'vue-router'
// Vue.use(VueRouter)

// import routes from './routes'

// const router = new VueRouter({
//     mode: 'history',
//     routes
// })

import VueRouter from 'vue-router'
Vue.use(VueRouter)

let routes = [
    
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
    {path: '/profile', name:'Profile', component: require('./pages/user-page/profile.vue').default},
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
* Global components
*/
Vue.component('notifycount', require('./components/notifyCountComponent.vue').default);
Vue.component('notifybar', require('./components/notificationBarComponent.vue').default);

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
  timer: 3000
});
window.toast = toast

const sure = swal.mixin({
  customClass: {
    confirmButton: 'btn btn-outline-danger btn-flat',
    cancelButton: 'btn btn-outline-primary btn-flat'
  },
  buttonsStyling: false,
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


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router
});
