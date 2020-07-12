/** Foundation frameworks */
import vue from 'vue';
window.Vue = vue;


/** To upperCase word */
Vue.filter('upWord', function(text){
    return text.toLowerCase()
    .split(' ')
    .map((s) => s.charAt(0).toUpperCase() + s.substring(1))
    .join(' ');
});


/** Formatting date */
Vue.filter('formatDate', function(created){
    return moment(created).format('MMMM Do YYYY');
});


/** Pop-up modal toast message */
import swal from 'sweetalert2';
window.swal = swal;

const toast = swal.mixin({
  toast: true,
  position: 'top',
  showConfirmButton: false,
  timer: 900,
  allowOutsideClick:	false,
  allowEscapeKey:	false,
  allowEnterKey: false
});
window.toast = toast;


/** Pop-up modal approve */
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
window.approve = approve;


/** Pop-up modal confirmation */
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
window.sure = sure;


/** Pop-up modal pay */
const paynow = swal.mixin({
  customClass: {
    confirmButton: 'btn btn-outline-primary btn-flat',
    cancelButton: 'btn btn-outline-danger btn-flat'
  },
  buttonsStyling: false,
})
window.paynow = paynow;


/** After create service */
const afterCreate = swal.mixin({
  title: 'Create new entry?',
  text: "Choose to create new or redirect to list",
  type: 'info',
  showCancelButton: true,
  confirmButtonText: 'Yes, go to create!',
  cancelButtonText: 'No, redirect to list ',
  focusCancel: true,
  reverseButtons: true,
  customClass: {
    confirmButton: 'btn btn-outline-primary btn-flat',
    cancelButton: 'btn btn-outline-secondary btn-flat'
  },
  buttonsStyling: false,
  allowOutsideClick:	false,
  allowEscapeKey:	false,
  allowEnterKey: false
})
window.afterCreate = afterCreate;


/** Emitter */
window.fire = new Vue();


/** Flash message */
import VueFlashMessage from 'vue-flash-message';
Vue.use(VueFlashMessage);


/** Loading feedback */
import VueElementLoading from 'vue-element-loading';
Vue.component('VueElementLoading', VueElementLoading);