<template>
    <div>
        <input type="text" v-model="displayValue" class="form-control" @blur="isInputActive = false" @focus="isInputActive = true"/>
    </div>
</template>        
<script>    
    export default {      
        props: ["value", "baseCurrency", 'minValue'],    
        data: function() {
            return {
                isInputActive: false
            }
        },
        computed: {
            displayValue: {
                get: function() {
                    if (this.isInputActive) {
                        return this.value.toString()
                    } else {
                        return this.baseCurrency+" " + this.value.toFixed(2).replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1,")
                    }
                },
                set: function(modifiedValue) {
                    let newValue = parseFloat(modifiedValue.replace(/[^\d\.]/g, ""))
                    if (isNaN(newValue) || newValue==0) {
                        newValue = parseInt(this.minValue);
                    }
                    this.$emit('input', newValue)
                }
            }
        }
    }
</script>