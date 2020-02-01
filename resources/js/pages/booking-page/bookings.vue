<template>
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
                                <template slot="column_dateStart" slot-scope="props">
                                    {{props.column.label}}&nbsp;
                                    <i class="fas fa-calendar-alt"></i>
                                </template>
                                <template slot="column_dateEnd" slot-scope="props">
                                    {{props.column.label}}&nbsp;
                                    <i class="fas fa-calendar-alt"></i>
                                </template>
                                <template slot="actions" slot-scope="props">
                                    <router-link :to="`/edit-room/${props.cell_value}`"  class="btn btn-outline-primary btn-flat btn-action"><i class="fa fa-edit"></i> Edit</router-link>
                                    <a href="#" @click.prevent="selectRoom(props.cell_value)" :data-id="props.cell_value" class="btn btn-outline-danger btn-flat btn-action"><i class="fa fa-trash"></i> Delete</a>
                                </template>
                            </vue-bootstrap4-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</template>
<script>
    export default {
        components: {
            //
        },
        data() {
            return {
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
                            label: "Check-In",
                            name: "dateStart",
                            filter: {
                                type: "simple",
                                placeholder: "Enter guest name"
                            },
                            sort: true,
                            slot_name: "dateStart",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "",
                        },
                        {
                            label: "Check-Out",
                            name: "dateEnd",
                            filter: {
                                type: "simple",
                                placeholder: "Enter guest name"
                            },
                            sort: true,
                            slot_name: "dateEnd",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "",
                        },
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
            loadBookings() {
                if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                    let self = this
                    axios.get('/api/bookings')
                    .then(
                        function (response) {
                            self.rowData = response.data;
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