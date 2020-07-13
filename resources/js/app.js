/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


 /** Foundation frameworks */
require('./bootstrap');
import vue from 'vue';
window.Vue = vue;


 /** State management */
import {store} from './store/index';


 /** Security auth */
import Gate from './gate';
Vue.prototype.$gate = new Gate(window.user);


 /** Request form */
import { Form, HasError, AlertError } from 'vform';
window.form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);


/** Date framework */
import moment from 'moment';
window.moment = moment;
Vue.prototype.moment = moment;


/** Router/url */
import VueRouter from 'vue-router';
Vue.use(VueRouter);
import routes from './routes';
const router = new VueRouter({
    mode: 'history',
    routes
})


/** Global helpers */
import './global';


/** Main vue app */
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
      if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
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
      // if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) this.listen('incompletebooking');
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
          if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
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
      if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
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


