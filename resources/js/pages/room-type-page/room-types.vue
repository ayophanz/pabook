<template>
    <div class="row justify-content-center">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <div class="card-tool">
                    <router-link to="/add-room-type">
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
                                    <th>Hotel</th>
                                    <th>Room Type</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="type in types" :key="type.id">
                                    <td>{{type.id}}</td>
                                    <td>{{type.room_type_refer.name}}</td>
                                    <td>{{type.name}}</td>
                                    <td>{{type.created_at | formatDate}}</td>
                                    <td>
                                        <router-link :to="`/edit-room-type/${type.id}`"  class="btn btn-outline-primary btn-flat"><i class="fa fa-edit"></i> Edit</router-link>&nbsp;&nbsp;
                                        <a href="#" @click.prevent="selectType(type.id)" :data-id="type.id" class="btn btn-outline-danger btn-flat"><i class="fa fa-trash"></i> Delete</a>
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
                types: []
            }
        },
        methods: {
            loadTypes() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    let self = this
                    axios.get('/api/room-types')
                    .then(
                        function (response) {
                            self.types = response.data
                        }
                    );
                }
            },
            deleteType(typeId) {
                if(this.$gate.superAdminOrhotelOwner()) {
                    axios.delete('/api/delete-type/'+typeId)
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
            selectType(typeId) {
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
                    this.deleteType(typeId);
                  }
                })
            }
        },
        created() {
            this.loadTypes();
            fire.$on('afterCreated',() => {this.loadTypes()});
        }
    }
</script>