<template>
  <div>
    <el-input v-model="labelModel" :placeholder="placeholder" 
      :clearable="clearable"
      :size="size"
      @clear="vModel = null"
      @click="showModal = true">
      <template #prepend v-if="prefix">
        {{ prefix }}
      </template>
    </el-input>
    <el-dialog v-model="showModal"
      :class="['min-w-[250px] max-w-[80%] p-0 py-4 mt-28',
        type == 'scroll' ? 'mt-40' : '',
      ]"
      header-class="flex items-center"
      body-class="relative px-0  text-[16px]">
      <template #header v-if="filterable">
        <el-input id="filterSelect" v-model="searchData" placeholder="Cari Data" 
          class="px-5" size="large"/>
      </template>
      <div v-if="type =='select'"
        class="max-h-[65vh] overflow-scroll">
        <div v-for="o in optionsFilter"
          :class="['px-5 py-1 active:bg-teal-100',
            (vModel == o.value ? 'bg-teal-100' : '')
          ]"
          @click="vModel = o.value;showModal = false;">
          <template v-if="$slots.prefix">
            <slot name="prefix" />
          </template>
          {{ o.label }}
        </div>
        <template v-if="$slots.footer">
          <el-divider class="my-2"/>
          <slot name="footer" />
        </template>
      </div>
      <div v-else-if="type=='scroll'"
      :class="['flex justify-center items-center gap-4 p-4 rounded-xl ']">
        <div class="relative h-[130px] w-[150px] mx-auto">
          <icons icon="fe:arrow-up" class="absolute z-[20] left-1/2 -translate-x-1/2"/>
          <ScrollPicker :options="listOptions" v-model:modelValue="vModel" />
          <icons icon="fe:arrow-down" class="absolute z-[20] bottom-0 left-1/2 -translate-x-1/2"/>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>

import  { VueScrollPicker } from 'vue-scroll-picker'

export default {
  name:'floating-select',
  components:{
    ScrollPicker:VueScrollPicker,
  },
  emits:['update:value','change'],
  props:{
    value:{type:[String, Number], default:'',},
    placeholder:{type:[String], default:'',},
    size:{type:[String], default:'',},
    filterable:{type:[Boolean], default:false,},
    clearable:{type:[Boolean], default:false,},
    multiple:{type:[Boolean], default:false,},
    options:{type:[Array, Object], default:[],},
    type:{type:[String], default:'select',},
    prefix:{type:[String], default:'',},
  },
  data: function() {
    return {
      vModel:'',
      showModal:false,
      searchData:'',
      labelModel:'',
      listOptions:[],
    }
  },
  computed:{
    optionsFilter: function() {
      var q = this.searchData.toLowerCase();
      if (q.length > 0) {
        return this.listOptions.filter(data => {
            return data.label.toLowerCase().includes(q)
          });
      }
      return this.listOptions;
    },  
  },
  watch:{
    showModal:{
      immediate: true,
        async handler(val) {
        let vm = this
        if (val) {
          setTimeout(() => {
            vm.jquery('#filterSelect.el-input__inner')[0]?.focus();
          }, 500)
          vm.searchData = ''
          if (vm.isEmpty(vm.vModel))
            vm.vModel = vm.listOptions[0]?.value
        } else {
          vm.changedValue(vm.vModel)
        }
      },
    },
    vModel(val){
      // console.log('model', val)
      this.selectOption(val)
      this.$emit('update:value', val)
    },
    value: {
      immediate: true,
      async handler(val) {
        // console.log(this.placeholder, val)
        this.vModel = val;
      },
    },
    options: {
      immediate: true,
      deep:true,
      async handler(val) {
      // console.log(val)
        let pre = this.prefix
        let opt = typeof val == 'object' ? Object.values(val) : val
        opt.forEach(d => {
          d.name = pre + ' ' + d.label
        })
        this.listOptions = opt
      }
    }
  },
  methods:{
    changedValue(val){
      // console.log('change float')
      this.$emit('change',val)
    },
    selectOption(val){
      let filter = this.listOptions.filter(d => {
        return d.value == val
      })[0]
      this.labelModel = filter?.label
    }
  }

}
</script>