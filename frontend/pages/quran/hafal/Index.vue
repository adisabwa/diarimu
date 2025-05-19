<template>
  <div id="quran" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
    v-model:id-anggota="idAnggota" @change="submittedData"/>
    <el-card v-if="['user','super-admin'].includes(user.role)"
      class="relative overflow-hidden
       bg-gradient-to-tr from-white/[0.8] from-50% to-yellow-200/[0.7] rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 ">
      <img :src="quran.image" height="90px" width="90px"
          class="absolute z-[0] top-[-10px] right-[-20px]
            opacity-[0.5]"/>
      <div class="relative w-fit overflow-x-scroll z-[10]
        snap-mandatory snap-x">
        <div class="w-[200%] flex h-[80px] py-4">
          <div class="snap-center w-full px-6 h-[90px] text-[14px] leading-[1.5]">
              <template v-if="!isEmpty(lastData.tanggal)">
              <div class="z-[10] text-gray-500">Setoran Terakhir : </div>
              <div class="z-[10] text-[22px] font-bold">{{ lastData.nama_surat_selesai }} ({{ lastData.surat_selesai }}) : {{ lastData.ayat_selesai }}
              </div>
              <div class="z-[10] text-gray-500 text-[15px]"><b>{{ dateDayIndo(lastData.tanggal) }}</b></div>
            </template>
            <template v-else>
              <div class="z-[10] text-2xl font-bold mb-3">
                Belum ada data
              </div>
            </template>
          </div>
          <div class="snap-center w-full px-6 h-[90px] overflow-scroll">
            <div class="z-[10] mb-2 text-md font-bold italic 
              py-2
              bg-white/[0.8]">
              <div >Total Hafalan :</div> 
              <ol class="m-0 pl-4 text-[90%] font-normal">
                <template v-for="data in juz">
                  <li class="py-[3px]">{{ data.nama_surat_mulai }} :{{ data.ayat_mulai }} ( Juz {{ data.juz_mulai }} ) - {{ data.nama_surat_selesai }} : {{ data.ayat_selesai }} ( Juz {{ data.juz_selesai }} )</li>
                </template>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </el-card>
    <el-card v-if="['user','super-admin'].includes(user.role)"
      class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="py-3 px-5"
      header-class="py-3 font-bold text-[18px]">
      <template #header>
        <div>Setoran Hafalan hari ini</div>
      </template>
      <template v-if="!showCreate">
        <div v-if="success"
          class="text-center text-green-500 text-[15px]
            mb-3
            flex items-center justify-center">
          <icons icon="mdi:check-circle" />
          <span>Anda berhasil menyimpan data baru</span>
        </div>
        <el-button size="large" type="primary"
          class="rounded-[15px] w-full font-bold"
          @click="showCreate = true">
          <icons icon="mdi:plus" />
          Tambah Catatan
        </el-button>
      </template>
      <template v-else>
        <form-comp ref="formHafal"
          class="[&_.el-form-item\_\_label]:mb-1 mb-2"
          :key="'form-hafal-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="quran/hafal/store"
          href-get="quran/hafal/get"
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
        <el-button 
          size="large" type="success"
          class="rounded-xl w-full font-bold py-1 text-[13px]"
          :loading="saving" :disable="saving"
          @click="$refs.formHafal.submitForm(); saving=false">
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
        <div>Data Setoran Hafalan Qur'an</div>
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
        href="quran/hafal/dashboard"
        :id-anggota="idAnggota"
        v-if="showData == 'chart'" 
        :add-options="{scales:{y:{
          title:{display:true, text:'Jumlah Ayat'},
          ticks: {stepSize:50},
        }}}"
        class="px-4"/>
      <ListData ref="quranListData"
        class="[--text-color:theme(colors.orange.900)]
          [--bg-color:theme(colors.orange.50)]
          [--border-color:theme(colors.orange.400)]
          [--bg-button-color:theme(colors.orange.100)]
          [--button-color:theme(colors.orange.200)]
        "
        :id-anggota="idAnggota"
        href="quran/hafal"
        v-if="showData =='list'"
        @edit-data="(({id}) => {
          dataId = id
          showCreate = true
        })">
        <template #subtitle="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #title="{ data }">
          <div class="mt-1 text-[16px] leading-[1]">Dari : 
						<span class="font-bold">
						{{ data.nama_surat_mulai }} ({{ data.surat_mulai }}) : {{ data.ayat_mulai }}
						</span>
					</div>
					<div class="text-[16px]">Sampai : 
						<span class="font-bold">
						{{ data.nama_surat_selesai }} ({{ data.surat_selesai }}) : {{ data.ayat_selesai }}
						</span>
					</div>
        </template>
      </ListData>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { setStatusText, setStatusType } from '@/helpers/quran'
import FilterAnggota from '../../components/FilterAnggota.vue';
import ListData from '@/pages/components/ListData.vue';
import Chart from '@/pages/components/DataChart.vue'
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
  components: {
    Chart,
    ListData,
    FilterAnggota,
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
      setStatusText: setStatusText,
      setStatusType: setStatusType,
      showCreate:false,
      success:false,
      quran: topMenu.quranHafal,
      showData:'list',
      idAnggota:null,
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
        await this.$http.get('/quran/hafal/get_last',{
          params:{
            id_anggota: this.idAnggota
          }
        })
          .then(result => {
            var res = result.data;
            this.fillAndAddObjectValue(this.lastData, res.last)
            this.juz = res.juz
          });

        await this.$http.get('/kolom/preparation?table=mu_quran_hafal&grouping=0&input=0')
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
    submittedData(){
      this.saving = false;
      this.showCreate = false
      this.success = true
      setTimeout(() => {
        this.updateChart();
      }, 400);
      this.getInitial();
    },
    updateChart(){
      if (this.showData == 'chart') this.$refs.quranChartData.getChart();
      if (this.showData == 'list') this.$refs.quranListData.getData(true);
    },
    changedValue({ field, parent, value}){
      // console.log(field, parent, value)
      if (field == 'surat_mulai-ayat_mulai') {
      // console.log(field)
        let changedField = 'surat_selesai-ayat_selesai'
        this.$refs.formHafal.changeData(changedField, parent, 'parent')
        this.$refs.formHafal.changeData(changedField, value)
      }
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
