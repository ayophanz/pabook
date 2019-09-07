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
      <collection-page-icon v-if="hotelId!=null"></collection-page-icon>
      <create-page-icon v-if="hotelId==null"></create-page-icon>
      <form @submit.prevent="register" role="form">
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
                    <label for="phone_number">Phone number / Tele. number <span class="required-asterisk">*</span></label>
                    <input v-model="form.phone_number" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('phone_number') }" id="phone_number" placeholder="ex. 09121232331, 754-3010">
                    <has-error :form="form" field="phone_number"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="email">Email <span class="required-asterisk">*</span></label>
                    <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="email">
                    <has-error :form="form" field="email"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="address">Address <span class="required-asterisk">*</span></label>
                    <input v-model="form.address" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('address') }" id="address" >
                    <has-error :form="form" field="address"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="address">City <span class="required-asterisk">*</span></label>
                    <input v-model="form.city" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('city') }" id="city">
                    <has-error :form="form" field="city"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="state_province">State / Province</label>
                    <input v-model="form.state_province" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('state_province') }" id="state_province" >
                    <has-error :form="form" field="state_province"></has-error>
                  </div>
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
                      <label for="name">Name <span class="required-asterisk">*</span></label>
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
                        <label class="custom-control-label" for="isChangeCover">Change cover photo</label>
                      </div>
                    </div>
                   <div class="form-group">
                      <label for="image">Cover Photo <span class="required-asterisk">*</span></label>
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
          cc
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
            currencyName: 'you are using global currency',
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
              changeCover: ''
            })
          }
        },
        methods: {
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
                    self.tempImage = response.data.image;
                    self.imageUrl = '../storage/images/upload/hotelImages/'+self.tempImage;
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

