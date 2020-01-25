import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        optionalAmenStore: [],
        currentOptionalAmenStore: [],
        currencyStore: '', 
        trigloaderNoticeStore: false,
        bookingPagiStore: 'page_1'
    },
    getters: {
        whenAddRoomNoGett(state) {
            return state.currentOptionalAmenStore;
        },

        trigLoaderNoticeGett(state) {
            return state.trigloaderNoticeStore;
        },

        bookingPagiGett(state) {
            return state.bookingPagiStore;
        }
    },
    mutations: {
        addRoomNoMutat(state, data) {
            state.optionalAmenStore.push(data);
        },

        hideRoomNoMutat(state, data) {
            state.optionalAmenStore.forEach(function(item, key){ 
                if(item.room==data) item.isVisible = false;
            });
        },

        emptyOptionAmenMutat(state) {
            state.optionalAmenStore = [];
        },

        visibleOptionalAmenMutat(state, data) {
            state.currentOptionalAmenStore = data;
            state.optionalAmenStore.forEach(function(item2, key2){
                item2.optAmen = data;
            });
        },

        notifymsgMutat() {
            axios.get('/api/warning-incomplete-booking').then((response) => {
                response.data.forEach(function(item, key){
                    Vue.prototype.$flashStorage.flash('#'+(key+1)+'('+moment(item.dateStart).format('MMMM Do YYYY')+' - '+moment(item.dateEnd).format('MMMM Do YYYY')+') please complete the booking <a href="#" class="redirectToBooking" data-id="'+item.id+'">Click here</a> or <a href="#" class="trigNoticeMsg" data-id="'+item.id+'">Cancel booking</a>', 'warning', {
                        important: true
                    });
                });
            });
        },

        trigLoaderNoticeMutat(state, data) {
            state.trigloaderNoticeStore = data;
        },

        bookingPagiMutat(state, data) {
            state.bookingPagiStore = data;
        }
    }
})