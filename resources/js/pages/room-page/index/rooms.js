import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';
    import MoneyFormat from 'vue-money-format';
    import summaryInfo from '../../../components/summaryInfoComponent';
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
        components: {
             Loading,
             MoneyFormat,
             summaryInfo,
             VueBootstrap4Table
        },
        data() {
            return {
                lang: 'en',
                base_currency: 'USD',
                fullPage: true,
                isLoading: false,
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
                if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                    let self = this
                    axios.get('/api/rooms')
                    .then(
                        function (response) {
                            self.rowData = response.data;
                            response.data.forEach(function(item, key){
                                if(item.status=='active') self.summaryInfo[0].count+=1;
                                else if(item.status=='pending') self.summaryInfo[1].count+=1;
                            });
                        }
                    );
                }
            },
            deleteRoom(roomId) {
                if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
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
        mounted() {

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