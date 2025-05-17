<template>
  <div id="quran" class="pt-[50px]">
    <div v-if="user.role == 'mentor'" 
      class="bg-white/[0.9] rounded-[10px] shadow-md
      mb-3 p-4">
      <div class="text-sm mb-2">Nama Anggota :</div>
      <el-select v-model="idAnggota" placeholder="Pilih Anggota"
        @change="submittedData">
        <el-option :value="anggotas.map(user => user.id_anggota).join(',')" label="Semua" />
        <el-option v-for="a in anggotas"
          :key="a.id"
          :value="a.id_anggota"
          :label="a.nama"/>
      </el-select>
    </div>
    <el-card v-if="user.role == 'user'"
       class="relative overflow-hidden
       bg-gradient-to-tr from-yellow-50/[0.8] from-50% to-lime-200/[0.7] rounded-[10px]
      z-[0] font-montserrat text-[13px]
      mb-3 p-0" 
      body-class="py-4 px-6">
      <div class="z-[10] text-gray-500">Terakhir Membaca : </div>
      <template v-if="!isEmpty(lastData.tanggal)">
        <div class="z-[10] text-[24px] font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
        </div>
        <div class="z-[10] text-gray-500"><b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
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
    <el-card v-if="user.role == 'user'"
      class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="py-3 px-5 text-[14px]"
      header-class="py-3 font-bold text-[16px]">
      <template #header>
        <div>Bacaan Quran hari ini</div>
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
          class="rounded-full w-full font-bold text-[13px]
          py-2"
          @click="showCreate = true">
          <icons icon="mdi:plus"/>
          Tambah Catatan
        </el-button>
      </template>
      <template v-else>
        <form-comp ref="formBaca"
          class="[&_.el-form-item\_\_label]:mb-1 mb-2"
          :key="'form-baca-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="quran/baca/store"
          href-get="quran/baca/get"
          :show-columns="['tanggal','surat_mulai-ayat_mulai','surat_selesai-ayat_selesai']"
          @saved="submittedData" 
          @changed-value="changedValue"
          @error="saving=false"
          size="large"
          :show-submit="false"
          label-position="top"
          form-item-class="mb-2"
          input-class=""
          :show-required-text="false">
        </form-comp>  
        <el-button size="large" type="success"
          class="rounded-full w-full font-bold text-[13px]
          py-2"
          :loading="saving" :disable="saving"
          @click="() => {
            $refs.formBaca.submitForm();
            saving=false
          }">
          Simpan Data
        </el-button>
      </template>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-0"
      header-class="py-3 font-bold text-[16px]
        text-lime-800
        flex justify-between items-center" >
      <template #header>
        <div>Data Membaca Al-Qur'an</div>
        <div class="flex items-center gap-1
          [&_*]:text-[20px] text-emerald-900/[0.4]">
          <icons icon="fa6-solid:chart-line" 
            @click="showData='chart'"
            :class="` ${showData == 'chart' ? 'text-emerald-900 pointer' : ''}`"/>
          <icons icon="material-symbols:view-list" 
            @click="showData='list'"
            :class="` ${showData == 'list' ? 'text-emerald-900 pointer' : ''}`"/>
        </div>
      </template>
      <chart ref="quranChartData" 
        href="quran/baca/dashboard"
        :id-anggota="idAnggota"
        v-if="showData == 'chart'" 
        :add-options="{scales:{y:{title:{display:true, text:'Jumlah Ayat'}}}}"
        class="px-4"/>
      <list-data ref="quranListData" 
        :id-anggota="idAnggota"
          :key="'quranData'+'formKey'"
        v-if="showData =='list'"
        @edit-data="(({id}) => {
          dataId = id
          showCreate = true
        })"/>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { setStatusText, setStatusType } from '@/helpers/quran'
import Form from '@/components/Form.vue'
import Chart from '@/components/statistics/DataChart.vue'
import ListData from './components/ListData.vue'
import { topMenu } from '@/helpers/menus.js'
import { scales } from 'chart.js';

export default {
  name: "quran",
  components: {
    'form-comp' : Form,
    Chart,
    ListData,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      formKey:1,
      dataId:-1,
      idAnggota:null,
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
      showCreate:true,
      success:false,
      saving:false,
      quran: topMenu.quranBaca,
      showData:'chart',
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapGetters({
      user: 'loggedUser',
      anggotas:'data/anggotas'
    }),
    labelPosition(){
      return this.sizeWindow < 800 ? 'top' : 'left'
    },
    
  },
  methods: {
    getInitial: async function() {
      this.loading = true;
      this.resetObjectValue(this.lastData)
      await this.$http.get('/quran/baca/get_last',{
        params:{
          id_anggota: this.idAnggota
        }
      })
        .then(result => {
          var res = result.data;
          this.fillAndAddObjectValue(this.lastData, res)
        });

      await this.$http.get('/kolom/preparation?table=mu_quran_baca&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          this.dataId = -1
          this.fields = this.fillAndAddObjectValue(this.fields, res)
          this.fields.tanggal.default = this.dateNow()
          this.fields.id_anggota.default = this.idAnggota
          this.formKey++
          this.loading = false
        });
    },
    changedValue({ field, parent, value}){
      console.log(field, parent, value)
      if (field == 'surat_mulai-ayat_mulai') {
      console.log(field)
        let changedField = 'surat_selesai-ayat_selesai'
        this.$refs.formBaca.changeData(changedField, parent, 'parent')
        this.$refs.formBaca.changeData(changedField, value)
      }
    },
    submittedData(){
      this.saving = false;
      this.showCreate = false
      this.success = true
      if (this.showData == 'chart') this.$refs.quranChartData.getChart();
      if (this.showData == 'list') this.$refs.quranListData.getData(true);
      this.getInitial();
    },
  },
  created: function() {
    // let filter = this.$store.getters.filter
    // this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
    // this.filter.kelas = this.isEmpty(filter.kelas) ? '' : filter.kelas
    this.getInitial()
    this.$store.dispatch('data/getAllAnggotaInGroup')
    this.idAnggota = this.$store.getters.loggedUser.id_anggota
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
