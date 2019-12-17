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
import { Calendar } from '@toast-ui/vue-calendar';
import 'tui-date-picker/dist/tui-date-picker.css';
export default {
    name: 'myCalendar',
    components: {
        Calendar
    },
    data() {
        return {
            viewModeOptions: [
                {
                title: 'Select view',
                value: 'month'
                },
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
        selectedView(newValue) {
            this.$refs.mycalendar.invoke('changeView', newValue, true);
            this.setRenderRangeText();
        }
    },
    methods: {
        init() {
            this.setRenderRangeText();
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

