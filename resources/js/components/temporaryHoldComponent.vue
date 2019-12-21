
<script>
Vue.component('temporary-hold', {
  template: `
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{dataValue.title}}</div>
                            <div class="card-body">
                               <strong>ID: {{dataValue.hotel_id}}</strong><br />
                               <strong>Hotel: {{dataValue.hotel_name}}</strong>
                               <br />
                               <br />
                               <div v-if="dataValue.visible_only=='user'">
                                {{dataValue.msg}}
                                <br />
                                <br />
                                <form v-if="dataValue.verify_token_link!='#'" @submit.prevent="verify" role="form">
                                        <div class="row justify-content-center">
                                                <div class="col-md-4">
                                                    <div class="form-group"><br />
                                                        <input id="hotelVerifyToken" type="text" v-model="form.hotelVerifyToken" class="form-control" :class="{ 'is-invalid': form.errors.has('hotelVerifyToken') }" name="hotelVerifyToken">
                                                        <has-error :form="form" field="hotelVerifyToken"></has-error>
                                                    </div> 
                                                </div>
                                                <div class="col-md-4"><br />
                                                    <button :disabled="form.busy" type="submit" class="btn btn-outline-primary btn-flat">Verify now</button>
                                                </div>
                                        </div><br /><br />
                                </form>
                                <p v-if="dataValue.link!='#'"><a v-bind:href="dataValue.link+dataValue.hotel_id"> {{dataValue.link_title}}</a> and paste above field.</p>
                               </div>
                               
                               <div <div v-if="dataValue.visible_only=='admin'">
                                <p class="text-center">{{dataValue.msg}}</p>
                                <form v-if="dataValue.download_action!='#'" @submit.prevent="verify" role="form">
                                        <div class="row justify-content-center">
                                                <div class="col-md-12 text-center">
                                                    <a v-bind:href="dataValue.download_link" v-bind:download="dataValue.download_filename" target="_blank">{{dataValue.download_action}}</a><br /><br />
                                                    <a href="#" @click.prevent="onApprove(dataValue.hotel_id)" class="btn btn-outline-primary btn-flat btn-action"><i class="fas fa-thumbs-up"></i> Approve now</a>
                                                </div>
                                        </div><br /><br />
                                </form>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `,
  props: ['dataValue'],
  data() {
    return {
        form: new form({
              hotelId: null,
              hotelVerifyToken: ''
          })
    }
  },
  methods: {
      verify() {
          console.log(this.form);
        this.form.get('/verify-hotel-token/'+this.form.hotelId+'/'+this.form.hotelVerifyToken).then(function (response) { 
            window.location.reload();
        }).catch(function (error) {
            console.log(error); 
            toast.fire({
                type: 'error',
                title: 'Something went wrong!'
            })

        });
      },
      onApprove($id) {
            approve.fire({
                title: 'Are you sure?',
                text: "You were going to approve this hotel",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'No, cancel! ',
                focusCancel: true,
                reverseButtons: true
            }).then((result) => {
                if(result.value) {
                    if(this.$gate.superAdmin()) {
                        this.form.put('/api/approve-hotel') .then(
                            function (response) {
                                toast.fire({
                                    type: 'success',
                                    title: 'This hotel is ready for booking.'
                                })
                                setTimeout(function() { location.reload(); }, 3000);
                            }
                        );
                    }
                }
            })
      }
  },
  mounted() {
    this.form.hotelId = this.dataValue.hotel_id;
  }
});
</script>