<template>
    <div>
        <ul class="exclude-rooms-no">
            <li v-for="(item, key) in rooms_no_Data">
                <ul class="exclude-optional-amen">
                    <li v-for="(item2, key2) in addOnOptionalAmen_Data">
                        <input checked :value="item2" class="form-check-input" type="checkbox" :id="'exclude-optionalAmen-'+key+'-'+key2" >
                        <label :for="'exclude-optionalAmen-'+key+'-'+key2" class="form-check-label">{{item2.value}} | {{currency_Data}}{{item2.price}}</label>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['roomsNoComp', 'addOnOptionalAmenComp', 'currencyComp'],
    data() {
        return {
            rooms_no_Data: [],
            addOnOptionalAmen_Data: [],
            currency_Data: ''
        }
    },
    mounted() {
        this.rooms_no_Data = Vue.util.extend([], this.rooms_no);
        this.addOnOptionalAmen_Data = Vue.util.extend([], this.addOnOptionalAmen);
        this.currency_Data = this.currency;
        console.log(this.rooms_no);
    }
}
</script>