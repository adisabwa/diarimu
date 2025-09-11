<template>
    <el-card class="rounded-[10px] w-full
      bg-gradient-to-tr from-[var(--bg-start-color)] from-0% to-[var(--bg-end-color)] 
      mb-3 p-0"
      body-class="relative p-0"
      header-class="relative p-0">
      <template #header>
        <slot name="header" />
        <DragScroll id="header-scroll" ref="headerScroll" snap-type="direction"
          :syncWith="syncWith?.header" class="relative px-0 py-4 font-bold text-[18px] overflow-x-scroll flex" >
          <div v-if="editTanggal" class="text-center w-full">
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
          <template v-else>
            <el-container v-for="(t, key) in tanggals" 
              :id="'header'+key"
              class="snap-center w-full px-4  shrink-0">
              <div @click="changeTanggal" class="w-full text-center">
                <span 
                  >{{ dateDayIndo(t) }}</span>
              </div>
            </el-container>
          </template>
        </DragScroll>
        <icons @click="scrollHeader(-1)"
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 left-5"
          icon="iconamoon:arrow-left-2-bold"/>
        <icons @click="scrollHeader(1)"
          class="m-0 text-[35px] pointer z-[999]
            absolute top-1/2 -translate-y-1/2 right-5" 
          icon="iconamoon:arrow-right-2-bold"/>
      </template>
      <div class="mt-1 text-center active:scale-90 cursor-pointer"
        @click="collapseInput = !collapseInput">
        <icons v-if="collapseInput" icon="fe:arrow-down" class="scale-x-[1.5] text-purple-900/[0.4]"/>
        <icons v-else icon="fe:arrow-up" class="scale-x-[1.5] text-purple-900/[0.4]"/>
      </div>
      <div :class="[collapseInput ? 'max-h-0 py-0' : 'max-h-[var(--max-height,100vh)] pt-0 pb-6','overflow-y-auto']">
        <DragScroll id="body-scroll" ref="bodyScroll" snap-type="direction"
          :syncWith="syncWith?.body" :class="[ `relative px-0 
          animate
          flex    
          overflow-y-auto
          overflow-x-auto`]"
          @scrollEnd="handleAfterScroll">
          <template v-for="(_data, ind) in datas"
              :key="'data'+ind+formKey">
            <el-container :id="'body'+ind" 
              class="shrink-0 snap-center font-montserrat
              px-5 w-full
              relative
              grid grid-cols-1"
              v-loading="loadings[ind]">
              <slot :data="_data" :tanggal="tanggals[ind]"/>
            </el-container>
          </template>
        </DragScroll>
      </div>
      <slot name="after" :datas="datas" />
    </el-card>
</template>


<script>
import DragScroll from '@/components/DragScroll.vue';

export default {
  name: "sholat",
  components: {
    DragScroll,
  },
  props:{
    defaultData:{type:Object, default:{} },
    idAnggota:{type:String, default:'' },
    dataTanggal:{type:String, default:'' },
  },
  data: function() {
    return {
      loading: false,
      editTanggal:false,
      dataId:-1,
      formKey:1,
      tanggals:[],
      tanggal:'',
      datas:[],
      loadings:[false, false, false],
      tipe:'',
      collapseInput:false,
      syncWith:{},
    };
  },
  watch: {
    editTanggal(val){
      let vm = this
      setTimeout(() => {
        vm.setHeaderToCenter()
      }, 50);
    },
    idAnggota(val){
        this.resetData()
    },
    dataTanggal(val) {
      this.tanggal = val
      this.resetData()
    }
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
  },
  methods: {
    createData: async function(index, id = -1) {
      let vm = this
      vm.loading = true;
      vm.loadings[index] = true;
      this.$emit('create-data',{
        data: this.datas[index],
        tanggal: this.tanggals[index],
        id: id,
      })
    },
    changeData(tanggal, updateData = {}){
        let ind = this.tanggals.findIndex(t => t == tanggal)
        // console.log(tanggal, ind)
        for (const [key, value] of Object.entries(updateData)) {
            this.datas[ind][key] = value
        }
        this.loadings[ind] = false;
        this.loading = false
        this.formKey++
        // console.log(this.datas, this.formKey)
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
        this.datas[index] = JSON.parse(JSON.stringify(this.defaultData))
        this.createData(index)
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
      let n_data = JSON.parse(JSON.stringify(vm.defaultData))
      if (course == -1) {
        vm.datas.pop()
        vm.datas.unshift(n_data)
        vm.createData(1)
      } else {
        vm.datas.shift()
        vm.datas.push(n_data)
        vm.createData(2)
      }
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
      let vm = this
      let bcenter = vm.jquery('#body1');
      this.$refs.bodyScroll.setScroll({
        left: bcenter[0].offsetLeft
      })

      if (vm.editTanggal) return
      let center = vm.jquery('#header1');
      this.$refs.headerScroll.setScroll({
        left: center[0].offsetLeft
      })

    //   console.log('center',  bcenter[0].offsetLeft, center[0].offsetLeft)
    },
    scrollHeader(course = -1){
      let duration = 0.7
      let vm = this
    //   console.log('set-to-center', course)
      // vm.removeClass('#header-scroll','snap-x snap-mandatory')
      if (course == -1) {
        vm.scrollElement('#header-scroll','#header0',duration)
      } else {
        vm.scrollElement('#header-scroll','#header2',duration)
      }
      // setTimeout(() => {
      //   vm.addClass('#header-scroll','snap-x snap-mandatory')
      // }, duration * 1000 + 100);
    },
    handleAfterScroll(){
    //   console.log('handle-after')
      let vm = this
      if (vm.editTanggal == true)
        return
      let header = this.jquery('#header-scroll')[0]
      let right = this.jquery('#header2')[0]
    //   console.log(header.scrollLeft, right?.offsetLeft)
      if ((Math.floor(header.scrollLeft / 10)) == 0) {
        this.changeTanggalData(-1)
        this.setHeaderToCenter()
      } else if (Math.floor(header?.scrollLeft / 10) == Math.floor(right?.offsetLeft / 10)) {
        this.changeTanggalData(1)
        this.setHeaderToCenter()
      }
      // setTimeout(() => {
        vm.loadings[0] = vm.loadings[1] = vm.loadings[2] = false
      // }, 500);
    },  
    resetData(){
        this.setTanggalInitial()
        this.setDataInitial()
    },
  },
  created: function() {
    this.tanggal = this.dateNow()
    this.resetData()
  },
  mounted: function() {
    let vm = this
    this.setHeaderToCenter()
    // window.addEventListener('scroll', this.handleScroll);
    this.syncWith = {
      header:[this.$refs.bodyScroll],
      body:[this.$refs.headerScroll],
    }
  },
}
</script>