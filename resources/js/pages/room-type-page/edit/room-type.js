import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import Select2 from 'v-select2-component';
export default {
    watch: {
        //
    },
    components: {
        Select2,
        Loading
    },
    data() {
        return {
            fullPage: true,
            isLoading: false,
            typeId: null,
            hotels: [],
            form: new form({
                hotel: '',
                name: ''
            })
        }
    },
    methods: {
        register () {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this
                this.form.put('/api/update-room-type/'+this.typeId).then(function (response) { 
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: 'Room Type updated successfully',
                    })
                })
                .catch(function (error) {
                    self.isLoading = false;
                    console.log(error);
                    toast.fire({
                        type: 'error',
                        title: 'Something went wrong!'
                    })
                });
            }
        },
        loadHotels() {
            if(this.$gate.superAdminOrhotelOwner()) {
                let self = this
                axios.get('/api/hotels')
                .then(
                    function (response) {
                        response.data.forEach(item => {
                        if(item.status=='verified')
                            self.hotels.push({id:item.id, text:item.name});
                        });
                    }
                );
            }
        },
        typeDetails(id) {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this;
                axios.get('/api/edit-room-type/'+id)
                .then(
                    function (response) {
                    self.form.hotel = response.data.hotel_id;
                    self.form.name = response.data.name;
                    self.isLoading = false;
                    }
                );    
            }
        }  
    },
    beforeCreate() {
        //
    },
    created(){
        this.loadHotels();
        this.typeId = this.$route.params.typeId;
        this.typeDetails(this.typeId);
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