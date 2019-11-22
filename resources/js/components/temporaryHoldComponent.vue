
<script>
Vue.component('temporary-hold', {
  template: `
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{title}}</div>
                            <div class="card-body">
                               {{msg}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `,
  props: ['title', 'msg', 'others']
});
</script>