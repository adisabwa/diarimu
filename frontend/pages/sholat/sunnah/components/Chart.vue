<template>
  <div class="px-4">
    <div class="flex gap-x-3">
      <el-select size="small" v-model="tipe" placeholder="Pilih Tipe Rekapitulasi"
        @change="getChart">
        <el-option value="week" label="7 Hari" />
        <el-option value="month" label="1 Bulan" />
      </el-select>
      <el-select size="small" v-model="dates" placeholder="Pilih Tanggal"
        ref="dateSelect"
        @change="getChart">
        <el-option :value="getValue(o)"
          v-for="o in dateOptions"
          :key="o.start"
          :label="`${dateMonthIndo(o.start)} s/d ${dateMonthIndo(o.end)}`"/>
        <template #footer>
          <el-button text bg size="small"
            @click="dateFunction(addDay(dateOptions[dateOptions.length - 1]?.start,'-1'))">
            Tanggal Sebelumnya
          </el-button>
        </template>
      </el-select>
    </div>
    <div class="mb-4">
      <div v-if="!isEmpty(statistic.datasets)">
        <line-chart class="h-[300px]"
          :statistic="statistic" :max="max" :min="min" />
      </div>
    </div>
  </div>
</template>


<script>
import LineChart from '@/components/charts/Line.vue'

export default {
  name: "sunnah",
  components: {
    LineChart,
  },
  data: function() {
    return {
      tipe:'',
      statistic:{
				labels:[],
				datasets:[],
      },
      max:5,
      min:-1,
      dates:'',
			dateOptions:[],
      selectKey:0,
		}
	},
  watch:{
    async tipe(val){
      this.dateOptions = []
      this.dateFunction(this.dateNow(), 4)
      this.dates = this.getValue(this.dateOptions[0]);
      this.selectKey = this.selectKey + 1
      console.log(this.selectKey)
      this.getChart()
    }
  },
	methods:{
    getValue(obj){
      return obj.start + '/' + obj.end
    },
    async dateFunction(last, number = 1){
      console.log(last)
      let newData = [];
      if (this.tipe == 'week')
        newData = this.getWeeklyRanges(last, number); 
      else if (this.tipe == 'month')
        newData = this.getMonthlyRanges(last, number); 
      
      console.log(newData)
      let newOpt = [...this.dateOptions, ...newData]
      this.dateOptions = JSON.parse(JSON.stringify(newOpt))
      // Let Vue update DOM, then manually refresh dropdown height
      // this.$refs.dateSelect.blur();
      this.$nextTick(() => {
        this.$refs.dateSelect.focus();
      });
    },
    async getChart(){
      // return;
      let dates = this.dates.split('/')
      await this.$http.get('sholat/sunnah/dashboard', {
          params: {
            start:dates[0],
            end:dates[1],
            tipe:this.tipe
          }
        })
          .then(res => {
            let data = res.data
            this.statistic = data
            this.min = data.min
            this.max = data.max
            this.loaded = true
          })
          .catch(err => {
            this.$notify({
              type:'error',
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right',
            });
          })
    }
	},
	mounted(){
    this.tipe = 'week'
	}
}
</script>