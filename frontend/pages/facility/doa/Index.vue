<template>
  <div id="bacaan-doa" class="pt-[55px] translate-y-[-10px] px-0">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-amber-100/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[18px] font-bold text-left text-center"
      body-class="body-data py-3 px-4 max-h-[calc(100vh-210px)] overflow-y-auto">
      <template #header>
        <div @click="showList = true">Bacaan Doa</div>
        <img :src="doa.image" height="90px" width="90px"
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
        <div v-if="showList" class="grid grid-cols-2 gap-x-4">
          <template v-for="(_datas, key) in [dataFavorites,dataNormal]">
            <div class="col-span-2 sticky top-0 z-[10]
              text-center bg-white text-slate-500 mt-4 mb-2 py-3
              ">
              <span>{{ key == 0 ? 'Doa Favorit' : 'Data Doa Lengkap' }}</span>
            </div>
            <template v-for="(item, key) in _datas">
              <div class="text-center bg-white text-amber-800 rounded-[15px]  shadow-md
                mb-3 z-[0]
                relative overflow-hidden
                cursor-pointer active:scale-90">
                <div class="text-left h-full py-3 mx-5
                  bg-white/[0.7] z-[2]"
                @click="showList = false;
                  dataKey = datas.findIndex(d => d.id == item.id);
                ">
                  {{ item.judul }}
                </div>
                <img :src="$baseUrl + '/assets/images/icons/' + item.icon" 
                  class="absolute bottom-[-10px] right-[-15px] w-[70px] z-[-1]
                    opacity-50"/>
              </div>
            </template>
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
                class="cursor-pointer text-[20px] text-amber-700 ml-3 z-[2] float-left"
              @click="dataKey--;direction='left'"/>
            <icons v-show="dataKey < (datas.length - 1)" icon="fe:arrow-right" 
              class="cursor-pointer text-[20px] text-amber-700 mr-3 z-[2] float-right"
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
                <div>{{ data.judul }}</div>
              </div>
              <el-divider class="my-3"></el-divider>
              <div :class="[
                data.fav ? 'text-yellow-800 ' : 'text-slate-400',
                'flex items-center justify-center text-[13px]'
              ]"
                @click="clickFavorite(data)">
                <icons icon="mdi:star" :class="[data.fav ? 'text-yellow-400 ' : 'text-slate-400', 'mb-[1px]']"/> 
                  {{ data.fav ? 'Doa ' : 'Tambah ke ' }} Favorit
              </div>
              <div class="text-center">
                <img :src="$baseUrl + '/assets/images/icons/' + data.icon" 
                  class="h-[150px] my-6 "/>
              </div>
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
    name: "bacaan-doa",
    components: {
      
    },
    data: function() {
      return {
        formValue:{},
        loading: false,
        dataKey: 0,
        datas: [],
        doa: facilityMenu.bacaanDoa,
        showList: true,
        direction: 'right',
        favorites:[],
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
      dataFavorites() {
        return this.datas.filter(d => d.fav)
      },
      dataNormal() {
        return this.datas.filter(d => !d.fav)
      } 
    },
    methods: {
      getInitial: async function() {
        this.loading = true;
        await this.$http.get('/data/bacaan/doa',{
            params: {
              limit:0 
            }
        })
          .then(result => {
            var res = result.data;
            let favorites = this.getDataFormStorage('data-favorites')
            res.forEach(d => {
              d.fav = favorites.includes(d.id);
            });
            this.datas = res;
            this.favorites = favorites
            this.orderData()
            // console.log( this.favorites,res)
          });
      },
      orderData(){
        this.datas = this.datas.sort((a, b) => {
          if (a.fav && !b.fav) return -1
          if (!a.fav && b.fav) return 1
          return a.id - b.id
        })
      },
      clickFavorite(data){
        if (data.fav)
          this.removeFromStorage('data-favorites', data.id)
        else
          this.saveToStorage('data-favorites', data.id)
        this.favorites = this.getDataFormStorage('data-favorites')
        data.fav = this.favorites.includes(data.id)
        this.orderData()
        setTimeout(() => {
          this.dataKey = this.datas.findIndex(d => d.id == data.id)
        }, 300)
      }
    },
    created: function() {
      this.getInitial();
    },
    updated: function() {
      this.favorites = this.getDataFormStorage('doa-favorites')
      console.log(this.favorites)
    },
  }
  </script>
  