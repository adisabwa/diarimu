<template>
  <div id="group-admin" class="pt-12 translate-y-[-20px]">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[15px] font-montserrat font-bold text-left"
      body-class="py-2 px-0">
      <template #header>
        <div class="relative flex justify-between items-center px-2">
          <div>Data Kelompok</div>
          <div>
            <el-button class="rounded-full p-0
              h-[25px] w-[25px]
              font-montserrat
              bg-emerald-500
              text-white
              active:scale-90"
              @click="showUpload = true;
                getInitial()">
              <icons icon="mdi:upload" class="m-0"/>
            </el-button>
            <el-button class="rounded-full p-0
              h-[25px] w-[25px]
              font-montserrat
              bg-teal-700
              text-white
              active:scale-90"
              @click="showAdd = true;
                getInitial()">
              <icons icon="mdi:plus" class="m-0"/>
            </el-button>
          </div>
        </div>
      </template>
      
      <UploadDialog v-model:show="showUpload" href="data/group"      @saved="onUpdated" title="Kelompok"/>\
        
      <ListData ref="listGroup"
        class="[--text-color:theme(colors.teal.900)]
          [--bg-color:theme(colors.teal.50)]
          [--border-color:theme(colors.teal.400)]
          [--bg-button-color:theme(colors.teal.100)]
          [--button-color:theme(colors.teal.200)]
          font-sans max-h-[calc(100vh-200px)] mt-2
        "
        :params="params"
        href="data/group"
        hrefDelete="data/group/delete"
        box-class="px-1 mb-4"
        @edit-data="(({id}) => {
          dataId = id
          showAdd = true
        })"
        @delete-data="submittedData">
        <template #title="{ data }">
          <div class="text-[16px] leading-[1.3]">
            Kel. {{ data.nama_group }}
          </div>
          <div class="text-[13px] leading-[1.3] mt-1 font-semibold">
            Unit {{ data.unit_kerja }}
          </div>
          <el-divider class="my-2"/>
        </template>
        <template #content="{ data }">
          <div class="text-[13px] font-semibold
            flex items-center justify-between
            pb-2
            "
            @click="data.show = !data.show">
            <div>
              Daftar Anggota <br/>
              <span class="italic"> ( {{data.anggota.filter(r => r.type == 'mentor').length }} Mentor & 
              {{ data.anggota.filter(r => r.type == 'anggota').length }} Anggota )</span>
            </div>
            <icons :icon="data.show ? 'fe:arrow-up' : 'fe:arrow-down'" 
              class="ml-1 text-[12px]"/>
          </div>
          <div :class="['animate bg-white px-2  overflow-hidden',
            data.show ? 'max-h-screen pt-1 pb-2' : 'max-h-0 p-0']">
            <div class="italic">Mentor :</div>
            <ol class="text-[12px] italic pl-4 m-0">
              <template v-for="(i, key) in data.anggota.filter(r => r.type == 'mentor')">
                <li class="pl-1">{{ i.nama }}</li>
              </template>
            </ol>
            <div class="mt-1 italic">Anggota :</div>
            <ol class="text-[12px] italic pl-4 m-0">
              <template v-for="(i, key) in data.anggota.filter(r => r.type == 'anggota')">
                <li class="pl-1">{{ i.nama }}</li>
              </template>
            </ol>
          </div>
        </template>
      </ListData>
    </el-card>
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit min-w-[300px] max-w-[90%] max-sm:w-[90%] py-3
        bg-gradient-to-tr from-white from-50% to-teal-100"
      header-class="font-bold text-[16px]"
      body-class="text-[14px]">
      <template #header>
        <div>Data Kelompok</div>
      </template>
      <form-comp ref="formGroup"
        class="[&_*]:rounded-[15px]"
        :key="'form-group-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        href="data/group/store"
        href-get="data/group/get"
        @saved="submittedData" 
        :pass-columns="['mu_group_anggota']"
        @error="errorData"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
        <template #default="{ form, errors, fields }">
          <template v-for="tipe in ['mentor', 'anggota']">
            <el-form-item
              class="col-span-6"
              :label="`Nama ${ucFirst(tipe)}`"
              :prop="`mu_group_anggota.${tipe}`">
              <floating-select
                class="w-full"
                :data-input="form['mu_group_anggota']?.filter?.(d => d?.type == tipe)?.map(data => data.id_anggota)"
                :options="fields['mu_group_anggota']?.fields?.id_anggota?.options"
                :placeholder="`Pilih ${ucFirst(tipe)}`"
                :clearable="true"
                :filterable="true"
                :multiple="true"
                @change="(ids) => {
                  console.log('ids', ids)
                  console.log('form', form['mu_group_anggota'])
                  if (isEmpty(form['mu_group_anggota'])) {
                    form['mu_group_anggota'] = []
                  }
                  form['mu_group_anggota'] = form['mu_group_anggota']?.filter?.(i => i?.type == undefined || i.type != tipe)                
                  if (Array.isArray(ids) == false) {
                    return
                  }
                  ids.forEach((id) => {
                    let index = form['mu_group_anggota'].findIndex(i => i.id_anggota == id)
                    console.log(id, index)
                    if (index >= 0) {
                      form['mu_group_anggota'][index].type = tipe
                    } else {
                      form['mu_group_anggota'].push({
                        id_anggota: id,
                        type: tipe,
                      })
                    }
                  })
                  // console.log(form['mu_group_anggota'])
                }"/>
              <div class="max-h-[150px] w-full overflow-y-auto">
                <table :id="'list-'+tipe" class="text-[15px] italic m-0 leading-[1.5] mt-2">
                  <tbody>
                    <template v-for="(i, key) in 
                    (form['mu_group_anggota'] ?? [])?.map((d, index) => {
                      return {...d, ...{index:index}}
                    })
                    .filter(i => i?.type == tipe)">
                      <tr>
                        <td width="20px" class="align-middle">
                          <icons icon="mdi:delete" class="text-red-600 cursor-pointer"
                           @click="() => {
                            // console.log(i.index)
                            form['mu_group_anggota'].splice(i.index,1)
                            // console.log(form['mu_group_anggota'])
                           }" />
                        </td>
                        <td width="10px" nowrap class="align-middle">
                          {{ key + 1 }}. 
                        </td>
                        <td  class="align-middle">
                          {{ runFunction({
                            data:i?.id_anggota, 
                            options:fields['mu_group_anggota']?.fields?.id_anggota?.options
                          }) }}
                        </td>
                      </tr> 
                      <tr v-if="errors['mu_group_anggota']?.[key]?.id_anggota"
                          class="text-red-500 text-[12px]">
                        <td></td>
                        <td></td>
                        <td>
                          {{ errors['mu_group_anggota']?.[key]?.id_anggota }}
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </table>
              </div>
            </el-form-item>
          </template>
        </template>
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
import ListData from '@/pages/components/ListData.vue';
import { indexOf } from 'lodash';
import { mapState } from 'pinia';
import UploadDialog from '@/components/UploadDialog.vue'

