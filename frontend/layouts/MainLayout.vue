<template>
  <div id="main-layout" class="bg-white 
    [--width-menu:250px]">
    <el-container class="z-[10]">
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
    </el-container>
    <el-container>
      <el-main class="p-0 mt-[50px] px-3 md:px-10 pb-12 overflow-[unset]
        min-h-[calc(100vh-40px)] 
        relative
        flex flex-col">
        <div class="fixed w-screen h-screen left-0 top-0
          -scale-x-100 z-[0]
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
    </el-container>
    <el-container class="w-full main-content-wrap ml-0 overflow-hidden">
      <el-footer height="auto" class="w-full h-[40px] px-0 py-2 z-[9] bg-gray-100 relative">
        <div id="bottom" class="min-w-[1200px]
          bg-contain bg-no-repeat bg-bottom
          absolute bottom-0 -translate-y-[40px] left-1/2 -translate-x-1/2
          h-[60px] w-full"
          :style="`background-image:url('${$baseUrl}assets/images/bottom.png')`"></div>
        <div class="text-center">
          &copy; 2023 by <a href="https://codev-app.my.id/" target="_blank" class="no-underline text-green-900">Codev-App</a>
        </div>
      </el-footer>
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
      console.log(index, this.menus)
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
      console.log(index)
      this.activeMenu = index
    },
    async getMenus(){
      let vm = this
      let index = vm.coalesce([vm.$route.meta.app, 'default'])
      await import(`@/helpers/menus/management.js`)
        .then(res => {
          console.log(res.default)
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