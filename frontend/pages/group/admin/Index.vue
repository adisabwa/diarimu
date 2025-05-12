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
    
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit max-w-[80%] py-3
        bg-gradient-to-tr from-white from-50% to-teal-100"
      header-class="font-bold text-[16px]"
      body-class="text-[14px]">
      <template #header>
        <div>Data Shadaqah</div>
      </template>
      <form-comp ref="formGroup"
        class="[&_*]:rounded-[15px]"
        :key="'form-group-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        href="data/group/store"
        href-get="data/group/get"
        @saved="submittedData" 
        @error="saving=false"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
      </form-comp>  
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button type="primary" @click="$refs.formGroup.submitForm()"
            class="bg-teal-700">
            Simpan
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import Form from '@/components/Form.vue'

export default {
  name:'group-admin',
  components:{
    'form-comp': Form,
  },
  data: () => {
    return {
      show:true,
      showAdd: true,
      formKey:0,
      fields:{},
      dataId:-1,
    }
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        
        await this.$http.get('/kolom/preparation?table=mu_group&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            // console.log(this.fields)
            this.formKey++
            this.loading = false
          });
      },
    submittedData(){
      this.saving = false
    },
  },
  created: function() {
    this.getInitial()
  },
}
</script>