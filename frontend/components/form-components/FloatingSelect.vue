<template>
  <div>
    <el-input v-model="vModel" :placeholder="placeholder" 
      :clearable="clearable"
      :size="size"
      @change="changedValue(value)">
      <template #prepend v-if="$slots.prefix">
        <slot name="prefix" />
      </template>
    </el-input>
    <div>
      <slot name="default" :FloatOption="FloatOption"/>
    </div>
  </div>
</template>

<script>
import Option from './Option.vue'

export default {
  name:'floating-select',
  components:{
    FloatOption: Option,
  },
  emits:['update:value','change'],
  props:{
    value:{type:[String, Number], default:'',},
    placeholder:{type:[String], default:'',},
    size:{type:[String], default:'',},
    filterable:{type:[Boolean], default:false,},
    clearable:{type:[Boolean], default:false,},
    multiple:{type:[Boolean], default:false,},
  },
  data: function() {
    return {
      vModel:'',
      showModal:true,
    }
  },
  watch:{
    vModel(val){
      console.log(val)
      this.$emit('update:value', val)
    },
    value: {
      immediate: true,
      async handler(val) {
        console.log(Val)
        this.vModel = val;
      },
    },
  },
  methods:{
    changedValue(val){
      this.$emit('change',val)
    }
  }

}
</script>