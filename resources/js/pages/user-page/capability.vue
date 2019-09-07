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
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
              <div class="card-header">
                <div class="card-tool">
                    <router-link to="/users"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                </div>
              </div>

                <div class="card-body">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6" v-if="isAdmin">
                                <div class="form-group">
                                    <label for="hotelOwner">Hotel Owner <span class="required-asterisk">*</span></label>
                                    <select v-model="form.hotelOwner" @change="ifChangehotelOwner" class="form-control" :class="{ 'is-invalid': form.errors.has('hotelOwner') }" id="hotelOwner">
                                      <option v-for="item in hotelOwners" :selected="item.id === form.hotelOwner" :value="item.id">{{item.email}}</option>
                                    </select>
                                    <has-error :form="form" field="hotelOwner"></has-error>
                                </div> 
                            </div>
                            <div :class="{'col-md-12':  isAdmin==false, 'col-md-6':  isAdmin==true}">
                                <div class="form-group">
                                    <label for="recep">Receptionist <span class="required-asterisk">*</span></label>
                                    <select @change="selectRecep" v-model="form.recep" class="form-control" :class="{ 'is-invalid': form.errors.has('recep') }" id="recep">
                                      <option v-for="item in receps" :selected="item.id === form.recep" :value="item.id">{{item.email}}</option>
                                    </select>
                                    <has-error :form="form" field="recep"></has-error>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="card set-h-500">
                                    <div class="card-header">
                                        <h6><strong>{{recepName}}</strong> capabilities</h6>
                                    </div>
                                    <div class="card-body">

                                         <!--Assign capability-->
                                        <div class="row text-left">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Assign Capabilities</label>
                                                    <div v-for="item in hotelsCapa" class="custom-control custom-switch"><input :checked="true" @click="isChck(item,$event)" :value="item.id" type="checkbox" :id="'user-capa-'+item.id" class="custom-control-input"> <label :for="'user-capa-'+item.id" class="custom-control-label">{{item.name}}</label></div>
                                                </div>
                                            </div>
                                        </div>
                                    <hr>
                                        <!--Unassign capability-->
                                        <div class="row text-left">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Unassign Capabilities</label>
                                                    <div v-for="item in hotels" class="custom-control custom-switch"><input :checked="false" @click="isChck(item,$event)" :value="item.id" type="checkbox" :id="'user-capa-'+item.id" class="custom-control-input"> <label :for="'user-capa-'+item.id" class="custom-control-label">{{item.name}}</label></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        components: {
            Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                rePick: false,
                isAdmin: false,
                hotelOwners: [],
                receps: [],
                hotels: [],
                hotelsCapa: [],
                recepName: 'No name',
                form: new form({
                  recep: '',
                  assignHotel: [],
                  hotelOwner: ''
                })
            }
        },
        methods: {
            isChck(item, event) {
                if(event.target.checked) {
                    this.hotels.splice(this.hotels.indexOf(item),1);
                    this.form.assignHotel.push({id:item.id,name:item.name});
                    this.recepCap('add');
                }else{
                   // this.hotelsCapa.splice(this.hotelsCapa.indexOf(item),1);
                    this.form.assignHotel.splice(this.form.assignHotel.indexOf(item),1);
                    this.recepCap('remove');
                }
            },
            recepCap(action) {
                if(this.$gate.superAdminOrhotelOwner()) { 
                    this.isLoading = true;
                    let self = this;
                    this.form.post('/api/recep-capability/'+action).then(function (response) {
                        self.loadUserCap();
                        self.isLoading = false;
                        toast.fire({
                            type: 'success',
                            title: 'User created successfully'
                        });
                    })
                    .catch(function (error) {
                        self.isLoading = false;
                        toast.fire({
                            type: 'error',
                            title: 'Something went wrong!'
                        });
                    });
                }
            },
            selectRecep() {
                this.rePick = true;
                let self = this
                this.receps.forEach(function(item, index){
                    if(self.form.recep==item.id) {
                        self.recepName = item.name;
                        self.loadUserCap();
                    }
                });
            },
            loadHotels() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.hotels = [];
                    let self = this;
                    axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/0')
                    .then(
                        function (response) {
                            response.data.forEach(function(item, index){
                                self.hotels.push({id:item.id,name:item.name});
                            });
                            if(self.rePick) {
                                self.isLoading = false;
                                self.rePick = false;
                            }
                        }
                    );
                }
            },
            loadUserCap() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    if(this.rePick) {
                        this.isLoading = true;
                    }
                    this.hotelsCapa = [];
                    let self = this;
                    axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/1')
                    .then(
                        function (response) {
                            response.data.forEach(function(item, index){
                                self.hotelsCapa.push({id:item.id,name:item.name});
                            });
                            self.form.assignHotel = self.hotelsCapa;
                            self.loadHotels();
                        }
                    );
                }
            },
            ifChangehotelOwner() {
                if(this.$gate.superAdmin()) {
                    this.hotels = [];
                    this.hotelsCapa = [];
                    this.isLoading = true;
                    let self = this
                    axios.get('/api/hotel-receptionist/'+this.form.hotelOwner)
                    .then(
                        function (response) {
                            self.receps = response.data
                            self.isLoading = false;
                        }
                    );
                }
            },
            loadOwner() {
                if(this.$gate.superAdmin()) {
                    let self = this
                    this.isAdmin = true;
                    axios.get('/api/hotel-owners')
                    .then(
                        function (response) {
                            self.hotelOwners = response.data
                        }
                    );
                }else if(this.$gate.hotelOwner()) {
                    this.form.hotelOwner = '0';
                    let self = this
                    this.isAdmin = false;
                    this.isLoading = true;
                    axios.get('/api/hotel-receptionist')
                    .then(
                        function (response) {
                            self.receps = response.data
                            console.log(response.data);
                            self.isLoading = false;
                        }
                    );
                }
            }
        },
        created() {
            this.loadOwner();
        }
    }
</script>
<style lang='scss'>

</style>
