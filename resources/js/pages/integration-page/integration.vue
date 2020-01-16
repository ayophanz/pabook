<template>
    <div class="container">
        <div class="row justify-content-center">
            <integrate></integrate>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Wordpress plugin</a></li>
                        <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">RestAPI</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Embeded</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="activity">

                            </div>
                            <div class="tab-pane" id="timeline">

                            </div>
                            <div class="tab-pane active" id="settings">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        beforeCreate() {
          //
        },
        created(){
          //
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
</script>
