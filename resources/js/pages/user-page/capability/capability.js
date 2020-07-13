import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
export default {
    components: {
        Loading
    },
    data() {
        return {
            fullPage: true,
            isLoading: false,
            rePick: false,
            isAdmin: false,
            hotelOwners: [],
            receps: [],
            hotels: [],
            hotelsCapa: [],
            recepName: 'No name',
            form: new form({
                recep: '',
                assignHotel: [],
                hotelOwner: ''
            })
        }
    },
    methods: {
        isChck(item, event) {
            if(event.target.checked) {
                this.hotels.splice(this.hotels.indexOf(item),1);
                this.form.assignHotel.push({id:item.id,name:item.name});
                this.recepCap('add');
            }else{
                // this.hotelsCapa.splice(this.hotelsCapa.indexOf(item),1);
                this.form.assignHotel.splice(this.form.assignHotel.indexOf(item),1);
                this.recepCap('remove');
            }
        },
        recepCap(action) {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) { 
                this.isLoading = true;
                let self = this;
                this.form.post('/api/recep-capability/'+action).then(function (response) {
                    self.loadUserCap();
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: 'User created successfully'
                    });
                })
                .catch(function (error) {
                    self.isLoading = false;
                    toast.fire({
                        type: 'error',
                        title: 'Something went wrong!'
                    });
                });
            }
        },
        selectRecep() {
            this.rePick = true;
            let self = this
            this.receps.forEach(function(item, index){
                if(self.form.recep==item.id) {
                    self.recepName = item.name;
                    self.loadUserCap();
                }
            });
        },
        loadHotels() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.hotels = [];
                let self = this;
                axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/0')
                .then(
                    function (response) {
                        response.data.forEach(function(item, index){
                            self.hotels.push({id:item.id,name:item.name});
                        });
                        if(self.rePick) {
                            self.isLoading = false;
                            self.rePick = false;
                        }
                    }
                );
            }
        },
        loadUserCap() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                if(this.rePick) {
                    this.isLoading = true;
                }
                this.hotelsCapa = [];
                let self = this;
                axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/1')
                .then(
                    function (response) {
                        response.data.forEach(function(item, index){
                            self.hotelsCapa.push({id:item.id,name:item.name});
                        });
                        self.form.assignHotel = self.hotelsCapa;
                        self.loadHotels();
                    }
                );
            }
        },
        ifChangehotelOwner() {
            if(this.$gate.superAdmin()) {
                this.hotels = [];
                this.hotelsCapa = [];
                this.isLoading = true;
                let self = this
                axios.get('/api/hotel-receptionist/'+this.form.hotelOwner)
                .then(
                    function (response) {
                        self.receps = response.data
                        self.isLoading = false;
                    }
                );
            }
        },
        loadOwner() {
            if(this.$gate.superAdmin()) {
                let self = this
                this.isAdmin = true;
                axios.get('/api/hotel-owners')
                .then(
                    function (response) {
                        self.hotelOwners = response.data
                    }
                );
            }else if(this.$gate.hotelOwner()) {
                this.form.hotelOwner = '0';
                let self = this
                this.isAdmin = false;
                this.isLoading = true;
                axios.get('/api/hotel-receptionist')
                .then(
                    function (response) {
                        self.receps = response.data
                        console.log(response.data);
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
        this.loadOwner();
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