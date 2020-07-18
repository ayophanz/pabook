import userService from './../../../services/user';
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
export default {
    props: ['userId'],
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
                recep: null,
                assignHotel: [],
                hotelOwner: null,
                hotelId: null,
                action: null,
                type: null,
            })
        }
    },
    watch : {
        //
    },
    methods: {
        isCheck(item, event) {
            this.isLoading = true;
            const self = this;
            
            this.form.type = item['type'];
            this.form.hotelId = item['hotel_id'];
            if (event.target.checked) this.form.action = 1;  
            else this.form.action = 0;
            Promise.all([
                userService.capability(this.form)
            ]).then(() => {
                self.isLoading = false;
                toast.fire({
                    type: 'success',
                    title: 'User created successfully'
                });
            }).catch((error) => {
                self.isLoading = false;
                console.log(error);
                toast.fire({
                    type: 'error',
                    title: 'Something went wrong!'
                });
            });
        },
        selectRecep() {
            this.rePick = true;
            let self = this
            this.receps.forEach(function(item, index){
                if(self.form.recep==item.id) {
                    self.recepName = item.name;
                    self.loadHotels();
                }
            });
        },
        loadHotels() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.hotels = [];
                let self = this;
                axios.get('/api/receptionist-assign/'+this.form.recep)
                .then(
                    function (response) {
                        response.data.forEach(function(item){
                            self.hotels.push({id: item.id, name: item.name, assign: item.receptionist_assign});
                        });
                        console.log(self.hotels);
                        if(self.rePick) {
                            self.isLoading = false;
                            self.rePick = false;
                        }
                    }
                );
            }
        },
        // loadUserCap() {
        //     if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
        //         if(this.rePick) {
        //             this.isLoading = true;
        //         }
        //         this.hotelsCapa = [];
        //         let self = this;
        //         axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep)
        //         .then(
        //             function (response) {
        //                 response.data.forEach(function(item, index){
        //                     self.hotelsCapa.push({id:item.id,name:item.name});
        //                 });
        //                 self.form.assignHotel = self.hotelsCapa;
        //                 self.loadHotels();
        //             }
        //         );
        //     }
        // },
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
                        if (self.form.recep  != null) {
                            self.selectRecep();
                        } else {
                            self.isLoading = false;
                        }
                    }
                );
            }
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.form.recep = this.userId;
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