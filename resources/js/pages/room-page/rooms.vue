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
                        <table id="table-rooms" class="table table-bordered table-striped dataTable" role="grid">
                            <thead>
                                <tr role="row">
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Hotel</th>
                                    <th class="text-center">Room Type</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Total Room</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr v-for="room in rooms" :key="room.id">
                                    <td class="align-middle text-center">{{room.id}}</td>
                                    <td class="align-middle text-center">{{room.room_type.room_type_refer.name}}</td>
                                    <td class="align-middle text-center">{{room.room_type.name}}</td>
                                    <td class="align-middle text-center">{{room.name}}</td>
                                    <td class="align-middle text-center">
                                        <money-format :value="room.price" 
                                          :locale='lang' 
                                          :currency-code='curreny' 
                                          :subunit-value="true" 
                                          :hide-subunits="true">
                                        </money-format>
                                    </td>
                                    <td class="align-middle text-center">{{room.total_room}}</td>
                                    <td class="align-middle text-center">{{room.status}}</td>
                                    <td class="align-middle text-center">{{room.created_at}}</td>
                                    <td class="align-middle text-center">
                                        <vue-pure-lightbox
                                            class="image-circle"
                                            :thumbnail="getImgUrl(room.image, room.id)"
                                            :images="[
                                              getImgUrl(room.image, room.id)
                                            ]"
                                        ></vue-pure-lightbox>
                                    </td>
                                    <td class="align-middle text-center">
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
    import MoneyFormat from 'vue-money-format'
    export default {
        components: {
             VuePureLightbox,
             Loading,
             MoneyFormat
        },
        data() {
            return {
                lang: 'en',
                curreny: 'USD',
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
