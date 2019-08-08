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
                    <router-link to="/add-room">
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
                                    <th>Room Type</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Total Room</th>
                                    <th>Image</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr v-for="room in rooms" :key="room.id">
                                    <td>{{room.id}}</td>
                                    <td>{{room.room_refer.name}}</td>
                                    <td>{{room.name}}</td>
                                    <td>{{room.description}}</td>
                                    <td>{{room.price}}</td>
                                    <td>{{room.total_room}}</td>
                                    <td>
                                        <vue-pure-lightbox
                                            class="image-circle"
                                            :thumbnail="getImgUrl(room.image, room.id)"
                                            :images="[
                                              getImgUrl(room.image, room.id)
                                            ]"
                                        ></vue-pure-lightbox>
                                    </td>
                                    <td>{{room.created_at}}</td>
                                    <td>
                                        <router-link :to="`/edit-room/${room.id}`"  class="btn btn-outline-primary btn-flat"><i class="fa fa-edit"></i> Edit</router-link>&nbsp;&nbsp;
                                        <a href="#" @click.prevent="selectRoom(room.id)" :data-id="room.id" class="btn btn-outline-danger btn-flat"><i class="fa fa-trash"></i> Delete</a>
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
    import styles from 'vue-pure-lightbox/dist/VuePureLightbox.css'
    import VuePureLightbox from 'vue-pure-lightbox'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        components: {
             VuePureLightbox,
             Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                rooms: []
            }
        },
        methods: {
            getImgUrl(image, id) {
                return '/storage/images/upload/roomImages/gallery-'+id+'/'+image+'?rnd'+new Date().getTime();
            },
            loadRooms() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    let self = this
                    axios.get('/api/rooms')
                    .then(
                        function (response) {
                            self.rooms = response.data
                        }
                    );
                }
            },
            deleteRoom(roomId) {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.isLoading = true;
                    let self = this
                    axios.delete('/api/delete-room/'+roomId)
                    .then(
                        function (response) {
                            self.isLoading = false;
                            toast.fire({
                              type: 'success',
                              title: 'User deleted successfully'
                            })
                            fire.$emit('afterCreated');
                        }
                    );
                }
            },
            selectRoom(roomId) {
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
                    this.deleteRoom(roomId);
                  }
                })
            }
        },
        created() {
            this.loadRooms();
            fire.$on('afterCreated',() => {this.loadRooms()});
        }
    }
</script>
