<template>
    <div id="root">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
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
                        <FullCalendar 
                        ref="fullCalendar"
                        defaultView="dayGridMonth" 
                        :plugins="calendarPlugins" 
                        :events="dataEvent"
                        @eventClick="showEvent"
                        />
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
                dataEvent: [
                            { 
                                title: 'event 1 | 4 days left', 
                                start: '2019-08-01', 
                                end: '2019-08-05', 
                                classNames: ['cal-checkin'] 
                            },
                            { 
                                title: 'event 2 | 4 days left', 
                                start: '2019-08-02', 
                                end: '2019-08-06', 
                                className: ['cal-book'] 
                            }
                          ],
                calendarPlugins: [ dayGridPlugin, interactionPlugin ]
            }
        },
        methods: {
            showEvent(arg) {
                console.log(arg);
                console.log(arg.event.title);
            },
            loadBookings() {
                if(this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
                    let self = this
                    axios.get('/api/bookings')
                    .then(
                        function (response) {
                            response.data.forEach(function(item, index){
                                
                                let remain = 'Check In';
                                let endConverted = new Date(item.dateEnd).toISOString();
                                const start = moment(new Date(), 'M/D/YYYY');
                                const end = moment(new Date(endConverted), 'M/D/YYYY');
                                const diffDays = end.diff(start, 'days');

                                let statusClass = 'cal-book';
                                if(item.status=='checkin') {
                                    statusClass = 'cal-checkin';
                                    remain = 'Check Out';
                                }
                                
                                self.dataEvent.push({
                                    title:item.name+' | '+diffDays+' days left to '+remain,
                                    start:item.dateStart,
                                    end:item.dateEnd,
                                    className:statusClass
                                });
                            });
                        }
                    );
                }
            }
        },
        created() {
            this.loadBookings();
        }
    }
</script>

<style lang='scss'>
    @import '~@fullcalendar/core/main.css';
    @import '~@fullcalendar/daygrid/main.css';

    .fc-button-primary {
        background-color: #3490dc !important;
        border-color: #3490dc !important;
        border-radius: 0px;
    }
    .fc-button-primary:not(:disabled):active:focus, .fc-button-primary:not(:disabled).fc-button-active:focus {
        -webkit-box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.2901960784313726) !important;
        box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.2901960784313726) !important; 
    }
    .fc-button-primary:focus {
        -webkit-box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.2901960784313726) !important; 
        box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.2901960784313726) !important; 
    }
    .fc-event, .fc-event-dot {
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
</style>