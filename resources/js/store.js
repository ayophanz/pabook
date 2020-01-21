import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
    state: {
        optionalAmenStore: [],
        currencyStore: ''
    },
    getters: {
        //
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
            // state.optionalAmenStore.forEach(function(item, key){
            //     item.optAmen = data;
            // });
        }
    }
})