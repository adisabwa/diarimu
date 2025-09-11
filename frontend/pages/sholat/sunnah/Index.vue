<style lang="postcss" scoped>
.el-input-number {
  :deep(.el-input__wrapper){
    @apply px-1 shadow-none
      border border-solid border-emerald-700;
  }
  :deep([role="button"]) {
    @apply shadow-none w-fit px-1 border-0;
  }
  :deep(.el-input__inner) {
    @apply w-[20px] text-left pl-2;
  }
}
</style>
<template>
  <div id="sholat" class="pt-[50px] sm:pt-5">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="reloadData"/>
    
    <InputScroll ref="inputScroll"
      :id-anggota="idAnggota"
      :data-tanggal="tanggal"
      class="[--bg-start-color:white]
        [--bg-end-color:theme(colors.red.100)]
        [--max-height:calc(60vh)]"
      @create-data="getData">
      <template #header>
        <img :src="sholat.image" height="90px" width="90px"
          class="absolute z-[0] top-[0px] right-[-20px]
            opacity-[0.5]"/>
      </template>
      <template #default="{ data, tanggal }">
        <template v-for="(sholat, key) in data">
          <div :class="`${ sholat.do ? 'bg-emerald-200 text-emerald-900  [&_*]:bg-emerald-200 [&_*]:text-emerald-900 ' : 'bg-white text-gray-400 [&_*]:bg-white [&_*]:text-gray-400 active:scale-90' }
            pt-4 pb-3 mb-4 px-5 mx-auto
            rounded-[15px] w-[-webkit-fill-available]
            shadow-md
            relative flex gap-x-2 items-center
            animate [--duration:0.5s]
            cursor-pointer`">
            <div class="w-full">
              <div class="text-[18px] font-semibold w-full
                flex gap-2 items-center"
                  @click="sholat.do = !sholat.do;
                  sholat.edit = false;
                  saveData(data, tanggal, key)">
                <icons v-if="sholat.do"
                  class="m-0 text-[30px]"
                  icon="material-symbols:check-circle-outline"/>
                <div v-else
                  class="w-[20px] h-[20px] rounded-full shrink-0
                  border border-solid border-gray-400 leading-0
                  "/>
                <div class="leading-[1.2]"
                  >{{ ucFirst(sholat.nama_sholat) }}
                </div>
              </div>
              <div v-if="sholat.do"
                class="ml-10 font-bold leading-[1]
                  text-sm w-fit">
                <div v-if="!sholat.edit"
                  @click="sholat.edit = true"
                  class="flex items-center translate-y-[-3px]">
                  <span class="mr-1 text-[13px] translate-y-[1px]"> {{ sholat.rakaat }} Rakaat</span>
                  <icons 
                    icon="flowbite:edit-solid" class="text-[20px] mr-1"/>
                </div>
              </div>
            </div>
            <div v-if="sholat.edit"
              class="flex flex-col items-center grow-0
                rounded-[5px] leading-[1]"> 
                <el-input-number v-model="sholat.rakaat" controls-position="right"
                  class="h-full border-0 w-[83px]"
                  :min="sholat.min" step="2">
                  <template #decrease-icon>
                    <icons icon="mdi:minus" class="text-sm"/>
                  </template>
                  <template #increase-icon>
                    <icons icon="mdi:plus" class="text-sm"/>
                  </template>
                  <template #prefix>
                    <icons icon="mdi:check" class="text-[16px] m-0
                      px-1 active:scale-75"
                      @click="saveData(data, tanggal, key); sholat.edit = false"/>  
                  </template>
                </el-input-number> 
                <div class="text-[12px] mt-1 font-semibold">Raka'at</div>     
            </div>                  
          </div>
        </template>
        <div class="text-center text-slate-400
            py-2 mb-5 px-5 mx-auto
            rounded-[15px] 
            w-[-webkit-fill-available]
            shadow-md
            relative flex items-center justify-center
            animate [--duration:0.5s]
            cursor-pointer
            active:scale-75"
            @click="showAddDialog(tanggal)">
            <icons icon="mdi:plus" class="text-[18px]" />
            <span class="font-semibold">Sholat Lainnya</span>
        </div>
      </template>
      <template #after="{ datas }">
        <div class="flex items-center gap-x-4 px-4 my-2">
          <div class="h-[2px] bg-slate-400 w-full"/>
          <div class="text-md font-montserrat text-center min-w-1/2 shrink-0">Penilaian Sholat Sunnah</div>
          <div class="h-[2px] bg-slate-400 w-full"/>
        </div>
        <star class="mx-auto mt-0 mb-5 relative gap-x-2" width="40px" :count="getCountSunnah(
          Object.values(datas[1])?.reduce((sum, item) => sum + (item.do ? parseInt(item.rakaat) : 0), 0)
        )" />
      </template>
    </InputScroll>
    <el-dialog v-model="showAdd"
      width="80%" 
      lock-scroll
      :append-to-body="true"
      header-class="text-center text-[17px]"
      body-class="relative">
      <template #header>
        <div>Tambah Data Sholat Baru</div>
      </template>
      <el-input v-model="filterSunnah" size="large"
        class="[&_*]:text-center mb-2"
        placeholder="Cari Sholat Sunnah">
      </el-input>
      <div class="relative flex flex-col
        max-h-[250px] overflow-auto">
        <template v-for="so in sholatSunnah.filter((res) => {
            let q = filterSunnah.toLowerCase()
            return res.nama_sholat.toLowerCase().includes(q)
          })">
          <div :class="[`px-4 py-2 mb-2
            text-center text-[16px]
            border border-solid border-slate-200
            rounded-xl
            shadow-md
            active:scale-[0.8]`,
            so.id == sunnahAdd ? 'bg-emerald-200 text-emerald-900 font-bold' : '']"
            @click="sunnahAdd = so.id">
            {{ so.nama_sholat }}
          </div>
        </template>
      </div>
      <div>
        <div v-if="showInput">
          <el-input v-model="sunnahInput" size="large"
            class="[&_*]:text-center"
            placeholder="Sholat Sunnah Baru">
            <template #prepend>
              <icons icon="mdi:close" class="m-0 active:scale-75"
                @click="showInput = false"/>
            </template>
            <template #append>
              <icons icon="mdi:check" class="m-0 active:scale-75"
                @click="saveSunnah"/>
            </template>
          </el-input>
        </div>
        <div v-else
          class="px-4 py-2 mb-1
          text-center text-[16px] text-white
          bg-slate-700
          border border-solid border-slate-200
          rounded-xl
          shadow-md
          flex items-center justify-center
          active:scale-[0.8]"
          @click="showInput = true">
          <icons icon="mdi:plus" />
          Tambah Baru
        </div>
      </div>
      <template #footer>
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button @click="addInput" type="success">Simpan</el-button>
      </template>
    </el-dialog>
    <statistic-data class="bg-white/[0.9] rounded-[10px] mb-3 p-0
          [--text-color:theme(colors.rose.900)]
          [--bg-color:theme(colors.rose.50)]
          [--border-color:theme(colors.rose.400)]
          [--bg-button-color:theme(colors.rose.100)]
          [--button-color:theme(colors.rose.200)]"
      ref="statisticDataSholat"
      :id-anggota="idAnggota"
      href-dashboard="sholat/sunnah/dashboard"
      href="sholat/sunnah"
      :group-by="['tanggal','id_anggota']"
      href-delete="sholat/sunnah/delete"
      :add-options-chart="{
        scales:{
          y:{
            title:{display:true, text:'Jml Rakaat'},
            ticks: {stepSize:2}
          }}}"
        @edit-data="editData"
      y-label-table="Total Rakaat"
      >
      <template #header>
        <div class="text-[var(--text-color)]">Data Sholat Sunnah</div>
      </template>
      <template #title="{ data }">
        {{ dateDayIndo(data.tanggal)}}
      </template>
      <template #content="{ data }">
        <div class="flex items-center"
          @click="data.show_detail = !data.show_detail">
          <icons v-if="data.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
          <icons v-else icon="fe:arrow-up" class="text-[12px]"/>
          Sholat Sunnah {{ data.total_rakaat }} Raka'at
          <star :count="getCountSunnah(data.total_rakaat)" width="12px"
            class="ml-3 gap-x-[2px]"/>
        </div>
        <ol v-show="data.show_detail"
          class="pl-[30px] italic mt-0 mb-1">
          <li v-for="(j) in data.daftar_sholat.split('/')"
            class="pl-1">
            {{ getLabelSholat(j) }}
          </li>
        </ol>
      </template>
    </statistic-data>
  </div>
</template>

<script setup>
 import { getCountSunnah } from '@/helpers/sholat.js'
</script>

<script>
import { mapState } from 'pinia';
import FilterAnggota from '../../components/FilterAnggota.vue';
import InputScroll from '../components/InputScroll.vue';
import { topMenu } from '@/helpers/menus.js'
import StatisticData from '@/pages/components/StatisticData.vue';

export default {
  name: "sholat",
  components: {
    StatisticData,
    FilterAnggota,
    InputScroll,
  },
  data: function() {
    return {
      showAdd:false,
      idAnggota:null,
      formKey:1,
      tanggal:'',
      tipe:'',
      hideOnClick:true,
      sholat: topMenu.sholatSunnah,
      sholatSunnah:[],
      filterSunnah:'',
      showInput:false,
      sunnahAdd:'',
      sunnahInput:'',
    };
  },
  watch: {
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    totals(){
      let total = []
      this.datas.forEach((data, i) => {
        // console.log(data)
        total[i]= Object.values(data)?.reduce((sum, item) => sum + (item.do ? parseInt(item.rakaat) : 0), 0)
      })
      // console.log(total)
      return total
    },
  },
  methods: {
    showAddDialog(tanggal){
      this.showAdd = true
      this.tanggal = tanggal
    },
    getLabelSholat(sholat){
      // console.log(sholat)
      let data = sholat.split('-')
      return `Sholat ${data[0]} - ${data[1]} Raka'at`
    },
    getData: async function({data, tanggal, id}) {
      // setTimeout(() => {
      console.log(this.idAnggota)
        this.$http.get('sholat/sunnah/get_initial', {
            params: {
              id_anggota:this.idAnggota,
              tanggal:tanggal,
            }
          })
            .then(res => {
              let data = res.data
              this.$refs.inputScroll.changeData(tanggal, data)
            })
            .catch(err => {
              console.log(err)
              this.$refs.inputScroll.changeData(tanggal)
              this.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengambil data',
                position: 'bottom-right',
              });
            })
      // }, 1000)
    },
    getAllSunnah(initial = true){
      this.$http.get('data/sholat-sunnah', {
            params: {
              where:{
                type:'additional'
              },
              limit:0,
            }
          })
            .then(res => {
              let data = res.data
              this.sholatSunnah = data
              if (!initial)
                this.sunnahAdd = data[data.length - 1]?.id
            })
    },
    saveSunnah()
    {
      let form = {
        id:-1,
        nama_sholat:this.sunnahInput,
      }
      var formData = window.jsonToFormData(form); 
      this.$http.post('data/sholat-sunnah/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          this.getAllSunnah(false);
          this.showInput = false
        })
        .catch(err => {
          
        });
    },
    async addInput(){
      let ind = 1
      let kolom = this.sholatSunnah.findIndex(res => res.id == this.sunnahAdd)
      let sholat = this.sholatSunnah[kolom]
      console.log(sholat, this.tanggal)
      let data = {}
      data[sholat.id] = {
        "id_sholat": sholat.id,
        "nama_sholat": sholat.nama_sholat,
        "do": true,
        "edit": false,
        "rakaat": 2,
        "min": 2,
      }
      await this.saveData(data, this.tanggal, sholat.id, {
        func: this.getData,
        arg: {tanggal:this.tanggal}
      })
      this.showAdd = false
    },
    async saveData(data, tanggal, kolom, callback = null){
      let form = {
        id_anggota:this.idAnggota,
        tanggal:tanggal,
        id_sholat:data[kolom].id_sholat,
        rakaat:data[kolom].rakaat,
        insert:data[kolom].do,
      }
      console.log(form)
      var formData = window.jsonToFormData(form); 
      this.$http.post('sholat/sunnah/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          // this.getData()
          if (callback) {
            callback.func(callback.arg)
          }
          this.$refs.statisticDataSholat.updateChart()
        })
        .catch(err => {
          
        });
    },
    async editData({tanggal}){
      console.log(tanggal)
      this.tanggal = tanggal
      window.scrollTo({
        top:0,
        behavior: 'smooth',
      })
    },
    reloadData(){
      // console.log(this.idAnggota)
      this.$refs.inputScroll.resetData()
      this.getLast()
      // this.formKey++
    },
  },
  created: function() {
    this.getAllSunnah()
  },

  mounted: function() {
    this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
  },
  unmounted(){
    // window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>
