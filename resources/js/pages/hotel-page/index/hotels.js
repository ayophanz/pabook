
// import styles from 'vue-pure-lightbox/dist/VuePureLightbox.css'
// import VuePureLightbox from 'vue-pure-lightbox'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import VueBootstrap4Table from 'vue-bootstrap4-table';
import summaryInfo from '../../../components/summaryInfoComponent';
export default {
    components:{
        //VuePureLightbox,
        Loading,
        VueBootstrap4Table,
        summaryInfo
    },
    data() {
        return {
            rowData:[],
            summaryInfo: [
                            {count:0, label:'Verified', iconClass:'fa-shield-alt', class:'icon-verified'},
                            {count:0, label:'Email Confirmation', iconClass:'fa-envelope-open-text', class:'icon-email'},
                            {count:0, label:'Document Verifying', iconClass:'fa-file-alt', class:'icon-document'}
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
                                "name" : "Email confirmation",
                                "value" : "email_verifying"
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
                        response.data.forEach(function(item, key){
                            if(item.status=='verified') self.summaryInfo[0].count+=1;
                            else if(item.status=='email_verifying') self.summaryInfo[1].count+=1;
                            else if(item.status=='verifying') self.summaryInfo[2].count+=1;
                        });
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
            });
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.loadHotels();
        fire.$on('HotelCreated',() => {this.loadHotels()});
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