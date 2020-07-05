import ExcludeOptionalAmen from '../../../components/excludeOptionalAmenComponent'
import 'tui-calendar/dist/tui-calendar.css'
import { Calendar } from '@toast-ui/vue-calendar'
import 'tui-date-picker/dist/tui-date-picker.css'
import HotelDatePicker from 'vue-hotel-datepicker'
import Select2 from 'v-select2-component'
import Loading from 'vue-loading-overlay'
import Multiselect from 'vue-multiselect'
import PrettyCheck from 'pretty-checkbox-vue/check'
import Vodal from 'vodal'
import { parse } from 'path'
import { setImmediate } from 'timers'
export default {
    components: {
        Calendar,
        HotelDatePicker,
        Select2,
        Multiselect,
        ExcludeOptionalAmen,
        Vodal,
        PrettyCheck,
        Loading
    },
    data() {
        return {
            fullPage: true,
            isLoading: false,
            hotels:[],
            roomWithRoomTypes:[],
            manyRooms: [],
            totalRooms: [],
            manyAdults:[],
            totalAdults:[],
            manyChilds:[],
            totalChilds:[],
            tempFixedAmenities: [],
            tempOptionalAmenities: [],
            fixedAmenities: [],
            optionalAmenities: [],
            disNext: true,
            disPrev: true,
            tempData: [],
            roomPrices:[],
            roomPrice: 0,
            nightNo: 0,
            currency: '',
            no_unit_avail: 0,
            tempRoomOptions: [],
            rooms_options: [],
            vodal_show: false,
            form: new form({
                hotel: '',
                checkInD: new Date().toString(),
                checkOutD: new Date(new Date().getTime()+(60 * 60 * 24 * 1000)).toString(),
                manyAdult: '',
                manyChild: '',
                manyRoom:'',
                currency_use: '',
                roomWithRoomType:'',
                name:'',
                phoneNo: '',
                email: '',
                rooms_no: [],
                addOnOptionalAmen: [],
                totalAmount: 0
                
            }),
            calendarList: [
                {
                    id: '0',
                    name: 'reserved'
                },
                {
                    id: '1',
                    name: 'occupied',
                    color: 'white',
                    bgColor: '#3490dc',
                    borderColor: '#3490dc',
                    dragBgColor: '#9e5fff'
                },
                {
                    id: '2',
                    name: 'incomplete',
                    color: 'white',
                    bgColor: 'red',
                    borderColor: '#3490dc',
                    dragBgColor: '#9e5fff'
                },
                {
                    id: '3',
                    name: 'unknown',
                    color: 'white',
                    bgColor: 'red',
                    borderColor: 'red',
                    dragBgColor: 'red'
                }
            ],
            scheduleList: [],
            viewModeOptions: [
                {
                title: 'Monthly',
                value: 'month'
                },
                {
                title: 'Weekly',
                value: 'week'
                },
                {
                title: 'Daily',
                value: 'day'
                }
            ],
            dateRange: '',
            selectedView: 'month',
            theme: {
                // month header 'dayname'
                'month.dayname.height': '42px',
                'month.dayname.borderLeft': 'none',
                'month.dayname.paddingLeft': '8px',
                'month.dayname.paddingRight': '0',
                'month.dayname.fontSize': '13px',
                'month.dayname.backgroundColor': 'inherit',
                'month.dayname.fontWeight': 'normal',
                'month.dayname.textAlign': 'left',

                // month day grid cell 'day'
                'month.holidayExceptThisMonth.color': '#f3acac',
                'month.dayExceptThisMonth.color': '#bbb',
                'month.weekend.backgroundColor': '#fafafa',
                'month.day.fontSize': '16px',

                // month schedule style
                'month.schedule.height': '18px',
                'month.schedule.marginTop': '2px',
                'month.schedule.marginLeft': '10px',
                'month.schedule.marginRight': '10px',

                // month more view
                'month.moreView.boxShadow': 'none',
                'month.moreView.paddingBottom': '0',
                'month.moreView.border': '1px solid #9a935a',
                'month.moreView.backgroundColor': '#f9f3c6',
                'month.moreViewTitle.height': '28px',
                'month.moreViewTitle.marginBottom': '0',
                'month.moreViewTitle.backgroundColor': '#f4f4f4',
                'month.moreViewTitle.borderBottom': '1px solid #ddd',
                'month.moreViewTitle.padding': '0 10px',
                'month.moreViewList.padding': '10px'
            }
        }
    },
    watch: {

        'selectedView': function(e) {
            this.$refs.mycalendar.invoke('changeView', e, true);
            this.setRenderRangeText();
        },

        'form.hotel': function() { 
            this.form.roomWithRoomType = [];
            this.roomWithRoomTypes = [];
            this.currency = ''; 
        },

        'form.roomWithRoomType': function() { 
           this.form.manyRoom = ''; 
            this.$store.state.currencyStore = this.currency;
        },
        
        'form.manyRoom': function() {
            this.form.rooms_no = []; 
            this.form.addOnOptionalAmen = []; 
            this.optionalAmenities.forEach(function(item, key){item.rooms = [];});
        },

        'form.rooms_no': function(newVal, oldVal) {
            let self = this;
            if(newVal.length==1 && newVal.length < oldVal.length) {
                 this.$store.state.optionalAmenStore.forEach(function(item, key){
                    if(item.isVisible==true) 
                        item.optAmen.forEach(function(item2, key2){ 
                            self.onRemoveOrAdd(['undo', item2.id, item.room]);
                            let isChecked = document.getElementById('optionalAmenItem-'+key+'-'+key2);
                            if(isChecked) isChecked.getElementsByTagName('input')[0].checked = true; 
                        });
                });
            }else if(newVal.length==0) this.$store.commit('emptyOptionAmenMutat');
        },

        'vodal_show': function(newVal, oldValue) { if(newVal==false) $('body').removeClass('overflow-hidden'); } 
            

    },
    computed: {

        nightNoFunc(){
            return parseInt(Math.ceil(Math.abs(new Date(this.form.checkOutD) - new Date(this.form.checkInD)) / (1000 * 60 * 60 * 24)));
        },

        totalAmountFunc() {
            let optionalAmenPrices = 0;
            this.form.addOnOptionalAmen.forEach(function(item, key){
                optionalAmenPrices += (parseFloat(item.price) * item.rooms.length);
            });
            return parseFloat((parseFloat(this.roomPrice) * this.nightNoFunc) * this.roomNoFunc) + optionalAmenPrices;
        },

        roomNoFunc() { return parseInt(this.form.manyRoom); },

        compareDate() {
            if(this.form.checkOutD!='' && Date.parse(new Date(this.form.checkOutD.getFullYear()+'-'+(this.form.checkOutD.getMonth()+1))) > Date.parse(new Date(this.dateRange))) this.disNext = false;
            else this.disNext = true;
            if(this.form.checkInD!='' && Date.parse(new Date(this.form.checkInD.getFullYear()+'-'+(this.form.checkInD.getMonth()+1))) < Date.parse(new Date(this.dateRange))) this.disPrev = false;
            else this.disPrev = true;
        }

    },
    methods: {

        resetData() {
            Object.assign(this.$data, this.$options.data.apply(this));
            this.loadHotels();
            this.selectedMonth = new Date();
            this.form.checkInD = new Date().toString();
            this.form.checkOutD = new Date(new Date().getTime()+(60 * 60 * 24 * 1000)).toString();
            this.setRenderRangeText();
            if(this.$store.getters.bookingPagiGett=='page_2') {
                this.$store.commit('summaryDetailsMutat', '');
                this.$store.commit('bookingPagiMutat', 'page_1');
            }
        },

        nightsNo(startD, endD) {
            const date1 = new Date(startD);
            const date2 = new Date(endD);
            const diffTime = Math.abs(date2 - date1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
            return diffDays;
        },

        onCheckOptionalAmen(e) { this.$store.commit('visibleOptionalAmenMutat', e); },
         
        excludeOptional() { 
            this.vodal_show = true; 
            $('body').addClass('overflow-hidden');
        },

        onRemoveOrAdd(value) {
            this.optionalAmenities.forEach(function(item, key){
                let exist = item.rooms.indexOf(value[2]) !== -1;
                if(item.id == value[1] && value[0]=='remove' && exist==true) item.rooms.splice(item.rooms.indexOf(value[2]), 1);
                else if(item.id == value[1] && value[0]=='undo' && exist==false) item.rooms.push(value[2]);
            });
        },

        roomsNoOnAdd(e) {
            this.no_unit_avail++;
            this.optionalAmenMani('undo', e.value);
        },

        roomsNoOnRemove(e) {
            this.no_unit_avail--;
            this.optionalAmenMani('remove', e.value);
        },

        optionalAmenMani(action, value) {
            let self = this;
            if(action=='remove') {
                this.optionalAmenities.forEach(function(item, key){
                    if(item.rooms.indexOf(value) !== -1) item.rooms.splice(item.rooms.indexOf(value), 1);
                    item.isChecked = true;
                });
                this.$store.commit('hideRoomNoMutat', value);
            }else if(action=='undo') {
                this.$store.commit('addRoomNoMutat', {room:value, optAmen:this.$store.getters.whenAddRoomNoGett, isVisible:true});
                this.optionalAmenities.forEach(function(item, key){ item.rooms.push(value); });
            }
        },

        validateEntries() {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                if(this.$store.getters.bookingPagiGett=='page_1') {
                    this.isLoading = true;
                    this.form.currency_use = this.currency;
                    this.form.totalAmount = this.totalAmountFunc;
                    this.form.checkInD = moment(this.form.checkInD).format('YYYY-MM-DD HH:MM:SS');
                    this.form.checkOutD = moment(this.form.checkOutD).format('YYYY-MM-DD HH:MM:SS');
                    this.form.post('/api/create-book').then(function (response) {
                        self.isLoading = false;
                        toast.fire({
                            timer: 2000,
                            type: 'success',
                            title: 'Room availability confirmed.'
                        })
                        self.$store.commit('summaryDetailsMutat', response.data);
                        self.$store.commit('bookingPagiMutat', 'page_2');
                    }).catch(function (error) {
                        self.isLoading = false;
                            toast.fire({
                            type: 'error',
                            title: 'Something went wrong!'
                        })
                    });
                }else if(this.$store.getters.bookingPagiGett=='page_2') {
                    this.form.post('/api/save-continue-booking/'+this.$store.getters.summaryDetailsGett[0].id).then(function (response) {
                        self.isLoading = false;
                            toast.fire({
                            timer:3000,
                            type: 'success',
                            title: 'Successful Booked.'
                        })
                        self.resetData();
                        fire.$emit('refreshNoticeMsg');
                    }).catch(function (error) {
                        self.isLoading = false;
                            toast.fire({
                            type: 'error',
                            title: 'Something went wrong!'
                        })
                    });
                }
            }
        },

        pickerClose(){
            this.form.checkInD = '';
            this.form.checkOutD = '';
            this.disPrev = true;
            this.disNext = true;
            this.$refs.mycalendar.invoke('setDate', new Date(), true);
            this.setRenderRangeText();
        },

        generateList(param, kind) {
            let tempParam = [];
            if (param.length > 0) {
                let tempList = (kind!='roomNo') ? param.find(e => parseInt(e.id) === parseInt(this.form.roomWithRoomType)).value : [];
                if(kind=='price')  tempParam.push(tempList);
                else if(kind=='roomNo') {
                    let self = this
                    param.forEach(function(item, key){ 
                        if(parseInt(item.id)==parseInt(self.form.roomWithRoomType)) {
                            const found = tempParam.some(el => el.value === item.value.value);
                            if (!found) tempParam.push(item.value);
                        }
                    });
                }
                else if(kind=='value') tempList.forEach(function(item, key){ tempParam.push(item.value); });
                else if(kind=='optional') tempList.forEach(function(item, key){ item['isVisible']=false; item['isChecked']=true; item['id']=(key+1);item['rooms']=[]; tempParam.push(item); });
                else if(kind=='total') for(let i=1;i<=parseInt(tempList);i++) tempParam.push({id:i, text:i});
                else if(kind=='many') for(let i=1;i<=(parseInt(tempList)*this.form.manyRoom);i++) tempParam.push({id:i, text:i});
            } 
            return tempParam;
        },

        isManyRoom() {
            this.rooms_options = this.generateList(this.tempRoomOptions, 'roomNo');
            this.manyAdults = this.generateList(this.totalAdults, 'many');
            this.manyChilds = this.generateList(this.totalChilds, 'many');
            this.manyChilds.unshift({id:0, text:'0'});
        },

        isRoomWithRoomType() {
            this.roomPrice = parseFloat(this.generateList(this.roomPrices, 'price')[0]);
            this.manyRooms = this.generateList(this.totalRooms, 'total');
            this.fixedAmenities = this.generateList(this.tempFixedAmenities, 'value');
            this.optionalAmenities = this.generateList(this.tempOptionalAmenities, 'optional');
        },

        isHotelChange(){
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this;
                axios.get('/api/hotel-with-room-types/'+this.form.hotel).then(
                    function (response) {
                        response.data.forEach(function(item, key) {
                            self.currency = item.room_type_hotel.base_currency.value;
                            if([item.room_type_hotel]!='') {
                                [item.room_type_hotel].forEach(function(item2, key2){
                                    JSON.parse(item2.hotel_rooms_no).forEach(function(item3, key3){
                                        self.tempRoomOptions.push({id:item3.assign_id, value:item3});
                                    });
                                });
                            }
                            item.room_type_rooms.forEach(function(item2, key2){
                                self.roomPrices.push({id:item2.id, value:item2.price});
                                self.roomWithRoomTypes.push({id:item2.id, text:item2.name+' - '+item.name});
                                self.totalRooms.push({id:item2.id, value:item2.total_room, available:'none'});
                                self.totalAdults.push({id:item2.id, value:item2.max_adult});
                                self.totalChilds.push({id:item2.id, value:item2.max_child});
                                self.tempOptionalAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature_optional.value), rooms:[] });
                                self.tempFixedAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature.value)});
                            });
                        });
                    });
            }
        },

        loadHotels() {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                axios.get('/api/hotels').then(
                    function (response) {
                        response.data.forEach(item => {
                            if(item.status=='verified')
                                self.hotels.push({id:item.id, text:item.name});
                        });
                    }
                );
            }
        },

        loadBookings() {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self =  this
                axios.get('/api/bookings').then(
                    function (response) {
                        console.log(response.data);
                        response.data.forEach((item, key) => {
                            self.scheduleList.push({
                                id: key,
                                calendarId: (item.status=='active')? '1': (item.status=='incomplete')? '2': (item.status=='reserved')? '0': '3',
                                title: item.name+' | '+item.room.name,
                                category: 'time',
                                dueDateClass: '',
                                start: item.dateStart,
                                end: item.dateEnd
                            });
                        });
                    }
                )
            }
        },

        checkInDate(e) {
            if(e!=null) {
              this.form.checkInD = e;
              this.$refs.mycalendar.invoke('setDate', e, true);
              this.dateRange = `${e.getFullYear()}-${(e.getMonth()+1)}`;
            }
        },

        checkOutDate(e) {
            if(e!=null) {
              this.form.checkOutD = e;
            }
        },

        onClickNavi(event) {
            if (event.target.tagName === 'BUTTON') {
                const {target} = event;
                let action = target.dataset ? target.dataset.action : target.getAttribute('data-action');
                action = action.replace('move-', '');
                this.$refs.mycalendar.invoke(action);
                this.setRenderRangeText();
            }
        },
        
        setRenderRangeText() {
            const {invoke} = this.$refs.mycalendar;
            const view = invoke('getViewName');
            const calDate = invoke('getDate');
            const rangeStart = invoke('getDateRangeStart');
            const rangeEnd = invoke('getDateRangeEnd');
            let year = calDate.getFullYear();
            let month = calDate.getMonth() + 1;
            let date = calDate.getDate();
            let dateRangeText = '';
            let endMonth, endDate, start, end;
            switch (view) {
                case 'month':
                    dateRangeText = `${year}-${month}`;
                    break;
                case 'week':
                    year = rangeStart.getFullYear();
                    month = rangeStart.getMonth() + 1;
                    date = rangeStart.getDate();
                    endMonth = rangeEnd.getMonth() + 1;
                    endDate = rangeEnd.getDate();
                    start = `${year}-${month}-${date}`;
                    end = `${endMonth}-${endDate}`;
                    dateRangeText = `${start} ~ ${end}`;
                    break;
                default:
                    dateRangeText = `${year}-${month}-${date}`;
            }
            this.dateRange = dateRangeText;
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
        this.loadHotels();
        this.selectedMonth = new Date();
        this.setRenderRangeText();

        fire.$on('bookingResetData', this.resetData);
        //this.$refs.mycalendar.usageStatistics = false;
        
        /**
         * jQuery
         * */ 
        let self = this
        $(document).on('click', '.datepicker__clear-button', function(e){
            self.pickerClose();
        });
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