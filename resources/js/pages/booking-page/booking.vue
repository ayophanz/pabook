<template>
    <div id="root">
        <div class="row justify-content-center">
            <booking-page-icon></booking-page-icon>
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-header">
                        <div class="card-tool">
                            <router-link to="/add-book-entry">
                                <button class="btn btn-outline-primary btn-flat">
                                    <i class="fa fa-plus-circle"></i> Add
                                </button>
                            </router-link>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <HotelDatePicker 
                                    @check-in-changed="checkInDate"
                                    @check-out-changed="checkOutDate"
                                    format="YYYY MMM. DD"
                                    :startDate="new Date()"
                                    :minNights="1"
                                    :maxNights="30"
                                    :closeDatepickerOnClickOutside="false"
                                    :showCloseButton="true"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="hotel">Hotel</label>
                                    <Select2 id="hotel" v-model="hotel" :options="hotels" :settings="{ placeholder: 'Please select hotel', containerCssClass:'form-control' }" @change="isHotelChange" />
                                </div>
                                <div class="form-group">
                                    <label for="roomWithRoomType">Room</label>
                                    <Select2 id="roomWithRoomType" v-model="form.roomWithRoomType" :options=" roomWithRoomTypes" :settings="{ placeholder: 'Please select room', containerCssClass:'form-control' }" @change="isRoomWithRoomType" />
                                </div>
                                <div class="form-group">
                                    <label for="manyRoom">No. of room</label>
                                    <Select2 id="manyRoom" v-model="form.manyRoom" :options="(form.roomWithRoomType!='' ? manyRooms:[])" :settings="{ placeholder: 'Please select how many rooms', containerCssClass:'form-control' }" />
                                </div>
                                <div class="form-group">
                                    <label for="manyAdult">No. of adult</label>
                                    <Select2 id="manyAdult" v-model="form.manyAdult" :options="(form.manyRoom!='' ? manyAdults:[])" :settings="{ placeholder: 'Please select how many adult(s)', containerCssClass:'form-control' }" />
                                </div>
                                <div class="form-group">
                                    <label for="manyChild">No. of child</label>
                                    <Select2 id="manyChild" v-model="form.manyChild" :options="(form.manyAdult!='' ? manyChilds:[])" :settings="{ placeholder: 'Please select how many child(s)', containerCssClass:'form-control' }" />
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="container book-rooms-quantity">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Total Rooms</h5>
                                                    <span>{{manyRooms.length}}</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5>Available Rooms</h5>
                                                    <span>6</span>
                                                </div>
                                                <div class="col-md-12">
                                                    <h5>Booked Rooms</h5>
                                                    <span>2</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                         <div class="container book-rooms-quantity">
                                             <div class="row">
                                                 <div class="col-md-12">
                                                     <h5>Fixed amenities</h5>
                                                     <ul>
                                                         <li v-for="item in fixedAmenities">{{item}}</li>
                                                     </ul>
                                                 </div>
                                                 <div class="col-md-12">
                                                     <h5>Optional amenities</h5>
                                                     <ul>
                                                          <li v-for="item in optionalAmenities">{{item}}</li>
                                                     </ul>
                                                 </div>
                                             </div>
                                         </div>
                                    </div>
                                </div>
                                <div class="form-group text-center mt-4">
                                    <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fas fa-concierge-bell"></i> Book Room</button>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div id="calendar-menu">
                                    <select v-model="selectedView" class="menuSelectedView">
                                        <option v-for="(options, index) in viewModeOptions" :value="options.value" :key="index">{{options.title}}</option>
                                    </select>
                                    <span id="menu-navi" @click="onClickNavi($event)">
                                        <!-- <button type="button" class="btn btn-default btn-sm move-today" data-action="move-today">Today</button> -->
                                        &nbsp;<button v-if="disPrev==false" :disabled="disPrev" type="button" class="btn btn-outline-primary btn-flat move-day" data-action="move-prev"><i class="fas fa-chevron-left" data-action="move-prev"></i> Prev</button>
                                        &nbsp;<button v-if="disNext==false" :disabled="disNext" type="button" class="btn btn-outline-primary btn-flat move-day" data-action="move-next">Next <i class="fas fa-chevron-right" data-action="move-next"></i></button>
                                        &nbsp;
                                    </span>
                                    <span class="render-range">{{dateRange}}</span>
                                </div>
                                <calendar ref="mycalendar" style="height: 800px;"
                                    :view="selectedView"
                                    :theme="theme"
                                    :calendars="calendarList"
                                    :schedules="scheduleList"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import 'tui-calendar/dist/tui-calendar.css'
