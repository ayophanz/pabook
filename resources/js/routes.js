
export default [
   
    /**
     * Integration
     */
    {path: '/integration', name:'Integration', component: require('./pages/integration-page/integration.vue').default},
    
    /**
     * Setting
     */
    {path: '/settings', name:'Settings', component: require('./pages/setting-page/index/settings.vue').default},

    /**
     * Booking
     */
    {path: '/', name:'Bookings', component: require('./pages/booking-page/index/bookings.vue').default},
    {path: '/add-book-entry', name:'New booking entry', component: require('./pages/booking-page/create/booking.vue').default},

    /**
     * RoomType
     */
    {path: '/room-types', name:'Room Types', component: require('./pages/room-type-page/index/room-types.vue').default},
    {path: '/add-room-type', name:'New Room Type', component: require('./pages/room-type-page/create/room-type.vue').default},
    {path: '/edit-room-type/:typeId', name:'Edit Room Type', component: require('./pages/room-type-page/edit/room-type.vue').default},

    /**
     * Room
     */
    {path: '/rooms', name:'Rooms', component: require('./pages/room-page/index/rooms.vue').default},
    {path: '/add-room', name:'New Room', component: require('./pages/room-page/create/room.vue').default},
    {path: '/edit-room/:roomId', name:'Edit Room', component: require('./pages/room-page/edit/room.vue').default},
    
    /**
     * Hotel
     */
    {path: '/hotels', name:'Hotels', component: require('./pages/hotel-page/index/hotels.vue').default},
    {path: '/add-hotel', name:'New Hotel', component: require('./pages/hotel-page/create/hotel.vue').default},
    {path: '/edit-hotel/:hotelId', name:'Edit Hotel', component: require('./pages/hotel-page/edit/hotel.vue').default},

    /**
     * User
     */
    {path: '/users', name:'Users', component: require('./pages/user-page/index/users.vue').default},
    {path: '/profile', name:'Account', component: require('./pages/user-page/profile/profile.vue').default},
    {path: '/add-user', name:'New User', component: require('./pages/user-page/create/user.vue').default},
    {path: '/edit-user/:userId', name:'Edit User', component: require('./pages/user-page/edit/user.vue').default},
    {path: '/users-capability', name:'User Capability', props: true, component: require('./pages/user-page/capability/capability.vue').default},

    /**
     * 404
     */
    {path: '*', name:'Not found', component: require('./pages/404.vue').default}
]