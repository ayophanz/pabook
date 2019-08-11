<template>
    <div id="root">
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

                    <div class="col-md-4" v-for="(room, key) in rooms" :key="room.key">
                        <div class="card card-item">
                            <div class="card-body">test body</div>
                            <div class="card-footer">test footer</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DateRangePicker from 'vue2-daterange-picker'
    import 'vue2-daterange-picker/dist/vue2-daterange-picker.css' 
    export default {
        components:{
            DateRangePicker
        },
        data() {
            return {
                 rooms: [{}],
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
                    let self = this;
                    axios.get('/api/load-rooms/'+start+'/'+end)
                    .then(
                        function (response) {
                            let data = response.data;
                            self.rooms.push({ 
                                name: data.name,
                                price: data.price,
                                available: data.total_room,
                                //hotel: data.room_type.room_type_refer.name,
                                //type: data.room_type.name, 
                                description: data.description,
                                featureImage: data.image,
                                //feature: data.room_feature.value,
                                //gallery: data.room_gallery.value
                            });
                        }
                    );
                    console.log(this.rooms);
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
</style>