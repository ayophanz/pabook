<template>
    <div class="row justify-content-center">
        <result-page-icon></result-page-icon>
        <div class="col-12">
          <notice-msg></notice-msg>
          <div class="card">
            <div class="card-header">
                <div class="card-tool">
                    <router-link to="/add-room-type">
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
                        <template slot="column_room_type" slot-scope="props">
                            {{props.column.label}}&nbsp;
                            <i class="fas fa-bed"></i>
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
                            <router-link :to="`/edit-room-type/${props.cell_value}`"  class="btn btn-outline-primary btn-flat btn-action"><i class="fa fa-edit"></i> Edit</router-link>
                            <a href="#" @click.prevent="selectType(props.cell_value)" :data-id="props.cell_value" class="btn btn-outline-danger btn-flat btn-action"><i class="fa fa-trash"></i> Delete</a>
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
                if(this.$gate.superAdminOrhotelOwner()) {
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
                if(this.$gate.superAdminOrhotelOwner()) {
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
</script>
<style lang="scss">
    .room-types-row-class-id {
        width: 150px;
    }
    .room-types-row-class-hotel {
        width: 300px;
    }
</style>