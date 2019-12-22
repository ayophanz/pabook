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
                                    :startDate="new Date(new Date().getFullYear(), new Date().getMonth(), 1)"
                                    :minNights="1"
                                    :maxNights="30"
                                    />
                                </div>
                                <div class="form-group">
                                    <label for="hotel">Hotel</label>
                                    <Select2 id="hotel" v-model="hotel" :options="hotels" :settings="{ placeholder: 'Please select hotel', containerCssClass:'form-control' }" @change="isHotelChange" />
                                </div>
                                <div class="form-group">
                                    <label for="roomType">Room</label>
                                    <Select2 id="roomType" v-model="roomType" :options="roomTypes" :settings="{ placeholder: 'Please select room type', containerCssClass:'form-control' }" @change="isRoomType" />
                                </div>
                                <div class="form-group">
                                    <label for="manyRoom">No. of room</label>
                                    <Select2 id="manyRoom" v-model="manyRoom" :options="(roomType!='' ? manyRooms:[])" :settings="{ placeholder: 'Please select how many rooms', containerCssClass:'form-control' }" />
                                </div>
                                <div class="form-group">
                                    <label for="manyAdult">No. of adult</label>
                                    <Select2 id="manyAdult" v-model="manyAdult" :options="(manyRoom!='' ? manyAdults:[])" :settings="{ placeholder: 'Please select how many adult(s)', containerCssClass:'form-control' }" />
                                </div>
                                <div class="form-group">
                                    <label for="manyChild">No. of child</label>
                                    <Select2 id="manyChild" v-model="manyChild" :options="(manyAdult!='' ? manyChilds:[])" :settings="{ placeholder: 'Please select how many child(s)', containerCssClass:'form-control' }" />
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div id="calendar-menu">
                                    <select v-model="selectedView" class="menuSelectedView">
                                        <option v-for="(options, index) in viewModeOptions" :value="options.value" :key="index">{{options.title}}</option>
                                    </select>
                                    <span id="menu-navi" @click="onClickNavi($event)">
                                        <button type="button" class="btn btn-default btn-sm move-today" data-action="move-today">Today</button>
                                        <button type="button" class="btn btn-default btn-sm move-day" data-action="move-prev"><i class="fas fa-chevron-left" data-action="move-prev"></i></button>
                                        <button type="button" class="btn btn-default btn-sm move-day" data-action="move-next"><i class="fas fa-chevron-right" data-action="move-next"></i></button>
                                    </span>
                                    <span class="render-range">{{dateRange}}</span>
                                </div>
                                <calendar ref="mycalendar" style="height: 800px;"
                                    :view="selectedView"
                                    :theme="theme"
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
            roomTypes:[],
            roomType:'',
            manyRooms: [],
            manyRoom:'',
            totalRooms: [],
            manyAdults:[
                {id:'1',text:'1'},{id:'2',text:'2'},{id:'3',text:'3'},
                {id:'4',text:'4'},{id:'5',text:'5'},{id:'6',text:'6'},
                {id:'7',text:'7'},{id:'8',text:'8'},{id:'9',text:'9'}
            ],
            manyAdult: '',
            manyChilds:[
                {id:'0', text:'0'},{id:'1',text:'1'},{id:'2',text:'2'},
                {id:'3',text:'3'},{id:'4',text:'4'},{id:'5',text:'5'},
                {id:'6',text:'6'},{id:'7',text:'7'},{id:'8',text:'8'},
                {id:'9',text:'9'}
            ],
            manyChild: '',
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
                'month.schedule.borderRadius': '5px',
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
        isRoomType() {
            this.manyRooms = [];
            if (this.totalRooms.length > 0) {
                var max = this.totalRooms.find(e => parseInt(e.id) === parseInt(this.roomType)).total;
                for(var i=1;i<=max;i++){
                    this.manyRooms.push({id:i,text:i});
                }
            }
        },
        isHotelChange(){
            if(this.$gate.superAdminOrhotelOwner()) {
                this.totalRooms = [];
                this.roomTypes = [];
                let self = this;
                axios.get('/api/room-types/'+this.hotel+',0').then(
                    function (response) {
                        response.data.forEach(function(item, key) {
                            if(item.room_type_rooms!='') {
                                self.roomTypes.push({id:item.id, text:item.name});
                                self.totalRooms.push({id:item.id, total:item.room_type_rooms[0].total_room, available:'none'});
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
              this.$refs.mycalendar.invoke('setDate', e, true);
              this.dateRange = `${e.getFullYear()}-${(e.getMonth()+1)}`;
            }
        },
        checkOutDate(e) {
            console.log(e);
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
    mounted() {
        this.init();
    }
}
</script>

