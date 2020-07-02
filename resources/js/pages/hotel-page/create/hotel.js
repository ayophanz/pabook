import countries_list from 'country-json/src/country-by-capital-city.json'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import cc from 'currency-codes'
import VueTimepicker from 'vue2-timepicker'
import 'vue2-timepicker/dist/VueTimepicker.css'
import Multiselect from 'vue-multiselect'
import Select2 from 'v-select2-component';
import TemporaryHold from '../../../components/temporaryHoldComponent'
export default {
    watch: {
        //
    },
    components: {
        Loading,
        cc,
        VueTimepicker,
        Multiselect,
        TemporaryHold,
        Select2
    },
    data() {
        return {
        fullPage: true,
        isLoading: false,
        isCheckCover: false,
        isAdmin: false,
        imageUrl: null,
        countries: [],
        organizations: [],
        currency: [],
        currencyName: 'you are using global currency',
        downloadUrl: '',
        rooms_options: [],
        roomTypeName: [],
        form: new form({
            owner: '',
            name: '',
            address: '',
            city: '',
            state_province: '',
            country: '',
            zip_code: '',
            phone_number: '',
            email: '',
            base_currency: 'use_global',
            image: '',
            changeCover: '',
            proofFile: '',
            check_in: '',
            check_out: '',
            website: '',
            rooms_no: []
        })
        }
    },
    methods: {
        
        getBase64Image(url, callback) {
            var httpRequest = new XMLHttpRequest();
            httpRequest.onload = function() {
            var fileReader = new FileReader();
                fileReader.onloadend = function() {
                    callback(fileReader.result);
                }
                fileReader.readAsDataURL(httpRequest.response);
            };
            httpRequest.open('GET', url);
            httpRequest.responseType = 'blob';
            httpRequest.send();
        },
        
        currencyCall(){
            if(this.form.base_currency!='use_global') {
                this.currencyName = cc.code(this.form.base_currency).currency;
            }else{
                this.currencyName = 'you are using global currency';
            }
        },

        updateProofDocx(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            if(file['size'] < 5242880) {
                reader.onloadend = (file) => {
                this.form.proofFile = reader.result;
                }              
                reader.readAsDataURL(file);
            }else{
                toast.fire({
                type: 'error',
                title: 'You are uploading large file of zip, Please upload only less than 5MB file.'
                })
            }
        },
        
        register() {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this
                this.form.changeCover = this.isCheckCover       
                this.form.post('/api/create-hotel') .then(function (response) { 
                    let msg = 'Hotel updated successfully';
                    if(self.hotelId==null) {
                        msg = 'Hotel created successfully';
                        self.form.reset();
                        self.imageUrl = null;
                    }
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: msg
                    })

                }).catch(function (error) {
                    self.isLoading = false;
                    toast.fire({
                        type: 'error',
                        title: 'Something went wrong!'
                    })
                });
            }
        },

        updateCover(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            if(file['size'] < 307200) {
                reader.onloadend = (file) => {
                this.imageUrl = reader.result;
                this.form.image = reader.result;
                }
                reader.readAsDataURL(file);
            }else{
                toast.fire({
                type: 'error',
                title: 'You are uploading large file of image, Please upload only less than 300KB file.'
                })
            }
        },

        addRoomNo (newRoomNo) {
            const roomNo = {
                value: newRoomNo,
                code: newRoomNo.substring(0, 2) + Math.floor((Math.random() * 10000000)),
                status: 'ready',
                assign_id: 'no'
            }
            this.rooms_options.push(roomNo)
            this.form.rooms_no.push(roomNo)
        },
        
        populateData() {
            let self = this;
            countries_list.forEach(function(item) {
                self.countries.push({id:item.country, text:item.country});
            });
            if(this.$gate.superAdmin()) {
                this.isAdmin = true;
                axios.get('/api/hotel-owners')
                .then(
                function (response) {
                    response.data.forEach(function(item){
                    self.organizations.push(item);
                    });
                }
                );
            }
        },

    },
    beforeCreate() {
        //
    },
    created(){
        this.populateData();
        //this.currency = cc.codes();
        let self = this
        cc.codes().forEach(function(item, key){
        self.currency.push({id:item, text:item});
        });
        this.currency.unshift('use_global');
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