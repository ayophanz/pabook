import Vodal from 'vodal'
import VueBootstrap4Table from 'vue-bootstrap4-table'
export default {
    components: {
        Vodal,
        VueBootstrap4Table,
    },
    data() {
        return {
            vodal_show: false,
            singlebooking: [],
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
                    rows_selectable: false,
                    per_page: 10,
                    show_refresh_button: false,
                    show_reset_button: false,
                    highlight_row_hover_color:'rgba(214, 214, 214, 0.26)',
                    per_page_options: [5, 10, 20, 30],
                }
        }
    },
    watch: {
        // 'vodal_show': function(newVal, oldValue) { if(newVal==false) $('body').removeClass('overflow-hidden'); } 
    },
    methods: {

        arrayExtract(data){
            let self = this
            let output = '';
            data.forEach(function(item, key){
               output += item+self.dynamicSemecolon(data, key, false);
            });
            return output;
        },

        optionalAmenTotal(data) {
            let total = 0;
            data.forEach(function(item, key){ total += item.price * item.rooms.length; });
            return total;
        },

        night(dateStart, dateEnd) {
            return Math.ceil(Math.abs(new Date(moment(dateEnd).format()) - new Date(moment(dateStart).format())) / (1000 * 60 * 60 * 24)); 
        },

        dynamicSemecolon(data, key, parseArray=true) {
            let dataTemp = data;
            if(parseArray) dataTemp = JSON.parse(data);
            return (dataTemp.length-1==key)?'':', ';
        },

        singleDetails(id) {
            if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
                let self = this
                axios.get('/api/single-booking-details/'+id)
                .then(
                    function (response) {
                        self.singlebooking = response.data;
                        console.log(self.singlebooking);
                    }
                );
            }
        },
        
        viewDetails(id) { 
            this.singleDetails(id);
            this.vodal_show = true;
            $('body').addClass('overflow-hidden');
        },

        loadBookings() {
            if(this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
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