<template>
    <div class="notify-bar">
        <loading 
            :height="128"
            :width="128"
            :transition="`fade`"
            :loader="`dots`"
            :background-color="`#fff`"
            :color="`#007bff`"
            :active.sync="isLoading" 
            :is-full-page="fullPage">
        </loading>
        <aside class="control-sidebar control-sidebar-dark">
            <div class="p-3">
              <h5>Title</h5>
              <p>Sidebar content</p>
              <ul v-for="notification in unreadNotifications" :key="notification.id">
                <li><span @click="markAsRead(notification)">{{notification.data.newReservation.name}}</span></li>
              </ul>
            </div>
        </aside>
    </div>
</template>
<script>
    import Loading from 'vue-loading-overlay'
    import 'vue-loading-overlay/dist/vue-loading.css' 
    export default {
        components: {
            Loading
        },
        data() {
            return {
                fullPage: true,
                isLoading: false,
                allNotifications: [],
                unreadNotifications: []
            }
        },
        watch:{
            allNotifications(val){
                this.unreadNotifications =  this.allNotifications.filter(notification => {
                    return notification.read_at == null;
                });
            }
        },
        methods: {
            markAsRead(notification) {
                console.log(notification);
                if (this.$gate.superAdmin() || this.$gate.hotelReceptionist() || this.$gate.hotelOwner()) {
                    this.isLoading = true;
                    let self = this
                    axios.put('/api/mark-as-read/'+notification.id).then(
                        function(response) {
                            fire.$emit('gotoMonth', new Date(notification.data.newReservation.dateStart.date).getMonth()+1);
                            self.allNotifications = response.data.notifications;
                            fire.$emit('loadCounterNotify');
                            self.isLoading = false;
                        }
                    ).catch(
                        function(error) {
                            self.isLoading = false;
                            toast.fire({
                               type: 'error',
                               title: 'Something went wrong!'
                            })
                        }
                    );
                }
            }
        },
        created() {
            this.allNotifications = window.user.notifications;
            this.unreadNotifications =  this.allNotifications.filter(notification => {
                return notification.read_at == null;
            });
            // Echo.private('App.User.' + window.user.id).notification((notification) => {
            //             console.log(notification);
            //             this.allNotifications.unshift(notification.notification); 
            //             fire.$emit('loadCounterNotify');
            //             });
            fire.$emit('loadCounterNotify');  
        }
    }
</script>

<style lang='scss'>
    .notify-bar span {
        cursor: pointer;
        background-color: transparent !important;
    }
</style>