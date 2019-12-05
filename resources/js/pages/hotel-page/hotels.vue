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
                            <i class="fas fa-hotel"></i>
                        </template>
                        <template slot="column_phone" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-phone"></i>
                        </template>
                        <template slot="column_email" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-at"></i>
                        </template>
                        <template slot="column_created_at" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-table"></i>
                        </template>
                        <template slot="column_status" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-external-link-square-alt"></i>
                        </template>
                        <template slot="status" slot-scope="props">
                            {{(props.cell_value=='verifying')? 'docx verifying': (props.cell_value=='email_verifying')? 'confirm email': props.cell_value}}&nbsp;
                        </template>
                        <template slot="column_actions" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-external-link-square-alt"></i>
                        </template>
                        <template slot="actions" slot-scope="props">
                            <router-link :to="`/edit-hotel/${props.cell_value}`"  class="btn btn-outline-primary btn-flat btn-action"><i class="fa fa-edit"></i> Edit</router-link>
                            <a href="#" @click.prevent="selectHotel(props.cell_value)" :data-id="props.cell_value" class="btn btn-outline-danger btn-flat btn-action"><i class="fa fa-trash"></i> Delete</a>
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
    import styles from 'vue-pure-lightbox/dist/VuePureLightbox.css'
    import VuePureLightbox from 'vue-pure-lightbox'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    import VueBootstrap4Table from 'vue-bootstrap4-table'
    export default {
        components:{
            //VuePureLightbox,
            Loading,
            VueBootstrap4Table
        },
        data() {
            return {
                rowData:[],
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
                            row_classes: "booking-row-class-id",
                        },
                        {
                            label: "Name",
                            name: "name",
                            filter: {
                                type: "simple",
                                placeholder: "Enter hotel name"
                            },
                            sort: true,
                            slot_name: "name",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "booking-row-class-name",
                        },
                        {
                            label: "Phone No.",
                            name: "phone_number",
                            sort: true,
                            slot_name: "phone",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                        },
                        {
                            label: "Email",
                            name: "email",
                            sort: true,
                            slot_name: "email",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                        },
                        {
                            label: "Status",
                            name: "status",
                            filter: {
                                type: "select",
                                placeholder: "Filter status",
                                mode: "multi",
                                options:[
                                {
                                    "name" : "Document verifying",
                                    "value" : "verifying"
                                },
                                {
                                    "name" : "Email verifying",
                                    "value" : "email_verifiying"
                                },
                                {
                                    "name" : "Verified",
                                    "value" : "verified"
                                }
                                ],
                                select_all_checkbox : {
                                    visibility: true,
                                    text: "Select all items"
                                },
                            },
                            sort: true,
                            slot_name: "status",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                        },
                        {
                            label: "Created at",
                            name: "created_at",
                            sort: true,
                            slot_name: "created_at",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
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
                },
                //hotels: '',
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
                            //self.hotels = response.data
                            self.rowData = response.data
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
<style lang='scss'>
    .booking-row-class-id {
        width: 150px !important;
    }

    .booking-row-class-country, .booking-row-class-name {
        width: 250px;
    }
    .tb-hotel-list .card-header {
        display: none;
    }
    .tb-hotel-list table thead tr:first-child th {
        border-top: 0px;
    }
    .page-item.active .page-link {
        z-index: 1;
        color: #fff;
        background-color: #32cc98;
        border-color: #38c172;
    }
    .page-link:focus {
        z-index: 2;
        outline: 0;
        box-shadow: 0 0 0 0.2rem #38c17259;
    }
    .table thead tr:first-child th {
        display: none;
    }
    ul.pagination div:last-child {
        display: none;
    }
</style>
