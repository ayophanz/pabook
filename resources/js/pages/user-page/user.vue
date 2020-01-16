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
       <profile-page-icon v-if="userId!=null"></profile-page-icon>
       <add-user-page-icon v-if="userId==null"></add-user-page-icon>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
              <div class="card-header">
                <div class="card-tool">
                    <router-link to="/users"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                    <router-link to="/users-capability"><button class="btn btn-outline-primary btn-flat">Capability <i class="fa fa-arrow-right"></i></button></router-link>
                </div>
              </div>

              <form @submit.prevent="register" role="form">
                <div class="card-body">
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
                  <div class="form-group">
                    <label for="role">Role <span class="required-asterisk">*</span></label>
                    <select v-model="form.role" @change="ifChangeRole" class="form-control" :class="{ 'is-invalid': form.errors.has('role') }" id="role">
                      <option v-for="item in role" :selected="item === form.role" :value="item">{{item}}</option>
                    </select>
                    <has-error :form="form" field="role"></has-error>
                  </div>
                  <div class="form-group" v-if="isSuperAdmin && isRecep">
                    <label for="assign_to">Assign to <span class="required-asterisk">*</span></label>
                    <select v-model="form.assignTo" class="form-control" :class="{ 'is-invalid': form.errors.has('assignTo') }" id="assignTo">
                      <option v-for="item in hotelOwnerEmails" :selected="item === form.assignTo" :value="item.id">{{item.email}}</option>
                    </select>
                    <has-error :form="form" field="assignTo"></has-error>
                  </div>
                  <div class="form-group">
                    <label for="status">Status <span class="required-asterisk">*</span></label>
                     <select v-model="form.status" class="form-control" :class="{ 'is-invalid': form.errors.has('status') }" id="status">
                      <option v-for="item in status" :selected="item === form.status" :value="item">{{item}}</option>
                     </select>
                    <has-error :form="form" field="status"></has-error>
                  </div>
                  <!-- <div class="form-group" v-if="userId != null">
                    <div class="custom-control custom-switch">
                      <input @click="toggleCheck" :checked="isCheckPass" type="checkbox" class="custom-control-input" id="isChangePass" name="isChangePass">
                      <label class="custom-control-label" for="isChangePass">Change password</label>
                    </div>
                  </div> -->
                  <!-- <div class="form-group" v-if="isCheckPass == true || userId == null">
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
                  </div> -->
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
               if(to.path === '/add-user') {
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
            isRecep: true,
            isSuperAdmin: false,
            isCheckPass: false,
            buttonText: 'Save',
            userId: null,
            hotelOwnerEmails: [],
            form: new form({
              fullname: '',
              email: '',
              role: '',
              status: '',
              password: '',
              changePass: '',
              assignTo: ''
            }),
            status: ['active', 'inactive'],
            role: ['hotel_receptionist']
          }
        },
        methods: {
          resetComponent() {
            this.buttonText = 'Save';
            this.userId = null;
            this.form.reset();
          },
          // toggleCheck () {
          //   if(this.isCheckPass)
          //     this.isCheckPass = false;
          //   else
          //     this.isCheckPass = true;
          // },
          register () {
            if(this.$gate.superAdminOrhotelOwner()) {
              this.isLoading = true;
              let self = this
              this.form.changePass = this.isCheckPass
              let action = null;
              if(this.userId!=null) 
                 action = this.form.put('/api/update-user/'+this.userId)
              else
                 action = this.form.post('/api/create-user')
              action.then(function (response) { 
                  if(self.userId==null) 
                      self.form.reset();
                  self.isLoading = false;
                  toast.fire({
                    type: 'success',
                    title: 'User created successfully'
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
          showHide () {
            let pwd = $("#password");
            let fa = $(".call-password");
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
          userDetails(id) {
            if(this.$gate.superAdminOrhotelOwner()) {
              this.isLoading = true;
              let self = this;
              axios.get('/api/edit-user/'+id)
                .then(
                  function (response) {
                    self.form.fullname = response.data.name;
                    self.form.email = response.data.email;
                    self.form.role = response.data.role;
                    self.form.status = response.data.status;
                    self.isLoading = false;
                  }
                );    
            }
          },
          ifChangeRole() {
            this.hotelOwners(this.form.role);
          },
          hotelOwners(role) {
            if(role == 'hotel_receptionist')
               this.isRecep = true;
            else
               this.isRecep = false;
          }
        },
        beforeCreate() {
          //
        },
        created(){
          if(this.$route.params.userId) {
              this.userId = this.$route.params.userId;  
              this.userDetails(this.userId);
              this.buttonText = 'Update';
              //if(this.$gate.hotelOwner()) 
                 //this.role.splice(0, 1);
              this.hotelOwners(this.form.role);
          }else{
              //
          }

          if(this.$gate.superAdmin()) {
              this.role.push('hotel_owner');
              this.isSuperAdmin = true;
              let self = this;
              axios.get('/api/hotel-owners')
              .then(
                function (response) {
                  response.data.forEach(function(item){
                    self.hotelOwnerEmails.push(item);
                  });
                }
              );
          } 
        },
        beforeUpdate() {
            //
        },
        updated() {
            //
        },
        beforeMount(){
            //
        },
        beforeDestroy() {
            //
        },
        destroyed() {
            //
        },
        beforeRouteEnter(to, from, next) {
            next();
        },
        beforeRouteleave() {
            next();
        }
    }
</script>
