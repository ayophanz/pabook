import RepeaterInput from '../../../components/repeaterFieldComponent';
import AutoCurrency from '../../../components/autoCurrencyComponent';
import ImageUploader from '../../../components/ImageUploaderComponent';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import Multiselect from 'vue-multiselect';
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
            this.imageUrl = null;
            this.isCheckCover = true;
            this.form.image = null;
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
        
        ifChange() {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                this.types = [];
                let self = this;
                let url = '/api/hotel-with-room-types/'+this.form.hotel;
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
                      self.populateRoomsNo(item.room_type_hotel.hotel_rooms_no, self);
                      return false;
                    }
                  });
              }
          );
        },
        
        register() {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.form.no_of_room = this.form.rooms_no.length;
                this.isLoading = true;
                fire.$emit('uploadImage');
                this.form.changeFeature = this.isCheckCover
                let self = this
                this.form.featureData.forEach(function(item, key){ if(item.value=='') self.form.featureData.splice(key, 1); });
                this.form.featureOptionalData.forEach(function(item, key){ if(item.value=='') self.form.featureOptionalData.splice(key, 1); });           
                this.form.post('/api/create-room')  .then(function (response) {
                    let msg = 'Room created successfully';
                    self.form.reset();
                    fire.$emit('reset');
                    fire.$emit('resetGallery');
                    self.imageUrl = null;
                    self.no_unit_avail = 0;

                    self.isLoading = false;
                    toast.fire({
                      type: 'success',
                      title: msg
                    }).then(function() {
                      afterCreate.fire()
                      .then((result) => {
                          if (result.value) {
                              location.reload();
                          } else {
                              self.$router.push('/rooms');
                          }
                      });
                    });

                })
                .catch(function (error) {
                    self.isLoading = false;
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
        
        populateRoomsNo(data, self){
          JSON.parse(data).forEach(function(item, key){
              if(item.assign_id=='no') self.rooms_options.push(item);
          });
        }
        
    },
    beforeCreate() {
      //
    },
    created(){
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