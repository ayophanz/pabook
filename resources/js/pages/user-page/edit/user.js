import Loading from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/vue-loading.css'
export default {
    watch: {
        //
    },
    components: {
        Loading
    },
    data() {
        return {
        fullPage: true,
        isLoading: false,
        isRecep: true,
        isSuperAdmin: false,
        isCheckPass: false,
        userId: null,
        hotelOwnerEmails: [],
        form: new form({
            fullname: '',
            email: '',
            role: '',
            status: '',
            password: '',
            changePass: '',
            assignTo: ''
        }),
        status: ['active', 'inactive'],
        role: ['hotel_receptionist']
        }
    },
    methods: {
        // toggleCheck () {
        //   if(this.isCheckPass)
        //     this.isCheckPass = false;
        //   else
        //     this.isCheckPass = true;
        // },
        register () {
            if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
                this.isLoading = true;
                let self = this;
                this.form.changePass = this.isCheckPass;
                this.form.put('/api/update-user/'+this.userId)
                .then(function () { 
                    self.isLoading = false;
                    toast.fire({
                        type: 'success',
                        title: 'User updated successfully'
                    })
                })
                .catch(function (error) {
                    self.isLoading = false;
                    toast.fire({
                    type: 'error',
                    title: 'Something went wrong!'
                    })
                });

            }
        },
        showHide () {
        let pwd = $("#password");
        let fa = $(".call-password");
        if(pwd.attr('type') === 'password') {
            pwd.attr('type', 'text');
            fa.removeClass('fa-eye');
            fa.addClass('fa-eye-slash');
        }else{
            fa.removeClass('fa-eye-slash');
            fa.addClass('fa-eye');
            pwd.attr('type', 'password')
        }
        },
        userDetails(id) {
        if(this.$gate.superAdmin() || this.$gate.hotelOwner()) {
            this.isLoading = true;
            let self = this;
            axios.get('/api/edit-user/'+id)
            .then(
                function (response) {
                self.form.fullname = response.data.name;
                self.form.email = response.data.email;
                self.form.role = response.data.role;
                self.form.status = response.data.status;
                self.isLoading = false;
                }
            );    
        }
        },
        ifChangeRole() {
        this.hotelOwners(this.form.role);
        },
        hotelOwners(role) {
        if(role == 'hotel_receptionist')
            this.isRecep = true;
        else
            this.isRecep = false;
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.userId = this.$route.params.userId;  
        this.userDetails(this.userId);
        //if(this.$gate.hotelOwner()) 
            //this.role.splice(0, 1);
        this.hotelOwners(this.form.role);
            
        if(this.$gate.superAdmin()) {
            this.role.push('hotel_owner');
            this.isSuperAdmin = true;
            let self = this;
            axios.get('/api/hotel-owners')
            .then(
            function (response) {
                response.data.forEach(function(item){
                self.hotelOwnerEmails.push(item);
                });
            });
        } 
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