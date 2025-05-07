
<template>
  <div id="sholat" class="pt-0">
    <el-card class="rounded-[10px]
      bg-gradient-to-tl from-white/[0.8] from-30% to-rose-200/[0.7] 
      mb-3 p-0"
      body-class="relative p-0"
      header-class="relative p-0">
      <img :src="sholat.image" height="90px" width="90px"
          class="absolute z-[0] top-[-70px] left-[-15px]
            opacity-[0.5]"/>
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
              @change="editTanggal = false;
                setTanggalInitial();
                setDataInitiall();"
            />
          </div>
          <div v-else class="w-[300%] flex">
            <div v-for="(t, key) in tanggals" 
              :id="'header'+key"
              class="snap-center w-full px-4 text-center">
              <div @click="changeTanggal">
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
          <el-container :id="'body'+ind" class="shrink-0 snap-center font-montserrat
            px-auto w-full
            relative
            flex-col"
            v-loading="loadings[ind]">
            <template v-for="sholat in _data">
              <div :class="`${ sholat.do ? 'bg-emerald-200 text-emerald-900' : '' }
                mx-4 mb-4 p-5 pb-4
                rounded-[20px] 
                shadow-md
                relative flex gap-x-2 items-center
                active:scale-90`">
                <div>
                  <div class="text-xl font-semibold
                    flex gap-2 items-center
                    group/pray"
                      @click="sholat.do = !sholat.do">
                    <icons v-if="sholat.do"
                      class="m-0 text-[30px]"
                      icon="material-symbols:check-circle-outline"/>
                    <div v-else
                      class="w-[20px] h-[20px] rounded-full shrink-0
                      border border-solid border-gray-400 leading-0
                      "/>
                    <div class="leading-[1.2]"
                      >{{ ucFirst(sholat.nama_sholat) }}</div>
                  </div>
                  <div v-if="sholat.do"
                    class="ml-10 text-md font-bold">
                    <el-dropdown trigger="click"
                      @command="(res) => {
                        sholat.value = res
                        showStar()
                        saveData(ind, sholat.nama_kolom)
                      }"
                      :popper-class="`${setStatusColor(sholat.value)}`"
                      class="">
                      <span class="el-dropdown-link h-fit
                        flex items-center">
                        <icons icon="mdi:edit" class=""/>
                        <span>{{ sholat.rakaat }} Rakaat</span>
                      </span>
                      <template #dropdown>
                        <el-dropdown-menu>
                          <template v-for="o in options">
                            <el-dropdown-item :command="o.value"
                              :class="`${o.value == sholat.value ? 'font-bold' : ''}`">
                              {{ o.label }} ( {{ o.value }} ) 
                            </el-dropdown-item>
                          </template>
                        </el-dropdown-menu>
                      </template>
                    </el-dropdown>
                  </div>
                </div>
                <!-- <el-dropdown trigger="click"
                  @command="(res) => {
                    sholat.value = res
                    showStar()
                    saveData(ind, sholat.nama_kolom)
                  }"
                  :popper-class="`${setStatusColor(sholat.value)}`"
                  class="h-[40px]">
                  <el-button class="rounded-full h-full w-[40px]">
                    <icons icon="mdi:edit" class="m-0"/>
                  </el-button>
                  <template #dropdown>
                    <el-dropdown-menu>
                      <template v-for="o in options">
                        <el-dropdown-item :command="o.value"
                          :class="`${o.value == sholat.value ? 'font-bold' : ''}`">
                          {{ o.label }} ( {{ o.value }} ) 
                        </el-dropdown-item>
                      </template>
                    </el-dropdown-menu>
                  </template>
                </el-dropdown> -->
              </div>
            </template>
          </el-container>
        </template>
      </div>
    </el-card>
    <el-card class="relative overflow-hidden
    bg-white/[0.9] 
      rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 ">
      <div class="relative flex px-3 py-5 gap-3 justify-center">
        <div class="relative w-fit text-center py-4 px-6
          border-2 border-solid border-indigo-200
          bg-sky-100/[0.4]
          rounded-[20px]">
          <div class="text-gray-500 font-bold leading-[1.2] w-[100px]
            mb-2">Nilai Terbaru</div>
          <div class="text-gray-500"><b>( {{ dateShortIndo(lastData.tanggal) }} )</b></div>
          <div class="text-[58px] leading-[1.1] font-semibold mb-0">{{ lastData.total_score }}</div>
          <div class="flex items-start justify-center">
            <star :id="'1starlastdata'" width="28px"/>
            <star :id="'2starlastdata'" width="33px"/>
            <star :id="'3starlastdata'" width="28px"/>
          </div>
        </div>
        <div class="relative w-fit text-center py-4 px-6
          border-2 border-solid border-indigo-200
          bg-sky-100/[0.4]
          rounded-[20px]">
          <div class="text-gray-500 font-bold leading-[1.2] w-[100px]
            mb-2">Nilai Terbaik</div>
          <div class=" text-gray-500"><b>( {{ dateShortIndo(bestData.tanggal) }} )</b></div>
          <div class="text-[58px] leading-[1.1] font-semibold mb-0">{{ bestData.total_score }}</div>
          <div class="flex items-start justify-center">
            <star :id="'1starbestdata'" width="28px"/>
            <star :id="'2starbestdata'" width="33px"/>
            <star :id="'3starbestdata'" width="28px"/>
          </div>
        </div>
      </div>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-5"
      header="Rekapitulasi Sholat Sunnah"
      header-class="py-3 font-bold text-xl text-center" >
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
import Star from '../components/Star.vue'
import { getGlobalThis } from '@vue/shared';

export default {
  name: "sholat",
  components: {
    'form-comp' : Form,
    LineChart,
    Star,
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
      loadings:[false, false, false],
      afterScroll: true,
      tipe:'',
      lastData:{
        tanggal:'',
        total_score:'',
      },
      bestData:{
        tanggal:'',
        total_score:'',
      },
      fields:{
        tanggal:'',
        surat_selesai:'',
        ayat_selesai:'',
      },
      formValue:{},
      sizeWindow:window.innerWidth,
      showCreate:false,
      success:false,
      sholat: topMenu.sholatSunnah,
      statistic:{
				labels:[],
				datasets:[],
      },
      max:5,
      min:-1,
    };
  },
  watch: {
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
    getLast(){
       this.$http.get('sholat/sunnah/get_last_and_best')
        .then( res => {
          let data = res.data
          this.fillObjectValue(this.lastData, data?.last)
          this.fillObjectValue(this.bestData, data?.best)
          console.log(data?.last.total_score, data?.best.total_score)
          this.toggleStarClass('starlastdata',data?.last.total_score / 5)
          this.toggleStarClass('starbestdata',data?.best.total_score / 5)
        })
    },
    getData: async function(index) {
      let vm = this
      vm.loading = true;
      vm.loadings[index] = true;
      // setTimeout(() => {
        vm.$http.get('sholat/sunnah/get_initial', {
            params: {
              id_anggota:vm.idAnggota,
              tanggal:vm.tanggals[index],
            }
          })
            .then(res => {
              let data = res.data
              this.datas[index] = data
              vm.showStar()
              setTimeout(() => {
                vm.loading = false
                vm.loadings[index] = false;
              }, 300);
            })
            .catch(err => {
              console.log(err, index, vm.datas[index])
              vm.loadings[index] = false;
              vm.loading = false
              vm.$notify({
                type:'error',
                title: 'Gagal',
                message: 'Tidak dapat mengambil data',
                position: 'bottom-right',
              });
            })
      // }, 1000)
    },
    showStar(){
      let vm = this
      vm.datas.forEach((d, ind)=> {
        let array = Object.keys(d.sholats)
        for (let i = 0; i < array.length; i++) {
          const key = array[i];
          let sholat = d.sholats[key]
          let value = sholat.value
          let id = 'star' + ind + vm.tanggals[ind] + key;
          this.toggleStarClass(id, value)
        }
      })
    },
    toggleStarClass(id, value){
      let vm = this
      if (value >= 50) {
        vm.removeClass('#1' + id,'scale-0')
        vm.removeClass('#2' + id,'scale-0')
        vm.removeClass('#3' + id,'scale-0')
      } else {
        vm.addClass('#1' + id,'scale-0')
        vm.addClass('#2' + id,'scale-0')
        vm.addClass('#3' + id,'scale-0')
      }

      
      if (value == 100) {
        vm.removeClass('#1' + id,'grayscale')
        vm.removeClass('#2' + id,'grayscale')
        vm.removeClass('#3' + id,'grayscale')
      } else if (value >= 75) {
        vm.addClass('#1' + id,'grayscale')
        vm.removeClass('#2' + id,'grayscale')
        vm.removeClass('#3' + id,'grayscale')
      } else if (value >= 50) {
        vm.addClass('#1' + id,'grayscale')
        vm.addClass('#2' + id,'grayscale')
        vm.removeClass('#3' + id,'grayscale')
      } 
    },
    saveData(ind, kolom){
      let data = this.datas[ind]
      let form = {
        id:data.id,
        id_anggota:this.idAnggota,
        tanggal:this.tanggals[ind],
      }
      form[kolom] = data.sholats[kolom].value
      console.log(form)
      var formData = window.jsonToFormData(form); 
      this.$http.post('sholat/sunnah/store', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          // this.getData()
          this.getLast()
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
      await this.$http.get('sholat/sunnah/dashboard', {
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
      console.log('center')
      let vm = this
      vm.afterScroll = false
      let body = vm.jquery('#body-scroll');
      let bcenter = vm.jquery('#body1');
      body[0].scrollLeft = bcenter[0].offsetLeft

      if (vm.editTanggal) return
      let header = vm.jquery('#header-scroll');
      let center = vm.jquery('#header1');
      header[0].scrollLeft = center[0].offsetLeft

      setTimeout(() => {
        console.log('bypass-handle')
        vm.afterScroll = true
      }, 700);
    },
    scrollHeader(course = -1){
      let duration = 0.7
      let vm = this
      // console.log(course)
      vm.removeClass('#header-scroll','snap-x snap-mandatory')
      if (course == -1) {
        vm.scrollElement('#header-scroll','#header0',duration)
      } else {
        vm.scrollElement('#header-scroll','#header2',duration)
      }
      setTimeout(() => {
        vm.addClass('#header-scroll','snap-x snap-mandatory')
      }, duration * 1000 + 100);
    },
    setTanggalInitial(){
      this.tanggals = [
        this.addDay(this.tanggal, -1),
        this.tanggal,
        this.addDay(this.tanggal, 1),
      ]
    },
    setDataInitiall(){
      this.datas = [];
      for (let index = 0; index < this.tanggals.length; index++) {
        this.datas[index] = {}
        this.getData(index)
      }
    },
    changeTanggalData(course = -1){
      console.log(course)
      let vm = this
      vm.tanggal = vm.addDay(vm.tanggal, course)
      for (let i = 0; i < vm.tanggals.length; i++) {
        vm.tanggals[i] = vm.addDay(vm.tanggals[i], course)
      }
      // unset(vm.datas[-1])
      // unset(vm.datas[3])
      let n_data = {}
      if (course == -1) {
        vm.datas.pop()
        vm.datas.unshift(n_data)
        vm.getData(1)
      } else {
        vm.datas.shift()
        vm.datas.push(n_data)
        vm.getData(2)
      }
    },
    handleAfterScroll(){
      if (!this.afterScroll) return
      console.log('handle-after')
      let vm = this
      if (vm.editTanggal == true)
        return
      let header = this.jquery('#header-scroll')[0]
      let right = this.jquery('#header2')[0]
      // console.log(header.scrollLeft)
      if (header.scrollLeft == 0) {
        this.changeTanggalData(-1)
        this.setHeaderToCenter()
      } else if (header?.scrollLeft == right?.offsetLeft) {
        this.changeTanggalData(1)
        this.setHeaderToCenter()
      }
      // setTimeout(() => {
        vm.loadings[0] = vm.loadings[1] = vm.loadings[2] = false
      // }, 500);
    },  
  },
  created: function() {
    // this.tanggal = this.dateNow()
    this.tanggal = '2025-05-01'
    this.idAnggota = this.$store.getters.loggedUser.id_anggota
    this.setTanggalInitial()
    this.setDataInitiall()
    this.getLast()
  },
  mounted: function() {
    let vm = this
    vm.sizeWindow = window.innerWidth

    window.addEventListener('resize', () => {
      vm.sizeWindow = window.innerWidth
    });

    let scrollTimeout;
    jquery('#header-scroll').on('scroll', () => {      
      const scrollLeft = jquery('#header-scroll')[0].scrollLeft;
      // console.log('header-scroll')
      vm.removeClass('#body-scroll','snap-x snap-mandatory')
      jquery('#body-scroll').scrollLeft(scrollLeft);
      vm.following = 'body'

      clearTimeout(scrollTimeout);
      if (vm.afterScroll) {
        vm.loadings[0] = vm.loadings[1] = vm.loadings[2] = true
      }
      scrollTimeout = setTimeout(() => {
        console.log('header', 'Scrolling has finished');
        setTimeout(() => {
          vm.addClass('#header-scroll','snap-x snap-mandatory')
          vm.addClass('#body-scroll','snap-x snap-mandatory')
          vm.handleAfterScroll()
        }, 500);
        // Your code here
      }, 200); // Adjust timeout duration as needed
    });
    
    jquery('#body-scroll').on('scroll', () => {
      const scrollLeft = jquery('#body-scroll')[0].scrollLeft;
      // console.log('body-scroll')
      // console.log(scrollLeft)
      vm.removeClass('#header-scroll','snap-x snap-mandatory')
      jquery('#header-scroll').scrollLeft(scrollLeft);
    });
    this.setHeaderToCenter()
    // window.addEventListener('scroll', this.handleScroll);
  },
  unmounted(){
    // window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>
