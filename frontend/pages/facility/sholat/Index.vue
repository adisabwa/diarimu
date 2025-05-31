<template>
  <div id="bacaan-sholat" class="pt-[55px] translate-y-[-10px] px-0">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-fuchsia-100/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[18px] font-bold text-left text-center"
      body-class="py-3 px-4 max-h-[calc(100vh-200px)] overflow-y-auto">
      <template #header>
        <div @click="showList = true">Bacaan Sholat</div>
        <img :src="sholat.image" height="90px" width="90px"
            class="absolute z-[-1] top-[0px] left-[-15px]
              opacity-[0.5]"/>
      </template>
      <transition-group name="fade"
        enter-active-class="animate"
        leave-active-class="animate"
        enter-from-class="opacity-50 -translate-y-full"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-50 translate-y-full"
        @after-leave="scrollToCoordinate('.body-data', 0, 1, 'top')">
        <div v-if="showList">
          <template v-for="(item, key) in datas">
            <div class="text-center bg-white text-fuchsia-800 rounded-[15px]  shadow-md
              mb-3 px-5 py-3
              cursor-pointer active:scale-90"
              @click="showList = false, dataKey = key">
              <div class="c">{{ item.nama }}</div>
            </div>
          </template>
        </div>
        <div v-if="!showList"
          class="bg-white rounded-[15px] 
            px-4 py-4 mb-4  text-left
            leading-[1.5]
            relative">
          <div class="text-[16px] font-bold mb-2
            absolute w-full top-[16px] left-0 z-[3]">
            <icons v-show="dataKey > 0" icon="fe:arrow-left" 
                class="cursor-pointer text-[20px] text-fuchsia-700 ml-3 z-[2] float-left"
              @click="dataKey--;direction='left'"/>
            <icons v-show="dataKey < (datas.length - 1)" icon="fe:arrow-right" 
              class="cursor-pointer text-[20px] text-fuchsia-700 mr-3 z-[2] float-right"
              @click="dataKey++;direction='right'"/>
          </div>
          <transition name="fade"
            enter-active-class="animate"
            leave-active-class="animate"
            :enter-from-class="'absolute opacity-0 ' + (direction == 'right' ? 'translate-x-full' : ' -translate-x-full')"
            enter-to-class="opacity-100 translate-x-0"
            leave-from-class=" opacity-100 translate-x-0"
            :leave-to-class="'absolute opacity-0 ' + (direction == 'right' ? '-translate-x-full' : ' translate-x-full')">
            <div :key="dataKey">
              <div class="relative text-center text-[16px] font-bold mb-2">
                <div>{{ data.nama }}</div>
              </div>
              <el-divider class="my-3"></el-divider>
              <div class="">
                <div class="italic font-semibold">
                  Lafaz Doa :
                </div>
                <div class="mt-1
                  whitespace-pre-line text-right rtl font-arabic text-[24px] leading-loose">{{ data.arab }}</div>
              </div>
              <div class="mt-5">
                <div class="underline underline-offset-4 italic font-semibold">
                  Tulisan Latin :
                </div>
                <div class="mt-1
                  whitespace-pre-line text-md italic">{{ data.latin }}</div>
              </div>
              <div class="mt-5">
                <div class="underline underline-offset-4 italic font-semibold">
                  Artinya : 
                </div>
                <div class="mt-1
                  whitespace-pre-line">{{ data.terjemah }}</div>
              </div>
            </div>
          </transition>
        </div>
      </transition-group>
    </el-card>
  </div>
</template>
  
  <script>
  import { mapGetters } from 'vuex';
  import { facilityMenu } from '@/helpers/menus.js'
import { data } from 'jquery';
  
  export default {
    name: "bacaan-sholat",
    components: {
      
    },
    data: function() {
      return {
        formValue:{},
        loading: false,
        dataKey: 0,
        datas: [],
        sholat: facilityMenu.bacaanSholat,
        showList: true,
        direction: 'right',
      };
    },
    watch: {
     
    },  
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),    
      data : function() {
        return this.datas[this.dataKey] || {};
      },  
    },
    methods: {
      getInitial: async function() {
        this.loading = true;
        await this.$http.get('/data/bacaan/sholat',{
            params: {
              limit:0 
            }
        })
          .then(result => {
            var res = result.data;
            this.datas = res;
          });
      },
    },
    created: function() {
      this.getInitial();
    },
    mounted: function() {
      
    },
  }
  </script>
  