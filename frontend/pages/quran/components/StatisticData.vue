<template>
  <div>
    <statistic-data ref="statisticDataQuran"
      :id-anggota="idAnggota"
      :href-dashboard="hrefDashboard"
      :href="href"
      :href-delete="hrefDelete"
      :add-options-chart="{scales:{y:{
        title:{display:true, text:'Jumlah Ayat'},
        ticks: {stepSize:20},
      }}}"
      @edit-data="(({id}) => {
        $emit('editData', {id})
      })">
      >
      <template #header>
        <slot name="header" />
      </template>
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
    </statistic-data>
  </div>
</template>

<script>
import StatisticData from '@/pages/components/StatisticData.vue';

export default {
  name: "statistic-quran",
  emits: ['editData'],
  components: {
    StatisticData,
  },
  props:{
    idAnggota:{type:[String, Number],default:'-1'},
    href:{type:String,default:''},
    hrefDashboard:{type:String,default:''},
    hrefDelete:{type:String,default:''},
  },
  data: function() {
    return {
    };
  },
  watch: {
  },  
  computed: {
    
  },
  methods: {
    updateChart(){
      this.$refs.statisticDataQuran.updateChart()
    }
  },
  created: function() {
    
  },
}
</script>
