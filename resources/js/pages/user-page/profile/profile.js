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
            isCheckPass: false,
            super_admin: false,
            isEdit: false,
            form: new form({
                fullname: '',
                email: '',
                role: '',
                status: '',
                old_password: '',
                password: '',
                changePass: ''
            }),
            status: ['active', 'inactive'],
            role: ['hotel_owner', 'hotel_receptionist']
        }
    },
    methods: {
        toggleCheck () {
            if(this.isCheckPass)
                this.isCheckPass = false;
            else
                this.isCheckPass = true;
        },
        showHide (id, icon) {
            let pwd = $(id);
            let fa = $(icon);
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
        updateProfile() {
            this.isLoading = true;
            let self = this
            this.form.changePass = this.isCheckPass
            this.form.put('/api/update-profile')
            .then(function (response) {
                if(response.data=='refresh') 
                    window.location.reload();
                self.isLoading = false;
                toast.fire({
                    type: 'success',
                    title: 'Profile updated successfully'
                })

            })
            .catch(function (error) {
                self.isLoading = false;
                toast.fire({
                    type: 'error',
                    title: 'Something went wrong!'
                })

            });
        },
        profile() {
            let self = this;
            axios.get('/api/profile')
            .then(
                function (response) {
                    self.form.fullname = response.data.name;
                    self.form.email = response.data.email;
                    self.form.status = response.data.status;
                    self.form.role = response.data.role;
                    if(self.form.role=='super_admin') 
                        self.super_admin = true;
                }
            ); 
        },
        toEdit(isEdit) {
            this.isEdit = isEdit;
        }
    },
    beforeCreate() {
        //
    },
    created(){
        this.profile();
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