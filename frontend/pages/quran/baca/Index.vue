<template>
  <div id="quran" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="submittedData"/>
    <el-card v-if="['user','super-admin'].includes(user.role)"
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
    <form-quran v-if="['user','super-admin'].includes(user.role)"
      href="quran/baca/store"
      href-get="quran/baca/get"
      table="mu_quran_baca"
      @saved="submittedData">
      <template #header>
        Bacaan Al-Qur'an Hari Ini
      </template>
    </form-quran>
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
      <chart v-if="showData == 'chart'" 
        ref="quranChartData" 
        href="quran/baca/dashboard"
        :id-anggota="idAnggota"
        :add-options="{scales:{y:{
          title:{display:true, text:'Jumlah Ayat'},
          ticks: {stepSize:50},
        }}}"
        class="px-4"/>
      <ListData ref="quranListData"
        class="[--text-color:theme(colors.lime.900)]
          [--bg-color:theme(colors.lime.50)]
          [--border-color:theme(colors.lime.400)]
          [--bg-button-color:theme(colors.lime.100)]
          [--button-color:theme(colors.lime.200)]
        "
        :id-anggota="idAnggota"
        href="quran/baca"
        href-delete="quran/baca/delete"
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
import FormQuran from '@/pages/quran/components/FormQuran.vue';
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
  components: {
    Chart,
    ListData,
    FilterAnggota,
    FormQuran,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      formKey:1,
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
      setStatusText: setStatusText,
      setStatusType: setStatusType,
      quran: topMenu.quranBaca,
      showData:'list',
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapGetters({
      user: 'loggedUser',
      anggotas:'data/anggotas'
    }),
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
    },
    submittedData(){
      this.updateChart();
    },
    updateChart(){
      if (this.showData == 'chart') this.$refs.quranChartData?.getChart();
      if (this.showData == 'list') this.$refs.quranListData?.getData(true);
    }
  },
  created: function() {
    this.getInitial()
    this.$store.dispatch('data/getAllAnggotaInGroup')
    this.idAnggota = this.$store.getters.loggedUser.id_anggota
  },
}
</script>
