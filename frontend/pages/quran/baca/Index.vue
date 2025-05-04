<template>
  <div id="quran" class="pt-0">
    <el-card class="relative overflow-hidden
       bg-gradient-to-br from-yellow-100/[0.8] to-lime-200/[0.4] rounded-[10px]
      z-[0]
      mb-3 p-0" 
      body-class="py-3 px-5">
      <div class="z-[10] text-gray-500">Terakhir Membaca : </div>
      <div class="z-[10] text-2xl font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
      </div>
      <div class="z-[10] text-gray-500">Terakhir : <b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
    
      <img :src="quran.image" height="70px" width="70px"
          class="absolute z-[0] top-[-10px] right-[-15px]"/>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="py-3 px-5"
      header="Bacaan Hari Ini"
      header-class="py-3 font-bold text-xl">
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
        <form-comp ref="formBaca"
          class="[&_*]:rounded-[15px]"
          :key="'form-baca-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="quran/baca/store"
          href-get="quran/baca/get"
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
          @click="$refs.formBaca.submitForm(); saving=false">
          Simpan Data
        </el-button>
      </template>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Rekap Membaca AL-Qur'an"
      header-class="py-3 font-bold">
      <el-select size="large" v-model="tipe" placeholder="Pilih Tipe Rekapitulasi">
        <el-option value="day" label="Per Hari" />
        <el-option value="week" label="Per Minggu" />
        <el-option value="month" label="Per Bulan" />
      </el-select>
      <div>
        Grafik
      </div>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { setStatusText, setStatusType } from '@/helpers/quran'
import Form from '@/components/Form.vue'
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
  components: {
    'form-comp' : Form,
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
      quran: topMenu.quranBaca
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
        await this.$http.get('/quran/baca/get_last')
          .then(result => {
            var res = result.data;
            this.lastData = this.fillAndAddObjectValue(this.lastData, res)
          });

        await this.$http.get('/kolom/preparation?table=mu_quran_baca&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            this.fields.id_anggota.default = this.$store.getters.loggedUser.id_anggota
            this.formKey++
            this.loading = false
          });
      },
    submittedData(){
      this.saving = false;
      this.showCreate = false
      this.success = true
      this.getInitial();
    },
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
