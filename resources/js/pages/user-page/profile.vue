<template>
      <div class="col-md-12">
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
        <profile-page-icon></profile-page-icon>
          <div class="container-fluid">
            <div class="row justify-content-center">
              <div class="col-md-8 col-sm-12 col-xm-12">
                <div class="card card-primary card-outline" v-if="isEdit==false">
                  <div class="card-header">
                  </div>
                  <div class="card-body box-profile">
                    <h3 v-model="form.fullname" class="profile-username text-center">{{form.fullname}}</h3>
                    <p class="text-muted text-center">Name</p>
                    <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                        <b>Status</b> <a v-model="form.status" class="float-right">{{form.status}}</a>
                      </li>
                      <li class="list-group-item">
                        <b>Role</b> <a v-model="form.role" class="float-right">{{form.role}}</a>
                      </li>
                      <li class="list-group-item">
                        <b>Email</b> <a v-model="form.email" class="float-right">{{form.email}}</a>
                      </li>
                    </ul>

                    <div class="text-center">
                        <a href="#" @click.prevent="toEdit(true)" class="btn btn-outline-primary btn-flat"><i class="fa fa-edit"></i> Edit</a>
                    </div>
                  </div>
                </div>

                <div class="card card-outline" v-if="isEdit">
                  <div class="card-header">
                    <div class="card-tool">
                        <a href="#" @click.prevent="toEdit(false)" class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Cancel</a>
                    </div>
                  </div>
                  <div class="card-body box-profile">
                    <form @submit.prevent="updateProfile" role="form">
                        <div class="form-group">
                            <label for="fullname">Fullname <span class="required-asterisk">*</span></label>
                            <input v-model="form.fullname" type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('fullname') }" id="fullname" placeholder="Enter fullname">
                            <has-error :form="form" field="fullname"></has-error>
                          </div>  
                          <div class="form-group">
                            <label for="email">Email address <span class="required-asterisk">*</span></label>
                            <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="email" placeholder="Enter email">
                            <has-error :form="form" field="email"></has-error>
                          </div>
                          <div class="form-group" v-if="super_admin==false">
                            <label for="role">Role <span class="required-asterisk">*</span></label>
                            <select v-model="form.role" class="form-control" :class="{ 'is-invalid': form.errors.has('role') }" id="role">
                              <option v-for="item in role" :selected="item === form.role" :value="item">{{item}}</option>
                            </select>
                            <has-error :form="form" field="role"></has-error>
                          </div>
                          <div class="form-group" v-if="super_admin">
                            <label for="role">Role <span class="required-asterisk">*</span></label>
                            <input disabled class="form-control" :value="form.role">
                          </div>
                          <div class="form-group" v-if="super_admin==false">
                            <label for="status">Status <span class="required-asterisk">*</span></label>
                             <select v-model="form.status" class="form-control" :class="{ 'is-invalid': form.errors.has('status') }" id="status">
                              <option v-for="item in status" :selected="item === form.status" :value="item">{{item}}</option>
                             </select>
                            <has-error :form="form" field="status"></has-error>
                          </div>
                          <div class="form-group" v-if="super_admin">
                            <label for="status">Status <span class="required-asterisk">*</span></label>
                            <input disabled class="form-control" :value="form.status">
                          </div>
                          <div class="form-group">
                            <div class="custom-control custom-switch">
                              <input @click="toggleCheck" :checked="isCheckPass" type="checkbox" class="custom-control-input" id="isChangePass" name="isChangePass">
                              <label class="custom-control-label" for="isChangePass">Change password</label>
                            </div>
                          </div>
                          <div class="form-group" v-if="isCheckPass == true">
                            <label for="password">Password <span class="required-asterisk">*</span></label>
                            <div class="input-group">
                                <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" id="password" name="password">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <a href="#" @click.prevent="showHide"><i class="call-password fa fa-eye"></i></a>
                                    </span>          
                                </div>
                                <has-error :form="form" field="password"></has-error>
                            </div>
                          </div>
                          <div class="text-center">
                            <button type="submit" class="btn btn-outline-primary btn-flat"><i class="fa fa-save"></i> Update</button>
                            </div> 
                      </form>
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
                isCheckPass: false,
                super_admin: false,
                isEdit: false,
                form: new form({
                  fullname: '',
                  email: '',
                  role: '',
                  status: '',
                  password: '',
                  changePass: ''
                }),
                status: ['active', 'inactive'],
                role: ['hotel_owner', 'hotel_receptionist']
            }
        },
        methods: {
            toggleCheck () {
                if(this.isCheckPass)
                  this.isCheckPass = false;
                else
                  this.isCheckPass = true;
            },
            showHide () {
                var pwd = $("#password");
                var fa = $(".call-password");
                if(pwd.attr('type') === 'password') {
                  pwd.attr('type', 'text');
                  fa.removeClass('fa-eye');
                  fa.addClass('fa-eye-slash');
                }else{
                  fa.removeClass('fa-eye-slash');
                  fa.addClass('fa-eye');
                  pwd.attr('type', 'password')
                }
            },
            updateProfile() {
                this.isLoading = true;
                let self = this
                this.form.changePass = this.isCheckPass
                this.form.put('/api/update-profile')
                .then(function (response) {  
                    self.isLoading = false;
                    toast.fire({
                      type: 'success',
                      title: 'Profile updated successfully'
                    })

                })
                .catch(function (error) {
                    self.isLoading = false;
                    toast.fire({
                      type: 'error',
                      title: 'Something went wrong!'
                    })

                });
            },
            profile() {
                let self = this;
                axios.get('/api/profile')
                .then(
                    function (response) {
                      self.form.fullname = response.data.name;
                      self.form.email = response.data.email;
                      self.form.status = response.data.status;
                      self.form.role = response.data.role;
                      if(self.form.role=='super_admin') 
                         self.super_admin = true;
                    }
                ); 
           },
           toEdit(isEdit) {
              this.isEdit = isEdit;
           }
        },
        created() {
            this.profile();
        }
    }
</script>
