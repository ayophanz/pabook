<template>
    <div>
        <ul class="exclude-rooms-no">
            <li v-for="(item, key) in rooms_no_Data">
                <i class="fas fa-bed"></i> {{item.value}}
                <ul class="exclude-optional-amen">
                    <li v-for="(item2, key2) in addOnOptionalAmen_Data">
                        <pretty-check @change="onCheckOrUncheck($event, item.value)" class="p-icon p-round p-tada" color="danger-o">
                            <i slot="extra" class="icon mdi mdi-heart fas fa-times"></i>
                            {{item2.value}} | {{currency_Data}}{{item2.price}}
                        </pretty-check>
                    </li>
                </ul>
                <br />
            </li>
        </ul>
    </div>
</template>

<script>
import PrettyCheck from 'pretty-checkbox-vue/check'
export default {
    props: ['roomsNoComp', 'addOnOptionalAmenComp', 'currencyComp'],
    components :{
        PrettyCheck
    },
    data() {
        return {
            rooms_no_Data: [],
            addOnOptionalAmen_Data: [],
            currency_Data: ''
        }
    },
    methods: {
        onCheckOrUncheck(e, value) {
            if(e==true) console.log(value);
            if(e==false) console.log(value);
        },
    },
    mounted() {
        this.rooms_no_Data = Vue.util.extend([], this.roomsNoComp);
        this.addOnOptionalAmen_Data = Vue.util.extend([], this.addOnOptionalAmenComp);
        this.currency_Data = this.currencyComp;
    }
}
</script>
