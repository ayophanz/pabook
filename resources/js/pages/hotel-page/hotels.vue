<template>
    <div class="row justify-content-center">

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
          <result-page-icon></result-page-icon>
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <div class="card-tool">
                    <router-link to="/add-hotel">
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
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Phone Number</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr v-for="hotel in hotels" :key="hotel.id">
                                    <td class="align-middle text-center">{{hotel.id}}</td>
                                    <td class="align-middle text-center">{{hotel.name}}</td>
                                    <td class="align-middle text-center">{{hotel.country}}</td>
                                    <td class="align-middle text-center">{{hotel.phone_number}}</td>
                                    <td class="align-middle text-center">{{hotel.email}}</td>
                                    <td class="align-middle text-center">
                                        <vue-pure-lightbox
                                            class="image-circle"
                                            :thumbnail="getImgUrl(hotel.image)"
                                            :images="[
                                              getImgUrl(hotel.image)
                                            ]"
                                        ></vue-pure-lightbox>
                                    </td>
                                    <td class="align-middle text-center">{{hotel.created_at | formatDate}}</td>
                                    <td class="align-middle text-center">
                                        <router-link :to="`/edit-hotel/${hotel.id}`"  class="btn btn-outline-primary btn-flat"><i class="fa fa-edit"></i> Edit</router-link>&nbsp;&nbsp;
                                        <a href="#" @click.prevent="selectHotel(hotel.id)" :data-id="hotel.id" class="btn btn-outline-danger btn-flat"><i class="fa fa-trash"></i> Delete</a>
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
        components:{
            VuePureLightbox,
            Loading
        },
        data() {
            return {
                hotels: '',
                fullPage: true,
                isLoading: false
            }
        },
        methods: {
            getImgUrl(image) {
                return '/storage/images/upload/hotelImages/'+image+'?rnd'+new Date().getTime();
            },
            loadHotels() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    let self = this
                    axios.get('/api/hotels')
                    .then(
                        function (response) {
                            self.hotels = response.data
                        }
                    );
                }
            },
            deleteHotel(hotelId) {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.isLoading = true;
                    let self = this;
                    axios.delete('/api/delete-hotel/'+hotelId)
                    .then(
                        function (response) {
                            self.isLoading = false;
                            toast.fire({
                              type: 'success',
                              title: 'User deleted successfully'
                            })
                            fire.$emit('HotelCreated');
                        }
                    );
                }
            },
            selectHotel(hotelId) {
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
                    this.deleteHotel(hotelId);
                  }
                })
            }
        },
        created() {
            this.loadHotels();
            fire.$on('HotelCreated',() => {this.loadHotels()});
        }
    }
</script>
