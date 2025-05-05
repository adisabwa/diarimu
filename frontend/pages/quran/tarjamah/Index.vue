<template>
  <div id="quran" class="pt-0">
    <el-card class="relative overflow-hidden
       bg-gradient-to-tr from-white/[0.8] from-50% to-sky-200/[0.7] rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="py-4 px-6">
      <div class="z-[10] text-gray-500">Terakhir Mentarjamah : </div>
      <template v-if="!isEmpty(lastData.tanggal)">
        <div class="z-[10] text-2xl font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
        </div>
        <div class="z-[10] text-gray-500">Terakhir : <b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
      </template>
      <template v-else>
        <div class="z-[10] text-2xl font-bold mb-3">
          Belum ada data
        </div>
      </template>
    
      <img :src="quran.image" height="90px" width="90px"
          class="absolute z-[0] top-[-10px] right-[-15px]
            opacity-[0.5]"/>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="py-3 px-5"
      header-class="py-3 font-bold text-xl">
      <template #header>
        <div>Tarjamahan hari ini</div>
      </template>
      <template v-if="!showCreate">
        <div v-if="success"
          class="text-center text-green-500 
            mb-5
            flex items-center justify-center">
          <icons icon="mdi:check-circle" />
          <span>Anda berhasil menyimpan data baru</span>
        </div>
        <el-button size="large" type="primary"
          class="rounded-[15px] w-full font-bold"
          @click="showCreate = true">
          Tambah Catatan
        </el-button>
      </template>
      <template v-else>
        <form-comp ref="formTarjamah"
          class="[&_*]:rounded-[15px]"
          :key="'form-tarjamah-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="quran/tarjamah/store"
          href-get="quran/tarjamah/get"
          :show-columns="['tanggal','surat_mulai-ayat_mulai','surat_selesai-ayat_selesai']"
          @saved="submittedData" 
          @error="saving=false"
          size="large"
          :show-submit="false"
          label-position="top"
          :show-required-text="false">
        </form-comp>  
        <el-button size="large" type="success"
          class="rounded-[15px] w-full font-bold"
          :loading="saving" :disable="saving"
          @click="$refs.formTarjamah.submitForm(); saving=false">
          Simpan Data
        </el-button>
      </template>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Rekap Mentarjamah AL-Qur'an"
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
import { setStatusText, setStatusType } from '@/helpers/quran'
import Form from '@/components/Form.vue'
import LineChart from '@/components/charts/Line.vue'
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
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
      fields:{
        tanggal:'',
        surat_selesai:'',
        ayat_selesai:'',
      },
      formValue:{},
      sizeWindow:window.innerWidth,
      setStatusText: setStatusText,
      setStatusType: setStatusType,
      showCreate:false,
      success:false,
      quran: topMenu.quranTarjamah,
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
        await this.$http.get('/quran/tarjamah/get_last')
          .then(result => {
            var res = result.data;
            this.lastData = this.fillAndAddObjectValue(this.lastData, res)
          });

        await this.$http.get('/kolom/preparation?table=mu_quran_tarjamah&grouping=0&input=0')
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
      await this.$http.get('quran/tarjamah/dashboard', {
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
