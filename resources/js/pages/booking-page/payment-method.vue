<template>
    <div class="container">
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
                                <label for="roomType">Room Id: <span>11</span></label><br />
                                <label for="roomType">Room type: <span>test</span></label><br />
                                <label for="roomName">Room name: <span>test</span></label><br />
                                <label for="dateStay">Date stay: <span>test</span></label><br />
                                <label for="night">Night(s): <span>{{$route.params.night}}</span></label>
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
    export default {
        data() {
            return {
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
                    dateStay: null,
                    night: 0,
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

            }
        },
        created() {

        }
    }
</script>
