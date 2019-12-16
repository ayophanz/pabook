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
                            <div class="col-md-3"></div>
                            <div class="col-md-9">
                                <div id="menu">
                                    <span id="menu-navi">
                                        <button type="button" class="btn btn-default btn-sm move-today" data-action="move-today">Today</button>
                                        <button type="button" class="btn btn-default btn-sm move-day" data-action="move-prev">
                                            <i class="fas fa-arrow-alt-circle-left" data-action="move-next"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm move-day" data-action="move-next">
                                            <i class="fas fa-arrow-alt-circle-right" data-action="move-next"></i>
                                        </button>
                                    </span>
                                    <span id="renderRange" class="render-range"></span>
                                </div>
                                <calendar style="height: 800px;"
                                    :view="view"
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
            view:'month',
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
    }
}
</script>

