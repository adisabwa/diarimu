<template>
  <div>
    <el-card id="list-data" class="relative overflow-hidden
         rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="px-6 pt-6 pb-2 text-[15px] font-montserrat font-bold text-center"
      body-class="py-4 px-0">
      <template #header v-if="$slots.headerList">
        <div>
          <slot name="headerList"/>
        </div>
      </template>     
      <ListData ref="ListData"
        :key="'ListData'+keyList"
        :id-anggota="idAnggota"
        :href="href"
        :href-delete="hrefDelete"
        :group-by="groupBy"
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
    </el-card>
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
          <icons icon="material-symbols:table" 
            @click="showData='table'"
            :class="`cursor-pointer ${showData == 'table' ? 'text-[var(--text-color)]' : ''}`"/>
        </div>
      </template>
      <chart v-if="showData == 'chart'" 
        :key="'ChartData'+keyChart"
        ref="ChartData" 
        :href="hrefDashboard"
        :id-anggota="idAnggota"
        :add-options="addOptionsChart"
        class="px-4">
         <template #filter="{filter}" v-if="$slots.chartFilter">
          <slot name="chartFilter" :filter="filter" />
         </template>
      </chart>
      <TableData v-if="showData == 'table'" 
        :key="'TableData'+keyTable"
        ref="TableData" 
        :href="hrefDashboard"
        :hrefDownload="hrefDownload || (href + '/download')"
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
  import { mapState } from 'pinia'
  
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
      hrefDownload:{type:String,default:''},
      hrefDelete:{type:String,default:''},
      addOptionsChart:{type:Object,default:{}},
      groupBy:{type:Array, default:[]},
    },
    data: function() {
      return {
        showData:'chart',
        keyChart:1,
        keyTable:1,
        keyList:1,
      };
    },
    watch: {
      showData: {
        immediate: true,
        handler(val){
          this.updateChart()
        }
      },
      defaultShowData(val){
        this.showData = val
      },
      idAnggota: {
        immediate: true,
        handler(val){
          this.updateChart()
        }
      },
    },  
    computed: {
      ...mapState(useAuthStore,{
        role: 'role',
      }),
    },
    methods: {
      updateChart(){
        this.updateChartDirect(this.showData)
        this.updateChartDirect()
      },
      updateChartDirect(type = 'list', reset = false){
        if (type == 'chart') {
          this.$refs.ChartData?.getChart();
          if (reset) this.keyChart++
        }
        if (type == 'table') {
          this.$refs.TableData?.getChart(true);
          if (reset) this.keyTable++
        }
         if (type == 'list') {
          this.$refs.ListData?.getData(true);
          if (reset) this.keyList++
         }
      }
    },
    created: function() {
      
    },
  }
  </script>
  