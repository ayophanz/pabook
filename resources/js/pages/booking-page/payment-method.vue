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
                                  <input @click="toggleCheck" :checked="isCheckConsent" type="checkbox" class="custom-control-input" id="isCheckConsent" name="isCheckConsent">
                                  <label class="custom-control-label" for="isCheckConsent">Make you have permission to collect data from guest before enable this.</label>
                                </div>
                            </div>
                            <br />
                            <h4>Booking info.</h4><hr>
                            <div class="form-group">
                                <label for="roomType">Room Id: <span>{{form.room_id}}</span></label><br />
                                <label for="roomType">Room type: <span>{{bookInfo['roomName']}}</span></label><br />
                                <label for="roomName">Room name: <span></span></label><br />
                                <label for="dateStay">Date start: <span>test</span></label><br />
                                <label for="dateStay">Date end: <span>test</span></label><br />
                                <label for="night">Night(s): <span></span></label>
                            </div>
                            <div class="form-group">
                                <label for="gRequest">Guest request </label>
                                <textarea v-model="form.gRequest" rows="6" class="form-control" :class="{ 'is-invalid': form.errors.has('gRequest') }" id="gRequest"></textarea>
                            </div>
                            <h4>Payment</h4><hr>
                            <div class="form-group">
                                <label for="amount">Total payment: <span>{{form.total}}</span></label>
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
                          <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> Book</button>
                          <button :disabled="form.busy" type="submit" class="btn btn-outline-success btn-flat"><i class="fa fa-save"></i> Check In</button>
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
        components:{
            Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                isCheckConsent: false,
                notEnough: true,
                bookInfo: [{}],
                form: new form({
                    fullname: '',
                    email: '',
                    phone_no: '',
                    consent: false,
                    amount: 0,
                    change: 0,
                    total: 100,
                    gRequest: '',
                    dateStart: null,
                    dateEnd: null,
                    room_id: null
                })
            }
        },
        methods: {
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

            },
            loadRoom() {
                this.form.room_id = this.$route.query.roomId;
                this.bookInfo.push({roomName:this.$route.query.roomName});
                this.bookInfo.push({roomType:this.$route.query.roomType});
                console.log(this.bookInfo);
            }
        },
        created() {
            this.loadRoom();
            console.log(this.$route.query.dateStay);
        }
    }
</script>
