
<script>
Vue.component('temporary-hold', {
  template: `
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{dataValue.title}}</div>
                            <div class="card-body">
                               ID: {{dataValue.hotel_id}} , Hotel: {{dataValue.hotel_name}}
                               <br />
                               <br />
                               {{dataValue.msg}}
                               <br />
                               <br />
                               <a href="#" v-if="dataValue.link!='#'" >Click here to send email verification</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `,
  props: ['dataValue']
});
</script>