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
      <verification v-if="(isVerified=='verifying' || isVerified=='email_verifying') && hotelId!=null"></verification>
      <temporary-hold v-if="(isVerified=='verifying' || isVerified=='email_verifying') && hotelId!=null" v-bind:dataValue="verificationValue"></temporary-hold>
      <collection-page-icon v-if="hotelId!=null && isVerified=='verified'"></collection-page-icon>
      <create-page-icon v-if="hotelId==null"></create-page-icon>
      <form v-if="isVerified=='verified' || hotelId==null"  @submit.prevent="register" role="form" enctype="multipart/form-data">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
              <div class="card-header">
                <div class="card-tool">
                    <router-link to="/hotels"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                </div>
              </div>

                <div class="card-body">
                  <div class="form-group">
                    <label for="country">Country <span class="required-asterisk">*</span></label>
                     <select v-model="form.country" class="form-control" :class="{ 'is-invalid': form.errors.has('country') }" id="country">
                      <option v-for="item in countries" :selected="item === form.country" :value="item">{{item}}</option>
                     </select>
                    <has-error :form="form" field="country"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="zip_code">Zip code</label>
                    <input v-model="form.zip_code" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('zip_code') }" id="zip_code">
                    <has-error :form="form" field="zip_code"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="state_province">State / Province</label>
                    <input v-model="form.state_province" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('state_province') }" id="state_province" >
                    <has-error :form="form" field="state_province"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="address">City <span class="required-asterisk">*</span></label>
                    <input v-model="form.city" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('city') }" id="city">
                    <has-error :form="form" field="city"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="address">Street Address <span class="required-asterisk">*</span></label>
                    <input v-model="form.address" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('address') }" id="address" >
                    <has-error :form="form" field="address"></has-error>
                  </div>
                  <div class="row">
                    <div class="col-auto">
                        <div class="form-group">
                          <label for="check_in">Check-in <span class="required-asterisk">*</span></label>
                          <vue-timepicker v-model="form.check_in" format="hh:mm A" :class="{ 'is-invalid': form.errors.has('check_in') }" id="check_in"></vue-timepicker>
                          <has-error :form="form" field="check_in"></has-error>
                        </div>  
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                          <label for="check_out">Check-out <span class="required-asterisk">*</span></label>
                          <vue-timepicker v-model="form.check_out" format="hh:mm A" :class="{ 'is-invalid': form.errors.has('check_in') }" id="check_in"></vue-timepicker>
                          <has-error :form="form" field="check_out"></has-error>
                        </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="email">Email <span class="required-asterisk">*</span></label>
                    <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="email">
                    <has-error :form="form" field="email"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="phone_number">Phone number / Tele. number <span class="required-asterisk">*</span></label>
                    <input v-model="form.phone_number" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('phone_number') }" id="phone_number" placeholder="ex. 09121232331, 754-3010">
                    <has-error :form="form" field="phone_number"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="website">Official website</label>
                    <input v-model="form.website" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('website') }" id="website" placeholder="ex. https://www.site.com">
                    <has-error :form="form" field="website"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="rooms_no">Rooms no. <span class="required-asterisk">*</span></label>
                      <div class="row room-status-code nopadding">
                        <div class="col-md-3">Ready</div>
                        <div class="col-md-3">Occupied</div>
                        <div class="col-md-3">Reserved</div>
                        <div class="col-md-3">Cleaning</div>
                      </div>
                    <multiselect :class="{ 'is-invalid': form.errors.has('rooms_no') }" v-model="form.rooms_no"  placeholder="ex. 101, 102" tag-placeholder="Add this as new room no." label="value" track-by="code" :options="rooms_options" :multiple="true" :taggable="true" @tag="addRoomNo"></multiselect>
                    <i>Note: You can only remove the unassign item.</i>
                    <has-error :form="form" field="rooms_no"></has-error>
                  </div>
                  <div v-if="hotelId==null" class="form-group">
                      <label for="proofFile">Please provide any proof of document that you own the hotel and it is legal <span class="required-asterisk">* ( Note: zip your file )</span></label>
                      </br>
                      <input type="file" @change="updateProofDocx" :class="{ 'is-invalid': form.errors.has('proofFile') }" name="proofFile">
                      <has-error :form="form" field="proofFile"></has-error>
                  </div> 
                </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="form-group" v-if="isAdmin">
                      <label for="owner">Hotel owner <span class="required-asterisk">*</span></label>
                       <select v-model="form.owner" class="form-control" :class="{ 'is-invalid': form.errors.has('owner') }" id="owner">
                        <option v-for="item in owners" :selected="item === form.owner" :value="item.id">{{item.email}}</option>
                       </select>
                      <has-error :form="form" field="owner"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="name">Hotel name <span class="required-asterisk">*</span></label>
                      <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="name">
                      <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                      <label for="base_currency">Base currency <span class="required-asterisk">*</span></label>
                       <select v-model="form.base_currency" @change="currencyCall" class="form-control" :class="{ 'is-invalid': form.errors.has('base_currency') }" id="base_currency">
                        <option v-for="item in currency" :selected="item.id === form.base_currency" :value="item">{{item}}</option>
                       </select>
                       <p>{{currencyName}} <router-link v-if="form.base_currency=='use_global'" to="/settings">settings</router-link></p>
                      <has-error :form="form" field="base_currency"></has-error>
                    </div>
                    <div class="form-group" v-if="hotelId != null">
                      <div class="custom-control custom-switch">
                        <input @click="toggleCheck" :checked="isCheckCover" type="checkbox" class="custom-control-input" id="isChangeCover" name="isChangeCover">
                        <label class="custom-control-label" for="isChangeCover">Change logo</label>
                      </div>
                    </div>
                   <div class="form-group">
                      <label for="image">Hotel Logo <span class="required-asterisk">*</span></label>
                      </br>
                      <input v-if="isCheckCover == true || hotelId == null" type="file" @change="updateCover" :class="{ 'is-invalid': form.errors.has('image') }" name="image">
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
    import countries_list from 'country-json/src/country-by-capital-city.json'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    import cc from 'currency-codes'
    import VueTimepicker from 'vue2-timepicker'
    import 'vue2-timepicker/dist/VueTimepicker.css'
    import Multiselect from 'vue-multiselect'
    export default {
        watch: {
            '$route' (to, from) {
               if(to.path === '/add-hotel') {
                  this.resetComponent();
               }else{
                  to.params.typeId
               }
            }
        },
        components: {
          Loading,
          cc,
          VueTimepicker,
          Multiselect
        },
        data() {
          return {
            fullPage: true,
            isLoading: false,
            tempImage: '',
            isCheckCover: false,
            isAdmin: false,
            imageUrl: null,
            hotelId: null,
            buttonText: 'Save',
            countries: [],
            owners: [],
            currency: [],
            isVerified: false,
            verificationValue: [],
            currencyName: 'you are using global currency',
            downloadUrl: '',
            rooms_options: [],
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
            if(status=='verifying') {
              if(this.$gate.superAdmin()) {
                this.verificationValue['download_action'] = 'Download prof docx';
                this.verificationValue['download_link'] = this.downloadUrl;
                this.verificationValue['download_filename'] = this.hotelId+'_'+this.form.name;
              }
              this.verificationValue['msg'] = 'We are verifying your documents it takes 2-3 days please be patient, as soon as the verification is complete we will notify you. Thank you';
              this.verificationValue['link'] = '#';
              this.verificationValue['link_title'] = '#';
              this.verificationValue['verify_token_link'] = '#';
            }else if(status=='email_verifying') {
              this.verificationValue['msg'] = 'Congratz! we verified your docs, please confirm the email you registered on this hotel for final verification. Thank you';
              this.verificationValue['link'] = '/hotel-email-verification/';
              this.verificationValue['verify_token_link'] = '/verify-hotel-token';
              this.verificationValue['link_title'] = 'Click here to send email verification';
            }
          },
          currencyCall(){
            if(this.form.base_currency!='use_global') {
              this.currencyName = cc.code(this.form.base_currency).currency;
            }else{
              this.currencyName = 'you are using global currency';
            }
          },
          resetComponent() {
             this.buttonText = 'Save';
             this.hotelId = null;
             this.imageUrl = null;
             this.form.reset();
          },
          toggleCheck () {
            if(this.isCheckCover) {
              this.isCheckCover = false;
              this.form.errors.clear('image');
              this.form.image = this.tempImage;
              this.imageUrl = '../storage/images/upload/hotelImages/'+this.tempImage;
            }else{
              this.imageUrl = null;
              this.isCheckCover = true;
              this.form.image = null;
            }
          },
          register() {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this
                this.form.changeCover = this.isCheckCover
                let action = null;
                if(this.hotelId!=null) 
                    action = this.form.put('/api/update-hotel/'+this.hotelId);
                else
                    action = this.form.post('/api/create-hotel')            
                action.then(function (response) { 
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
          updateCover(e) {
            let file = e.target.files[0];
            let reader = new FileReader();
            if(file['size'] < 307200) {
              reader.onloadend = (file) => {
                this.imageUrl = reader.result;
                this.form.image = reader.result;
                console.log(reader.result);
              }
              console.log(file);
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
                console.log(reader.result);
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
              self.countries.push(item.country);
            });
            if(this.$gate.superAdmin()) {
              this.isAdmin = true;
              axios.get('/api/hotel-owners')
              .then(
                function (response) {
                  response.data.forEach(function(item){
                    self.owners.push(item);
                  });
                }
              );
            }
          },
          hotelDetails(id) {
            if(this.$gate.superAdminOrhotelOwner()) {
              this.isLoading = true;
              let self = this;
              axios.get('/api/edit-hotel/'+id)
                .then(
                  function (response) {
                    self.form.owner = response.data.owner_id;
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
                    self.imageUrl = '../storage/images/upload/hotelImages/'+self.tempImage;
                    self.downloadUrl='../storage/ImportantFiles/'+response.data.id+'.zip';
                    self.isVerified = response.data.status;
                    self.form.rooms_no = (response.data.hotel_rooms_no)? JSON.parse(response.data.hotel_rooms_no):[];
                    self.verificationData(self.isVerified, self.form.name, response.data.id);
                    if(response.data.base_currency!=null) {
                      self.form.base_currency = response.data.base_currency.value;
                      self.currencyName = cc.code(self.form.base_currency).currency;
                    }
                    self.isLoading = false;
                  }
                );  
            }
          }
        },
        created() {
          this.populateData();
          this.currency = cc.codes();
          this.currency.unshift('use_global');
          if(this.$route.params.hotelId) {
              this.hotelId = this.$route.params.hotelId;  
              this.hotelDetails(this.hotelId);
              this.buttonText = 'Update'; 
          }else{
              // do something if add
          }
        }
    }
</script>

