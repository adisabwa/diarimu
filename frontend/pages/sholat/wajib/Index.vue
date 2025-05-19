
<template>
  <div id="sholat relative" class="pt-[50px]">
    <FilterAnggota v-if="user.role != 'user'" 
      v-model:id-anggota="idAnggota" @change="reloadData"/>
    <el-card v-show="['user','super-admin'].includes(user.role)"
      class="rounded-[10px]
      bg-gradient-to-tr from-white/[0.8] from-30% to-purple-200/[0.7] 
      mb-3 p-0"
      body-class="relative p-0"
      header-class="relative p-0">
      <img :src="sholat.image" height="90px" width="90px"
          class="absolute z-[0] top-[-60px] right-[-20px]
            opacity-[0.5]"/>
      <template #header>
        <div id="header-scroll" class="relative px-0 py-4 font-bold text-[18px] overflow-x-scroll
        snap-x snap-mandatory" >
          <div v-if="editTanggal" class="text-center">
            <date-wheel-picker
              ref="editTanggal"
              id="editTanggal"
              class="w-fit mx-auto"
              v-model:value="tanggal"
              value-format="YYYY-MM-DD"
              format="DD MMMM YYYY"
              clearable 
              size="large"
              @change="editTanggal = false;
                setTanggalInitial();
                setDataInitial();"
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
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 left-5"
          icon="iconamoon:arrow-left-2-bold"/>
        <icons @click="scrollHeader(1)"
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 right-5" 
          icon="iconamoon:arrow-right-2-bold"/>
      </template>
      <div class="mt-1 text-center active:scale-90"
        @click="collapseInput = !collapseInput">
        <icons v-if="collapseInput" icon="fe:arrow-down" class="scale-x-[1.5] text-purple-900/[0.4]"/>
        <icons v-else icon="fe:arrow-up" class="scale-x-[1.5] text-purple-900/[0.4]"/>
      </div>
      <div id="body-scroll" :class="[collapseInput ? 'max-h-0 py-0' : 'max-h-screen pt-0 pb-6', `relative px-0 
        animate
        flex    
        overflow-x-scroll
        snap-x snap-mandatory`]">
        <template v-for="(_data, ind) in datas">
          <el-container :id="'body'+ind" class="shrink-0 snap-center font-montserrat
            px-5 w-full
            relative
            grid grid-cols-1"
            v-loading="loadings[ind]">
            <template v-for="sholat in _data.sholats">
              <div :class="`${setStatusColor(sholat.value)}
                pt-4 pb-3 mb-4 px-7
                rounded-[15px] min-w-[240px] max-w-[300px]
                shadow-md
                relative flex gap-x-3 items-center`">
                <div class="text-[18px] font-bold leading-[1.3] w-full">
                  <div>{{ ucFirst(sholat.nama_kolom) }}</div>
                  <div class="text-[12px] font-semibold opacity-70">
                    {{ getLabel(sholat.value) }}
                  </div>
                </div>
                <div class="flex gap-x-1 mx-3">
                  <star :id="'1star'+ind+tanggals[ind]+sholat.nama_kolom" />
                  <star :id="'2star'+ind+tanggals[ind]+sholat.nama_kolom" />
                  <star :id="'3star'+ind+tanggals[ind]+sholat.nama_kolom" />
                </div>
                <el-dropdown v-if="user.role == 'user'"
                  trigger="click"
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
                </el-dropdown>
              </div>
            </template>
          </el-container>
        </template>
      </div>
    </el-card>
    <el-card class="relative w-full
      overflow-hidden
    bg-white/[0.9] 
      rounded-[10px]
      z-[0] font-montserrat
      mb-3 p-0" 
      body-class="relative p-0 leading-[1]">
      <div class="relative flex px-3 py-5 gap-3 justify-center">
        <template v-for="(data, ind) in {last:lastData, best:bestData}">
          <div class="shrink-1 text-center py-4 px-4
            border-2 border-solid border-indigo-200
            bg-purple-100/[0.4]
            rounded-[20px]">
            <div class="text-gray-500 font-bold w-[100px] text-[14px]
              mb-4">Nilai {{ ind == 'best' ? 'Terbaik' : 'Terakhir' }}</div>
            <template v-if="!isEmpty(data.tanggal)">
              <div class="mb-2  text-gray-500  text-[13px]"><b>( {{ dateShortIndo(data.tanggal) }} )</b></div>
              <div class="mb-2 text-[45px] font-semibold leading-[1]">{{ data.total_score }}</div>
              <div class="flex items-start justify-center">
                <star :id="'1star'+ind+'data'" width="28px"/>
                <star :id="'2star'+ind+'data'" width="33px"/>
                <star :id="'3star'+ind+'data'" width="28px"/>
              </div>
            </template>
            <template v-else>
              <div class="mb-2  text-gray-500  text-[13px]"><b>Belum ada Data</b></div>
            </template>
          </div>
        </template>
      </div>
    </el-card>
    <el-card class="bg-white/[0.9] rounded-[10px] mb-3 p-0"
      body-class="py-3 px-0"
      header-class="py-3 font-bold text-[16px]
        text-lime-800
        flex justify-between items-center" >
      <template #header>
        <div>Data Sholat Wajib</div>
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
      <chart ref="wajibChartData" 
        href="sholat/wajib/dashboard"
        :id-anggota="idAnggota"
        :add-options="{
          scales:{
            y:{
              title:{display:true, text:'Total Score'},
              ticks: {stepSize:50}
            }}}"
           v-if="showData == 'chart'" 
        class="px-4"/>
      <ListData ref="wajibListData"
        class="[--text-color:theme(colors.purple.900)]
          [--bg-color:theme(colors.purple.50)]
          [--border-color:theme(colors.purple.400)]
          [--bg-button-color:theme(colors.purple.100)]
          [--button-color:theme(colors.purple.200)]
        "
        :id-anggota="idAnggota"
        href="sholat/wajib"
        v-if="showData =='list'"
        @edit-data="(({id}) => {
          dataId = id
          showCreate = true
        })">
        <template #title="{ data }">
          {{ dateDayIndo(data.tanggal)}}
        </template>
        <template #content="{ data }">
          <div class="flex items-center"
            @click="data.show_detail = !data.show_detail">
            <icons v-if="data.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
            <icons v-else icon="fe:arrow-up" class="text-[12px]"/>
            Sholat Wajib {{ (
              (data.shubuh > 0 ? 2 : 0) + (data.dhuhur > 0 ? 4 : 0) + (data.asar > 0 ? 4 : 0) + (data.maghrib > 0 ? 3 : 0) + (data.isya > 0 ? 4 : 0) 
            ) }} Raka'at
          </div>
          <ol v-show="data.show_detail"
            class="pl-[30px] italic mt-0 mb-1">
            <li v-for="ind in ['shubuh','dhuhur','asar','maghrib','isya']"
              class="pl-1">
              Sholat {{ ucFirst(ind) }} ( {{ getLabel(data[ind]) }} ) 
              <template v-if="data[ind] >= 25">
                <star :id="'3star'+ind+'data'" :class="['scale-100', data[ind] == 100 ? '' : 'grayscale']" width="10px"/>
                <star :id="'3star'+ind+'data'" :class="['scale-100', data[ind] >= 75 ? '' : 'grayscale']" width="10px"/>
                <star :id="'3star'+ind+'data'" :class="['scale-100', data[ind] >= 50 ? '' : 'grayscale']" width="10px"/>
              </template>
            </li>
          </ol>
        </template>
      </ListData>
    </el-card>
  </div>
</template>

<script setup>
  import { setStatusColor, options, getLabel } from '@/helpers/sholat.js'
</script>

<script>
import { mapGetters } from 'vuex';
import Chart from '@/pages/components/DataChart.vue'
import { topMenu } from '@/helpers/menus.js'
import FilterAnggota from '../../components/FilterAnggota.vue';
import ListData from '@/pages/components/ListData.vue';
import { set } from 'lodash';

export default {
  name: "sholat",
  components: {
    Chart,
    ListData,
    FilterAnggota,
  },
  data: function() {
    return {
      loading: false,
      editTanggal:false,
      dataId:-1,
      idAnggota:null,
      formKey:1,
      tanggals:[],
      tanggal:'',
      datas:[],
      loadings:[false, false, false],
      dataSholat: {
        id:'-1',
        sholats:{
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
        }
      },
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
      sizeWindow:window.innerWidth,
      sholat: topMenu.sholatWajib,
      showData:'chart',
      collapseInput:false,
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
      anggotas:'data/anggotas'
    }),
    labelPosition(){
      return this.sizeWindow < 800 ? 'top' : 'left'
    },
    
  },
  methods: {
    getLast(){
      this.resetObjectValue(this.lastData)
      this.resetObjectValue(this.bestData)
       this.$http.get('sholat/wajib/get_last_and_best', {
            params: {
              id_anggota:this.idAnggota,
            }
          })
        .then( res => {
          let data = res.data
          this.fillObjectValue(this.lastData, data?.last)
          this.fillObjectValue(this.bestData, data?.best)
          this.toggleStarClass('starlastdata',data?.last?.total_score / 5)
          this.toggleStarClass('starbestdata',data?.best?.total_score / 5)
        })
    },
    getData: async function(index) {
      let vm = this
      vm.loading = true;
      vm.loadings[index] = true;
      // setTimeout(() => {
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
              vm.datas[index].id = this.coalesce([data?.id,-1])
              let keys = Object.keys(vm.dataSholat.sholats)
              keys.forEach(( k, ind) => {
                if (data[k] !== null) {
                  vm.datas[index].sholats[k].value = parseInt(data[k])
                } 
                else {
                  vm.datas[index].sholats[k].value = null
                }
                // console.log(index, k, vm.datas[index][k])
                vm.showStar()
              })
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
      if (value >= 25) {
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
      } else if (value >= 25) {
        vm.addClass('#1' + id,'grayscale')
        vm.addClass('#2' + id,'grayscale')
        vm.addClass('#3' + id,'grayscale')
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
      this.$http.post('sholat/wajib/store', formData, {
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
      // console.log('change', this.tanggal)
      setTimeout(() => {
        vm.jquery('#editTanggal .el-input__inner')[0].focus();
        this.$refs.editTanggal.showModal = true
      }, 100);
    },
    setHeaderToCenter(){
      // console.log('center')
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
        // console.log('bypass-handle')
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
    setDataInitial(){
      this.datas = [];
      for (let index = 0; index < this.tanggals.length; index++) {
        this.datas[index] = JSON.parse(JSON.stringify(this.dataSholat))
        this.getData(index)
      }
    },
    changeTanggalData(course = -1){
      // console.log(course)
      let vm = this
      vm.tanggal = vm.addDay(vm.tanggal, course)
      for (let i = 0; i < vm.tanggals.length; i++) {
        vm.tanggals[i] = vm.addDay(vm.tanggals[i], course)
      }
      // unset(vm.datas[-1])
      // unset(vm.datas[3])
      let n_data = JSON.parse(JSON.stringify(vm.dataSholat))
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
      // console.log('handle-after')
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
    reloadData(){
      // console.log(this.idAnggota)
      this.setTanggalInitial()
      this.setDataInitial()
      this.getLast()
      setTimeout(() => {
        this.updateChart();
      }, 400);
      // this.formKey++
    },
    updateChart(){
      if (this.showData == 'chart') this.$refs.wajibChartData?.getChart();
      if (this.showData == 'list') this.$refs.wajibListData?.getData(true);
    }
  },
  created: function() {
    this.tanggal = this.dateNow()
    // this.tanggal = '2025-05-01'
    this.idAnggota = this.$store.getters.loggedUser.id_anggota
    this.$store.dispatch('data/getAllAnggotaInGroup')
    this.setTanggalInitial()
    this.setDataInitial()
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
        // console.log('header', 'Scrolling has finished');
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
