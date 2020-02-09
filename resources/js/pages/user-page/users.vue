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
                    <router-link to="/add-user">
                        <button class="btn btn-outline-primary btn-flat">
                            <i class="fa fa-plus-circle"></i> Add
                        </button>
                    </router-link>
                    <router-link to="/users-capability">
                        <button class="btn btn-outline-primary btn-flat">
                            Capability <i class="fa fa-arrow-right"></i>
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
                            <i class="fas fa-user-tie"></i>
                        </template>
                        <template slot="column_email" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-at"></i>
                        </template>
                        <template slot="column_role" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-user-tag"></i>
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
                            <router-link :to="`/edit-user/${props.cell_value}`"  class="btn btn-outline-primary btn-flat btn-action"><i class="fa fa-edit"></i> Edit</router-link>
                            <a href="#" @click.prevent="selectUser(props.cell_value)" :data-id="props.cell_value" class="btn btn-outline-danger btn-flat btn-action"><i class="fa fa-trash"></i> Delete</a>
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
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css'
    export default {
        components: {
            Loading
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
</script>
<style scoped>
    .user-row-class-id {
        width: 150px;
    }
    td.text-left.user-row-class-name {
        width: 250px;
    }
</style>
