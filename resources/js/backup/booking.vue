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
                        <FullCalendar ref="fullCalendar" :defaultView="calendarView" :plugins="calendarPlugins" :events="dataEvent" @eventClick="showEvent" @eventRender="render" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
export default {
    components: {
        FullCalendar
    },
    data() {
        return {
            calendarApi: null,
            monthAppend: [],
            btnGuestAct: '',
            dataEvent: [],
            calendarPlugins: [dayGridPlugin, interactionPlugin],
            calendarView: 'dayGridMonth' //listMonth , dayGridMonth
        }
    },
    methods: {
        jQueryAction(action, id=null, eventId=null) {
            if (action == '#mFilter') {
                $(action).on('change', () => {
                    this.mFilter($('#mFilter option:selected').val());
                });
            }else{
                $(action).on('click', () => {
                    this.calendarAction(action, id, eventId);
                })
            }
        },
        calendarAction(action, id, eventId) {
            if (this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                let query = null
                if (action == '#checkInCall') {
                    query = axios.put('/api/room-check-in/'+id)
                }else if(action == '#cancelCall') {
                    query = axios.put('/api/room-book-cancel/'+id)
                }else if (action == '#checkOutCall') {
                    query = axios.put('/api/room-check-out/'+id)
                }else if (action == '#extendCall') {
                    query = axios.put('/api/room-extend/'+id)
                }
                query.then(
                    function(response) {
                        self.updateEventData(action, eventId);
                        toast.fire({
                          type: 'success',
                          title: 'Check out successfully'
                        })
                    }
                ).catch(
                    function(error) {
                        toast.fire({
                          type: 'error',
                          title: 'Something went wrong!'
                        })
                    }
                );
             }
        },
        updateEventData(action, eventId) {
            let calendar = this.$refs.fullCalendar.getApi();
            const Event = calendar.getEventById(eventId);
            if(action == '#checkOutCall') {
                Event.setProp('classNames', ['cal-checkout']);
                Event.setProp('title', Event.title.split('|')[0]);
            }else if(action == '#cancelCall') {
                Event.setProp('classNames', ['cal-cancel']);
                Event.setProp('title', Event.title.split('|')[0]);    
            }else if(action == '#checkInCall') {
                Event.setProp('classNames', ['cal-checkin']);
                Event.setProp('title', Event.title.split('|')[0]+ ' | '+this.dateDiff(Event.start - Event.end)+' days left to Check Out');
            }
        },
        mFilter(month) {
            let calendarApi = this.$refs.fullCalendar.getApi();
            calendarApi.gotoDate(new Date(new Date().getFullYear() + '-' + month + '-01'));
        },
        dateDiff(dateS, dateE) {
            const start = moment(new Date(dateS), 'M/D/YYYY');
            const end = moment(new Date(dateE), 'M/D/YYYY');
            return end.diff(start, 'days');
        },
        generateButton(arg) {
            let base_currency = arg.event.extendedProps.currencyy;
            let status = arg.event.classNames[0];
            let roomId = arg.event.extendedProps.roomId;
            let roomType = arg.event.extendedProps.roomType;
            let roomName = arg.event.extendedProps.roomName;
            let hotel = arg.event.extendedProps.hotel;
            let name = arg.event.extendedProps.name;
            let email = arg.event.extendedProps.email;
            let phone = arg.event.extendedProps.phone;
            let dateS = moment(arg.event.extendedProps.dateS).format('MMMM Do YYYY');
            let dateE = moment(arg.event.extendedProps.dateE).format('MMMM Do YYYY');
            let price = arg.event.extendedProps.price;
            let amount = arg.event.extendedProps.amount;
            let night = this.dateDiff(arg.event.extendedProps.dateS, arg.event.extendedProps.dateE);
            let features = JSON.parse(arg.event.extendedProps.features);
            let featuresApp = '';
            if (!features.length == 0) {
                featuresApp = '<ul>';
                features.forEach(function(item, index) {
                    featuresApp += '<li><i class="fas fa-check"></i> ' + item.value + '</li>';
                });
                featuresApp += '</ul>'
            }

            this.btnGuestAct = $('<div>')
            if (status == 'cal-checkin') {
                this.btnGuestAct.prepend(this.createButton('Check Out', 'btn-primary', 'fa-sign-out-alt', 'checkOutCall'));
                this.btnGuestAct.append(this.createButton('Extend Stay', 'btn-primary', 'fa-plus', 'extendCall'));
            } else if (status == 'cal-confirm-checkout') {
                this.btnGuestAct.prepend(this.createButton('Check Out', 'btn-primary', 'fa-sign-out-alt', 'checkOutCall'));
            }else if(status == 'cal-book'){
                this.btnGuestAct.prepend(this.createButton('Cancel this book', 'btn-primary', 'fa-sign-out-alt', 'cancelCall'));
            }else if(status == 'cal-confirm-checkin') {
                this.btnGuestAct.prepend(this.createButton('Confirm Check-in', 'btn-primary', 'fa-sign-out-alt', 'checkInCall'));
            }
            this.btnGuestAct.prepend(
                '<div class="details">' + 'Date: <span>' + dateS + ' - ' + dateE + '</span><br />' + 'CheckIn Time: <span>2:00pm</span> | CheckOut Time: <span>12:00pm</span><br />' + 'Night Stay: <span>' + night + '</span><br />' + 'Price: <span>' + base_currency + ' ' + price + '</span><br />' + 'Total Price: <span>' + base_currency + ' ' + amount + '</span><br />' + 'Room Id: <span>' + roomId + '</span><br />' + 'Room Name: <span>' + roomName + '</span><br />' + 'Room Type: <span>' + roomType + '</span><br />' + 'Hotel: <span>' + hotel + '</span><br />' + 'Amenities: ' + featuresApp + '<div class="guestInfo">Name: <span>' + name + '</span><br />' + 'Phone: <span>' + phone + '</span><br />' + 'Email: <span>' + email + '</span><br />' + '</div><br />');
        },
        createButton(text, btnCss, icon, eventt) {
            return $('<button id="' + eventt + '" class="btn ' + btnCss + ' btn-flat"><i class="fas ' + icon + '"></i> ' + text + '</button>');
        },
        showEvent(arg) {
            let id = arg.event.extendedProps.bookId;
            let eventId = arg.event.id;
            this.generateButton(arg);
            guestAction.fire({
                title: '<strong>Details</strong>',
                type: 'info',
                html: this.btnGuestAct,
                showConfirmButton: false,
                showCloseButton: true,
                showCancelButton: false
            })
            this.jQueryAction('#extendCall', id, eventId);
            this.jQueryAction('#checkOutCall', id, eventId);
            this.jQueryAction('#cancelCall', id, eventId);
            this.jQueryAction('#checkInCall', id, eventId);
        },
        render(event) {
            if (event.event.classNames[0] == 'cal-checkin') {
                event.el.firstChild.innerHTML = '<i class="fas fa-calendar-check"></i> ' + event.el.firstChild.innerHTML;
            } else if (event.event.classNames[0] == 'cal-book') {
                event.el.firstChild.innerHTML = '<i class="fas fa-calendar-minus"></i> ' + event.el.firstChild.innerHTML;
            } else {
                event.el.firstChild.innerHTML = '<i class="fas fa-calendar-times"></i> ' + event.el.firstChild.innerHTML;
            }
        },
        loadingCustomHead() {
            let append = '';
            this.monthAppend.forEach(function(item, index) {
                let selected = '';
                if (new Date().getMonth() == item.value)
                    selected = 'selected';
                append += '<option ' + selected + ' value="' + item.value + '">' + item.name + '</option>';
            });
            $(".fc-header-toolbar .fc-right").prepend('<select id="mFilter" class="custom-select-month-header fc-button-primary" style="cursor:pointer;height:2.4em;vertical-align:middle;min-width:100px;">' + append + '</select>');

            this.jQueryAction('#mFilter');
        },
        loadBookings() {
            if (this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                let self = this
                axios.get('/api/bookings')
                    .then(
                        function(response) {
                            response.data.forEach(function(item, index) {

                                let remain = 'Check In';
                                const start = moment(new Date(), 'M/D/YYYY');
                                let end = moment(new Date(item.dateStart), 'M/D/YYYY');
                                let diffDays = end.diff(start, 'days');

                                let statusClass = 'cal-book';
                                if (item.status == 'checkin') {
                                    statusClass = 'cal-checkin';
                                    end = moment(new Date(item.dateEnd), 'M/D/YYYY');
                                    diffDays = end.diff(start, 'days');
                                    if (diffDays < 0 ) {
                                        statusClass = 'cal-confirm-checkout';
                                    }
                                    remain = ' | ' + diffDays + ' days left to Check Out';
                                }else if(item.status == 'checkout') {
                                    remain = '';
                                    statusClass = 'cal-checkout';
                                }else if(item.status == 'cancel') {
                                    remain = ' | ' + diffDays + ' days past | Cancelled';
                                    statusClass = 'cal-cancel';
                                }else{
                                    diffDays = end.diff(start, 'days');
                                    if(diffDays <= 0) {
                                        statusClass = 'cal-confirm-checkin';
                                    }
                                    remain = ' | ' + diffDays + ' days left to Check In';
                                }

                                let base_currency = ((item.room.room_type.room_type_hotel.base_currency != null) ? item.room.room_type.room_type_hotel.base_currency.value : item.room.room_type.room_type_hotel.global_base_currency.value);

                                self.dataEvent.push({
                                    title: item.name + remain,
                                    start: item.dateStart,
                                    end: item.dateEnd,
                                    className: statusClass,
                                    id: item.id,
                                    extendedProps: {
                                        bookId: item.id,
                                        currencyy: base_currency,
                                        roomId: item.room_id,
                                        roomType: item.room.room_type.name,
                                        roomName: item.room.name,
                                        features: item.room.room_feature.value,
                                        amount: item.amount,
                                        email: item.email,
                                        phone: item.phone_no,
                                        dateS: item.dateStart,
                                        dateE: item.dateEnd,
                                        status: item.status,
                                        name: item.name,
                                        price: item.room.price,
                                        hotel: item.room.room_type.room_type_hotel.name
                                    }
                                });

                                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                                let monthValue = new Date(item.dateStart).getMonth();
                                let existData = false;
                                self.monthAppend.forEach(function(item, index) {
                                    if (item.name == monthNames[monthValue]) {
                                        existData = true;
                                    }
                                });
                                if (existData == false) {
                                    let current_month = new Date().getMonth()+1;
                                    if(current_month<=(monthValue+1)) {
                                        self.monthAppend.push({
                                            name: monthNames[monthValue],
                                            value: monthValue+1
                                        });
                                    }
                                }
                            });
                            self.loadingCustomHead();
                        }
                    );
            }
        },
        bookCancel() {
            if (this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                axios.put('/api/book-cancelled').then(
                    function(response) {
                        console.log('ok');
                    }
                ).catch(
                    function(error) {
                        console.log(error);
                    }
                );
            }
        }
    },
    created() {
        Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.bookCancel();
        this.loadBookings();
        let self = this
        fire.$on('gotoMonth', (month)=>{
            self.mFilter(month);
        });
    }
}
</script>
<style lang='scss'>
@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';
.fc-button-primary, .fc-button-primary:hover {
    color: #fff !important;
    border-color: #38d39f !important;
    background: #28a745;
    background: -webkit-linear-gradient(to left, #28a745, #38c172);
    background: -o-linear-gradient(to eft, #28a745, #38c172);
    background: -moz-linear-gradient(to left, #28a745, #38c172);
    background: linear-gradient(to left, #28a745, #38c172);
    -webkit-transition: all 0.4s;
    -o-transition: all 0.4s;
    -moz-transition: all 0.4s;
    transition: all 0.4s;
}

.fc-button-primary:not(:disabled):active:focus,
.fc-button-primary:not(:disabled).fc-button-active:focus {
    -webkit-box-shadow: 0 0 0 0.2rem rgba(4,154,121, 0.2901960784313726) !important;
    box-shadow: 0 0 0 0.2rem rgba(4,154,121, 0.2901960784313726) !important;
}

.fc-button-primary:focus {
    -webkit-box-shadow: 0 0 0 0.2rem rgba(4,154,121, 0.2901960784313726) !important;
    box-shadow: 0 0 0 0.2rem rgba(4,154,121, 0.2901960784313726) !important;
}

.fc-event,
.fc-event-dot {
    background-color: #3788d8 !important;
    border-radius: 0px;
    color: white !important;
    font-size: 16px;
    cursor: pointer;
}

.cal-checkin {
    background-color: #28a745 !important;
    border: 1px;
}

.fc-time {
    display: none;
}

.cal-checkout {
    background-color: gray!important;
    border: 1px;
}

.cal-confirm-checkout {
    background-color: #e3342f !important;
    border: 1px;
}

.cal-confirm-checkin {
    background-color: #ae29d0 !important;
    border: 1px;
}

.cal-cancel {
    background-color: #dad7d7 !important;
    border: 1px;
    color: black !important;
    text-decoration: line-through !important;
    text-decoration-color: red !important;
}

.fc-day-grid-event .fc-content {
    padding: 0px 5px;
}

.details span {
    font-weight: 700;
}

.details {
    text-align: left;
    line-height: 1.6;
}

.details ul {
    list-style: none;
}

.fc-widget-header {
    background: #f7f7f7;
    color: #495057;
}

.custom-select-month-header option {
    background-color: white;
    color: black;
}

.fc-unthemed .fc-divider,
.fc-unthemed .fc-popover .fc-header,
.fc-unthemed .fc-list-heading td {
    background: #eee;
    color: black;
}

tr.fc-list-item.cal-book,
tr.fc-list-item.cal-book:hover {
    cursor: pointer;
    background-color: #3490dc !important;
    color: white;
}

tr.fc-list-item.cal-checkin,
tr.fc-list-item.cal-checkin:hover {
    cursor: pointer;
    background-color: #28a745 !important;
    color: white;
}

.fc-unthemed .fc-list-item:hover td {
    background-color: none !important;
    color: black;
}

td.fc-list-item-time.fc-widget-content {
    padding: 0px 5px;
}

td.fc-list-item-title.fc-widget-content {
    padding: 0px 5px;
}

#toggle-view {
    background-color: transparent !important;
    color: #3490dc;
    border: 0px;
}
</style>
