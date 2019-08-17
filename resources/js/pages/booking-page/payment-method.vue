<template>
    <div class="container">
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
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <div class="card-tool">
                            <router-link to="/add-book-entry"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                        </div>
                    </div>

                    <form @submit.prevent="register" role="form">
                        <div class="card-body">
                            <h4>Guest info.</h4><hr>
                            <div class="form-group">
                                <label for="fullname">Fullname <span class="required-asterisk">*</span></label>
                                <input v-model="form.fullname" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('fullname') }" id="fullname" placeholder="Enter fullname">
                                <has-error :form="form" field="fullname"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="email">Email address </label>
                                <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="email" placeholder="Enter email">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="phone_no">Phone no. <span class="required-asterisk">*</span></label>
                                <input v-model="form.phone_no" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('phone_no') }" id="phone_no" placeholder="Enter phone_no">
                                <has-error :form="form" field="phone_no"></has-error>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                  <input v-model="form.consent" @click="toggleCheck" :checked="isCheckConsent" type="checkbox" class="custom-control-input" id="isCheckConsent" name="isCheckConsent">
                                  <label class="custom-control-label" for="isCheckConsent">Make you sure have permission to collect data from guest before enable this.</label>
                                </div>
                                <has-error :form="form" field="consent"></has-error>
                            </div>
                            <br />
                            <h4>Booking info.</h4><hr>
                            <div class="form-group">
                                <label for="roomType">Room Id: <span>{{form.room_id}}</span></label><br />
                                <label for="roomType">Room type: <span>{{bookInfo['roomType']}}</span></label><br />
                                <label for="roomName">Room name: <span>{{bookInfo['roomName']}}</span></label><br />
                                <label for="dateStay">Date start: <span>{{form.dateStart}}</span></label><br />
                                <label for="dateStay">Date end: <span>{{form.dateEnd}}</span></label><br />
                                <label for="night">Night(s): <span>{{bookInfo['night']}}</span></label><br />
                                <label for="checkInTime">Check In: <span>2:00pm</span></label><br />
                                <label for="checkOutTime">Check Out: <span>12:00pm</span></label><br />
                            </div>
                            <div class="form-group">
                                <label for="gRequest">Guest request </label>
                                <textarea v-model="form.gRequest" rows="6" class="form-control" :class="{ 'is-invalid': form.errors.has('gRequest') }" id="gRequest"></textarea>
                            </div>
                            <h4>Payment</h4><hr>
                            <div class="form-group">
                                <label for="amount">Total payment: <span>
                                <money-format :value="form.total" 
                                  :locale='lang' 
                                  :currency-code='curreny' 
                                  :subunit-value="true" 
                                  :hide-subunits="true">
                                </money-format>
                                </span></label>
                            </div>
                            <div class="form-group">
                                <label for="amount">Cash amount </label>
                                <input v-model="form.amount" @input="moneyChange" type="number" class="form-control" :class="{ 'is-invalid': notEnough }" id="amount" placeholder="Enter amount">
                                <has-error :form="form" field="amount"></has-error>
                            </div>
                            <div class="form-group">
                                <label for="amount">Change: <span>{{form.change}}</span></label>
                            </div>
                        </div>
                        <div class="card-footer">
                          <button :disabled="form.busy" type="submit" @click="submitTrigger('book')" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> Book</button>
                          <button :disabled="form.busy" type="submit" @click="submitTrigger('checkin')" class="btn btn-primary btn-flat"><i class="fa fa-save"></i> Check In</button>
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
    import MoneyFormat from 'vue-money-format'
    export default {
        components:{
            Loading,
            MoneyFormat
        },
        data() {
            return {
                lang: 'en',
                curreny: 'USD',
                fullPage: true,
                isLoading: false,
                isCheckConsent: false,
                notEnough: true,
                bookInfo: [],
                form: new form({
                    btnSubmit: '',
                    fullname: '',
                    email: '',
                    phone_no: '',
                    consent: false,
                    amount: 0,
                    change: 0,
                    total: 0,
                    gRequest: '',
                    dateStart: '',
                    dateEnd: '',
                    room_id: null
                })
            }
        },
        methods: {
            submitTrigger(value) {
                this.form.btnSubmit = value;
            },
            moneyChange(event) {
                this.form.change = event.target.value - this.form.total;
                if(this.form.total <= this.form.amount)
                    this.notEnough = false;
                else
                    this.notEnough = true;
            },
            toggleCheck() {
                if(this.isCheckConsent) {
                    this.isCheckConsent = false;
                }else{
                    this.isCheckConsent = true;
                }
            },
            register() {
                if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                    this.isLoading = true;
                    let self = this
                    this.form.post('/api/create-book').then(
                    function (response) {
                        let msg = 'Booked';
                        //self.form.reset();
                        if(self.form.btnSubmit=='checkin') {
                            msg = 'Check In';
                        }

                        self.isLoading = false;
                        toast.fire({
                          type: 'success',
                          title: msg+' successfully'
                        })
                        self.$router.push({ path: '/', query: {value:'test'} });

                    }).catch(function (error) {
                        console.log(error); 
                        self.isLoading = false;
                        toast.fire({
                          type: 'error',
                          title: 'Something went wrong!'
                        })
                    });
                }
            },
            loadRoom() {
                this.form.room_id = this.$route.query.roomId;
                this.bookInfo['roomName'] = this.$route.query.roomName;
                this.bookInfo['roomType'] = this.$route.query.roomType;
                this.form.dateStart = moment(this.$route.query.dateStay.split('<>')[0]).format('MMMM Do YYYY')
                this.form.dateEnd = moment(this.$route.query.dateStay.split('<>')[1]).format('MMMM Do YYYY')
                const start = moment(new Date(this.$route.query.dateStay.split('<>')[0]), 'M/D/YYYY');
                const end = moment(new Date(this.$route.query.dateStay.split('<>')[1]), 'M/D/YYYY');
                const diffDays = end.diff(start, 'days');

                this.bookInfo['night'] =  diffDays;

                this.form.total = (this.$route.query.price) * this.bookInfo['night'];
            }
        },
        mounted() {
            this.loadRoom();
        }
    }
</script>
