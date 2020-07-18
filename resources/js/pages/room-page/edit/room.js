import RepeaterInput from '../../../components/repeaterFieldComponent'
import AutoCurrency from '../../../components/autoCurrencyComponent'
import ImageUploader from '../../../components/ImageUploaderComponent'
import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
import Multiselect from 'vue-multiselect'
import VueNumberInput from '@chenfengyuan/vue-number-input';
import Select2 from 'v-select2-component';
export default {
    watch: {
        //
    },
    components: {
        RepeaterInput,
        AutoCurrency,
        ImageUploader,
        Multiselect,
        Select2,
        VueNumberInput,
        Loading
    },
    data() {
        return {
            fullPage: true,
            isLoading: false,
            roomId: null,
            tempImage: '',
            tempData: [],
            isCheckCover: false,
            isCheckStatus: false,
            hotels: [],
            types: [],
            imageUrl: null,
            base_currency: 'none',
            rooms_options: [],
            no_unit_avail: 0,
            form: new form({
                status: 'pending',
                type: null,
                name: '',
                description: '',
                price: 1,
                no_of_room: 0,
                image: '',
                hotel: 0,
                changeFeature: '',
                featureData: null,
                featureOptionalData: [{value:null, price:0}],
                gallery: [],
                rooms_no: [],
                max_child: 0,
                max_adult: 1
            })
        }
    },
    methods: {

        deleteRoom () {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                sure.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel! ',
                    focusCancel: true,
                    reverseButtons: true
                }).then((result) => {
                    if(result.value) {
                        this.isLoading = true;
                        let self = this
                        axios.delete('/api/delete-room/'+this.roomId)
                        .then(function () {
                            self.isLoading = false;
                            toast.fire({
                                type: 'success',
                                title: 'User deleted successfully'
                            })
                            self.$router.push('/rooms');
                        });
                    }
                })
            }
        },
        
        roomsNoOnAdd(value) {
            if(value.status=='ready')
            this.no_unit_avail++;
            
        },
        
        roomsNoOnRemove(value) {
            if(value.status=='ready')
            this.no_unit_avail--;
        },

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
        
        toggleCheck () {
            if(this.isCheckCover) {
                this.isCheckCover = false;
                this.form.errors.clear('image');
                this.form.image = this.tempImage;
                let self = this
                this.getBase64Image('../storage/images/upload/roomImages/gallery-'+this.roomId+'/'+this.tempImage, function(base64image){ self.imageUrl = base64image;});
            }else{
                this.imageUrl = null;
                this.isCheckCover = true;
                this.form.image = null;
            }
        },
        
        toggleStatus() {
            if(this.isCheckStatus) {
            this.isCheckStatus = false;
            this.form.status = 'pending';
            }else{
            this.isCheckStatus = true;
            this.form.status = 'active';
            }
        },
        
        loadHotels() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
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
        
        ifChange() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.isLoading = true;
                this.types = [];
                let self = this;
                let url = '/api/room-with-room-types/'+this.form.hotel+'/'+this.roomId;
                axios.get(url)
                .then(
                    function (response) {
                            response.data.forEach(function(item){
                            self.types.push({id:item.id, text:item.name});
                            self.base_currency = item.room_type_hotel.base_currency.value;
                            self.$refs.repeaterOptionalUpdate.currency = self.base_currency;
                            });
                        self.isLoading = false;
                    }
                );
            }
        },
        
        ifChangeType(e){
            this.rooms_options = [];
            this.form.rooms_no = [];
            let self = this;
            axios.get('/api/room-types')
            .then(
                function (response) {
                    response.data.forEach(function(item, key){
                        if(item.id==e) {
                        self.populateRoomsNo(item.room_type_hotel.hotel_rooms_no, self, self.roomId);
                        return false;
                        }
                    });
                }
            );
        },
        
        register() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.form.no_of_room = this.form.rooms_no.length;
                this.isLoading = true;
                fire.$emit('uploadImage');
                this.form.changeFeature = this.isCheckCover
                let self = this
                this.form.featureData.forEach(function(item, key){ if(item.value=='') self.form.featureData.splice(key, 1); });
                this.form.featureOptionalData.forEach(function(item, key){ if(item.value=='') self.form.featureOptionalData.splice(key, 1); });          
                this.form.post('/api/update-room/'+this.roomId).then(function (response) {
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: 'Room updated successfully',
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
        
        updateImage(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            if(file['size'] < 300000) {
                reader.onloadend = (file) => {
                this.imageUrl = reader.result;
                this.form.image = reader.result;
                }
                reader.readAsDataURL(file);
            }else{
                toast.fire({
                type: 'error',
                title: 'You are uploading large file, Please upload only less than 300kb file.'
                })
            }
        },
        
        roomDetails(id) {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                let self = this
                axios.get('/api/edit-room/'+id)
                .then(
                    function (response) {
                    self.form.status = response.data.status;
                    self.form.type = response.data.room_type_id;
                    self.form.name = response.data.name;
                    self.form.description = response.data.description;
                    self.form.price = response.data.price;
                    self.form.max_adult = parseInt(response.data.max_adult);
                    self.form.max_child = parseInt(response.data.max_child);
                    if(response.data.room_type.room_type_hotel.base_currency!=null) {
                        self.base_currency = response.data.room_type.room_type_hotel.base_currency.value;
                    }else{
                        self.base_currency = response.data.room_type.room_type_hotel.global_base_currency.value;
                    }
                    self.$refs.repeaterOptionalUpdate.currency = self.base_currency;
                    //self.form.no_of_room = response.data.total_room;
                    self.tempImage = response.data.image;
                    let url = '../storage/images/upload/roomImages/gallery-'+id+'/';
                    //self.imageUrl = url+self.tempImage;
                    self.getBase64Image('../storage/images/upload/roomImages/gallery-'+id+'/'+self.tempImage, function(base64image){ self.imageUrl = base64image;});
                    self.form.hotel = response.data.room_type.hotel_id;
                    try{
                        self.form.featureData = JSON.parse(response.data.room_feature.value);
                        self.$refs.repeaterUpdate.fields = self.form.featureData;
                    }catch(err) {}
                    try{
                        self.form.featureOptionalData = JSON.parse(response.data.room_feature_optional.value);
                        self.$refs.repeaterOptionalUpdate.fields = self.form.featureOptionalData;
                    }catch(err) {}
                    self.populateRoomsNo(response.data.room_type.room_type_hotel.hotel_rooms_no, self, self.roomId);
                    try{
                        let images = JSON.parse(response.data.room_gallery.value);
                        images.forEach(item => {
                            self.getBase64Image(url+item[1]['filename'], function(base64image){
                                self.$refs.uploaderUpdate.images.push(base64image);
                            });
                            self.$refs.uploaderUpdate.files.push({
                                'name':item[1]['filename'],
                                'size':item[0]['filesize']
                            });
                        });
                    }catch(err) {}
                    self.ifChange();
                    if(self.form.status=='active')
                        self.isCheckStatus = true;
                    else
                        self.isCheckStatus = false;
                    }
                ); 
                
            }
        },
        
        populateRoomsNo(data, self, room_id){
            JSON.parse(data).forEach(function(item, key){
                if(item.status=='ready' && item.assign_id==room_id) self.no_unit_avail++; 
                if(item.assign_id=='no') self.rooms_options.push(item);
                if(item.assign_id==room_id) self.form.rooms_no.push(item);
            });
        }
        
    },
    beforeCreate() {
        //
    },
    created(){
        this.roomId = this.$route.params.roomId;  
        this.roomDetails(this.roomId);
        this.loadHotels();
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