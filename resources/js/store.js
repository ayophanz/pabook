import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        summaryDetailsStore: [],
        bookingPagiStore: 'page_1',
        trigloaderNoticeStore: false
    },
    getters: {
        bookingPagiGett(state) {
            return state.bookingPagiStore;
        },

        summaryDetailsGett(state) {
            return state.summaryDetailsStore;
        },

        trigLoaderNoticeGett(state) {
            return state.trigloaderNoticeStore;
        }
    },
    mutations: {
        bookingPagiMutat(state, data) {
            state.bookingPagiStore = data;
        },

        summaryDetailsMutat(state, data) {
            state.summaryDetailsStore = data;
        },

        trigLoaderNoticeMutat(state, data) {
            state.trigloaderNoticeStore = data;
        },

        notifymsgMutat() {
            axios.get('/api/warning-incomplete-booking').then((response) => {
                response.data.forEach(function(item, key){
                    Vue.prototype.$flashStorage.flash('#'+(key+1)+'('+moment(item.dateStart).format('MMMM Do YYYY')+' - '+moment(item.dateEnd).format('MMMM Do YYYY')+') please complete the booking <a href="#" class="redirectToBooking" data-id="'+item.id+'">Click here</a> or <a href="#" class="trigNoticeMsg" data-id="'+item.id+'">Cancel booking</a>', 'warning', {
                        important: true
                    });
                });
            });
        }
    }
})