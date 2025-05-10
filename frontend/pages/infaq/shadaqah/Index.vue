<template>
    <div id="infaq" class="pt-0 translate-y-[-10px]">
      <el-card class="relative overflow-hidden
         bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
        z-[0]
          font-montserrat
        mb-3 p-0" 
        header-class="px-6 pt-6 pb-2 text-[15px] font-montserrat font-bold text-center"
        body-class="py-4 px-0">
        <template #header>
          <span>Data Infaq Anda</span>
            <img :src="infaq.image" height="90px" width="90px"
                class="absolute z-[-1] top-[-10px] right-[-15px]
                  opacity-[0.5]"/>
        </template>
        <div class="px-8">
          <el-button class="rounded-full w-full
            font-montserrat
            mb-4
            bg-teal-700
            text-white
            active:scale-90"
            @click="showAdd = true;">
            <icons icon="mdi:plus" />Tambah Data
          </el-button>
        </div>
        <div 
          v-infinite-scroll="loadingData"
          class="min-h-[200px] max-h-[50vh] overflow-auto px-8 "
          :infinite-scroll-disabled="noMoreScrolling"
          infinite-scroll-delay="1000"
          infinite-scroll-distance="10">
          <template v-for="(s, key) in datas">
            <div v-if="s.tanggal.slice(0, 7) != datas[key - 1]?.tanggal?.slice(0,7)" 
              class="bg-slate-100
              text-slate-400 text-[15px]
              p-2 py-1 mb-3">{{ monthIndo(s.tanggal) }}</div>
            <div class="h-fit px-6 py-3 mb-3
              rounded-[15px]
              bg-cyan-50/[0.8] border border-solid border-cyan-200
              text-cyan-900
              flex items-center justify-between">
              <div class="leading-[1.3]">
                <div class="font-semibold text-[13px] opacity-70">
                {{ dateDayIndo(s.tanggal)}}</div>
                <div class="font-bold text-[20px]">{{ toIDR(s.jumlah) }}</div>
                <div class="font-semibold text-[13px] opacity-70">{{ s.keterangan }}</div>
              </div>
              <el-button class="rounded-full h-[40px] w-[40px]
                bg-cyan-100/[0.4]"
                @click="() => {
                  dataId = s.id
                  showAdd = true
                }">
                <icons icon="mdi:edit" class="m-0 text-[20px]" />
              </el-button>
            </div>
          </template>
          <p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
          <p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
        </div>
      </el-card>
      <el-dialog v-model="showAdd" draggable
        :append-to-body="true"
        class="w-fit max-w-[80%] py-3
          bg-gradient-to-tr from-white from-50% to-teal-100"
        header-class="font-bold text-[16px]"
        body-class="text-[14px]">
        <template #header>
          <div>Data Shadaqah</div>
        </template>
        <form-comp ref="formInfaq"
          class="[&_*]:rounded-[15px]"
          :key="'form-shadaqah-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="infaq/shadaqah/store"
          href-get="infaq/shadaqah/get"
          :show-columns="['tanggal','jumlah','keterangan']"
          @saved="submittedData" 
          @changed-value="changedValue"
          @error="saving=false"
          size="large"
          :show-submit="false"
          label-position="top"
          :show-required-text="false">
        </form-comp>  
        <template #footer>
          <div class="dialog-footer">
            <el-button @click="showAdd = false">Batal</el-button>
            <el-button type="primary" @click="$refs.formInfaq.submitForm()"
              class="bg-teal-700">
              Simpan
            </el-button>
          </div>
        </template>
      </el-dialog>
      <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
        body-class="py-3 px-5"
        header="Statistik Sadaqah"
        header-class="py-3 font-bold text-[18px] text-center" >
        <chart />
      </el-card>
    </div>
</template>
  
  <script>
  import { mapGetters } from 'vuex';
  import Form from '@/components/Form.vue'
  import Chart from './components/Chart.vue'
  import { topMenu } from '@/helpers/menus.js'
  
  export default {
    name: "infaq",
    components: {
      'form-comp' : Form,
      Chart,
    },
    data: function() {
      return {
        loading: false,
        showAdd: false,
        tipe:'',
        formKey:1,
        dataId:-1,
        datas:[],
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
          jumlah:'',
          keterangan:'',
        },
        loadingScroll:true,
        noMoreScrolling:false,
        limit:5,
        offset:null,
        formValue:{},
        sizeWindow:window.innerWidth,
        showCreate:false,
        success:false,
        saving:false,
        infaq: topMenu.infaqShadaqah,
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
        // await this.$http.get('/infaq/shadaqah/get_last')
        //   .then(result => {
        //     var res = result.data;
        //     this.lastData = this.fillAndAddObjectValue(this.lastData, res)
        //   });
        
        await this.$http.get('/kolom/preparation?table=mu_infaq_shadaqah&grouping=0&input=0')
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
        // this.datas = [];
        // await this.getData();
      },
      async getData(reset = true){
        this.loading = true
        await this.$http.get('infaq/shadaqah/', {
            params: {
              limit:this.limit,
              offset:this.offset,
            }
          }).then(result => {
            var res = result.data;
            this.datas = reset ? res : [...this.datas, ...res]
            this.loading = false
            this.loadingScroll = false
            if (this.isEmpty(res)) {
              this.noMoreScrolling = true
            } else {
              this.offset += 5
            }
          });
      },
      changedValue({ field, parent, value}){
        console.log(field, parent, value)
        if (field == 'surat_mulai-ayat_mulai') {
        console.log(field)
          let changedField = 'surat_selesai-ayat_selesai'
          this.$refs.formInfaq.changeData(changedField, parent, 'parent')
          this.$refs.formInfaq.changeData(changedField, value)
        }
      },
      submittedData(){
        this.saving = false;
        this.showCreate = false
        this.success = true
        this.showAdd = false;
        this.limit = this.offset
        this.offset = 0
        this.getData();
      },
      async loadingData(){
        this.loadingScroll = true
        await this.getData(false)
      },
      async getChart(){
        // return;
        await this.$http.get('infaq/shadaqah/dashboard', {
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
  