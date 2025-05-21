<template>
    <el-container class="z-[10]">
      <top-menu>
        <el-header class="bg-orange-100 h-[40px] w-full relative"></el-header>
        <div id="top" class="add-play bg-cover bg-bottom
          h-[80px] w-[1600px] absolute z-[101] top-[1px]
          scale-x-[0.5]
          translate-x-[calc(50vw-50%)] sm:-translate-x-[675px]"
            :style="`background-image:url('${$baseUrl}assets/images/top.png')`"></div>
        <img id="logo" :src="$baseUrl + 'assets/images/logo-kecil.png'" height="80px" 
          @click="$router.push({name:defaultRoute})"
          class="pointer animate hover:scale-[0.8]
          absolute z-[103] top-[-10px]
          mt-5
          translate-x-[calc(50vw-50%)]
          sm:-translate-x-[calc(50%-125px)]"/>
        <div class="absolute w-full h-full top-0
            border-0 border-b-[5px] border-solid border-orange-300">
          <el-menu :default-active="activeMenu"
            mode="horizontal"
            class="el-menu-demo absolute z-[20]
              flex justify-end
              top-[0px] right-10 h-[inherit]
              w-[calc(100vw-575px)] bg-orange-100 
              [&_*]:text-teal-700
							[--el-menu-active-color:theme(colors.teal.700)]">
            <template v-for="menu in menus">
              <template v-if="menu.type == 'submenu'">
                <el-sub-menu :index="menu.index"
                  class="menu-item-custom title"
                  :popper-offset="10">
                  <template #title>
                    <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                    <span class="">{{ menu.title }}</span>
                  </template>
                  <template v-for="child in menu.children">
                    <el-menu-item @click="$router.push(child.route)"
                      :index="child.index"
                      class="menu-item-custom title">
                      <icons v-if="!isEmpty(child.icon)" class="mr-2" :icon="child.icon" />
                      <span class="">{{ child.title }}</span>
                    </el-menu-item>
                  </template>
                </el-sub-menu>
              </template>
              <template v-else>
                <el-menu-item @click="$router.push(menu.route)"
                  :index="menu.index"
                  class="menu-item-custom title">
                  <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                  <span class="">{{ menu.title }}</span>
                </el-menu-item>
              </template>
            </template>
            <el-menu-item class="menu-item-custom title"
              @click="$emit('toggle', '1')">
              <icons icon="gg:sidebar-open" class="mr-2"/>
              <span>Menu Vertical</span>
            </el-menu-item>
          </el-menu>
          <div class="absolute w-full h-full bg-orange-100 top-0 left-0 z-[1]">
          </div>
        </div>
      </top-menu>
    </el-container>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import TopMenu from './TopMenu.vue';
  
  export default {
    name: 'horizontal-menu',
    components:{
      TopMenu,
    },
    emits:[],
    props:{
      activeMenu: {
        type:String,
        default:'',
      },
      menus:{
        type:[Array, Object],
        default:[],
      }
    },
    data: function() {
      return {
      };
    },
    watch: {
  
    },
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),
    },
    methods: {
      handleActionClick(val){
        this.$emit('action', val)
      }
    },
    updated: function() {
      
    },
    beforeRouteLeave(to, from){
      
    }
  }
  </script>
  
  <style lang="postcss" scoped>
    .menu-item-custom{
      @apply 
        transition-all ease-in-out duration-300
        bg-gradient-to-l from-transparent from-50% to-teal-700 to-50%
        bg-[length:200%_200%] bg-right-bottom 
        text-[13px]
        leading-[0]
        border-0
        [--el-menu-item-height:45px]
        [--el-menu-sub-item-height:45px]
        hover:bg-left-top
      !important;
    }
    .menu-item-custom.is-active {
      @apply
        bg-teal-100
      !important;
    }
    .menu-item-custom.title {
      @apply 
        [&>*]:text-teal-700
        [&>*]:hover:text-white
        !important;
    }
    .menu-item-custom.is-active {
      @apply
        bg-orange-300
      !important;
    }
</style>