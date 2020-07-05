import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import VueBootstrap4Table from 'vue-bootstrap4-table';
export default {
    components: {
        Loading,
        VueBootstrap4Table
    },
    data() {
        return {
            //users : '',
            fullPage: true,
            isLoading: false,
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
                        row_classes: "user-row-class-id",
                    },
                    {
                        label: "Fullname",
                        name: "name",
                        filter: {
                            type: "simple",
                            placeholder: "Enter name"
                        },
                        sort: true,
                        slot_name: "name",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "user-row-class-name",
                    },
                    {
                        label: "Email",
                        name: "email",
                        sort: true,
                        slot_name: "email",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "user-row-class-email",
                    },
                    {
                        label: "Role",
                        name: "role",
                        sort: true,
                        slot_name: "role",
                        row_text_alignment: "text-left",
                        column_text_alignment: "text-left",
                        row_classes: "user-row-class-role",
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
                        row_classes: "user-row-class-date",
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
        loadUsers() {
            if(this.$gate.superAdminOrhotelOwner()) {
                let self = this
                axios.get('/api/users')
                .then(
                    function (response) {
                        //self.users = response.data
                        self.rowData = response.data
                    }
                );
            }
        },
        deleteUser(userId) {
            if(this.$gate.superAdminOrhotelOwner()) {
                this.isLoading = true;
                let self = this
                axios.delete('/api/delete-user/'+userId)
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
        selectUser(userId) {
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
                this.deleteUser(userId);
                }
            })
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.loadUsers(); 
        fire.$on('afterCreated',() => {this.loadUsers()});   
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