export default {
  name:'group-admin',
  components:{
    UploadDialog,
    ListData,
  },
  data: () => {
    return {
      show:true,
      showAdd: false,
      showUpload: true,
      formKey:0,
      fields:{},
      datas:{},
      dataId:-1,
      params:{},
    }
  },
  computed:{
    ...mapState(useAuthStore,{
      user: 'loggedUser',
    })
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        
        await this.$http.get('/kolom/preparation?table=mu_group&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.id_unit.default = this.idAnggota
            if (this.user.role == 'admin')
              this.fields.id_unit.readonly = true
            // console.log(this.fields)
            this.formKey++
            this.loading = false
          });
      },
    submittedData(){
      this.saving = false
      this.showAdd = false
      setTimeout(this.$refs.listGroup?.getData?.(), 1000)
    },
    errorData(){
      console.log('error')
      this.saving = false
      setTimeout(() => {
        this.scrollElement('#list-mentor','.text-red-500',0.5, 'top')
        this.scrollElement('#list-anggota','.text-red-500',0.5, 'top')
      },1000)
    }
  },
  created: function() {
    this.getInitial()
    if (this.user.role == 'admin')
      this.params = {where:{id_unit:this.user.id_unit}}
    else if (this.user.role == 'admin-bidang')
      this.params = {where:{bidang:this.user.bidang}}
  },
  mounted(){
  }
}
</script>