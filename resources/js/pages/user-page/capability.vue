<template>
    <div class="row justify-content-center">
          <loading 
            :height="128"
            :width="128"
            :transition="`fade`"
            :loader="`dots`"
            :background-color="`#fff`"
            :color="`#007bff`"
            :active.sync="isLoading" 
            :is-full-page="fullPage">
           </loading>
        <div class="col-12">
            <div class="card">
          <div class="card-header">
            <div class="card-tool">
                <router-link to="/users"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
            </div>
          </div>

            <div class="card-body">
                <div class="container">
                    <div class="row justify-content-center set-mb-100">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="hotelOwner">Hotel Owner <span class="required-asterisk">*</span></label>
                                <select v-model="form.hotelOwner" @change="ifChangehotelOwner" class="form-control" :class="{ 'is-invalid': form.errors.has('hotelOwner') }" id="hotelOwner">
                                  <option v-for="item in hotelOwners" :selected="item.id === form.hotelOwner" :value="item.id">{{item.email}}</option>
                                </select>
                                <has-error :form="form" field="hotelOwner"></has-error>
                            </div> 
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="recep">Receptionist <span class="required-asterisk">*</span></label>
                                <select v-model="form.recep" class="form-control" :class="{ 'is-invalid': form.errors.has('recep') }" id="recep">
                                  <option v-for="item in receps" :selected="item.id === form.recep" :value="item.id">{{item.email}}</option>
                                </select>
                                <has-error :form="form" field="recep"></has-error>
                            </div> 
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="goBtn" style="visibility:hidden;">testdsadsdadsad</label>
                                <button type="submit" @click="selectRecep" class="btn btn-outline-primary btn-flat" id="goBtn"><i class="fas fa-search"></i> Go</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <div class="card set-h-500">
                                <div class="card-header">
                                    <h6><strong>{{recepName}}</strong> capabilities</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-left">
                                        <div class="col-12">
                                            <div v-for="item in hotelsCapa" class="custom-control custom-switch"><input @click="isNotChck(item,$event)" :value="item.id" type="checkbox" :id="'user-capa-'+item.id" class="custom-control-input"> <label :for="'user-capa-'+item.id" class="custom-control-label">{{item.name}}</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center align-self-center">
                            <button @click="addCap" :disabled="isAvailEmpty" type="button" class="btn btn-outline-primary btn-flat"><i class="fas fa-arrow-left  fa-2x"></i></button><br /><br />
                            <button @click="removeCap" type="button" class="btn btn-outline-primary btn-flat"><i class="fas fa-arrow-right fa-2x"></i></button>
                        </div>
                        <div class="col-md-5 text-center">
                            <div class="card set-h-500">
                                <div class="card-header">
                                    <h6>Available capabilities</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-left">
                                        <div class="col-12">
                                            <div v-for="item in hotels" class="custom-control custom-switch"><input :checked="false" @click="isChck(item,$event)" :value="item.id" type="checkbox" :id="'capa-'+item.id" class="custom-control-input"> <label :for="'capa-'+item.id" class="custom-control-label">{{item.name}}</label></div>
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
                hotelOwners: [],
                receps: [],
                hotels: [],
                hotelsCapa: [],
                tempArrLeft: [],
                tempArrRight: [],
                isAvailEmpty: false,
                recepName: 'No name',
                form: new form({
                  recep: '',
                  assignHotel: [],
                  hotelOwner: ''
                })
            }
        },
        methods: {
            isNotChck(item, event) {
                if(event.target.checked) {
                    this.tempArrRight.push({id:item.id,name:item.name});            
                }else{
                    this.tempArrRight.splice(this.tempArrRight.indexOf(item),1); 
                }
            },
            isChck(item, event) {
                if(event.target.checked) {
                    this.tempArrLeft.push({id:item.id,name:item.name});
                }else{
                     this.tempArrLeft.splice(this.tempArrLeft.indexOf(item),1);
                }
            },
            recepCap(action) {
                if(this.$gate.superAdmin()) {
                    this.isLoading = true;
                    let self = this;
                    this.form.post('/api/recep-capability/'+action).then(function (response) {
                        
                        toast.fire({
                            type: 'success',
                            title: 'User created successfully'
                        });
                        self.loadUserCap();
                        self.loadHotels();
                        self.isLoading = false;
                    })
                    .catch(function (error) {
                        toast.fire({
                            type: 'error',
                            title: 'Something went wrong!'
                        });
                        self.isLoading = false;
                    });
                }
            },
            addCap() {
                this.form.assignHotel = this.tempArrLeft;
                this.recepCap('add'); 
            },
            removeCap() {
                this.form.assignHotel = this.tempArrRight;
                this.recepCap('remove'); 
            },
            selectRecep() {
                this.tempArrLeft = [];
                this.tempArrRight = [];
                this.form.assignHotel = [];
                let self = this
                this.receps.forEach(function(item, index){
                    if(self.form.recep==item.id) {
                        self.recepName = item.name;
                    }
                });
                this.loadUserCap(this.receps);
                this.loadHotels(this.receps);

            },
            loadHotels() {
                if(this.$gate.superAdmin()) {
                    this.hotels = [];
                    this.isLoading = true;
                    let self = this;
                    axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/0')
                    .then(
                        function (response) {
                            response.data.forEach(function(item, index){
                                self.hotels.push({id:item.id,name:item.name});
                            });
                            self.isLoading = false;
                        }
                    );
                }
            },
            loadUserCap() {
                if(this.$gate.superAdmin()) {
                    this.hotelsCapa = [];
                    this.isLoading = true;
                    let self = this;
                    axios.get('/api/hotels/'+this.form.hotelOwner+'/'+this.form.recep+'/1')
                    .then(
                        function (response) {
                            response.data.forEach(function(item, index){
                                self.hotelsCapa.push({id:item.id,name:item.name});
                            });
                            self.isLoading = false;
                        }
                    );
                }
            },
            ifChangehotelOwner() {
                if(this.$gate.superAdmin()) {
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
                    axios.get('/api/hotel-owners')
                    .then(
                        function (response) {
                            self.hotelOwners = response.data
                        }
                    );
                }
            }
        },
        created() {
            this.loadOwner();
            console.log('Component mounted.')
        }
    }
</script>
<style lang='scss'>

</style>
