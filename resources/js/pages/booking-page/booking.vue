<template>
    <div id="root">
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
        <vodal :show="vodal_show" animation="zoom" :closeOnEsc="false" :width="600" className="vodal-style" @hide="vodal_show = false">
            <div class="swal2-header">
                <div class="swal2-icon swal2-info swal2-animate-info-icon" style="display: flex;"></div>
                <h4 class="swal2-title" style="display: flex;"><strong>Exclude optional amenities <br /> to specific room</strong></h4>
            </div>
            <div class="swal2-content">
                <br />
                <h6 class="text-left ml-5">*Uncheck item to exclude</h6>
                <exclude-optional-amen ref="dataOptionalFeature" @removeOrAddRoomOnAmen="onRemoveOrAdd"></exclude-optional-amen>
                <br />
            </div>
        </vodal>
        <div class="row justify-content-center" :data-test="test">
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
                        <form  @submit.prevent="validateEntries" role="form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div v-if=" pageIn=='page_1'" class="tab-pagination page-1">
                                        <div class="form-group mb-1">
                                            <HotelDatePicker 
                                                @check-in-changed="checkInDate"
                                                @check-out-changed="checkOutDate"
                                                :startingDateValue="new Date(form.checkInD)"
                                                :endingDateValue="new Date(form.checkOutD)"
                                                format="YYYY MMM. DD"
                                                :startDate="new Date()"
                                                :minNights="1"
                                                :maxNights="30"
                                                :closeDatepickerOnClickOutside="false"
                                                :showCloseButton="true"
                                            />
                                            <has-error v-if="form.checkInD==''" :form="form" field="checkInD"></has-error>
                                            <has-error v-else-if="form.checkOutD==''" :form="form" field="checkOutD"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="hotel">Hotel</label>
                                            <Select2 id="hotel" v-model="form.hotel" :options="hotels" :settings="{ placeholder: 'Please select hotel', containerCssClass:'form-control' }" @change="isHotelChange" />
                                            <has-error :form="form" field="hotel"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="roomWithRoomType">Room</label>
                                            <Select2 id="roomWithRoomType" v-model="form.roomWithRoomType" :options="roomWithRoomTypes" :settings="{ placeholder: 'Please select room', containerCssClass:'form-control' }" @change="isRoomWithRoomType" />
                                            <has-error :form="form" field="roomWithRoomType"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="manyRoom">No. of room</label>
                                            <Select2 id="manyRoom" v-model="form.manyRoom" :options="(form.roomWithRoomType!='' ? manyRooms:[])" :settings="{ placeholder: 'Please select how many rooms', containerCssClass:'form-control' }" @change="isManyRoom" />
                                            <has-error :form="form" field="manyRoom"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="rooms_no">Room no.</label>
                                            <multiselect v-model="form.rooms_no" :max="parseInt(form.manyRoom)" placeholder="Please select designated room no." @remove="roomsNoOnRemove" @select="roomsNoOnAdd" :class="{ 'is-invalid': form.errors.has('rooms_no') }" label="value" track-by="code" :options="rooms_options" :multiple="true">
                                                <template slot="tag" slot-scope="{ option, remove }"><span :class="option.status" class="multiselect__tag"><span>{{ option.value }}</span><span v-if="option.status=='ready'" :class="option.status" class="custom__remove" @click="remove(option)"><i aria-hidden="true" tabindex="1" class="multiselect__tag-icon"></i></span></span></template>
                                                <span slot="noResult">Oops! No results</span>
                                                <span slot="maxElements">{{form.manyRoom}} item allowed</span>
                                            </multiselect>
                                            <has-error :form="form" field="rooms_no"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="manyAdult">No. of adult</label>
                                            <Select2 id="manyAdult" v-model="form.manyAdult" :options="(form.manyRoom!='' ? manyAdults:[])" :settings="{ placeholder: 'Please select how many adult(s)', containerCssClass:'form-control' }" />
                                            <has-error :form="form" field="manyAdult"></has-error>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label class="mb-0" for="manyChild">No. of child</label>
                                            <Select2 id="manyChild" v-model="form.manyChild" :options="(form.manyAdult!='' ? manyChilds:[])" :settings="{ placeholder: 'Please select how many child(s)', containerCssClass:'form-control' }" />
                                            <has-error :form="form" field="manyChild"></has-error>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-md-12">
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
                                                            <ul class="optionalAmen-list">
                                                                <li v-for="(item, key) in optionalAmenities">
                                                                    <div class="form-check pl-0">
                                                                        <pretty-check v-model="form.addOnOptionalAmen" :value="item" :disabled="no_unit_avail < 1 && item.rooms.length <= 0" class="p-icon p-round p-tada" color="success-o">
                                                                            <i slot="extra" class="icon mdi mdi-heart fas fa-heart"></i>
                                                                            {{item.value}} | {{currency}}{{item.price}}
                                                                        </pretty-check>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a v-if="no_unit_avail > 1 && form.addOnOptionalAmen.length != 0" @click.prevent="excludeOptional"  href="">Exclude specific room for optional amenities</a>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0 mt-3">
                                            <label>Room price:</label>&nbsp;&nbsp;
                                            <span class="mb-0">{{currency}}{{roomPrice}}</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-0">Subtotal:</label>
                                            <div class="optionalList">
                                                <p class="mb-0 ml-2">{{currency}}{{roomPrice}} x ({{ nightNoFunc }})night</p>
                                                <p class="mb-0 ml-2">{{currency}}{{roomPrice}} x ({{ roomNoFunc }})no. of room</p>
                                                <p class="mb-0 ml-2" v-for="item in form.addOnOptionalAmen">{{currency}}{{item.price}} x ({{item.rooms.length}}){{item.value}}</p>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0 booking-total">
                                            <label>Total:</label>&nbsp;&nbsp;
                                            <span>{{currency}}{{totalAmountFunc}}</span>
                                        </div>
                                        <div class="form-group text-center mt-4">
                                            <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fas fa-concierge-bell"></i> Book Room</button>
                                        </div>
                                    </div>
                                    <div v-else-if=" pageIn=='page_2'" class="tab-pagination page-2">
                                        <i @click="backIsClick" class="btn btn-outline-primary btn-flat fas fa-arrow-left"></i>
                                        <div class="form-group mt-4">
                                            <label for="summary">Booking Summary </label>
                                            <div class="summary-container">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Name <span class="required-asterisk">*</span></label>
                                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" id="name">
                                            <has-error :form="form" field="name"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone_no">Phone no. <span class="required-asterisk">*</span></label>
                                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('phone_no') }" id="phone_no">
                                            <has-error :form="form" field="phone_no"></has-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email </label>
                                            <input type="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" id="email">
                                            <i>Note: if not empty system will send receipt.</i>
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address </label>
                                            <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('address') }" id="address">
                                        </div>
                                        <div class="form-group text-center mt-4">
                                            <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat"><i class="fas fa-thumbs-up"></i> Done</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div id="calendar-menu">
                                        <div class="row mt-0 room-cell-container">
                                            <div class="col-md-3 pr-0 pl-0">
                                                <h5 class="text-center mt-3">Total Rooms</h5>
                                                <h3 class="text-center">{{manyRooms.length}}</h3>
                                            </div>
                                            <div class="col-md-3 pr-0 pl-0">
                                                <h5 class="text-center mt-3">Available Rooms</h5>
                                                <h3 class="text-center">6</h3>
                                            </div>
                                            <div class="col-md-3 pr-0 pl-0">
                                                <h5 class="text-center mt-3">Reserved Rooms</h5>
                                                <h3 class="text-center">2</h3>
                                            </div>
                                            <div class="col-md-3 pr-0 pl-0">
                                                <h5 class="text-center mt-3">Occupied Rooms</h5>
                                                <h3 class="text-center">2</h3>
                                            </div>
                                        </div><br />
                                        <select v-model="selectedView" class="menuSelectedView">
                                            <option v-for="(options, index) in viewModeOptions" :value="options.value" :key="index">{{options.title}}</option>
                                        </select>
                                        <span id="menu-navi" @click="onClickNavi($event)">
                                            <!-- <button type="button" class="btn btn-default btn-sm move-today" data-action="move-today">Today</button> -->
                                            &nbsp;<button v-if="disPrev==false" :disabled="disPrev" type="button" class="btn btn-outline-primary btn-flat move-day" data-action="move-prev"><i class="fas fa-chevron-left" data-action="move-prev"></i> Prev</button>
                                            &nbsp;<button v-if="disNext==false" :disabled="disNext" type="button" class="btn btn-outline-primary btn-flat move-day" data-action="move-next">Next <i class="fas fa-chevron-right" data-action="move-next"></i></button>
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ExcludeOptionalAmen from '../../components/excludeOptionalAmenComponent'
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
            test: '',
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
            pageIn: 'page_1',
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
                roomWithRoomType:'',
                rooms_no: [],
                addOnOptionalAmen: []
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

        'selectedView': function(e) {
            this.$refs.mycalendar.invoke('changeView', e, true);
            this.setRenderRangeText();
        },

        'form.hotel': function() { 
            this.form.roomWithRoomType = [];
            this.currency = ''; 
            this.roomWithRoomTypes = [];
        },

        'form.roomWithRoomType': function() { this.form.manyRoom = ''; },
        
        'form.manyRoom': function() {
            this.form.rooms_no = [];
            this.form.addOnOptionalAmen = [];
            this.optionalAmenities.forEach(function(item, key){item.rooms = [];});
        },
        'form.rooms_no': function() {
            this.$refs.dataOptionalFeature.rooms_no_Data = [ ...new Set(this.$refs.dataOptionalFeature.rooms_no_Data)];
            this.$refs.dataOptionalFeature.addOnOptionalAmen_Data = this.form.addOnOptionalAmen;
        },
        'form.addOnOptionalAmen': function() {
            this.form.addOnOptionalAmen.forEach(function(item, key){ item.rooms = [ ...new Set(item.rooms)]; });
            this.$refs.dataOptionalFeature.addOnOptionalAmen_Data = this.form.addOnOptionalAmen;
            this.$refs.dataOptionalFeature.currency_Data = this.currency;
        }

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
         
        excludeOptional() { this.vodal_show = true; },

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
            let hideRoom = this.$refs.dataOptionalFeature.hideRoom;
            let addRoom = this.$refs.dataOptionalFeature.rooms_no_Data;
            if(action=='remove') {
                let self = this;
                this.form.addOnOptionalAmen.forEach(function(item, key){
                    if(item.rooms.indexOf(value) !== -1) item.rooms.splice(item.rooms.indexOf(value), 1);
                });
                this.optionalAmenities.forEach(function(item, key){ 
                    if(self.form.rooms_no.length == 2 && item.rooms.length == 0) item.rooms.push(self.form.rooms_no[0]);
                });
                hideRoom.push(value);
            }else if(action=='undo') {
                this.optionalAmenities.forEach(function(item, key){ 
                    item.rooms.push(value); 
                    item.isChecked = true; 
                });
                if(hideRoom.indexOf(value) !== -1) hideRoom.splice(hideRoom.indexOf(value), 1);
                addRoom.push(value);
            }
        },

        backIsClick() { this.pageIn = 'page_1'; },

        validateEntries() {
            if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                this.isLoading = true;
                let self = this
                this.form.post('/api/create-book').then(function (response) {
                    self.isLoading = false;
                    toast.fire({
                      timer: 2000,
                      type: 'success',
                      title: 'Room availability confirmed.'
                    })
                    self.pageIn = 'page_2'
                }).catch(function (error) {
                    self.isLoading = false;
                    toast.fire({
                      type: 'error',
                      title: 'Something went wrong!'
                    })
                });
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
                else if(kind=='optional') tempList.forEach(function(item, key){ item['isChecked'] = true; item['id'] = (key+1);item['rooms'] = []; tempParam.push(item); });
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
            if(this.$gate.superAdminOrhotelOwner()) {
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
                            if(item.room_type_rooms!='') {
                                item.room_type_rooms.forEach(function(item2, key2){
                                    self.roomPrices.push({id:item2.id, value:item2.price});
                                    self.roomWithRoomTypes.push({id:item2.id, text:item2.name+' - '+item.name});
                                    self.totalRooms.push({id:item2.id, value:item2.total_room, available:'none'});
                                    self.totalAdults.push({id:item2.id, value:item2.max_adult});
                                    self.totalChilds.push({id:item2.id, value:item2.max_child});
                                    self.tempOptionalAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature_optional.value), rooms:[] });
                                    self.tempFixedAmenities.push({id:item2.id, value:JSON.parse(item2.room_feature.value)});
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
        this.loadHotels();
        this.selectedMonth = new Date();
        this.setRenderRangeText();
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
</script>
<style lang='scss'>
    .book-rooms-quantity {
        border: 1px solid #e5e5e5;
        padding: 20px 10px;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 14.3rem;
        min-height: 14.3rem;
    }

    .book-rooms-quantity h5 {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 0px;
        margin: 0px;
    }

    .book-rooms-quantity span {
        font-size: 18px;
        font-family: cursive;
    }

    .book-rooms-quantity ul {
        list-style: circle;
    }

    .swal2-loading .swal2-styled {
        border-left-color: #38d39f !important;
        border-right-color: #38d39f !important;
    }

    .summary-container {
        border: 1px solid #e5e5e5;
        min-height: 300px;
        overflow-y: auto;
    }

    .room-cell-container {
        margin: auto;
        margin-top: 10px;
    }

    .room-cell-container div {
        border: 1px solid #e5e5e5;
    }

    .booking-total {
        border: 1px solid #d7d9e2;
        padding: 5px 5px;
        display: inline;
    }
    
    .multiselect__tags {
        padding: 8px 40px 0 20px !important;
    }

    .pretty.p-icon .state .icon {
        width: calc(1em + 1px) !important;
        height: calc(1em + 4px) !important;
    }

    .vodal-style {
        border-radius: .3125em;
    }

    .vodal-close:hover:before, .vodal-close:hover:after {
        background: #ce7070 !important;
    }

    .vodal-dialog {
        height: max-content !important;
    }

    ul.exclude-rooms-no {
        text-align: left;
        list-style: none;
    }

    .vodal-dialog {
        padding: 1.25em !important;
    }

    .vodal-mask {
        background-color: rgba(0,0,0,.4) !important;
    }

</style>

