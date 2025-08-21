<template>
    <div>
      <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
        body-class="py-3 px-0"
        header-class="py-3 font-bold text-[16px]
          text-lime-800
          flex justify-between items-center" >
        <template #header v-if="$slots.header">
          <div>
            <slot name="header"/>
          </div>
          <div class="flex items-center gap-1
            [&_*]:text-[20px] text-[var(--border-color)]">
            <icons icon="fa6-solid:chart-line" 
              @click="showData='chart'"
              :class="`cursor-pointer ${showData == 'chart' ? 'text-[var(--text-color)]' : ''}`"/>
            <icons icon="material-symbols:view-list" 
              @click="showData='list'"
              :class="`cursor-pointer ${showData == 'list' ? 'text-[var(--text-color)]' : ''}`"/>
            <icons icon="material-symbols:table" 
              @click="showData='table'"
              :class="`cursor-pointer ${showData == 'table' ? 'text-[var(--text-color)]' : ''}`"/>
          </div>
        </template>
        <chart v-if="showData == 'chart'" 
          ref="ChartData" 
          :href="hrefDashboard"
          :id-anggota="idAnggota"
          :add-options="addOptionsChart"
          class="px-4"/>
        <ListData ref="ListData"
          :id-anggota="idAnggota"
          :href="href"
          :href-delete="hrefDelete"
          :group-by="groupBy"
          v-if="showData =='list'"
          @edit-data="(({id}) => {
            $emit('editData', {id})
          })">
          <template #subtitle="{ data }">
            <slot name="subtitle" :data="data" />
          </template>
          <template #title="{ data }">
            <slot name="title" :data="data"/>
          </template>
          <template #content="{ data }">
            <slot name="content" :data="data" />
          </template>
        </ListData>
        <TableData v-if="showData == 'table'" 
          ref="TableData" 
          :href="hrefDashboard"
          :id-anggota="idAnggota"
          :add-options="{}"
          class="px-4"/>
      </el-card>
    </div>
</template>
  
<script>
  import ListData from '@/pages/components/ListData.vue'
  import Chart from '@/pages/components/DataChart.vue'
  import TableData from '@/pages/components/TableData.vue'
  
  export default {
    name: "statistic-",
    emits: ['editData'],
    components: {
      Chart,
      ListData,
      TableData,
    },
    props:{
      idAnggota:{type:[String, Number],default:'-1'},
      href:{type:String,default:''},
      hrefDashboard:{type:String,default:''},
      hrefDelete:{type:String,default:''},
      addOptionsChart:{type:Object,default:{}},
      groupBy:{type:Array, default:[]},
    },
    data: function() {
      return {
        showData:'chart',
      };
    },
    watch: {
      showData: {
        immediate: true,
        handler(val){
          this.updateChart()
        }
      },
      idAnggota: {
        immediate: true,
        handler(val){
          this.updateChart()
        }
      },
    },  
    computed: {
      
    },
    methods: {
      updateChart(){
        if (this.showData == 'chart') this.$refs.ChartData?.getChart();
        if (this.showData == 'list') this.$refs.ListData?.getData(true);
        if (this.showData == 'table') this.$refs.TableData?.getChart(true);
      }
    },
    created: function() {
      
    },
  }
  </script>
  