<template>
    <div id="root">
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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-tool">
                            <router-link to="/bookings"><button class="btn btn-outline-primary btn-flat"><i class="fa fa-arrow-left"></i> Back</button></router-link>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                Select Date:
                                <date-range-picker
                                        ref="picker"
                                        :opens="direction"
                                        :locale-data="{ firstDay: 1, format: 'MMMM Do YYYY' }"
                                        v-model="dateRange",
                                        :time-picker="false",
                                        :ranges="false",
                                        @update="updateValues"
                                >
                                    <div slot="input" slot-scope="picker" style="min-width: 350px;">
                                        {{ defaultStartDate=picker.startDate | formatDate }} - {{ defaultEndDate=picker.endDate | formatDate }}
                                    </div>
                                </date-range-picker>
                                <div class="night-stay" style="text-align:center;">
                                    <span><i class="fas fa-moon"></i> {{ totalNights() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="result-wrapper container">
                <div class="row justify-content-center">

                    <div class="col-md-4" v-for="room in rooms" :key="room.id">
                        <div class="card card-item">
                            <div class="card-body">
                                <vue-pure-lightbox
                                    :style="{ 'background-image' : `url(${getImgUrl(room.id, room.image)})` }"
                                    class="item-image"
                                    :images="gallery(room.id, room.room_gallery.value)"
                                ></vue-pure-lightbox>
                                <div class="room-details">
                                    <span>name: {{room.name}}</span><br />
                                    <span>price: {{room.price}}</span><br />
                                    <span>type: {{room.room_type.name}}</span><br />
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary btn-flat"><i class="fas fa-map-marked-alt"></i> Book Now</button>
                            </div>
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
    import DateRangePicker from 'vue2-daterange-picker'
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css' 
    export default {
        components:{
            VuePureLightbox,
            DateRangePicker,
            Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                rooms: [],
                night: 0,
                defaultStartDate: '',
                defaultEndDate: '',
                direction: 'center',
                separator: ' - ',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                dateRange: '',
                firstDay: moment.localeData().firstDayOfWeek()
            }
        },
        methods: {
            gallery(id, images) {
                let tempImg = JSON.parse(images);
                let newImages = [];
                tempImg.forEach(function(item, index){
                    newImages.push('../storage/images/upload/roomImages/gallery-'+id+'/'+item[1]['filename']);
                });
                return newImages;
            },
            getImgUrl(id,img) {
                return '../storage/images/upload/roomImages/gallery-'+id+'/'+img;
            },
            updateValues() {
                const start = moment(this.dateRange.startDate, 'M/D/YYYY');
                const end = moment(this.dateRange.endDate, 'M/D/YYYY');
                const diffDays = end.diff(start, 'days');
                this.night = diffDays;
                this.loadRooms(start, end);
            },
            totalNights() {
                return ((this.night <= 1)? this.night+' night' : this.night+' nights');
            },
            loadRooms(start, end) {
                if(this.$gate.superAdminOrhotelOwner()) {
                    this.isLoading = true;
                    let self = this;
                    axios.get('/api/load-rooms/'+start+'/'+end)
                    .then(
                        function (response) {
                            self.rooms = response.data;
                            self.isLoading = false;
                        }
                    );
               }
            }
        },
        created() {

        }
    }
</script>

<style type="text/css">
.night-stay span {
    font-size: 26px;
}
.night-stay {
    margin-top: 20px;
}
.item-image {
    height: 230px;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    cursor: pointer;
}
.card-item .card-body {
    padding: 0px;
}
.room-details {
    padding: 15px;
}
 .card-item .card-footer {
    text-align: right;
}
.item-image a {
    display: block;
}
</style>