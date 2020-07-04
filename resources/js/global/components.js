
/** Foundation frameworks */
import vue from 'vue';
window.Vue = vue;
 
/**
* Helper components
*/
Vue.component('notifycount', require('./../components/notifyCountComponent.vue').default);
Vue.component('notifybar', require('./../components/notificationBarComponent.vue').default);
Vue.component('noticeMsg', require('./../components/noticeMsgComponent.vue').default);
Vue.component(require('./../components/autoCurrencyComponent').default);

/**
* Page icon components
*/
Vue.component('not-found-page-icon', require('./../page-icon/404Component.vue').default);
Vue.component('booking-page-icon', require('./../page-icon/bookingComponent.vue').default);
Vue.component('traveler-page-icon', require('./../page-icon/travelerComponent.vue').default);
Vue.component('payment-page-icon', require('./../page-icon/paymentComponent.vue').default);
Vue.component('add-user-page-icon', require('./../page-icon/addUserComponent.vue').default);
Vue.component('result-page-icon', require('./../page-icon/resultComponent.vue').default);
Vue.component('settings-page-icon', require('./../page-icon/settingsComponent.vue').default);
Vue.component('profile-page-icon', require('./../page-icon/profileComponent.vue').default);
Vue.component('collection-page-icon', require('./../page-icon/collectionComponent.vue').default);
Vue.component('create-page-icon', require('./../page-icon/createComponent.vue').default);
Vue.component('design-page-icon', require('./../page-icon/designComponent.vue').default);
Vue.component('default-profile', require('./../page-icon/defaultProfileComponent.vue').default);
Vue.component('integrate', require('./../page-icon/IntegrateComponent.vue').default);
Vue.component('verification', require('./../page-icon/verificationComponent.vue').default);
Vue.component('email-icon', require('./../page-icon/emailImageComponent.vue').default);
