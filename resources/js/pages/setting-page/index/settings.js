import cc from 'currency-codes'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
export default {
    components: {
        cc,
        Loading
    },
    data() {
        return {
            isAdmin: false,
            fullPage: true,
            isLoading: false,
            currencyName: '',
            currency: [],
            owners: [],
            form: new form({
                base_currency: '',
                user_id: null
            })
        }
    },
    methods: {
        selectedOwner(){
            if(this.$gate.superAdmin()) {
                this.isLoading = true;
                let self = this
                axios.get('/api/config/'+this.form.user_id)
                .then(
                    function (response) {
                        self.form.base_currency  = response.data.value;
                        self.isLoading = false;
                    }
                );
            }
        },
        currencyCall() {
            this.currencyName = cc.code(this.form.base_currency).currency;
        },
        hotelOwner() {
            if(this.$gate.superAdmin()) {
                this.isAdmin = true;
                this.isLoading = true;
                let self = this
                axios.get('/api/hotel-owners')
                .then(
                    function (response) {
                        self.owners  = response.data
                        self.isLoading = false;
                    }
                );
            }else if(this.$gate.hotelOwner()) {
                this.isLoading = true;
                let self = this
                axios.get('/api/config')
                .then(
                    function (response) {
                        self.form.base_currency  = response.data.value;
                        self.isLoading = false;
                    }
                );
            }
        },
        register() {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this
                this.form.post('/api/create-config')
                .then(
                    function (response) {
                        self.isLoading = false;
                        toast.fire({
                            type: 'success',
                            title: 'Settings successfully save'
                        })
                    }
                ).
                catch(function(e){
                    console.log(e); 

                    self.isLoading = false;
                    toast.fire({
                        type: 'error',
                        title: 'Something went wrong!'
                    })

                });
            }
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.currency = cc.codes();
        this.hotelOwner();
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