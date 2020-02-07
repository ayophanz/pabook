<template>
    <div id="root">
        <vodal :closeOnClickMask="false" :show="vodal_show" animation="zoom" :closeOnEsc="false" :width="600" className="vodal-style" @hide="vodal_show = false">
            <div class="swal2-header">
                <div class="swal2-icon swal2-info swal2-animate-info-icon" style="display: flex;"></div>
                <h4 class="swal2-title" style="display: flex;"><strong>Booking details</strong></h4>
            </div>
            <div class="swal2-content">
                <br />
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
                                        <button @click.prevent="viewDetails" class="btn btn-outline-primary btn-flat btn-action"><i class="fas fa-eye"></i> View</button>
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
            bookings: [],
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
    methods: {
        
        viewDetails() { 
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