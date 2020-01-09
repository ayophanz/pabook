<template>
    <div>
        <ul class="exclude-rooms-no">
            <li v-for="(item, key) in rooms_no_Data">
                <i class="fas fa-bed"></i> {{item.value}}
                <ul class="exclude-optional-amen">
                    <li v-for="(item2, key2) in addOnOptionalAmen_Data">
                        <pretty-check :value="item2" class="p-icon p-round p-tada" color="danger-o">
                            <i slot="extra" class="icon mdi mdi-heart fas fa-heart"></i>
                            {{item2.value}} | {{currency_Data}}{{item2.price}}
                        </pretty-check>
                    </li>
                </ul>
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
    mounted() {
        this.rooms_no_Data = Vue.util.extend([], this.roomsNoComp);
        this.addOnOptionalAmen_Data = Vue.util.extend([], this.addOnOptionalAmenComp);
        this.currency_Data = this.currencyComp;
    }
}
</script>
<style lang='scss'>
    ul.exclude-rooms-no {
        text-align: left;
        list-style: none;
    }

    .vodal-dialog {
        padding: 1.25em !important;
    }

    .vodal-mask {
        background-color: rgba(0,0,0,.4) !important;
    }
</style>