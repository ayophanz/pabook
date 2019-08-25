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
        <div class="col-md-12">
            <form @submit.prevent="register" role="form">
                <div class="card">
                    <div class="card-body">
                        <div v-if="isAdmin" class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_id">Hotel Owner <span class="required-asterisk">*</span></label>
                                    <select v-model="form.owner_id" @change="selectedOwner" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('owner_id') }" id="owner_id">
                                      <option v-for="item in owners" :selected="item.id === form.owner_id" :value="item.id">{{item.email}}</option>
                                    </select>
                                    <has-error :form="form" field="owner_id"></has-error>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="base_currency">Base currency (global) <span class="required-asterisk">*</span></label>
                                    <select v-model="form.base_currency" @change="currencyCall" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('base_currency') }" id="base_currency">
                                      <option v-for="item in currency" :selected="item.id === form.base_currency" :value="item">{{item}}</option>
                                    </select>
                                    <p>{{currencyName}}</p>
                                    <has-error :form="form" field="base_currency"></has-error>
                                </div> 
                            </div>
                        </div> 
                    </div>
                    <div class="card-footer">
                      <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import cc from 'currency-codes'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        components: {
            cc,
            Loading
        },
        data() {
            return {
                isAdmin: false,
                fullPage: true,
                isLoading: false,
                currencyName: '',
                currency: [],
                owners: [],
                form: new form({
                    base_currency: '',
                    owner_id: null
                })
            }
        },
        methods: {
            selectedOwner(){

            },
            currencyCall() {
                this.currencyName = cc.code(this.form.base_currency).currency;
            },
            hotelOwner() {
                if(this.$gate.superAdmin()) {
                    this.isAdmin = true;
                    this.isLoading = true;
                    let self = this
                    axios.get('/api/hotel-owners')
                    .then(
                        function (response) {
                            self.owners  = response.data
                            self.isLoading = false;
                        }
                    );
                }
            },
            register() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.isLoading = true;
                    this.form.post('/api/create-config')
                    .then(
                        function (response) {
                            self.isLoading = false;
                            toast.fire({
                              type: 'success',
                              title: 'Settings successfully save'
                            })
                        }
                    ).
                    catch(function(e){
                        console.log(e); 

                        self.isLoading = false;
                        toast.fire({
                          type: 'error',
                          title: 'Something went wrong!'
                        })

                    });
                }
            }
        },
        created() {
            this.currency = cc.codes();
            this.hotelOwner();
        }
    }
</script>