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
      body-class="relative p-0"
      header-class="relative p-0">
      <template #header>
        <div id="header-scroll" class="relative px-0 py-4 font-bold text-xl overflow-x-scroll
        snap-x snap-mandatory" >
          <div v-if="editTanggal" class="text-center">
            <el-date-picker
              id="editTanggal"
              class="w-[200px]"
              v-model="tanggal"
              value-format="YYYY-MM-DD"
              format="DD MMMM YYYY"
              size="large"
              @blur="editTanggal = false"
              @change="editTanggal = false"
            />
          </div>
          <div v-else class="w-[300%] flex"
            @click="changeTanggal">
            <div v-for="(t, key) in tanggals" 
              :id="'header'+key"
              class="snap-center w-full px-4 text-center">
              <div @click="">
                <span 
                  >{{ dateDayIndo(t) }}</span>
              </div>
            </div>
          </div>
        </div>
        <icons @click="scrollHeader(-1)"
          class="m-0 text-3xl pointer
            absolute top-1/2 -translate-y-1/2 left-5"
          icon="iconamoon:arrow-left-2-bold"/>
        <icons @click="scrollHeader(1)"
          class="m-0 text-3xl pointer
            absolute top-1/2 -translate-y-1/2 right-5" 
          icon="iconamoon:arrow-right-2-bold"/>
      </template>
      <div id="body-scroll" class="relative px-0 py-6
        flex    
        overflow-x-scroll
        snap-x snap-mandatory">
        <template v-for="(_data, ind) in datas">
          <div :id="'body'+ind" class="shrink-0 snap-center font-montserrat px-3">
            <template v-for="sholat in _data">
              <div :class="`${setStatusColor(sholat.value)}
                mx-5 pt-4 pb-3 mb-4 px-7
                rounded-[15px] 
                shadow-md
                relative flex gap-x-3 items-center`">
                <div class="text-xl font-bold leading-[1.3] w-full">
                  <div>{{ ucFirst(sholat.nama_kolom) }}</div>
                  <div class="text-sm font-semibold">
                    {{ getLabel(sholat.value) }}
                  </div>
                </div>
                <div class="flex gap-x-1 mx-3">
                  <img :id="'1star'+ind+tanggals[ind]+sholat.nama_kolom"
                    :src="`${$baseUrl}assets/images/star.png`"
                    width="20px"
                    class="animate scale-0" />
                  <img :id="'2star'+ind+tanggals[ind]+sholat.nama_kolom"
                    :src="`${$baseUrl}assets/images/star.png`"
                    width="20px"
                    class="animate scale-0" />
                  <img :id="'3star'+ind+tanggals[ind]+sholat.nama_kolom"
                    :src="`${$baseUrl}assets/images/star.png`"
                    width="20px"
                    class="animate scale-0" />
                </div>
                <el-select v-model="sholat.value"
                  placeholder="Pilih" clearable
                  @change="showStar(ind + '' + tanggals[ind], sholat.nama_kolom, sholat.value);
                  saveData(sholat.nama_kolom)"
                  :popper-class="`${setStatusColor(sholat.value)}`"
                  class="w-[70px]">
                  <template #prefix>
                    <icons icon="mdi:edit" />
                  </template>
                  <template v-for="o in options">
                    <el-option :value="o.value" :label="o.label"/>
                  </template>
                </el-select>
              </div>
            </template>
          </div>
        </template>
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

<script setup>
  import { setStatusColor, options, getLabel } from '@/helpers/sholat.js'
</script>

<script>
import { mapGetters } from 'vuex';
import Form from '@/components/Form.vue'
import LineChart from '@/components/charts/Line.vue'
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "sholat",
  components: {
    'form-comp' : Form,
    LineChart,
  },
  data: function() {
    return {
      loading: false,
      editTanggal:false,
      dataId:-1,
      idAnggota:-1,
      tanggals:[],
      tanggal:'',
      datas:[],
      dataSholat: {
        shubuh:{
          nama_kolom: 'shubuh',
          value:null,
        },
        dhuhur:{
          nama_kolom: 'dhuhur',
          value:null,
        },
        asar:{
          nama_kolom: 'asar',
          value:null,
        },
        maghrib:{
          nama_kolom: 'maghrib',
          value:null,
        },
        isya:{
          nama_kolom: 'isya',
          value:null,
        },
      },
      tipe:'',
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
    tanggal(val){
      this.setDataInitiall()
      this.getInitial()
    },
    editTanggal(val){
      let vm = this
      setTimeout(() => {
        vm.setHeaderToCenter()
      }, 50);
        
    },
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
      let vm = this
      vm.loading = true;
      for (let index = 0; index < this.datas.length; index++) {
        const element = this.datas[index];
        vm.$http.get('sholat/wajib/get_where', {
            params: {
              where: {
                id_anggota:vm.idAnggota,
                tanggal:vm.tanggals[index],
              }
            }
          })
            .then(res => {
              let data = res.data
              console.log(data)
              if (index == 1)
                vm.dataId = data.id

              let keys = Object.keys(vm.dataSholat)
              keys.forEach(( k, ind) => {
                if (data[k] !== null) {
                  vm.datas[index][k].value = parseInt(data[k])
                  vm.showStar(index + '' + vm.tanggals[index], k, vm.datas[index][k].value)
                } else {
                  vm.datas[index][k].value = null
                  vm.showStar(index + '' + vm.tanggals[index], k, vm.datas[index][k].value)
                }
                // console.log(index, k, vm.datas[index][k])
              })
              vm.loading = false
            })
            .catch(err => {
              console.log(err)
              vm.loading = false
              vm.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengambil data',
                position: 'bottom-right',
              });
            })
      }
    },
    showStar(prev, label, value){
      let id = 'star' + prev + label;
      // console.log(id)
      if (value >= 50) {
        this.removeClass('#1' + id,'scale-0')
        this.removeClass('#2' + id,'scale-0')
        this.removeClass('#3' + id,'scale-0')
      } else {
        this.addClass('#1' + id,'scale-0')
        this.addClass('#2' + id,'scale-0')
        this.addClass('#3' + id,'scale-0')
      }

      
      if (value == 100) {
        this.removeClass('#1' + id,'grayscale')
        this.removeClass('#2' + id,'grayscale')
        this.removeClass('#3' + id,'grayscale')
      } else if (value >= 75) {
        this.addClass('#1' + id,'grayscale')
        this.removeClass('#2' + id,'grayscale')
        this.removeClass('#3' + id,'grayscale')
      } else if (value >= 50) {
        this.addClass('#1' + id,'grayscale')
        this.addClass('#2' + id,'grayscale')
        this.removeClass('#3' + id,'grayscale')
      } 
    },
    saveData(kolom){
      let form = {
        id:this.dataId,
        id_anggota:this.idAnggota,
        tanggal:this.tanggal,
      }
      form[kolom] = this.dataSholat[kolom].value
      console.log(form)
      var formData = window.jsonToFormData(form); 
      this.$http.post('sholat/wajib/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          this.getInitial()
        })
        .catch(err => {
          
        });
    },
    changeTanggal(){
      let vm = this
      vm.editTanggal = true;
      setTimeout(() => {
        vm.jquery('#editTanggal.el-input__inner')[0].focus();
      }, 300);
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
    },
    setHeaderToCenter(){
      let body = this.jquery('#body-scroll');
      let bcenter = this.jquery('#body1');
      body[0].scrollLeft = bcenter[0].offsetLeft

      if (this.editTanggal) return
      let header = this.jquery('#header-scroll');
      let center = this.jquery('#header1');
      header[0].scrollLeft = center[0].offsetLeft
    },
    scrollHeader(course = -1){
      let duration = 0.7
      let vm = this
      console.log(course)
      vm.removeClass('#header-scroll','snap-x snap-mandatory')
      if (course == -1) {
        vm.scrollElement('#header-scroll','#header0',duration)
      } else {
        vm.scrollElement('#header-scroll','#header2',duration)
      }
      setTimeout(() => {
        vm.tanggal = vm.addDay(vm.tanggal, course)
        vm.setHeaderToCenter()
        vm.addClass('#header-scroll','snap-x snap-mandatory')
      }, duration * 1000 + 100);
    },
    setDataInitiall(){
      this.tanggals = [
        this.addDay(this.tanggal, -1),
        this.tanggal,
        this.addDay(this.tanggal, 1),
      ]
      this.datas = [];
      for (let index = 0; index < this.tanggals.length; index++) {
        this.datas[index] = JSON.parse(JSON.stringify(this.dataSholat))
      }
    },
    handleAfterScroll(){
      // console.log('handle')
      let header = this.jquery('#header-scroll')[0]
      let right = this.jquery('#header2')[0]
      // console.log(header.scrollLeft)
      if (header.scrollLeft == 0) {
        this.tanggal = this.tanggals[0]
        this.setHeaderToCenter()
      } else if (header?.scrollLeft == right?.offsetLeft) {
        this.tanggal = this.tanggals[2]
        this.setHeaderToCenter()
      }
    },  
  },
  created: function() {
    // let filter = this.$store.getters.filter
    // this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
    this.tanggal = this.dateNow()
    this.idAnggota = this.$store.getters.loggedUser.id_anggota
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

    let scrollTimeout;
    jquery('#header-scroll, #body-scroll').on('scroll', () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        console.log('Scrolling has finished');
        setTimeout(() => {
          vm.addClass('#header-scroll','snap-x snap-mandatory')
          vm.addClass('#body-scroll','snap-x snap-mandatory')
          vm.handleAfterScroll()
        }, 200);
        // Your code here
      }, 200); // Adjust timeout duration as needed
    });
    jquery('#header-scroll').on('scroll', () => {
      const scrollLeft = jquery('#header-scroll')[0].scrollLeft;
      console.log(scrollLeft)
      vm.removeClass('#body-scroll','snap-x snap-mandatory')
      jquery('#body-scroll').scrollLeft(scrollLeft);
    });
    this.setHeaderToCenter()
    // window.addEventListener('scroll', this.handleScroll);
  },
  unmounted(){
    // window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>
