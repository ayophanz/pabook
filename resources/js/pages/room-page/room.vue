<template>
    <div class="" id="root">
      <loading 
        :height="128"
        :width="128"
        :transition="`fade`"
        :loader="`dots`"
        :background-color="`#fff`"
        :color="`#38d39f`"
        :active.sync="isLoading" 
        :is-full-page="fullPage">
      </loading>
      <collection-page-icon v-if="roomId!=null"></collection-page-icon>
      <create-page-icon v-if="roomId==null"></create-page-icon>
        <form @submit.prevent="register" role="form">
            <div class="row justify-content-center">
                <div class="col-md-12">
                  <notice-msg></notice-msg>
                </div>
                <div class="col-md-9">
                    <div class="card">
                  <div class="card-header">
                    <div class="card-tool">
                        <router-link to="/rooms"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Go to list</button></router-link>
                    </div>
                  </div>

                    <div class="card-body">
                      <div class="form-group">
                        <label for="hotel">Hotel <span class="required-asterisk">*</span></label>
                        <Select2 id="hotel" v-model="form.hotel" :options="hotels" :settings="{ placeholder: 'Please select hotel', containerCssClass:'form-control' }" @change="ifChange" />
                        <has-error :form="form" field="hotel"></has-error>
                      </div>
                      <div class="form-group">
                        <label for="type">Room Type <span class="required-asterisk">*</span></label>
                        <Select2 v-if="types!=''" id="type" v-model="form.type" :options="types" :settings="{ placeholder: 'Please select room type', containerCssClass:'form-control' }" @change="ifChangeType($event)" />
                        <has-error v-if="types!=''" :form="form" field="type"></has-error>
                        <p v-if="types==''">This hotel doesn't have any room type available, <router-link to="/add-room-type">click here to add.</router-link></p>
                      </div>    
                      <div class="form-group">
                        <label for="description">Short description </label>
                        <textarea v-model="form.description" rows="6" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" id="description"></textarea>
                        <has-error :form="form" field="description"></has-error>
                      </div>
                      <div class="row">
                        <div class="col-auto">
                            <div class="form-group">
                              <label for="max_adult">Max Adult <span class="required-asterisk">*</span></label>
                              <number-input v-model="form.max_adult" :class="{ 'is-invalid': form.errors.has('max_adult') }" size="small" :value="1" :min="1" :inputtable="false" inline center controls id="max_adult"></number-input>
                              <has-error :form="form" field="max_adult"></has-error>
                            </div>  
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                              <label for="max_child">Max Child <span class="required-asterisk">*</span></label>
                              <number-input v-model="form.max_child" :class="{ 'is-invalid': form.errors.has('max_child') }" size="small" :value="0" :min="0" :inputtable="false" inline center controls id="max_child"></number-input>
                              <has-error :form="form" field="max_child"></has-error>
                            </div>
                        </div>
                      </div> 
                      <div class="form-group">
                        <label for="feature">Fixed amenities </label>
                        <repeater-input :repeaterType="`single`" :dataValue="form.featureData" ref="repeaterUpdate" @dataFeature="form.featureData = $event"></repeater-input>
                      </div>
                      <div class="form-group">
                        <label for="feature_optional">Optional amenities </label>
                        <repeater-input :repeaterType="`double`" :baseCurrency="base_currency" :dataValue="form.featureOptionalData" ref="repeaterOptionalUpdate" @dataFeature="form.featureOptionalData = $event"></repeater-input>
                      </div>
                      <div class="form-group">
                        <label for="feature">Gallery </label>
                        <image-uploader ref="uploaderUpdate" @images="form.gallery = $event"></image-uploader>
                      </div> 
                    </div>
                  
                </div>
              </div>
              <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                          <label for="isChangeStatus">Status: </label>
                          <div class="custom-control custom-switch">
                            <input @click="toggleStatus" :checked="isCheckStatus" type="checkbox" class="custom-control-input" id="isChangeStatus" name="isChangeStatus">
                            <label class="custom-control-label" for="isChangeStatus">{{ form.status }}</label>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="required-asterisk">*</span></label>
                            <input v-model="form.name" type="text" placeholder="ex. double bed, single bed" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="name">
                            <has-error :form="form" field="name"></has-error>
                        </div>  
                        <div class="form-group">
                            <label for="price">Price <span class="required-asterisk">*</span></label>
                            <auto-currency :baseCurrency="base_currency" :minValue="`1`" v-model="form.price"></auto-currency>
                            <has-error :form="form" field="price"></has-error>
                        </div>
                        <div class="form-group">
                          <div class="container">
                            <div class="row">
                              <div class="col nopadding">
                                <label for="no_of_room">No. of unit </label>
                                <!-- <input v-model="form.no_of_room" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('no_of_room') }" id="no_of_room"> -->
                                <p class="form-control" id="no_of_room">{{form.rooms_no.length}}</p>
                                <has-error :form="form" field="no_of_room"></has-error>
                              </div>
                              <div class="col nopadding">
                                <label for="no_of_room_available">No. of unit available</label>
                                <p class="form-control" id="no_of_room_available">{{no_unit_avail}}</p>
                              </div>
                              <div class="col-md-12 nopadding">
                                <label for="rooms_no">Rooms no.</label>
                                <div class="container">
                                  <div class="row today-status"><div class="col-md-12 text-center"><strong>Today status</strong></div></div>
                                  <div class="row room-status-code">
                                    <div class="col-md-3">Ready</div>
                                    <div class="col-md-3">Occupied</div>
                                    <div class="col-md-3">Reserved</div>
                                    <div class="col-md-3">Cleaning</div>
                                  </div>
                                </div>
                                <multiselect @remove="roomsNoOnRemove" @select="roomsNoOnAdd" :class="{ 'is-invalid': form.errors.has('rooms_no') }" v-model="form.rooms_no" label="value" track-by="code" :options="rooms_options" :multiple="true">
                                  <template slot="tag" slot-scope="{ option, remove }"><span :class="option.status" class="multiselect__tag"><span>{{ option.value }}</span><span v-if="option.status=='ready'" :class="option.status" class="custom__remove" @click="remove(option)"><i aria-hidden="true" tabindex="1" class="multiselect__tag-icon"></i></span></span></template>
                                  <span slot="noResult">Oops! No results</span>
                                </multiselect>
                                <i>Note: you can only remove "ready" item.</i>
                                <has-error :form="form" field="rooms_no"></has-error>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group" v-if="roomId != null">
                          <div class="custom-control custom-switch">
                            <input @click="toggleCheck" :checked="isCheckCover" type="checkbox" class="custom-control-input" id="isChangeCover" name="isChangeCover">
                            <label class="custom-control-label" for="isChangeCover">Change feature image</label>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="image">Feature image <span class="required-asterisk">*</span></label>
                            </br>
                            <input v-if="isCheckCover == true || roomId == null" type="file" @change="updateImage" :class="{ 'is-invalid': form.errors.has('image') }" name="image">
                            <has-error :form="form" field="image"></has-error>
                            <div class="cover-preview">
                              <img :src="imageUrl">
                            </div>
                        </div> 
                    </div>
                    <div class="card-footer">
                      <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> {{buttonText}}</button>
                    </div>
                </div>    
              </div>
            </div>
        </form>
    </div>
