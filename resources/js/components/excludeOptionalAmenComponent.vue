<template>
    <div>
        <ul class="exclude-rooms-no">
            <li v-for="(item, key) in rooms_no_Data">
                <i class="fas fa-bed"></i> {{item.value}}
                <ul class="exclude-optional-amen">
                    <li v-for="(item2, key2) in addOnOptionalAmen_Data">
                        <pretty-check v-model="item2.isChecked" @change="onCheckOrUncheck($event, item2.id, item.value)" class="p-icon p-round p-tada" color="success-o">
                            <i slot="extra" class="icon mdi mdi-heart fas fa-heart"></i>
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
    components: {
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
