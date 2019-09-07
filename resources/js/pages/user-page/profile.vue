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
              <div class="col-md-3">
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
              <div class="col-md-9">
                <div class="card">
                  <div class="card-header p-2">
                    <ul class="nav nav-pills">
                      <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                      <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                      <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                    </ul>
                  </div>
                  <div class="card-body">
                    <div class="tab-content">
                      <div class="active tab-pane" id="activity">
                        <div class="post">
                          <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="" alt="user image">
                            <span class="username">
                              <a href="#">Jonathan Burke Jr.</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Shared publicly - 7:30 PM today</span>
                          </div>

                          <p>
                            Lorem ipsum represents a long-held tradition for designers,
                            typographers and the like. Some people hate it and argue for
                            its demise, but others ignore the hate as they create awesome
                            tools to help create filler text for everyone from bacon lovers
                            to Charlie Sheen fans.
                          </p>

                          <p>
                            <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                            <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                            <span class="float-right">
                              <a href="#" class="link-black text-sm">
                                <i class="far fa-comments mr-1"></i> Comments (5)
                              </a>
                            </span>
                          </p>

                          <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                        </div>
                        <div class="post clearfix">
                          <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="" alt="User Image">
                            <span class="username">
                              <a href="#">Sarah Ross</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Sent you a message - 3 days ago</span>
                          </div>
                          <p>
                            Lorem ipsum represents a long-held tradition for designers,
                            typographers and the like. Some people hate it and argue for
                            its demise, but others ignore the hate as they create awesome
                            tools to help create filler text for everyone from bacon lovers
                            to Charlie Sheen fans.
                          </p>

                          <form class="form-horizontal">
                            <div class="input-group input-group-sm mb-0">
                              <input class="form-control form-control-sm" placeholder="Response">
                              <div class="input-group-append">
                                <button type="submit" class="btn btn-danger">Send</button>
                              </div>
                            </div>
                          </form>
                        </div>

                        <div class="post">
                          <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="" alt="User Image">
                            <span class="username">
                              <a href="#">Adam Jones</a>
                              <a href="#" class="float-right btn-tool"><i class="fas fa-times"></i></a>
                            </span>
                            <span class="description">Posted 5 photos - 5 days ago</span>
                          </div>
                          <div class="row mb-3">
                            <div class="col-sm-6">
                              <img class="img-fluid" src="" alt="Photo">
                            </div>
                            <div class="col-sm-6">
                              <div class="row">
                                <div class="col-sm-6">
                                  <img class="img-fluid mb-3" src="" alt="Photo">
                                  <img class="img-fluid" src="" alt="Photo">
                                </div>
                                <div class="col-sm-6">
                                  <img class="img-fluid mb-3" src="" alt="Photo">
                                  <img class="img-fluid" src="" alt="Photo">
                                </div>
                              </div>
                            </div>
                          </div>

                          <p>
                            <a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
                            <a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
                            <span class="float-right">
                              <a href="#" class="link-black text-sm">
                                <i class="far fa-comments mr-1"></i> Comments (5)
                              </a>
                            </span>
                          </p>

                          <input class="form-control form-control-sm" type="text" placeholder="Type a comment">
                        </div>
                      </div>
                      <div class="tab-pane" id="timeline">
                        <ul class="timeline timeline-inverse">
                          <li class="time-label">
                            <span class="bg-danger">
                              10 Feb. 2014
                            </span>
                          </li>
                          <li>
                            <i class="fas fa-envelope bg-primary"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="far fa-clock"></i> 12:05</span>

                              <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                              <div class="timeline-body">
                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                quora plaxo ideeli hulu weebly balihoo...
                              </div>
                              <div class="timeline-footer">
                                <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                              </div>
                            </div>
                          </li>
                          <li>
                            <i class="fas fa-user bg-info"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="far fa-clock"></i> 5 mins ago</span>

                              <h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request
                              </h3>
                            </div>
                          </li>
                          <li>
                            <i class="fas fa-comments bg-warning"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="far fa-clock"></i> 27 mins ago</span>

                              <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                              <div class="timeline-body">
                                Take me to your leader!
                                Switzerland is small and neutral!
                                We are more like Germany, ambitious and misunderstood!
                              </div>
                              <div class="timeline-footer">
                                <a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
                              </div>
                            </div>
                          </li>
                          <li class="time-label">
                            <span class="bg-success">
                              3 Jan. 2014
                            </span>
                          </li>
                          <li>
                            <i class="fas fa-camera bg-purple"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="far fa-clock"></i> 2 days ago</span>

                              <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                              <div class="timeline-body">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                              </div>
                            </div>
                          </li>
                          <li>
                            <i class="far fa-clock bg-gray"></i>
                          </li>
                        </ul>
                      </div>

                      <div class="tab-pane" id="settings">
                        <form class="form-horizontal">
                          <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-10">
                              <input type="email" class="form-control" id="inputName" placeholder="Name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                              <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputName2" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="inputName2" placeholder="Name">
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                            <div class="col-sm-10">
                              <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                            <div class="col-sm-10">
                              <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <div class="checkbox">
                                <label>
                                  <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <button type="submit" class="btn btn-danger">Submit</button>
                            </div>
                          </div>
                        </form>
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
