
<template>
  <div id="sholat relative" class="pt-[50px] sm:pt-5">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="reloadData"/>
    <InputScroll ref="inputScroll"
      :id-anggota="idAnggota"
      :data-tanggal="tanggal"
      class="[--bg-start-color:white]
        [--bg-end-color:theme(colors.purple.100)]"
      @create-data="getData"
      :default-data="dataSholat">
      <template #header>
        <img :src="sholat.image" height="90px" width="90px"
          class="absolute z-[0] top-[0px] right-[-20px]
            opacity-[0.5]"/>
      </template>
      <template #default="{ data, tanggal }">
        <template v-for="sholat in data.sholats">
          <div :class="`${setStatusColor(sholat.value)}
            pt-4 pb-3 mb-4 px-7 mx-auto
            rounded-[15px] min-w-[240px] max-w-[300px]
            shadow-md
            relative flex gap-x-3 items-center`">
            <div class="text-[18px] font-bold leading-[1.3] w-full">
              <div>{{ ucFirst(sholat.nama_kolom) }}</div>
              <div class="text-[12px] font-semibold opacity-70">
                {{ getLabel(sholat.value) }}
              </div>
            </div>
            <div class="flex gap-x-1 mx-3">
              <star :count="getCount(sholat.value)"/>
            </div>
            <el-dropdown
              trigger="click"
              @command="(res) => {
                sholat.value = res
                saveData(data, tanggal, sholat.nama_kolom)
              }"
              :popper-class="`${setStatusColor(sholat.value)}`"
              class="h-[40px]">
              <el-button class="rounded-full h-full w-[40px]">
                <icons icon="mdi:edit" class="m-0"/>
              </el-button>
              <template #dropdown>
                <el-dropdown-menu>
                  <template v-for="o in options">
                    <el-dropdown-item :command="o.value"
                      :class="`${o.value == sholat.value ? 'font-bold' : ''}`">
                      {{ o.label }} ( {{ o.value }} ) 
                    </el-dropdown-item>
                  </template>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </div>
        </template>
      </template>
    </InputScroll>
    <el-card v-show="['user','super-admin'].includes(user.role)"
      class="relative w-full
      overflow-hidden
    bg-white/[0.9] 
      rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 leading-[1]">
      <div class="relative flex px-3 py-5 gap-3 justify-center">
        <template v-for="(data, ind) in {last:lastData, best:bestData}">
          <div class="shrink-1 text-center py-4 px-4
            border-2 border-solid border-indigo-200
            bg-purple-100/[0.4]
            rounded-[20px]">
            <div class="text-gray-500 font-bold w-[100px] text-[14px]
              mb-4">Nilai {{ ind == 'best' ? 'Terbaik' : 'Terakhir' }}</div>
            <template v-if="!isEmpty(data.tanggal)">
              <div class="mb-2  text-gray-500  text-[13px]"><b>( {{ dateShortIndo(data.tanggal) }} )</b></div>
              <div class="mb-2 text-[45px] font-semibold leading-[1]">{{ data.total_score }}</div>
              <div class="flex items-start justify-center">
                <star width="28px" :count="getCount(data.total_score / 5)"
                  class="gap-0 *:mx-[-3px] [&>*:not(:first-child):not(:last-child)>*]:w-[34px]"/>
              </div>
            </template>
            <template v-else>
              <div class="mb-2  text-gray-500  text-[13px]"><b>Belum ada Data</b></div>
            </template>
          </div>
        </template>
      </div>
    </el-card>
    <statistic-data class="bg-white/[0.9] rounded-[10px] mb-3 p-0
          [--text-color:theme(colors.purple.900)]
          [--bg-color:theme(colors.purple.50)]
          [--border-color:theme(colors.purple.400)]
          [--bg-button-color:theme(colors.purple.100)]
          [--button-color:theme(colors.purple.200)]"
      ref="statisticDataSholat"
      :id-anggota="idAnggota"
      href-dashboard="sholat/wajib/dashboard"
      href="sholat/wajib"
      href-delete="sholat/wajib/delete"
      :add-options-chart="{scales:{y:{
        title:{display:true, text:'Total Score'},
        ticks: {stepSize:50}
      }}}"
      y-label-table="Total Score"
      @edit-data="editData">
      >
      <template #header>
        <div class="text-[var(--text-color)]">Data Sholat Wajib</div>
      </template>
      <template #title="{ data }">
        {{ dateDayIndo(data.tanggal)}}
      </template>
      <template #content="{ data }">
        <div class="flex items-center"
          @click="data.show_detail = !data.show_detail">
          <icons v-if="data.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
          <icons v-else icon="fe:arrow-up" class="text-[12px]"/>
          Sholat Wajib {{ (
            (data.shubuh > 0 ? 2 : 0) + (data.dhuhur > 0 ? 4 : 0) + (data.asar > 0 ? 4 : 0) + (data.maghrib > 0 ? 3 : 0) + (data.isya > 0 ? 4 : 0) 
          ) }} Raka'at
        </div>
        <ol v-show="data.show_detail"
          class="pl-[30px] italic mt-0 mb-1">
          <li v-for="ind in ['shubuh','dhuhur','asar','maghrib','isya']"
            class="pl-1">
            <div class="flex items-center gap-x-3">
              Sholat {{ ucFirst(ind) }} ( {{ getLabel(data[ind]) }} ) 
              <template v-if="data[ind] >= 25">
                <star :count="getCount(data[ind])" width="12px"
                  class="gap-x-[2px]"/>
              </template>
            </div>
          </li>
        </ol>
      </template>
    </statistic-data>
  </div>
</template>

<script setup>
  import { setStatusColor, options, getLabel, getCount } from '@/helpers/sholat.js'
</script>

<script>
import { mapState, mapActions } from 'pinia';
import { topMenu } from '@/helpers/menus.js'
import FilterAnggota from '../../components/FilterAnggota.vue';
import InputScroll from '../components/InputScroll.vue';
import StatisticData from '@/pages/components/StatisticData.vue';

export default {
  name: "sholat",
  components: {
    FilterAnggota,
    InputScroll,
    StatisticData,
  },
  data: function() {
    return {
      idAnggota:null,
      formKey:1,
      tanggal:'',
      dataSholat: {
        id:'-1',
        tanggal:'',
        sholats:{
          shubuh:{
            nama_kolom: 'shubuh',
            value:null,
          },
          dhuhur:{
            nama_kolom: 'dhuhur',
            value:null,
          },
          asar:{
            nama_kolom: 'asar',
            value:null,
          },
          maghrib:{
            nama_kolom: 'maghrib',
            value:null,
          },
          isya:{
            nama_kolom: 'isya',
            value:null,
          },
        }
      },
      lastData:{
        tanggal:'',
        total_score:'',
      },
      bestData:{
        tanggal:'',
        total_score:'',
      },
      sholat: topMenu.sholatWajib,
    };
  },
  watch: {
    
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
  },
  methods: {
    getData: async function({data, tanggal, id}) {
      let vm = this
      let where = {
        id_anggota: vm.idAnggota,
      }
      if (id != -1) {
        where.id = id
      } else {
        where.tanggal = tanggal
      }
      // setTimeout(() => {
        vm.$http.get('sholat/wajib/get_where', {
            params: {
              where: where
            }
          })
            .then(res => {
              let data = res.data
              let updateData = JSON.parse(JSON.stringify(this.dataSholat))
              updateData.id = data?.id ?? -1
              updateData.tanggal = data?.tanggal ?? ''
              let keys = Object.keys(vm.dataSholat.sholats)
              keys.forEach(( k, ind) => {
                if (data[k] !== null) {
                  updateData.sholats[k].value = parseInt(data[k])
                } 
                else {
                  updateData.sholats[k].value = null
                }
                // console.log(index, k, vm.datas[index][k])
              })
              this.$refs.inputScroll.changeData(tanggal, updateData)
            })
            .catch(err => {
              console.log(err)
              this.$refs.inputScroll.changeData(tanggal)
              vm.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengambil data',
                position: 'bottom-right',
              });
            })
      // }, 1000)
    },
    getLast(){
      this.resetObjectValue(this.lastData)
      this.resetObjectValue(this.bestData)
       this.$http.get('sholat/wajib/get_last_and_best', {
            params: {
              id_anggota:this.idAnggota,
            }
          })
        .then( res => {
          let data = res.data
          this.fillObjectValue(this.lastData, data?.last)
          this.fillObjectValue(this.bestData, data?.best)
        })
    },
    async editData({tanggal}){
      console.log(tanggal)
      this.tanggal = tanggal
      window.scrollTo({
        top:0,
        behavior: 'smooth',
      })
    },
    saveData(data, tanggal, kolom){
      let form = {
        id:data.id,
        id_anggota:this.idAnggota,
        tanggal:tanggal,
      }
      form[kolom] = data.sholats[kolom].value
      console.log(form)
      var formData = window.jsonToFormData(form); 
      this.$http.post('sholat/wajib/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          let res = result.data
          this.$refs.inputScroll.changeData(tanggal, {
            id: res.id
          })
          this.getLast()
          this.$refs.statisticDataSholat.updateChart()
        })
        .catch(err => {
          console.log(err)
        });
    },
    reloadData(){
      // console.log(this.idAnggota)
      this.$refs.inputScroll.resetData()
      this.getLast()
      // this.formKey++
    },
  },
  created: function() {
  },
  mounted: function() {
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
    this.getLast()
  },
  unmounted(){
    // window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>
