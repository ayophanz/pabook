
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
                               {{dataValue.msg}}
                               <br />
                               <br />
                               <a v-if="dataValue.link!='#'" v-bind:href="dataValue.link"> {{dataValue.link_title}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `,
  props: ['dataValue']
});
</script>