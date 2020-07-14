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
            hotelId: null,
            fullPage: true,
            isLoading: false,
            tempImage: '',
            isCheckCover: false,
            isAdmin: false,
            imageUrl: null,
            countries: [],
            organization: [],
            currency: [],
            isVerified: false,
            verificationValue: [],
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

        getRoomType(assign_id){
            let room = (this.roomTypeName.length > 0)? this.roomTypeName.find(element => element.room_id === parseInt(assign_id)) :'error';
            return room.type+' | '+room.name;
        },

        loadRoomType(rooms) {
            this.roomTypeName = [];
            let roomIds = rooms.map(function(item){return (item.assign_id!='no')?item.assign_id:0;});
            roomIds = roomIds.filter(function(elem, index, self) {return index === self.indexOf(elem);})
            let self = this
            axios.get('/api/specific-rooms/'+roomIds)
            .then(function (response) {
                response.data.forEach(function(item, key){
                self.roomTypeName.push({'room_id':item.id, 'type':item.room_type.name, 'name':item.name});
                });
            });
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
        
        verificationData(status, hotel_name, hotel_id) {
            this.verificationValue['hotel_id'] = hotel_id;
            this.verificationValue['hotel_name'] = hotel_name;
            this.verificationValue['title'] = 'Information';
            this.verificationValue['download_action'] = '#';
            this.verificationValue['download_link'] = '#';
            this.verificationValue['download_filename'] = '#';
            this.verificationValue['visible_only'] = 'user';
            if(status=='verifying') {
                this.verificationValue['msg'] = 'We are verifying your documents it takes 2-3 days please be patient, as soon as the verification is complete we will notify you. Thank you';
                this.verificationValue['link'] = '#';
                this.verificationValue['link_title'] = '#';
                this.verificationValue['verify_token_link'] = '#';
                if(this.$gate.superAdmin()) {
                this.verificationValue['download_action'] = 'Download prof docx';
                this.verificationValue['download_link'] = this.downloadUrl;
                this.verificationValue['download_filename'] = this.hotelId+'_'+this.form.name;
                this.verificationValue['visible_only'] = 'admin';
                this.verificationValue['msg'] = 'Please take action on this request.';
                }
            }else if(status=='email_verifying') {
                this.verificationValue['msg'] = 'Congratz! we verified your docs, please confirm the email you registered on this hotel for final verification. Thank you';
                this.verificationValue['link'] = '/hotel-email-verification/';
                this.verificationValue['verify_token_link'] = '/verify-hotel-token';
                this.verificationValue['link_title'] = 'Click here to send email verification';
                if(this.$gate.superAdmin()) {
                this.verificationValue['visible_only'] = 'admin';
                this.verificationValue['msg'] = 'Waiting to confirm the verification code.';
                }
            }
        },
        
        currencyCall(){
            if(this.form.base_currency!='use_global') {
                this.currencyName = cc.code(this.form.base_currency).currency;
            }else{
                this.currencyName = 'you are using global currency';
            }
        },
        
        toggleCheck () {
            if(this.isCheckCover) {
                this.isCheckCover = false;
                this.form.errors.clear('image');
                this.form.image = this.tempImage;
                let self = this
                this.getBase64Image('../storage/images/upload/hotelImages/'+this.tempImage, function(base64image){ self.imageUrl = base64image;});
            }else{
                this.imageUrl = null;
                this.isCheckCover = true;
                this.form.image = null;
            }
        },
        
        register() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.isLoading = true;
                let self = this
                this.form.changeCover = this.isCheckCover
                this.form.put('/api/update-hotel/'+this.hotelId).then(function (response) { 
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: 'Hotel updated successfully',
                    })

                }).catch(function (error) {
                    self.isLoading = false;
                    console.log(error);
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
                    self.organization.push(item);
                    });
                }
                );
            }
        },
        
        hotelDetails(id) {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.isLoading = true;
                let self = this;
                axios.get('/api/edit-hotel/'+id)
                .then(
                    function (response) {
                        self.form.owner = response.data.user_id;
                        self.form.name = response.data.name;
                        self.form.address = response.data.address;
                        self.form.city = response.data.city;
                        self.form.state_province = response.data.state_province;
                        self.form.country = response.data.country;
                        self.form.zip_code = response.data.zip_code;
                        self.form.phone_number = response.data.phone_number;
                        self.form.email = response.data.email;
                        self.form.check_in = response.data.check_in;
                        self.form.check_out = response.data.check_out;
                        self.form.website = response.data.website;
                        self.tempImage = response.data.image;
                        self.getBase64Image('../storage/images/upload/hotelImages/'+self.tempImage, function(base64image){ self.imageUrl = base64image;});
                        self.downloadUrl='../storage/ImportantFiles/'+response.data.id+'.zip';
                        self.isVerified = response.data.status;
                        self.form.rooms_no = (response.data.hotel_rooms_no)? JSON.parse(response.data.hotel_rooms_no):[];
                        self.verificationData(self.isVerified, self.form.name, response.data.id);
                        if(response.data.base_currency!=null) {
                            self.form.base_currency = response.data.base_currency.value;
                            self.currencyName = cc.code(self.form.base_currency).currency;
                        }
                        self.isLoading = false;
                        self.loadRoomType(self.form.rooms_no);
                    }
                );  
            }
        }

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
        this.hotelId = this.$route.params.hotelId;  
        this.hotelDetails(this.hotelId);
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