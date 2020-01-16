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
                        <template slot="column_hotel" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-hotel"></i>
                        </template>
                        <template slot="column_room" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-bed"></i>
                        </template>
                        <template slot="column_room_type" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-door-closed"></i>
                        </template>
                        <template slot="column_total_room" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-hashtag"></i>
                        </template>
                        <template slot="column_status" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-shield-alt"></i>
                        </template>
                        <template slot="column_created_at" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-table"></i>
                        </template>
                        <template slot="column_actions" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-external-link-square-alt"></i>
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
    //import styles from 'vue-pure-lightbox/dist/VuePureLightbox.css'
    //import VuePureLightbox from 'vue-pure-lightbox'
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    import MoneyFormat from 'vue-money-format'
    export default {
        components: {
            // VuePureLightbox,
             Loading,
             MoneyFormat
        },
        data() {
            return {
                lang: 'en',
                base_currency: 'USD',
                fullPage: true,
                isLoading: false,
                //rooms: [],
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
                            row_classes: "room-row-class-id",
                        },
                        {
                            label: "Hotel",
                            name: "room_type.room_type_hotel.name",
                            filter: {
                                type: "simple",
                                placeholder: "Enter hotel name"
                            },
                            sort: true,
                            slot_name: "hotel",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "room-row-class-hotel",
                        },
                        {
                            label: "Room",
                            name: "name",
                            filter: {
                                type: "simple",
                                placeholder: "Enter room name"
                            },
                            sort: true,
                            slot_name: "room",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "room-row-class-room",
                        },
                        {
                            label: "Room Type",
                            name: "room_type.name",
                            filter: {
                                type: "simple",
                                placeholder: "Enter room type"
                            },
                            sort: true,
                            slot_name: "room_type",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "room-row-class-name-type",
                        },
                        {
                            label: "Total room",
                            name: "total_room",
                            sort: true,
                            slot_name: "total_room",
                            row_text_alignment: "text-left",
                            column_text_alignment: "text-left",
                            row_classes: "room-row-class-number",
                        },
                        {
                            label: "Status",
                            name: "status",
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
                            row_classes: "room-row-class-date",
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
            getImgUrl(image, id) {
                return '/storage/images/upload/roomImages/gallery-'+id+'/'+image+'?rnd'+new Date().getTime();
            },
            loadRooms() {
                if(this.$gate.superAdminOrhotelOwner()) {
                    let self = this
                    axios.get('/api/rooms')
                    .then(
                        function (response) {
                            //self.rooms = response.data;
                            self.rowData = response.data;
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
        beforeCreate() {
          //
        },
        created(){
          this.loadRooms();
          fire.$on('afterCreated',() => {this.loadRooms()});
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
<style lang='scss'>
    .room-row-class-id {
        width: 150px;   
    }
    .room-row-class-hotel, .room-row-class-room {
        width: 200px;
    }
    .room-row-class-name-type {
        width: 200px;
    }
    .room-row-class-number {
        width: 150px;
    }
    .room-row-class-date {
        width: 160px;
    }
</style>
