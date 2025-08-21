<template>
  <div id="group-admin" class="pt-16 translate-y-[-20px]">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[15px] font-montserrat font-bold text-left"
      body-class="py-2 px-0">
      <template #header>
        <div class="relative text-center">
          {{ isEmpty(data.nama_group) ? 'Kelompok' : 'Kel.' }} {{ data.nama_group }}
        </div>
      </template>
      <template v-if="isEmpty(data.id)" >
        <div class="text-center h-20 w-[200px]
          flex items-center mx-auto">
          Anda belum memiliki grup
        </div>
      </template>
      <template v-else>
        <table class="w-full
          [&_td]:align-top text-[13px] px-6">
          <tbody>
            <tr class="font-bold">
              <td >Mentor</td>
              <td width="20" class="text-center">:</td>
              <td v-if="!isEmpty(data.anggota)">{{ data.anggota[0].nama }}</td>
            </tr>
            <tr class="font-bold">
              <td >Anggota</td>
              <td width="20" class="text-center">:</td>
              <td class="font-normal">
                <ol class="text-[13px] pl-4 m-0">
                  <template v-for="(i, key) in data.anggota">
                    <li class="pl-1"
                      v-if="key > 0">{{ i.nama }}</li>
                  </template>
                </ol>
              </td>
            </tr>
            <tr class="font-bold">
              <td >Aktivitas</td>
              <td width="20" class="text-center">:</td>
              <td class="text-right">
                <el-button class="[&_*]:text-[11px] h-fit py-1 active:scale-90
                  bg-teal-700 text-white"
                  @click="showAdd = true; dataId = -1;"
                  >
                  <icons icon="mdi:plus"/>Tambah Data
                </el-button>
              </td>
            </tr>
          </tbody>
          </table>
        <ListData v-if="data.id"
          ref="groupListData"
          class="[--text-color:theme(colors.teal.900)]
            [--bg-color:theme(colors.teal.50)]
            [--border-color:theme(colors.teal.400)]
            [--bg-button-color:theme(colors.teal.100)]
            [--button-color:theme(colors.teal.200)]
            max-h-[70vh] mt-4
          "
          :id-anggota="data?.id"
          nama-id="id_group"
          :order-by="['tanggal DESC','id DESC']"
          href="data/group/activity"
          href-delete="data/group/activity/delete"
          @edit-data="(({id}) => {
            dataId = id
            showAdd = true
          })">
          <template #title="{ data }">
            <div class="font-bold italic text-[14px]">{{ dateDayIndo(data.tanggal) }}</div>
            <el-divider class="my-1 
              border-0 border-b border-solid border-teal-700/[0.5]"/>
          </template>
          <template #content="{ data }">
            <div :class="[`text-[12px] 
              inline-block overflow-hidden`,
              data.show ? '' : 'max-h-[35px]']">{{ data.kegiatan }}</div>
            <div class="text-teal-700 text-[10px] float-right"
              @click="data.show = !data.show">Show All</div>
          </template>
        </ListData>
      </template>
    </el-card>
    <teleport to="body">
      <el-dialog v-model="showAdd" draggable
        :append-to-body="true"
        class="w-fit max-w-[80%] py-3
          bg-gradient-to-tr from-white from-50% to-teal-100"
        header-class="font-bold text-[16px]"
        body-class="text-[14px]">
        <template #header>
          <div>Data Aktifitas</div>
        </template>
        <form-comp ref="formActiviity"
          class="[&_*]:rounded-[15px]"
          :key="'form-activity-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          href="data/group/activity/store"
          href-get="data/group/activity/get"
          :show-columns="['tanggal','kegiatan']"
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
            <el-button type="primary" @click="$refs.formActiviity.submitForm()"
              class="bg-teal-700">
              Simpan
            </el-button>
          </div>
        </template>
      </el-dialog>
    </teleport>
  </div>
</template>

<script>
import ListData from '@/pages/components/ListData.vue';
import { orderBy } from 'lodash';

export default {
  name:'group-user',
  components:{
    ListData
  },
  data: () => {
    return {
      loading:true,
      showAdd: false,
      formKey:1,
      dataId:-1,
      fields:{
        tanggal:'',
        kegiatan:'',
      },
      data:{},
      listActivity:[],
      formValue:{},
    }
  },
  watch:{
    showAdd(){
      this.formKey = this.formKey + 1
    }
  },
  methods: {
      getInitial: async function() {
        this.loading = true;
        // await this.$http.get('/infaq/shadaqah/get_last')
        //   .then(result => {
        //     var res = result.data;
        //     this.lastData = this.fillAndAddObjectValue(this.lastData, res)
        //   });
        
        await this.getData();
        await this.$http.get('/kolom/preparation?table=mu_group_activity&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            this.fields.id_group.default = this.data.id
            this.loading = false
          });
        // this.datas = [];
        // await this.getData();
      },
    async getData() {
      this.loading = true;
      this.$http.get('/data/group',{
        params: {
          where: {
            id_anggota:useAuthStore()?.loggedUser?.id_anggota
          }
        }
      })
          .then(result => {
            var res = result.data;
            // console.log(res)
            this.data = res[0] ?? {}
            this.loading = false
          });
    },
    submittedData(){
      this.loading = false;
      this.showAdd = false;
      this.$refs.groupListData.getData(true)
    },
  },
  created: function() {
    this.getInitial()
  },
}
</script>