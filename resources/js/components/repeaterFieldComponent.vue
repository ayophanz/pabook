
  <template>  
    <div>
      <div v-for="(field, key) in fields"  class="input-group input-group repeater-input-container">
            <input type="text" placeholder="name" v-model="field.value" class="form-control">
            <auto-currency v-if="type=='double'" :baseCurrency="currency" :minValue="'0'" v-model="field.price"></auto-currency>
            <span class="input-group-append">
              <button type="button" @click="RemoveField(key)" class="btn btn-outline-danger btn-flat"><i class="fas fa-times-circle"></i></button>
            </span>
          </div>
      <button type="button" @click="AddField" class="btn btn-primary btn-flat"><i class="fas fa-plus-circle"></i> Add</button>
    </div>
  </template>

  <script>           
  import AutoCurrency from '../components/autoCurrencyComponent' 
  export default {            
    props: ['dataValue', 'repeaterType', 'baseCurrency'], 
    components: {
        AutoCurrency
    },         
    data() {
      return {
          fields: [{}],
          type: '',
          currency:'USD'
      }
    },
    methods: {
      AddField() {
        if(this.type=='single') {
          this.fields.push({ value: '' });
        }
        if(this.type=='double') {
          this.fields.push({ value: '', price: 0 });
        }
      },
      RemoveField(key) {
        this.fields.splice(key, 1);
      },
      resetField() {
        this.fields = [{}];
      }
    },
    mounted() {
      this.fields = Vue.util.extend([{}], this.dataValue);
      this.type =  this.repeaterType;
      this.currency =  this.baseCurrency;
      this.$emit('dataFeature', this.fields);
      fire.$on('reset', this.resetField); 
    }
  }
</script>
<style lang='scss'>
  .repeater-input-container {
    margin-bottom:4px;
  }
</style>