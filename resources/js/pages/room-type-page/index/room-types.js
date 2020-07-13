import VueBootstrap4Table from 'vue-bootstrap4-table';
export default {
    components: {
        VueBootstrap4Table
    },
    data() {
        return {
            //types: [],
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
                        row_classes: "room-types-row-class-id",
                    },
                    {
                        label: "Hotel name",
                        name: "room_type_hotel.name",
                        filter: {
                            type: "simple",
                            placeholder: "Enter hotel name"
                        },
                        sort: true,
                        slot_name: "hotel",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "room-types-row-class-hotel",
                    },
                    {
                        label: "Room Type",
                        name: "name",
                        sort: true,
                        slot_name: "room_type",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "room-types-row-class-name",
                    },
                    {
                        label: "Created at",
                        name: "created_at",
                        sort: true,
                        slot_name: "created_at",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "room-types-row-class-date",
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
        loadTypes() {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                let self = this
                axios.get('/api/room-types')
                .then(
                    function (response) {
                        //self.types = response.data
                        self.rowData = response.data
                    }
                );
            }
        },
        deleteType(typeId) {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                axios.delete('/api/delete-type/'+typeId)
                .then(
                    function (response) {
                        fire.$emit('afterCreated');
                    }
                );
                toast.fire({
                    type: 'success',
                    title: 'User deleted successfully'
                })
            }
        },
        selectType(typeId) {
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
                this.deleteType(typeId);
                }
            })
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.loadTypes();
        fire.$on('afterCreated',() => {this.loadTypes()});
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