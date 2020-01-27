import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const booking_store = new Vuex.Store({
    state: {
        optionalAmenStore: [],
        currentOptionalAmenStore: [],
        currencyStore: ''
    },
    getters: {
        whenAddRoomNoGett(state) {
            return state.currentOptionalAmenStore;
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
        }
    }
})