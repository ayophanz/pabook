/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import moment from 'moment'

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
     * RoomType
     */
    {path: '/room-types', component: require('./pages/room-type-page/room-types.vue').default},
    {path: '/add-room-type', component: require('./pages/room-type-page/room-type.vue').default},
    {path: '/edit-room-type/:typeId', component: require('./pages/room-type-page/room-type.vue').default},

    /**
     * Room
     */
    {path: '/rooms', component: require('./pages/room-page/rooms.vue').default},
    {path: '/add-room', component: require('./pages/room-page/room.vue').default},
    {path: '/edit-room/:roomId', component: require('./pages/room-page/room.vue').default},
    
    /**
     * Hotel
     */
    {path: '/hotels', component: require('./pages/hotel-page/hotels.vue').default},
    {path: '/add-hotel', component: require('./pages/hotel-page/hotel.vue').default},
    {path: '/edit-hotel/:hotelId', component: require('./pages/hotel-page/hotel.vue').default},

    /**
     * User
     */
    {path: '/profile', component: require('./pages/user-page/profile.vue').default},
    {path: '/users', component: require('./pages/user-page/users.vue').default},
    {path: '/add-user', component: require('./pages/user-page/user.vue').default},
    {path: '/edit-user/:userId', component: require('./pages/user-page/user.vue').default}
]

const router = new VueRouter({
    mode: 'history',
    routes
})

Vue.filter('upWord', function(text){
    return text.toLowerCase()
    .split(' ')
    .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
    .join(' ');
})

Vue.filter('formatDate', function(created){
    return moment(created).format('MMMM Do YYYY');
})

import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
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
