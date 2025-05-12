<template>
  <div id="group-admin" class="pt-0 translate-y-[-20px]">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[15px] font-montserrat font-bold text-left"
      body-class="py-2 px-0">
      <template #header>
        <div class="relative">
          <el-button class="absolute right-2 top-1/2 -translate-y-1/2
            rounded-full p-0
            h-[25px] w-[25px]
            flex items-center justify-center
            font-montserrat
            bg-teal-700
            text-white
            active:scale-90"
            @click="showAdd = true;">
            <icons icon="mdi:plus" class="m-0"/>
          </el-button>
          Data Group
        </div>
      </template>
      <div class="px-4">
        <div class="py-3 px-4 bg-white/[0.9] rounded-[15px]
          border border-solid border-teal-700/[0.2]
          text-[15px]">
          <div>Kelompok 1</div>
          <el-divider class="my-1"/>
          <div class="text-[13px]
            flex items-center"
            @click="show = !show">
            <icons :icon="show ? 'fe:arrow-down' : 'fe:arrow-up'" 
              class="text-[13px]"/>
            Nama Ketua</div>
          <ol v-if="show" class="text-[12px] pl-8 m-0">
            <template v-for="i in range(4)">
              <li class="pl-1">Anggota {{ i }}</li>
            </template>
          </ol>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script>
export default {
  name:'group-admin',
  data: () => {
    return {
      show:true,
    }
  }
}
</script>