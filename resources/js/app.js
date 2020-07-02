/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import vue from 'vue';
window.Vue = vue;

import {store} from './store';

import moment from 'moment';
window.moment = moment;
Vue.prototype.moment = moment;

import { Form, HasError, AlertError } from 'vform';

import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);

window.form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import routes from './routes';

const router = new VueRouter({
    mode: 'history',
    routes
})

/**
* Global components
*/
Vue.component('notifycount', require('./components/notifyCountComponent.vue').default);
Vue.component('notifybar', require('./components/notificationBarComponent.vue').default);
Vue.component('noticeMsg', require('./components/noticeMsgComponent.vue').default);
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
    cancelButton: 'btn btn-outline-secondary btn-flat'
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

import VueFlashMessage from 'vue-flash-message'
Vue.use(VueFlashMessage)

import VueElementLoading from 'vue-element-loading'
Vue.component('VueElementLoading', VueElementLoading)

const app = new Vue({
  el: '#app',
  router,
  store: store,
  methods:{
    refreshNoticeMsg() {
      Vue.prototype.$flashStorage.destroyAll();
      this.$store.commit('trigLoaderNoticeMutat', true);
      this.$store.commit('notifymsgMutat');
      this.$store.commit('trigLoaderNoticeMutat', false);
    },

    listen(name){
      if(name=='incompletebooking') {
        // let self = this
        // Echo.private('incomplete-booking').listen('incompleteBooking', (e)=> {
        //   if(e.bookId!=null) self.refreshNoticeMsg();
        // });
      }
    },

    twoFactorCheck() {
      if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
        // let timeOut;
        // axios.get('/api/check-two-factor-if-expired').then((response) => {
        //   if (response.data=='reload') {
        //       window.clearTimeout(timeOut);
        //     window.location.reload();
        //   }
        // });
        // timeOut = window.setTimeout(this.twoFactorCheck, 5000);
      }
    },

    queryIncompleteBook() {
      // if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) this.listen('incompletebooking');
    },

    cancelBooking(id) {
      sure.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel! ',
        focusCancel: true,
        reverseButtons: true
      }).then((result) => {
        if(result.value) {
          let self = this
          if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
            axios.delete('/api/cancel-booking/'+id)
            .then(function (response) {
                toast.fire({
                  type: 'success', 
                  title: 'Booking is successfully cancelled'
                })
                self.refreshNoticeMsg();
                self.$store.commit('bookingPagiMutat', 'page_1');
                self.$store.commit('summaryDetailsMutat', '');
                fire.$emit('bookingResetData');
            });
          }
        }
      });
    },

    continueBooking(id) {
      let self = this
      if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
        axios.get('/api/continue-booking/'+id)
        .then(function (response) {
          self.$store.commit('summaryDetailsMutat', response.data);
        });
      }
    }
  },

  created(){
    //
  },

  mounted() {
    this.twoFactorCheck();
    this.queryIncompleteBook();
    this.$store.commit('notifymsgMutat');

    fire.$on('refreshNoticeMsg', this.refreshNoticeMsg); 

    /**
    * jQuery
    **/ 
    let self = this
    $(document).on('click', '.trigNoticeMsg', function(e) {
      e.preventDefault();
      self.cancelBooking($(this).attr('data-id'));
    });

    $(document).on('click', '.redirectToBooking', function(e) {
      e.preventDefault();
      self.continueBooking($(this).attr('data-id'));
      self.$store.commit('bookingPagiMutat', 'page_2');
      if(self.$router.currentRoute.path!='/' && self.$router.currentRoute.path!='/add-book-entry') {
        if($('.nav-booking').parent().hasClass('menu-open')==false) {
          $('.nav-booking').click();
          self.$router.push('/add-book-entry');
        }
      }else if(self.$router.currentRoute.path=='/') {
        self.$router.push('/add-book-entry');
      }
    });

    $(document).on('click', '.nav-new-booking', function(e){
      self.$store.commit('bookingPagiMutat', 'page_1');
    });
  }
});


