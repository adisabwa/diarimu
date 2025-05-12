<template>
  <div class="">
    <div class="z-[2] h-[40px]">
      <div class="absolute top-0 overflow-hidden w-full h-[100px]"> 
        <el-header class="bg-orange-300 h-[40px] w-full relative"></el-header>
        <div id="top" class="add-play bg-cover bg-bottom
          h-[70px] w-[1400px] absolute z-[51] top-[1px]
          translate-x-[calc(50vw-50%)] md:-translate-x-[calc(675px)]"
            :style="`background-image:url('${$baseUrl}assets/images/top.png')`"></div>
        <img id="logo" :src="$baseUrl + 'assets/images/logo-kecil.png'" height="90px" 
          @click="$router.push({name:defaultRoute})"
          class="pointer animate hover:scale-[0.8]
          absolute z-[53] top-[5px]
          mt-2
          translate-x-[calc(50vw-50%)]
          md:-translate-x-[calc(50%-125px)]"/>
      </div>
    </div>
    <div id="menu-vertical" class="h-screen w-[--width-menu] bg-white
      animate
      -translate-x-full
      fixed left-0 top-0
      z-[50] md:z-[1]
      flex flex-col justify-between">
      <el-menu :default-active="activeMenu"
        @select="handleSelect"
        class="el-menu-vertical-demo
          w-full h-full
          pt-8 md:pt-28">
        <div class="mt-7 mb-6 [&_*]:text-teal-700 
          flex flex-col items-center space-y-3">
          <el-dropdown trigger="click" @command="handleActionClick">
            <el-button type="primary" link size="small"> 
              <div class="mx-7">
                <div class="font-semibold text-[17px]">{{ user.nama }}</div>
                <div class="font-semibold text-[14px]
                  mt-2 leading-[1.3] whitespace-normal" >{{ user.unit }}</div>
              </div>
            </el-button>
            <template #dropdown>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item 
                  :command="{action: 'edit'}">
                  <icons icon="material-symbols:edit-outline"/> Ubah Password</el-dropdown-item>
                <el-dropdown-item 
                  :command="{action: 'logout'}">
                  <icons icon="material-symbols:power-settings-new"/> Logout</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
        <template v-for="menu in menus">
          <template v-if="menu.type == 'submenu'">
            <el-sub-menu :index="menu.index" class="pl-5 [&>*]:p-0 text-left menu-item-custom title">
              <template #title>
                <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
                <span class="">{{ menu.title }}</span>
              </template>
              <template v-for="child in menu.children">
                <el-menu-item @click="$router.push(child.route)"
                  :index="child.index" class="pl-6 menu-item-custom title">
                  <icons v-if="!isEmpty(child.icon)" class="mr-2" :icon="child.icon" />
                  <span class="">{{ child.title }}</span>
                </el-menu-item>
              </template>
            </el-sub-menu>
          </template>
          <template v-else>
            <el-menu-item @click="$router.push(menu.route)"
              :index="menu.index" class="pl-5 text-left menu-item-custom title">
              <icons v-if="!isEmpty(menu.icon)" class="mr-2" :icon="menu.icon" />
              <span class="">{{ menu.title }}</span>
            </el-menu-item>
          </template>
        </template>
      </el-menu>
      <div class="text-teal-700
        text-center px-2 pb-10">
        <div class="mb-2 text-[12px]">Ubah Menu</div>
        <div class="flex items-center justify-center
          w-[150px] py-1 px-1 mx-auto
          text-teal-700 bg-teal-50 pointer text-[13px]
          border border-teal-700 border-solid
          transitian-all duration-300 hover:scale-90"
          @click="$emit('toggle', '0')">
          <icons icon="tdesign:menu-filled"/>
          <span>Menu Horizontal</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'vertical-menu',
  components:{
  },
  props:{
    activeMenu: {
      type:String,
      default:'',
    },
    menus:{
      type:Array,
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
    },
    handleSelect: function(action) {
      this.addClass('.el-menu-vertical-demo','-translate-x-full md:translate-x-0');
    },
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
</style>