<template>
  <div id="sholat" class="pt-0">
    <el-card class="relative overflow-hidden
       bg-gradient-to-tr from-white/[0.8] from-50% to-yellow-200/[0.7] rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 ">
      <img :src="sholat.image" height="90px" width="90px"
          class="absolute z-[0] top-[-10px] right-[-20px]
            opacity-[0.5]"/>
      <div class="relative w-fit overflow-x-scroll z-[10]
        snap-mandatory snap-x">
        <div class="w-[200%] flex h-[90px] py-4">
          <div class="snap-center w-full px-6 h-[90px]">
            <div class="z-[10] text-gray-500">Setoran Terakhir : </div>
            <div class="z-[10] text-2xl font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
            </div>
            <div class="z-[10] text-gray-500">Terakhir : <b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
            
          </div>
          <div class="snap-center w-full px-6 h-[90px] overflow-scroll">
            <div class="z-[10] mb-2 text-md font-bold italic">
              <div>Total Hafalan :</div> 
              <ol class="m-0 pl-4 text-[90%]">
                <template v-for="data in juz">
                  <li>{{ data.nama_surat_mulai }} :{{ data.ayat_mulai }} ( Juz {{ data.juz_mulai }} ) s/d {{ data.nama_surat_selesai }} : {{ data.ayat_selesai }} ( Juz {{ data.juz_selesai }} )</li>
                </template>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="body-sholat relative py-3 px-0 overflow-x-scroll
        snap-x snap-mandatory
        flex"
      header-class="py-3 font-bold text-xl">
      <template #header>
        <div @click="removeClass('.body-sholat','snap-x snap-mandatory');
          scrollElement('.body-sholat','#2');
        ">Setoran Hafalan hari ini</div>
      </template>
        <div id="1" class="shrink-0 snap-center w-full px-6 h-[90px]">
          <div class="z-[10] text-gray-500">Setoran Terakhir : </div>
          <div class="z-[10] text-2xl font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
          </div>
          <div class="z-[10] text-gray-500">Terakhir : <b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
          
        </div>
        <div id="2" class="shrink-0 snap-center w-full px-6 h-[90px] overflow-scroll">
          <div class="z-[10] mb-2 text-md font-bold italic">
            <div>Total Hafalan :</div> 
            <ol class="m-0 pl-4 text-[90%]">
              <template v-for="data in juz">
                <li>{{ data.nama_surat_mulai }} :{{ data.ayat_mulai }} ( Juz {{ data.juz_mulai }} ) s/d {{ data.nama_surat_selesai }} : {{ data.ayat_selesai }} ( Juz {{ data.juz_selesai }} )</li>
              </template>
            </ol>
          </div>
        </div>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Rekap Mengwajib AL-Qur'an"
      header-class="py-3 font-bold text-xl" >
      <el-select size="large" v-model="tipe" placeholder="Pilih Tipe Rekapitulasi"
        @change="getChart">
        <el-option value="day" label="Per Hari" />
        <el-option value="week" label="Per Minggu" />
        <el-option value="month" label="Per Bulan" />
      </el-select>
      <div class="mb-4">
        <div v-if="!isEmpty(statistic.datasets)">
          <line-chart class="h-[300px]"
            :statistic="statistic" :max="max" :min="min" />
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Form from '@/components/Form.vue'
import LineChart from '@/components/charts/Line.vue'
import { topMenu } from '@/helpers/menus.js'
import { removeClass } from 'element-plus/es/utils/index.mjs';

export default {
  name: "sholat",
  components: {
    'form-comp' : Form,
    LineChart,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      tipe:'',
      formKey:1,
      dataId:-1,
      lastData:{
        tanggal:'',
        surat_mulai:'',
        surat_selesai:'',
        nama_surat_mulai:'',
        nama_surat_selesai:'',
        ayat_mulai:'',
        ayat_selesai:'',
      },
      juz:[],
      fields:{
        tanggal:'',
        surat_selesai:'',
        ayat_selesai:'',
      },
      formValue:{},
      sizeWindow:window.innerWidth,
      showCreate:false,
      success:false,
      sholat: topMenu.sholatWajib,
      statistic:{
				labels:[],
				datasets:[],
      },
      max:5,
      min:-1,
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapGetters({
      user: 'loggedUser',
    }),
    labelPosition(){
      return this.sizeWindow < 800 ? 'top' : 'left'
    },
    
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        await this.$http.get('/sholat/wajib/get_last')
          .then(result => {
            var res = result.data;
            this.lastData = this.fillAndAddObjectValue(this.lastData, res.last)
            this.juz = res.juz
          });

        await this.$http.get('/kolom/preparation?table=mu_sholat_wajib&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            this.fields.id_anggota.default = this.$store.getters.loggedUser.id_anggota
            this.formKey++
            this.loading = false
          });
        await this.getChart();
      },
    submittedData(){
      this.saving = false;
      this.showCreate = false
      this.success = true
      this.getInitial();
    },
    async getChart(){
      // return;
      await this.$http.get('sholat/wajib/dashboard', {
          params: {}
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
  created: function() {
    // let filter = this.$store.getters.filter
    // this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
    // this.filter.kelas = this.isEmpty(filter.kelas) ? '' : filter.kelas
    this.getInitial()
    // console.log(this.$router);
  },
  mounted: function() {
    let vm = this
    vm.sizeWindow = window.innerWidth
    window.addEventListener('resize', () => {
      vm.sizeWindow = window.innerWidth
    });
    // this.searchData()
  },
}
</script>
