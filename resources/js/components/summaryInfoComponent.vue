  <template>  
    <div>
        <div id="summary-info" class="content pl-3">
            <div v-for="(item, key) in summaryInfo_Data" :key="key">
                <i :class="'fas '+item.iconClass+' fa-2x '+item.class"></i>
                <h6 class="m-0"><strong>{{item.label}}: <i>{{item.count}}</i></strong></h6>
            </div>
        </div>
    </div>
  </template>

  <script>
  export default {            
      props: ['summaryInfo_Data']
  }
</script>
<style lang='scss'>
    div#summary-info {
        display: flex;
        flex-wrap: nowrap;
    }
    div#summary-info div {
        margin: 10px;
        display: inline-flex;
    }
    div#summary-info div h6 {
        text-indent: 5px;
    }
    div#summary-info div h6 strong {
        vertical-align: sub;
    }
    div#summary-info .icon-active,
    div#summary-info .icon-pending,
    div#summary-info .icon-verified,
    div#summary-info .icon-email,
    div#summary-info .icon-document {
        color: #28a745;
    }
</style>