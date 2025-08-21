<template>
    <div class="relative">
      <div class="flex gap-x-3 mb-3">
        <slot name="filter" :filter="filter"/>
        <el-select size="small" v-model="filter.tipe" placeholder="Pilih Tipe Rekapitulasi"
          @change="getChart">
          <el-option value="week" label="7 Hari" />
          <el-option value="month" label="1 Bulan" />
        </el-select>
        <el-select size="small" v-model="filter.dates" placeholder="Pilih Tanggal"
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
      <div class="mb-4 relative w-full min-h-[calc(100vh-400px)]">
        <div v-if="!isEmpty(statistic.datasets)"
          class="absolute w-full h-full overflow-auto">
          <table class="table [&_td]:border-solid [&_td]:border [&_td]:border-[var(--text-color)]">
            <thead>
                <tr class="bg-[var(--bg-color)]">
                    <td rowspan="2" width="20">No</td>
                    <td rowspan="2" >Nama</td>
                    <td :colspan="statistic.labels.length"
                        class="text-left">
                        Tanggal / Jumlah Ayat
                    </td>
                </tr>
                <tr class="bg-[var(--bg-color)]">
                    <td v-for="(label, key) in statistic.labels"
                        class="text-center">
                        {{ label }}
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(stat, key) in statistic.datasets">
                    <td>{{ key + 1 }}</td>
                    <td>{{ stat.label }}</td>
                    <td v-for="d in stat.data"
                        class="text-center">
                        {{ d }}
                    </td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</template>
  
  
<script>
  
  export default {
    name: "table-data",
    props:{
      idAnggota:{
        type:[String, Number],
        default:null,
      },
      addOptions:{
        type:[Object],
        default:{},
      },
      href:{
        type:[String],
        default:null,
      },
    },
    components: {
    },
    data: function() {
      return {
        filter: {
          tipe:'',
          dates:'',
        },
        statistic:{
                  labels:[],
                  datasets:[],
        },
        max:5,
        min:-1,
              dateOptions:[],
        selectKey:0,
          }
      },
    watch:{
      async 'filter.tipe'(val){
        let vm = this
        vm.dateOptions = []
        vm.dateFunction(vm.dateNow(), 4)
        vm.filter.dates = vm.getValue(vm.dateOptions[0]);
        vm.selectKey = vm.selectKey + 1
        setTimeout(() => {
          vm.getChart()
        },300)
      },
      idAnggota(val){
        let vm = this
        setTimeout(() => {
          vm.getChart()
        },300)
      },
    },
    computed:{
      
    },
      methods:{
      getValue(obj){
        return obj.start + '/' + obj.end
      },
      async dateFunction(last, number = 1){
        // console.log(last)
        let newData = [];
        if (this.filter.tipe == 'week')
          newData = this.getWeeklyRanges(last, number); 
        else if (this.filter.tipe == 'month')
          newData = this.getMonthlyRanges(last, number); 
        
        // console.log(newData)
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
        let dates = this.filter.dates.split('/')
        await this.$http.get(this.href, {
            params: {
              start:dates[0],
              end:dates[1],
              tipe:this.filter.tipe,
              id_anggota:this.idAnggota
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
      this.filter.tipe = 'week'
      }
  }
  </script>