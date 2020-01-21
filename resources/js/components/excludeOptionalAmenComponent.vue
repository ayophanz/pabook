<template>
    <div>
        <ul class="exclude-rooms-no">
            <li v-for="(item, key) in optionalAmenities" v-if="item.isVisible">
                <i class="fas fa-bed"></i> {{item.room}}
                <ul class="exclude-optional-amen">
                    <li v-for="(item2, key2) in item.optAmen">
                        <pretty-check v-model="item2.isChecked" :id="'optionalAmenItem-'+key+'-'+key2" :key="item2.id" @change="onCheckOrUncheck($event, item2.id, item.room)" class="p-icon p-round p-tada" color="success-o">
                            <i slot="extra" class="icon mdi mdi-heart fas fa-heart"></i>
                            {{item2.value}} | {{currency}}{{item2.price}}
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
    components: {
        PrettyCheck
    },
    watch: {
        //
    },
    data() {
        return {
            //
        }
    },
    computed: {
        optionalAmenities() {
            console.log(this.$store.state.optionalAmenStore);
            return this.$store.state.optionalAmenStore;
        },
        currency() {
            return this.$store.state.currencyStore;
        }
    },
    methods: {
        onCheckOrUncheck(e, id, value) {
            if(e==false) this.$emit('removeOrAddRoomOnAmen', ['remove', id, value]);
            else this.$emit('removeOrAddRoomOnAmen', ['undo', id, value]);
        }
    },
    beforeCreate() {
        //
    },
    created(){
        //
    },
    beforeUpdate() {
        //
    },
    updated() {
        //
    },
    beforeMount(){
        //
    },
    beforeDestroy() {
        //
    },
    destroyed() {
        //
    },
    beforeRouteEnter(to, from, next) {
        next();
    },
    beforeRouteleave() {
        next();
    }
}
</script>
