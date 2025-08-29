<template>
  <div id="persyarikatan" class="pt-[50px] translate-y-[-10px] px-0">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="submittedData"/>
    <statistic-list class="bg-white/[0.9] rounded-[10px] mb-3 p-0
          [--text-color:theme(colors.indigo.900)]
          [--bg-color:theme(colors.indigo.50)]
          [--border-color:theme(colors.indigo.400)]
          [--bg-button-color:theme(colors.indigo.100)]
          [--button-color:theme(colors.indigo.200)]
          [&_#list-data]:bg-gradient-to-tr 
          [&_#list-data]:from-white/[0.8] 
          [&_#list-data]:from-40% 
          [&_#list-data]:to-indigo-200/[0.7]"
      ref="statisticListPersyarikatan"
      :key="'statisticListPersyarikatan'+formKey"
      :id-anggota="idAnggota"
      href-dashboard="persyarikatan/dashboard"
      href="persyarikatan"
      :group-by="['tanggal','id_anggota']"
      href-delete="persyarikatan/delete"
      @edit-data="({id}) => {
        showAdd = true;
        dataId = id
      }"
      y-label-table="Jumlah Kegiatan"
      :add-options-chart="{
        scales: {
          y: {
            title:{
              display:true, 
              text: 'Jumlah Kegiatan',
            },
            ticks: {
              font: {
                size: 10
              },
              stepSize:1,
            }
          }
        },
      }"
      >
      <template #headerList>
        <span>Kegiatan Persyarikatan</span>
        <img :src="persyarikatan.image" height="90px" width="90px"
            class="absolute z-[-1] top-[-10px] right-[-15px]
              opacity-[0.5]"/> 
        <div class="px-8"
          v-if="['user','super-admin'].includes(user?.role)">
          <el-button class="rounded-full w-full
            font-montserrat
            mt-4
            bg-indigo-700
            text-white
            active:scale-90"
            @click="showAdd = true; dataId = -1">
            <icons icon="mdi:plus" />Tambah Data
          </el-button>
        </div>
      </template>
      <template #subtitle="{ data }">
        {{ dateDayIndo(data.tanggal)}}
      </template>
      <template #title="{ data }">
        <div class="text-[16px] ">
          {{ ucFirst(data.kegiatan) }}
        </div>
        <div class="text-[13px]">
          {{ ucFirst(data.lokasi) }}
        </div>
      </template>
      <template #content="{ data }">
        <div class="text-[12px]">
          Diselenggarakan oleh {{ ucFirst(data.penyelenggara) }}
        </div>
        <div class="text-[12px]">
          Materi : {{ ucFirst(data.isi) }}
        </div>
      </template>
      <template #header>
        <div class="text-[var(--text-color)]">Statistik Kegiatan Persyarikatan</div>
      </template>
    </statistic-list>
    <teleport to="body">
      <el-dialog v-model="showAdd" draggable
        :append-to-body="true"
        class="w-fit min-w-[300px] max-w-[90%] py-3
          bg-gradient-to-tr from-white from-50% to-indigo-100"
        header-class="font-bold text-[16px]"
        body-class="">
        <template #header>
          <div>Data </div>
        </template>
        <form-comp ref="formKajian"
          class="min-w-[280px]"
          :key="'form-shadaqah-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="persyarikatan/store"
          href-get="persyarikatan/get"
          :pass-columns="['id_anggota']"
          @saved="submittedData();updateChart();" 
          @error="saving=false"
          size="large"
          :show-submit="false"
          label-position="top"
          :show-required-text="false">
        </form-comp>  
        <template #footer>
          <div class="dialog-footer">
            <el-button @click="showAdd = false">Batal</el-button>
            <el-button type="primary" @click="$refs.formKajian.submitForm()"
              class="bg-indigo-700">
              Simpan
            </el-button>
          </div>
        </template>
      </el-dialog>
    </teleport>
  </div>
</template>
  
  <script>
  import { mapState } from 'pinia';
  import FilterAnggota from '@/pages/components/FilterAnggota.vue';
  import StatisticList from '@/pages/components/StatisticList.vue';
  import { organizationMenu } from '@/helpers/menus.js'
  
  export default {
    name: "persyarikatan",
    components: {
      StatisticList,
      FilterAnggota,
    },
    data: function() {
      return {
        loading: false,
        showAdd: false,
        tipePersyarikatan:'0',
        idAnggota:'-1',
        formKey:1,
        dataId:-1,
        fields:{
        },
        formValue:{},
        showCreate:false,
        success:false,
        saving:false,
        persyarikatan: organizationMenu.persyarikatan,
      };
    },
    watch: {
      showAdd(val){
        if (val) {
          // console.log(val, this.idAnggota)
          this.$nextTick(() => {
            this.$refs.formKajian.changeData({
              field:'id_anggota', 
              value:this.idAnggota
            })
          })
        }
      }
    },  
    computed: {
      ...mapState(useAuthStore, {
        user: 'loggedUser',
      }),
      
    },
    methods: {
      getInitial: async function() {
        let vm = this
        this.loading = true;
        
        await this.$http.get('/kolom/preparation?table=mu_kegiatan_persyarikatan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            // console.log(this.idAnggota)
            this.fields.id_anggota.default = this.idAnggota
            // console.log(this.fields)
            this.loading = false
          });
        // this.datas = [];
        // await this.getData();
      },
      submittedData(){
        this.saving = false;
        this.showCreate = false
        this.success = true
        this.showAdd = false;
        // setTimeout(this.updateChart(), 1000)
      },
      updateChart(){
        this.$refs?.statisticListPersyarikatan?.updateChart()
      }
    },
    created: function() {
      
    },
    mounted: function() {
      this.idAnggota = useAuthStore()?.loggedUser?.id_anggota
      this.getInitial()
    },
  }
  </script>
  