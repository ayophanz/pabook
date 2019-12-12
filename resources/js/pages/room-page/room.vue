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
                <div class="col-md-9">
                    <div class="card">
                  <div class="card-header">
                    <div class="card-tool">
                        <router-link to="/rooms"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                    </div>
                  </div>

                    <div class="card-body">
                      <div class="form-group">
                        <label for="hotel">Hotel <span class="required-asterisk">*</span></label>
                        <select v-model="form.hotel" @change="ifChange" class="form-control" :class="{ 'is-invalid': form.errors.has('hotel') }" id="hotel">
                          <option v-for="item in hotels" :selected="item.id === form.hotel" :value="item.id">{{item.name}}</option>
                        </select>
                        <has-error :form="form" field="hotel"></has-error>
                      </div>
                      <div class="form-group">
                        <label for="type">Type <span class="required-asterisk">*</span></label>
                        <select v-if="types!=''" v-model="form.type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }" id="type">
                          <option v-for="item in types" :selected="item.id === form.type" :value="item.id">{{item.name}}</option>
                        </select>
                        <has-error :form="form" field="type"></has-error>
                        <p v-if="types==''">This hotel doesn't have any room type available, <router-link to="/add-room-type">click here to add.</router-link></p>
                      </div>    
                      <div class="form-group">
                        <label for="description">Short description </label>
                        <textarea v-model="form.description" rows="6" class="form-control" :class="{ 'is-invalid': form.errors.has('description') }" id="description"></textarea>
                        <has-error :form="form" field="description"></has-error>
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
                          <div class="custom-control custom-switch">
                            <input @click="toggleStatus" :checked="isCheckStatus" type="checkbox" class="custom-control-input" id="isChangeStatus" name="isChangeStatus">
                            <label class="custom-control-label" for="isChangeStatus">{{ form.status }}</label>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="required-asterisk">*</span></label>
                            <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="name">
                            <has-error :form="form" field="name"></has-error>
                        </div>  
                        <div class="form-group">
                            <label for="price">Price <span class="required-asterisk">*</span></label>
                            <my-currency-input :baseCurrency="base_currency" v-model="form.price"></my-currency-input>
                            <has-error :form="form" field="price"></has-error>
                        </div>
                        <div class="form-group">
                          <div class="container">
                            <div class="row">
                              <div class="col nopadding">
                                <label for="no_of_room">No. of unit <span class="required-asterisk">*</span></label>
                                <input v-model="form.no_of_room" type="number" class="form-control" :class="{ 'is-invalid': form.errors.has('no_of_room') }" id="no_of_room">
                                <has-error :form="form" field="no_of_room"></has-error>
                              </div>
                              <div class="col nopadding">
                                <label for="no_of_room_available">No. of unit available</label>
                                <p class="form-control" id="no_of_room_available">3</p>
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
    import RepeaterInputComponent from '../../components/repeaterFieldComponent'
    import ImageUploader from '../../components/ImageUploaderComponent'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        watch: {
            '$route' (to, from) {
               if(to.path === '/add-room') {
                  this.resetComponent();
               }else{
                  to.params.roomId
               }
            }
        },
        components: {
            RepeaterInputComponent,
            ImageUploader,
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
                base_currency: 'USD',
                form: new form({
                    status: 'pending',
                    type: null,
                    name: '',
                    description: '',
                    price: 1,
                    no_of_room: 1,
                    image: '',
                    hotel: 0,
                    changeFeature: '',
                    featureData: null,
                    featureOptionalData: [{value:'', price:0}],
                    gallery: []
                })
            }
        },
        methods: {
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
                              self.hotels.push(item);
                          });
                        }
                    );
                }
            },
            ifChange() {
                if(this.$gate.superAdminOrhotelOwner()) {
                  this.isLoading = true;
                    let self = this
                    axios.get('/api/room-types/'+self.form.hotel+','+self.roomId)
                    .then(
                        function (response) {
                            self.types = response.data;
                            self.isLoading = false;
                        }
                    );
                }
            },
            register() {
                if(this.$gate.superAdminOrhotelOwner()) {
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
                        }
                        
                        self.isLoading = false;
                        toast.fire({
                          type: 'success',
                          title: msg
                        })

                    })
                    .catch(function (error) {
                        console.log(error); 

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
                  let self = this;
                  axios.get('/api/edit-room/'+id)
                    .then(
                      function (response) {
                        self.form.status = response.data.status;
                        self.form.type = response.data.type_id;
                        self.form.name = response.data.name;
                        self.form.description = response.data.description;
                        self.form.price = response.data.price;
                        if(response.data.room_type.room_type_refer.base_currency!=null) {
                          self.base_currency = response.data.room_type.room_type_refer.base_currency.value;
                        }else{
                          self.base_currency = response.data.room_type.room_type_refer.global_base_currency.value;
                        }
                        self.$refs.repeaterOptionalUpdate.currency = self.base_currency;
                        self.form.no_of_room = response.data.total_room;
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
            }
        },
        created() {
            if(this.$route.params.roomId) {
              this.roomId = this.$route.params.roomId;  
              this.roomDetails(this.roomId);
              this.buttonText = 'Update';
            }else{
              //
            }
            this.loadHotels();
        }
    }
</script>