import { Calendar } from '@toast-ui/vue-calendar'
import 'tui-date-picker/dist/tui-date-picker.css'
import HotelDatePicker from 'vue-hotel-datepicker'
import Select2 from 'v-select2-component';
export default {
    name: 'myCalendar',
    components: {
        Calendar,
        HotelDatePicker,
        Select2
    },
    data() {
        return {
            hotels:[],
            hotel: '',
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
            form: new form({
                checkInD: '',
                checkOutD: '',
                manyAdult: '',
                manyChild: '',
                manyRoom:'',
                roomWithRoomType:''
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
                }
            ],
            scheduleList: [
                {
                    id: '1',
                    calendarId: '1',
                    title: 'my schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2019-12-18T22:30:00+09:00',
                    end: '2019-12-19T02:30:00+09:00'
                },
                {
                    id: '2',
                    calendarId: '1',
                    title: 'second schedule',
                    category: 'time',
                    dueDateClass: '',
                    start: '2019-12-18T17:30:00+09:00',
                    end: '2019-12-19T17:31:00+09:00'
                }
            ],
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
        selectedView(e) {
            this.$refs.mycalendar.invoke('changeView', e, true);
            this.setRenderRangeText();
        }
    },
    methods: {
        init() {
            this.selectedMonth = new Date();
            this.setRenderRangeText();
            this.loadHotels();
            //this.$refs.mycalendar.usageStatistics = false;
        },
        pickerClose(){
            this.disPrev = true;
            this.disNext = true;
            this.$refs.mycalendar.invoke('setDate', new Date(), true);
            this.setRenderRangeText();
        },
        compareDate() {
            if(this.form.checkOutD!='' && Date.parse(new Date(this.form.checkOutD.getFullYear()+'-'+(this.form.checkOutD.getMonth()+1))) > Date.parse(new Date(this.dateRange))) this.disNext = false;
            else this.disNext = true;
            if(this.form.checkInD!='' && Date.parse(new Date(this.form.checkInD.getFullYear()+'-'+(this.form.checkInD.getMonth()+1))) < Date.parse(new Date(this.dateRange))) this.disPrev = false;
            else this.disPrev = true;
        },
        resetList(){
            this.manyAdults = [];
            this.manyChilds = [];
            this.manyRooms = [];
            this.roomWithRoomTypes = [];
        },
        generateList(param, kind) {
            var tempParam = [];
            if (param.length > 0) {
                var tempList = param.find(e => parseInt(e.id) === parseInt(this.form.roomWithRoomType)).value;
                if(kind=='value') {
                    tempList.forEach(function(item, key){
                        tempParam.push(item.value);
                    });
                }
                if(kind=='total') for(var i=1;i<=parseInt(tempList);i++) tempParam.push({id:i, text:i});
            } 
            
            return tempParam;
        },
        isRoomWithRoomType() {
            this.manyRooms = this.generateList(this.totalRooms, 'total');
            this.manyAdults = this.generateList(this.totalAdults, 'total');
            this.manyChilds = this.generateList(this.totalChilds, 'total');
            this.manyChilds.unshift({id:0, text:'0'});
            this.fixedAmenities = this.generateList(this.tempOptionalAmenities, 'value');
            this.optionalAmenities = this.generateList(this.tempFixedAmenities, 'value');
        },
        isHotelChange(){
            if(this.$gate.superAdminOrhotelOwner()) {
                this.resetList();
                let self = this;
                axios.get('/api/hotel-with-room-types/'+this.hotel).then(
                    function (response) {
                        response.data.forEach(function(item, key) {
                            if(item.room_type_rooms!='') {
                                item.room_type_rooms.forEach(function(item2, key2){
                                    self.roomWithRoomTypes.push({id:item2.id, text:item2.name+' - '+item.name});
                                    self.totalRooms.push({id:item2.id, value:item2.total_room, available:'none'});
                                    self.totalAdults.push({id:item2.id, value:item2.max_adult});
                                    self.totalChilds.push({id:item2.id, value:item2.max_child});
                                    self.tempOptionalAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature_optional.value)}); //= JSON.parse(item2.room_feature_optional.value);
                                    self.tempFixedAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature.value)}); //= JSON.parse(item2.room_feature.value);
                                });
                            }
                        });
                    });
            }
        },
        loadHotels() {
            if(this.$gate.superAdminOrhotelOwner()) {
                let self = this
                axios.get('/api/hotels').then(
                    function (response) {
                        response.data.forEach(item => {
                            if(item.status=='verified')
                                self.hotels.push({id:item.id, text:item.name});
                        });
                    });
            }
        },
        checkInDate(e) {
            if(e!=null) {
              this.form.checkInD = e;
              this.$refs.mycalendar.invoke('setDate', e, true);
              this.dateRange = `${e.getFullYear()}-${(e.getMonth()+1)}`;
              this.compareDate();
            }
        },
        checkOutDate(e) {
            if(e!=null) {
              this.form.checkOutD = e;
              this.compareDate();
            }
        },
        onClickNavi(event) {
            if (event.target.tagName === 'BUTTON') {
                const {target} = event;
                let action = target.dataset ? target.dataset.action : target.getAttribute('data-action');
                action = action.replace('move-', '');
                this.$refs.mycalendar.invoke(action);
                this.setRenderRangeText();
                this.compareDate();
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
    mounted() {
        this.init();

        /**
         * jQuery
         * */ 
        let self = this
        $(document).on('click', '.datepicker__clear-button', function(e){
            self.pickerClose();
        });
    }
}
</script>

