<template>
    <div id="root">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <FullCalendar defaultView="dayGridMonth" :plugins="calendarPlugins" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid'
    export default {
        components: {
            FullCalendar
        },
        data() {
            return {
              calendarPlugins: [ dayGridPlugin ]
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style lang='scss'>
    @import '~@fullcalendar/core/main.css';
    @import '~@fullcalendar/daygrid/main.css';

</style>