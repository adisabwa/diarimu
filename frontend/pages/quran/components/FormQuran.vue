<template>
  <div id="form-quran">
    <el-card
      v-loading="loading"
      class="bg-white/[0.9] rounded-[10px]
      mb-3 p-0"
      body-class="py-3 px-5 text-[14px]"
      header-class="py-3 font-bold text-[16px]">
      <template #header v-if="$slots.header">
        <slot name="header" />
      </template>
      <template v-if="!showCreate">
        <div v-if="success"
          class="text-center text-green-500 
            mb-5
            flex items-center justify-center">
          <icons icon="mdi:check-circle" />
          <span>Anda berhasil menyimpan data baru</span>
        </div>
        <el-button size="large" type="primary"
          class="rounded-full w-full font-bold text-[13px]
          py-2"
          @click="showCreate = true">
          <icons icon="mdi:plus"/>
          Tambah Catatan
        </el-button>
      </template>
      <template v-else>
        <form-comp ref="formQuran"
          class="[&_.el-form-item\_\_label]:mb-1 mb-2"
          :key="'form-quran-'+formKey"
          :fields="fields" 
          v-model:id="dataId"
          v-model:form-value="formValue" 
          :href="href"
          :href-get="hrefGet"
          :show-columns="showColumns"
          @saved="submittedData" 
          @changed-value="changedValue"
          @error="saving=false"
          size="large"
          :show-submit="false"
          label-position="top"
          form-item-class="mb-2"
          input-class=""
          :show-required-text="false">
        </form-comp>  
        <el-button size="large" type="success"
          class="rounded-full w-full font-bold text-[13px]
          py-2"
          :loading="saving" :disable="saving"
          @click="() => {
            $refs.formQuran.submitForm();
            saving=false
          }">
          Simpan Data
        </el-button>
      </template>
    </el-card>
  </div>
</template>

<script>

export default {
  name: "form-quran",
  props:{
    idAnggota:{type:[String, Number],default:'-1'},
    href:{type:String,default:''},
    hrefGet:{type:String,default:''},
    table:{type:String,default:''},
  },
  components: {
  },
  data: function() {
    return {
      loading: false,
      formKey:1,
      dataId:-1,
      fields:{
        tanggal:'',
        surat_selesai:'',
        ayat_selesai:'',
      },
      formValue:{},
      showCreate:true,
      success:false,
      saving:false,
    };
  },
  watch: {
   
  },  
  computed: {
    showColumns(){
      let show = ['tanggal','jenis_input']
      let input = this.formValue.jenis_input
      if (input == 'ayat') {
        show = [...show,...['surat_mulai-ayat_mulai','surat_selesai-ayat_selesai']]
      } else if (input == 'halaman') {
        show = [...show,...['halaman_mulai','halaman_selesai']]
      } else {
        show = [...show,...['juz_mulai','juz_selesai']]
      }
      return show
    }
  },
  methods: {
    getInitial: async function() {
      await this.$http.get('/kolom/preparation?table='+ this.table + '&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          this.dataId = -1
          this.fields = this.fillAndAddObjectValue(this.fields, res)
          console.log(res, this.fields)
          this.fields.tanggal.default = this.dateNow()
          this.fields.id_anggota.default = this.idAnggota
          this.fields.juz_mulai.span = 3
          this.fields.juz_selesai.span = 3
          this.formKey++
          this.loading = false
        }).
        catch((err) => {
          console.log(err)
        })
    },
    changedValue({ field, parent, value, option}){
      // console.log(field, parent, value)
      if (field == 'surat_mulai-ayat_mulai') {
      // console.log(field)
        let changedField = 'surat_selesai-ayat_selesai'
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:parent, 
          dest:'parent'
        })
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:value
        })
        this.$refs.formQuran.changeData({
          field:'juz_mulai', 
          value:value,
          func:this.searchFromAyat,
        })
        this.$refs.formQuran.changeData({
          field:'halaman_mulai', 
          value:value,
          func:this.searchFromAyat,
        })

      } else  if (field == 'surat_selesai-ayat_selesai') {
      // console.log(field)
        this.$refs.formQuran.changeData({
          field:'juz_selesai', 
          value:value,
          func:this.searchFromAyat,
        })
        this.$refs.formQuran.changeData({
          field:'halaman_selesai', 
          value:value,
          func:this.searchFromAyat,
        })

      } else if (field == 'juz_mulai') {
        this.$refs.formQuran.changeData({
          field:'juz_selesai', 
          value:value
        })
        this.$refs.formQuran.changedValue('juz_selesai')
        this.$refs.formQuran.changeData({
          field:'halaman_mulai', 
          value:value,
          func:this.searchFromAyat,
        })

        let changedField = 'surat_mulai-ayat_mulai'
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_mulai, 
          dest:'parent'
        })
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_mulai+'-'+option?.ayat_mulai
        })
        
      } else if (field == 'juz_selesai') {
        this.$refs.formQuran.changeData({
          field:'halaman_selesai', 
          value:value,
          func:this.searchFromAyat,
        })
        let changedField = 'surat_selesai-ayat_selesai'
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_selesai, 
          dest:'parent'
        })
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_selesai+'-'+option?.ayat_selesai
        })
      } else if (field == 'halaman_mulai') {
        this.$refs.formQuran.changeData({
          field:'halaman_selesai', 
          value:value
        })
        this.$refs.formQuran.changedValue('halaman_selesai')

        this.$refs.formQuran.changeData({
          field:'juz_mulai', 
          value:value,
          func:this.searchFromAyat,
        })
        let changedField = 'surat_mulai-ayat_mulai'
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_mulai, 
          dest:'parent'
        })
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_mulai+'-'+option?.ayat_mulai
        })
        
      } else if (field == 'halaman_selesai') {
        
        this.$refs.formQuran.changeData({
          field:'halaman_mulai', 
          value:value,
          func:this.searchFromAyat,
        })

        let changedField = 'surat_selesai-ayat_selesai'
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_selesai, 
          dest:'parent'
        })
        this.$refs.formQuran.changeData({
          field:changedField, 
          value:option?.surat_selesai+'-'+option?.ayat_selesai
        })
      }
    },
    searchFromAyat({field, fieldData, value}){
      let values = value?.split('-')
      let surat = values?.[0]
      let ayat = values?.[1]
      let number = parseInt(surat) * 1000 + parseInt(ayat)
      let opt = fieldData?.options ?? []
      let data = opt.find(d => {
        let mulai = parseInt(d.surat_mulai) * 1000 + parseInt(d.ayat_mulai)
        let selesai = parseInt(d.surat_selesai) * 1000 + parseInt(d.ayat_selesai)
        return number >= mulai && number <= selesai
      })
      return data?.value
    },
    submittedData(){
      this.saving = false;
      this.showCreate = false
      this.success = true
      this.getInitial();
      setTimeout(() => {
        this.$emit('saved')
      }, 400);
      // this.formKey++
    },
  },
  created: function() {
    this.getInitial()
    // console.log(this.$router);
  },
}
</script>
