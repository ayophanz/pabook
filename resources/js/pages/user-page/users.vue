<template>
    <div class="row justify-content-center">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <div class="card-tool">
                    <router-link to="/add-user">
                        <button class="btn btn-outline-primary btn-flat">
                            <i class="fa fa-plus-circle"></i> Add
                        </button>
                    </router-link>
                </div>
            </div>
            <div class="card-body">
              <div class="row">
                  <div class="col-sm-12">
                        <table id="table-user" class="table table-bordered table-striped dataTable" role="grid">
                            <thead>
                                <tr role="row">
                                    <th>Id</th>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Member since</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr v-for="user in users" :key="user.id">
                                    <td>{{user.id}}</td>
                                    <td>{{user.name | upWord}}</td>
                                    <td>{{user.email}}</td>
                                    <td>{{user.role}}</td>
                                    <td>{{user.status}}</td>
                                    <td>{{user.created_at | formatDate}}</td>
                                    <td>
                                        <router-link :to="`/edit-user/${user.id}`"  class="btn btn-outline-primary btn-flat"><i class="fa fa-edit"></i> Edit</router-link>&nbsp;&nbsp;
                                        <a href="#" @click.prevent="selectUser(user.id)" :data-id="user.id" class="btn btn-outline-danger btn-flat"><i class="fa fa-trash"></i> Delete</a>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

</template>

<script>
    export default {
        data() {
            return {
                users : ''
            }
        },
        methods: {
            loadUsers() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    let self = this
                    axios.get('/api/users')
                    .then(
                        function (response) {
                            self.users = response.data
                        }
                    );
                }
            },
            deleteUser(userId) {
                if(this.$gate.superAdminOrhotelOwner()) {
                    axios.delete('/api/delete-user/'+userId)
                    .then(
                        function (response) {
                            fire.$emit('afterCreated');
                        }
                    );
                    toast.fire({
                      type: 'success',
                      title: 'User deleted successfully'
                    })
                }
            },
            selectUser(userId) {
                sure.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete it!',
                  cancelButtonText: 'No, cancel! ',
                  focusCancel: true,
                  reverseButtons: true
                }).then((result) => {
                  if(result.value) {
                    this.deleteUser(userId);
                  }
                })
            }
        },
        created() {
            this.loadUsers(); 
            fire.$on('afterCreated',() => {this.loadUsers()});
        }
    }
</script>
