<template>
  <div id="main-layout" class="bg-white 
    [--width-menu:250px]">
    <div class="z-[10] w-full">
      <transition name="slide-in" mode="out-in"
        enter-active-class="transition-all ease-in-out duration-500"
        leave-active-class="transition-all ease-in-out duration-500"
        enter-from-class="translate-x-[-100%]"
        enter-to-class="translate-x-0"
        leave-to-class="translate-x-[-100%]"
        leave-from-class="translate-x-0">
        <component :is="MenuComponent" :key="MenuComponent" 
          :active-menu="activeMenu" :menus="menus" @action="handleActionClick" @toggle="toggleMenu"/>
      </transition>
      <el-button class="fixed z-[200] bottom-7 right-7 rounded-full
        w-[70px] h-[70px] p-3
        md:hidden flex justify-center items-center
        bg-yellow-50/[0.7]"  
        @click="toggleClass('.el-menu-vertical-demo','-translate-x-full')">
        <icons icon="mdi:menu" class="m-0 text-4xl text-teal-700"/>
      </el-button>
    </div>
    <el-container>
      <el-main class="p-0 mt-[50px] px-3 md:px-10 pb-12 overflow-visible
        min-h-[calc(100vh-110px)] 
        relative
        flex flex-col">
        <div class="fixed w-screen h-screen left-0 top-0
          -scale-x-100 z-[0]
          opacity-50
          bg-cover bg-no-repeat bg-left-center bg-fixed" 
          :style="`background-image:url('${$baseUrl}assets/images/back-sketch.png')`">
        </div>
        <div :class="`${user.vertical == '1' ? 'md:ml-[--width-menu]' : 'ml-0' } h-full flex-1 bg-transparent z-[1]`">
          <router-view v-slot="{ Component , route}" >
            <transition name="slide-in" mode="out-in"
              enter-active-class="transition-all ease-in-out duration-500"
              leave-active-class="transition-all ease-in-out duration-500"
              :enter-from-class="route.meta.enterFromClass"
              :enter-to-class="route.meta.enterToClass"
              :leave-from-class="route.meta.leaveFromClass"
              :leave-to-class="route.meta.leaveToClass">
              <component :is="Component" :key="route.path" />
            </transition>
          </router-view>
        </div>
      </el-main>
      <el-footer height="auto" class="h-[20px] px-0 z-[99999] relative">
        <div class="overflow-hidden h-[45px] w-screen
          absolute bottom-0 left-1/2 -translate-x-1/2 -translate-y-[20px]">
          <div id="bottom" class="bg-cover bg-top bg-repeat 
            h-full min-w-[600px] w-full"
            :style="`background-image:url('${$baseUrl}assets/images/bottom.png')`">
          </div>
        </div>
        <div class="text-[12px] text-center h-full bg-gray-100 flex items-center justify-center gap-2">
          &copy; 2023 by <a href="https://codev-app.my.id/" target="_blank" class="no-underline text-green-900"> Codev-App</a>
        </div>
      </el-footer>
      <div class="fixed bottom-0 h-[50px] w-screen z-[99999]
        bg-white">
        <icons icon="mdi:home"/>
        <icons icon="mdi:logout"/>
      </div>
    </el-container>
  </div>
</template>

<script setup>
  import menu from '@/helpers/menus.js';
</script>

<script>
import { mapGetters } from 'vuex';
import VerticalMenu from './components/VerticalMenu.vue';
import HorizontalMenu from './components/HorizontalMenu.vue';

export default {
  name: 'dashboard-layout',
  data: function() {
    return {
      activeMenu: '',
      showMenu: false,
      showMenu2: true,
      scrollPosition:0,
      menus:[],
    };
  },
  components: {
    VerticalMenu,
    HorizontalMenu,
  },
  computed: {
    ...mapGetters({
      user: 'loggedUser',
      pageTitle: 'pageTitle',
      pageSubTitle: 'pageSubTitle',
    }),
    MenuComponent(){
      return VerticalMenu
    }
  },
  methods: {
    setActiveMenu: function() {
      let vm = this
      let index = ''
      // console.log(index, this.menus)
      this.menus.forEach(m => {
        if (m.type == 'menu') {
          if (!index)
            index = vm.checkIndex(vm.$route, m)
        } else {
          m.children.forEach(c => {
            if (!index)
              index = vm.checkIndex(vm.$route, c)
          })
        }
      })
      // console.log(index)
      this.activeMenu = index
    },
    async getMenus(){
      let vm = this
      let index = vm.coalesce([vm.$route.meta.app, 'default'])
      await import(`@/helpers/menus/management.js`)
        .then(res => {
          // console.log(res.default)
          vm.menus = res.default
          this.setActiveMenu()
        })
        .catch( () => {
          vm.menus = []
        })
    },
    checkIndex(route, menu){
      let _route = menu.route
      if (route.name == _route.name) {
        if (this.isEmpty(_route.params))
          return menu.index
        else {
          if (JSON.stringify(_route.params) == JSON.stringify(route.params) )
            return menu.index
        }
      }
      return false
    },
    handleActionClick(obj){
      let action = obj.action
      if (action == 'edit')
        this.$router.push({name:'account'})
      else if (action == 'logout')
        this.doLogout()
    },
    doLogout: function() {
      this.$store.dispatch('logout')
        .then(res => {
          this.$router.replace({ name: 'login' });
        })
        .catch(err => {
          this.$notify({
            type:'error',
            title: 'Gagal',
            message: 'Terjadi kesalahan pada server',
            position: 'bottom-right'
          });
        });
    },
    toggleMenu(to){
      let id = this.user.id
      this.$store.dispatch('pengguna/store', {id : id, vertical: to})
				.then(res => {
					this.$store.dispatch('pengguna/get',{id:id});
					this.$store.dispatch('resetAccount');
				})
    }
  },
  created: async function() {
    this.getMenus()
    this.scrollPosition = window.scrollY;
  },
  mounted(){
   
  },
  beforeRouteLeave(to, from){
  }
}
</script>

<style lang="scss">
  .el-sub-menu__title {
    &:hover {
      background-color: transparent;
    }
  }
</style>