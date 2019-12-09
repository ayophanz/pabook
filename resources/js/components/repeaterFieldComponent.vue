<script>
Vue.component('repeater-input', {
  template: `
            <div>
            <div v-for="(field, key) in fields" style="margin-bottom:4px;" class="input-group input-group">
                  <input type="text" placeholder="name" v-model="field.value" class="form-control">
                  <input v-if="type=='double'" type="text" placeholder="add-on price" v-model="field.value" class="form-control">
                  <span class="input-group-append">
                    <button type="button" @click="RemoveField(key)" class="btn btn-outline-danger btn-flat"><i class="fas fa-times-circle"></i></button>
                  </span>
                </div>
            <button type="button" @click="AddField" class="btn btn-primary btn-flat"><i class="fas fa-plus-circle"></i> Add</button>
            </div>
            `,
  props: ['dataValue', 'repeaterType'],          
  data() {
    return {
        fields: [{}],
        type: ''
    }
  },
  methods: {
    AddField() {
      this.$emit('dataFeature', this.fields);
      if(this.type=='single') {
        this.fields.push({ value: '' });
      }
      if(this.type=='double') {
        this.fields.push({ value: '', price: '' });
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
    fire.$on('reset', this.resetField); 
  }
});
</script>