</template>

<script>
    import RepeaterInput from '../../components/repeaterFieldComponent'
    import AutoCurrency from '../../components/autoCurrencyComponent'
    import ImageUploader from '../../components/ImageUploaderComponent'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    import Multiselect from 'vue-multiselect'
    import VueNumberInput from '@chenfengyuan/vue-number-input';
    import Select2 from 'v-select2-component';
    export default {
        watch: {
            '$route' (to, from) {
               if(to.path === '/add-room') this.resetComponent();
               else to.params.roomId;
               
            }
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
                buttonText: 'Save',
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
                    featureOptionalData: [{value:'', price:0}],
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
            resetComponent() {
               this.buttonText = 'Save';
               this.roomId = null;
               this.imageUrl = null;
               fire.$emit('reset'); 
               fire.$emit('resetGallery');
               this.form.reset();
            },
            getBase64Image(imgUrl, callback) {
                let img = new Image();
                img.onload = function(){
                  let canvas = document.createElement("canvas");
                  canvas.width = img.width;
                  canvas.height = img.height;
                  let ctx = canvas.getContext("2d");
                  ctx.drawImage(img, 0, 0);
                  let dataURL = canvas.toDataURL("image/png");
                      dataURL = dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
                  callback(dataURL); // the base64 string
                };
                img.setAttribute('crossOrigin', 'anonymous'); 
                img.src = imgUrl;
            },
            toggleCheck () {
                if(this.isCheckCover) {
                  this.isCheckCover = false;
                  this.form.errors.clear('image');
                  this.form.image = this.tempImage;
                  this.imageUrl = '../storage/images/upload/roomImages/gallery-'+this.roomId+'/'+this.tempImage;
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
                    let url = '/api/room-with-room-types/'+this.form.hotel+'/'+this.roomId;
                    if(this.roomId==null) url = '/api/hotel-with-room-types/'+this.form.hotel;
                    axios.get(url)
                    .then(
                        function (response) {
                             response.data.forEach(function(item, key){
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
              if(this.roomId==null) {
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
              }
            },
            register() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.form.no_of_room = this.form.rooms_no.length;
                    this.isLoading = true;
                    fire.$emit('uploadImage');
                    this.form.changeFeature = this.isCheckCover
                    let self = this
                    let action = null;
                    this.form.featureData.forEach(function(item, key){ if(item.value=='') self.form.featureData.splice(key, 1); });
                    this.form.featureOptionalData.forEach(function(item, key){ if(item.value=='') self.form.featureOptionalData.splice(key, 1); });
                    if(this.roomId!=null) 
                        action = this.form.post('/api/update-room/'+this.roomId)
                    else
                        action = this.form.post('/api/create-room')            
                    action.then(function (response) {
                        let msg = 'Room updated successfully';
                        if(self.roomId==null)  {
                            msg = 'Room created successfully';
                            self.form.reset();
                            fire.$emit('reset');
                            fire.$emit('resetGallery');
                            self.imageUrl = null;
                            self.no_unit_avail = 0;
                        }
                        self.isLoading = false;
                        toast.fire({
                          type: 'success',
                          title: msg
                        })

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
            roomDetails(id) {
                if(this.$gate.superAdminOrhotelOwner()) {
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
                        self.imageUrl = url+self.tempImage;
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
                                  self.$refs.uploaderUpdate.images.push('data:image/jpeg;base64,'+base64image);
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
          if(this.$route.params.roomId) {
              this.roomId = this.$route.params.roomId;  
              this.roomDetails(this.roomId);
              this.buttonText = 'Update';
            }else{
              //
            }
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
</script>
<style lang='scss'>
  .custom-control-input:focus:not(:checked) ~ .custom-control-label::before {
      border-color: #28a7456b;
  }

  .custom-control-input:checked ~ .custom-control-label::before {
      color: #fff;
      border-color: #28a745;
      background-color: #28a745;
  }
  
  .custom-control-input:focus ~ .custom-control-label::before {
      box-shadow: 0 0 0 0.2rem #28a7456e;
  }
</style>