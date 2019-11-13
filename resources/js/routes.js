export const routes = [

    /**
     * Integration
     */
    {path: '/integration', name:'Integration', component: require('./pages/integration-page/integration.vue').default},
    
    /**
     * Setting
     */
    {path: '/settings', name:'Settings', component: require('./pages/setting-page/settings.vue').default},

    /**
     * Booking
     */
    {path: '/', name:'Bookings', component: require('./pages/booking-page/booking.vue').default},
    {path: '/add-book-entry', name:'New Reservation', component: require('./pages/booking-page/add-entry.vue').default},
    {path: '/walk-in-payment', name:'Payment', component: require('./pages/booking-page/payment-method.vue').default},

    /**
     * RoomType
     */
    {path: '/room-types', name:'Room Types', component: require('./pages/room-type-page/room-types.vue').default},
    {path: '/add-room-type', name:'New Room Type', component: require('./pages/room-type-page/room-type.vue').default},
    {path: '/edit-room-type/:typeId', name:'Edit Room Type', component: require('./pages/room-type-page/room-type.vue').default},

    /**
     * Room
     */
    {path: '/rooms', name:'Rooms', component: require('./pages/room-page/rooms.vue').default},
    {path: '/add-room', name:'New Room', component: require('./pages/room-page/room.vue').default},
    {path: '/edit-room/:roomId', name:'Edit Room', component: require('./pages/room-page/room.vue').default},
    
    /**
     * Hotel
     */
    {path: '/hotels', name:'Hotels', component: require('./pages/hotel-page/hotels.vue').default},
    {path: '/add-hotel', name:'New Hotel', component: require('./pages/hotel-page/hotel.vue').default},
    {path: '/edit-hotel/:hotelId', name:'Edit Hotel', component: require('./pages/hotel-page/hotel.vue').default},

    /**
     * User
     */
    {path: '/profile', name:'Profile', component: require('./pages/user-page/profile.vue').default},
    {path: '/users', name:'Users', component: require('./pages/user-page/users.vue').default},
    {path: '/add-user', name:'New User', component: require('./pages/user-page/user.vue').default},
    {path: '/edit-user/:userId', name:'Edit User', component: require('./pages/user-page/user.vue').default},
    {path: '/users-capability', name:'User Capability', component: require('./pages/user-page/capability.vue').default},

    /**
     * 404
     */
     {path: '*', name:'Not found', component: require('./pages/404.vue').default}
]

