<template>
    <div id="root">
        <vodal :closeOnClickMask="false" :show="vodal_show" animation="zoom" :closeOnEsc="false" :width="600" className="vodal-style" @hide="vodal_show = false">
            <div class="swal2-header">
                <div class="swal2-icon swal2-info swal2-animate-info-icon" style="display: flex;"></div>
                <h4 class="swal2-title" style="display: flex;"><strong>Booking details</strong></h4>
            </div>
            <div class="swal2-content">
                <br />
                <ul v-if="vodal_show" class="booking-details">
                    <li>Name: {{singlebooking.name}}</li>
                    <li>Phone No.: {{singlebooking.phoneNo}}</li>
                    <li v-if="singlebooking.email">Email: {{singlebooking.email}}</li>
                    <li v-if="singlebooking.address">Address: {{singlebooking.address}}</li>
                    <hr>
                    <li>Boooking ID: {{singlebooking.id}}</li>
                    <li>Hotel: {{(Object.keys(singlebooking).length>0)?singlebooking.hotel.name:''}}</li>
                    <li>Room: {{(Object.keys(singlebooking).length>0)?singlebooking.room.name:''}}</li>
                    <li>Room Type: {{(Object.keys(singlebooking).length>0)?singlebooking.room.room_type.name:''}}</li>
                    <li>CheckIn: {{moment(singlebooking.DateStart).format("MMM Do YYYY")}}</li>
                    <li>CheckOut: {{moment(singlebooking.DateEnd).format("MMM Do YYYY")}}</li>
                    <li>Night(s): soon</li>
                    <li>Total Room(s): {{singlebooking.manyRoom}}</li>
                    <li>Adult(s): {{singlebooking.manyAdult}}</li>
                    <li>Child(s): {{singlebooking.manyChild}}</li>
                    <li>Room No.: <span v-for="(item, key) in ((Object.keys(singlebooking).length>0)?JSON.parse(singlebooking.roomsNo):[])">{{item.value}}{{dynamicSemecolon(singlebooking.roomsNo, key)}}</span></li>
                    <li>
                        <ul class="booking-details-amenities amen-fixed">
                            <li v-for="(item, key) in ((Object.keys(singlebooking).length>0)?JSON.parse(singlebooking.room.room_feature.value):[])">{{item.value}}{{dynamicSemecolon(singlebooking.room.room_feature.value, key)}}</li>
                        </ul>
                    </li>
                </ul>
                <br />
            </div>
        </vodal>
        <div class="row justify-content-center">
            <result-page-icon></result-page-icon>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-tool">
                            <router-link to="/add-book-entry">
                                <button class="btn btn-outline-primary btn-flat">
                                    <i class="fa fa-plus-circle"></i> New book
                                </button>
                            </router-link>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <vue-bootstrap4-table class="tb-hotel-list" :rows="rowData" :columns="columns" :config="config">
                                    <template slot="sort-asc-icon">
                                        <i class="fas fa-sort-up"></i>
                                    </template>
                                    <template slot="sort-desc-icon">
                                        <i class="fas fa-sort-down"></i>
                                    </template>
                                    <template slot="no-sort-icon">
                                        <i class="fas fa-sort"></i>
                                    </template>
                                    <template slot="column_id" slot-scope="props">
                                        {{props.column.label}}&nbsp;
                                        <i class="fab fa-slack-hash"></i>
                                    </template>
                                    <template slot="column_name" slot-scope="props">
                                        {{props.column.label}}&nbsp;
                                        <i class="fas fa-user-friends"></i>
                                    </template>
                                    <template slot="column_checkInCheckOut" slot-scope="props">
                                        {{props.column.label}}&nbsp;
                                        <i class="fas fa-calendar-alt"></i>
                                    </template>
                                    <template slot="column_actions" slot-scope="props">
                                        {{props.column.label}}&nbsp;
                                        <i class="fas fa-external-link-square-alt"></i>
                                    </template>
                                    <template slot="actions" slot-scope="props">
                                        <button @click.prevent="viewDetails(props.cell_value)" class="btn btn-outline-primary btn-flat btn-action"><i class="fas fa-eye"></i> View</button>
                                    </template>
                                </vue-bootstrap4-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</template>
<script>
import Vodal from 'vodal'
export default {
    components: {
        Vodal
    },
    data() {
        return {
            vodal_show: false,
            singlebooking: [],
            rowData:[],
            summaryInfo: [
                            {count:0, label:'Active', iconClass:'fa-grin-hearts', class:'icon-active'},
                            {count:0, label:'Pending', iconClass:'fa-frown-open', class:'icon-pending'}
                        ],
                columns: [{
                        label: "Id",
                        name: "id",
                        filter: {
                            type: "simple",
                            placeholder: "id"
                        },
                        sort: true,
                        slot_name: "id",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "",
                    },
                    {
                        label: "Name",
                        name: "name",
                        filter: {
                            type: "simple",
                            placeholder: "Enter guest name"
                        },
                        sort: true,
                        slot_name: "name",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "",
                    },
                    {
                        label: "Check-In & Check-Out",
                        name: "checkInCheckOut",
                        filter: {
                            type: "simple",
                            placeholder: "Enter guest name"
                        },
                        sort: true,
                        slot_name: "checkInCheckOut",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "",
                    },
                    {
                        label: "Actions",
                        name: "id",
                        sort: false,
                        slot_name: "actions",
                        row_text_alignment: "text-center",
                        column_text_alignment: "text-center",
                    }
                ],
                config: {
                checkbox_rows: false,
                rows_selectable: true,
                per_page: 10,
                show_refresh_button: false,
                show_reset_button: false,
                highlight_row_hover_color:'rgba(214, 214, 214, 0.26)'
            }
        }
    },
    watch: {
        'vodal_show': function(newVal, oldValue) { if(newVal==false) $('body').removeClass('overflow-hidden'); } 
    },
    methods: {

        dynamicSemecolon(data, key) {
            return (JSON.parse(data).length-1==key)?'':', ';
        },

        singleDetails(id) {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                axios.get('/api/single-booking-details/'+id)
                .then(
                    function (response) {
                        self.singlebooking = response.data;
                        console.log(self.singlebooking);
                    }
                );
            }
        },
        
        viewDetails(id) { 
            this.singleDetails(id);
            this.vodal_show = true;
            $('body').addClass('overflow-hidden');
        },

        loadBookings() {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                axios.get('/api/bookings')
                .then(
                    function (response) {
                        response.data.forEach(function(item, key){
                            self.rowData.push({ id:item.id, name:item.name, checkInCheckOut:moment(item.dateStart).format("MMM Do YY")+' - '+moment(item.dateEnd).format("MMM Do YY") });
                        });
                    }
                );
            }
        }

    },
    beforeCreate() {
        //
    },
    created(){
        //
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
    mounted() {
        this.loadBookings();
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
<style scoped>
    ul.booking-details {
        text-align: left;
    }
</style>