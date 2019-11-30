<template>
    <div class="container" id="root">
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
      <collection-page-icon v-if="typeId!=null"></collection-page-icon>
      <create-page-icon v-if="typeId==null"></create-page-icon>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
              <div class="card-header">
                <div class="card-tool">
                    <router-link to="/room-types"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                </div>
              </div>

              <form @submit.prevent="register" role="form">
                <div class="card-body">
                  <div class="form-group">
                    <label for="hotel">Assign To <span class="required-asterisk">*</span></label>
                    <select :disabled="typeId!=null" v-model="form.hotel" class="form-control" :class="{ 'is-invalid': form.errors.has('hotel') }" id="hotel">
                      <option v-for="item in hotels" :selected="item.id === form.hotel" :value="item.id">{{item.name}}</option>
                    </select>
                    <has-error :form="form" field="role"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="name">Name <span class="required-asterisk">*</span></label>
                    <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="name">
                    <has-error :form="form" field="name"></has-error>
                  </div>
                </div>

                <div class="card-footer">
                  <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> {{buttonText}}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
</template>
<script>
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
      watch: {
            '$route' (to, from) {
               if(to.path === '/add-room-type') {
                  this.resetComponent();
               }else{
                  to.params.typeId
               }
            }
        },
        components: {
            Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                typeId: null,
                hotels: [],
                buttonText: 'Save',
                form: new form({
                    hotel: '',
                    name: ''
                })
            }
        },
        methods: {
            resetComponent() {
                this.typeId = null;
                this.buttonText = 'Save';
                this.form.reset();
            },
            register () {
                if(this.$gate.superAdminOrhotelOwner()) {
                  this.isLoading = true;
                  let self = this
                  let action = null;
                  if(this.typeId!=null) 
                    action = this.form.put('/api/update-room-type/'+this.typeId)
                else
                    action = this.form.post('/api/create-room-type')  
                  action.then(function (response) { 
                      let msg = 'Room Type updated successfully';
                      if(self.typeId==null) {
                        self.form.name = '';
                        msg = 'Room Type created successfully';
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
            typeDetails(id) {
                if(this.$gate.superAdminOrhotelOwner()) {
                  this.isLoading = true;
                  let self = this;
                  axios.get('/api/edit-room-type/'+id)
                    .then(
                      function (response) {
                        self.form.hotel = response.data.hotel_id;
                        self.form.name = response.data.name;
                        self.isLoading = false;
                      }
                    );    
                }
            }  
        },
        created() {
            this.loadHotels();
            if(this.$route.params.typeId) {
              this.typeId = this.$route.params.typeId;
              this.typeDetails(this.typeId);
              this.buttonText = 'Update'; 
            }else{

            }
        }
    }
</script>