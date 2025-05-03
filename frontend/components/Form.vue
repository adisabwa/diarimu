<template>
	<div class="">
    <el-form :label-width="labelWidth" :label-position="labelPosition" v-loading="saving">
      <template  v-for="(field, ind) in fieldsData">
        <el-form-item class="mb-3"
          v-if="showColumns.length > 0 ? showColumns.includes(field.nama_kolom) : !passColumns.includes(field.nama_kolom)"
          :error="errors[field.nama_kolom]">
          <template #label v-if="showLabel">
            <span :class="`${field.required == '1' ? 'required' : ''} leading-[1.5] mt-2`"> {{ field.label }} </span>
          </template>
          <div class="flex space-x-3 w-full">
            <el-select v-if="showOriginal" v-model="original[field.nama_kolom]" class="w-[130px] grow-0 shrink-0" size="large">
              <el-option :value="true" label="Nilai Asli"></el-option>
              <el-option :value="false" label="Berubah"></el-option>
            </el-select>
            <template v-if="field.input == 'input' || isEmpty(field.input)">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                class="w-full" @change="searchData(ind)" @input="form[field.nama_kolom] = runFunction(field.function_input, form[field.nama_kolom])"
                :size="size">
                <template #prepend v-if="!isEmpty(field.prepend)"> {{ field.prepend }}</template>
                <template #apppend v-if="!isEmpty(field.apppend)"> {{ field.append }}</template>  
              </el-input>
            </template> 
            <template v-else-if="field.input == 'password'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                class="w-full in-password" type="password" show-password
                :size="size">
                <template #prefix>
                  <icons icon="material-symbols:lock-outline" />
                </template>
              </el-input>
            </template>
            <template v-else-if="field.input == 'number'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                class="w-full" type="number"
                :size="size"/>
            </template>
            <template v-else-if="field.input == 'textarea'">
              <el-input v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                class="w-full" type="textarea" row="3"
                :size="size"/>
            </template>
            <template v-else-if="field.input == 'select' || field.input == 'select-multiple'">
              <el-select v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label}`" class="w-full"
                filterable clearable :multiple="field.input == 'select-multiple'"
                popper-class="h-auto min-w-[350px] max-w-[800px]
                  [&_li]:h-auto
                  [&_li_span]:whitespace-normal
                  [&_li_span]:block [&_li_span]:leading-[1.6] [&_li_span]:py-2"
                :size="size">
                <template 
                  v-for="item in field.options"
                  :key="item">
                  <el-option
                    :value="item.value"
                    :label="item.label">
                  </el-option>
                </template>
                <template v-if="field.allow_add" #footer>
                  <el-button v-if="!field.isAdding" type="primary" text size="small" @click="field.isAdding = !field.isAdding">
                    Tambah Pilihan
                  </el-button>
                  <el-dialog v-model="field.isAdding"
                    :title="'Tambah ' + field.label"
                    class="p-7"
                    :close-on-click-modal="false"
                    width="500px">
                    <Form 
                      :fields="field.addFields" 
                      ref="formAdd"
                      :key="'from'+field.nama_kolom"
                      size="default"
                      :show-label="false"
                      :href="field.addHref"
                      :href-get="field.addHrefGet"
                      @saved="field.isAdding = false; resetOptions(ind, field.addReset);"
                      :show-required-text="false"
                      :text-submit="'Konfirmasi'"
                     />
                    <el-button size="default" @click="field.isAdding = !field.isAdding"
                     class="float-left translate-y-[-25px]">Batal</el-button>
                  </el-dialog>
                </template>
              </el-select>
            </template>
            <template v-else-if="field.input == 'select-double'">
              <el-select v-model="field.parentSelect" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label1}`" class="w-full"
                filterable clearable 
                popper-class="h-auto max-w-[600px]
                  [&_li]:h-auto [&_li]:py-1
                  [&_li_span]:whitespace-normal [&_li_span]:block [&_li_span]:leading-[1.6] [&_li_span]:py-2"
                @change="form[field.nama_kolom] = ''"
                :size="size">
                <template 
                  v-for="item in field.options"
                  :key="item">
                  <el-option
                    :value="item.value"
                    :label="item.label">
                  </el-option>
                </template>
              </el-select>
              <el-select v-model="form[field.nama_kolom]" :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Pilih ${field.label2}`" class="w-full"
                filterable clearable
                popper-class="h-auto max-w-[600px]
                  [&_li]:h-auto [&_li]:py-1
                  [&_li_span]:whitespace-normal [&_li_span]:block [&_li_span]:leading-[1.6] "
                :size="size">
                <template 
                  v-for="subItem in field.options[field.parentSelect] === undefined ? [] : field.options[field.parentSelect].options"
                  :key="subItem">
                  <el-option
                    :value="subItem.value"
                    :label="subItem.label">
                  </el-option>
                </template>
                <template v-if="field.allow_add" #footer>
                  <el-button v-if="!field.isAdding" type="primary" text size="small" @click="field.isAdding = !field.isAdding">
                    Tambah Pilihan
                  </el-button>
                </template>
              </el-select>
              <el-dialog v-model="field.isAdding"
                :title="'Tambah ' + field.label"
                class="p-7"
                :close-on-click-modal="false"
                width="500px">
                <Form 
                  :fields="field.addFields" 
                  ref="formAdd"
                  :key="'from'+field.nama_kolom"
                  size="default"
                  :show-label="false"
                  :href="field.addHref"
                  :href-get="field.addHrefGet"
                  @saved="field.isAdding = false; resetOptions(ind, field.addReset);"
                  :show-required-text="false"
                  :text-submit="'Konfirmasi'"
                  />
                <el-button size="default" @click="field.isAdding = !field.isAdding"
                  class="float-left translate-y-[-25px]">Batal</el-button>
              </el-dialog>
            </template>
            <template v-else-if="field.input=='radio'">
              <el-radio-group v-model="form[field.nama_kolom]">
                <template 
                  v-for="item in field.options"
                  :key="item">
                  <el-radio-button :value="item.value" :size="size">{{ item.label }}</el-radio-button>
                </template>
              </el-radio-group>
            </template>
            <template v-else-if="field.input=='date'">
              <el-date-picker
                class="w-full"
                v-model="form[field.nama_kolom]"
                value-format="YYYY-MM-DD"
                format="DD MMMM YYYY"
                :placeholder="!isEmpty(field.placeholder) ? field.placeholder : `Masukkan ${field.label}`"
                @change="changeData"
                @blur="changeData"
                :size="size"
              />
            </template>
            <template v-else-if="field.input == 'file'">
              <div class="w-full">
                <div v-if="!isEmpty(links[field.nama_kolom])">
                  <el-checkbox v-model="field.change">Upload Ulang</el-checkbox>
                </div>
                <file v-if="!field.change" :href="`${links[field.nama_kolom]}`" :title="`Unduh ${field.label}`"/>
                <el-upload 
                  v-else
                  class="doc-upload-wrap max-w-[400px]" 
                  :ref="field.nama_kolom" 
                  :auto-upload="false"
                  :multiple="false"
                  :limit="1"
                  :on-change="getFileRaw(field.nama_kolom)"
                  accept="application/pdf,image/jpeg,image/jpg,image/png"
                  action=""
                  drag>
                  <icons icon="material-symbols:cloud-upload" class="text-5xl text-blue-600"/>
                  <div class="mx-auto w-[80%] el-upload__text leading-[1.5] text-[13px]">Tarik dokumen kesini atau <em>klik untuk mengunggah</em></div>
                  <div class="mx-auto w-[80%] el-upload__tip leading-[1.5] text-[11px] my-1" slot="tip">
                    Dokumen JPG/PNG/PDF, maks. 5 MB.
                  </div>
                </el-upload>
              </div>
            </template>
          </div>
        </el-form-item>
      </template>
      <slot :errors="errors" :form="form"></slot>
      <div class="text-md mt-10" v-if="showRequiredText">
        <span class="text-red-500">)*</span> isian harus diisi
      </div>
    </el-form>
    <div class="text-right" v-if="showSubmit">
      <el-button type="success" :size="size" @click="submitForm"
        :disabled="saving">
        <icons v-if="saving" icon="eos-icons:loading"/>
        {{ textSubmit }}
      </el-button>
    </div>
	</div>
</template>

<script>

export default {
  name: 'loading',
  components: {
  },
  props: {
    id: {
      type:[Number, String, Array],
      default: ''
    },
    fields: {
      type:[Array, Object],
      default:[],
    },
    size: {
      type: String,
      default: 'large',
    },
    showLabel: {
      type: Boolean,
      default: true,
    },
    labelWidth: {
      type: String,
      default: '100px',
    },
    labelPosition: {
      type: String,
      default: 'left',
    },
    textSubmit:{
      type:String,
      default: 'Submit',
    },
    showSubmit:{
      type:Boolean,
      default: true,
    },
    href:{
      type:String,
      default: '',
    },
    hrefGet:{
      type:String,
      default: '',
    },
    errorSubmitText:{
      value:String,
      default:'Tidak dapat menyimpan'
    },
    showRequiredText:{
      type:Boolean,
      default: true,
    },
    showNotification:{
      type:Boolean,
      default: true,
    },
    passColumns:{
      type:Array,
      default:[],
    },
    showColumns:{
      type:Array,
      default:[],
    },
  },
  emits:['update:id','saved','error','changeId','formValue'],
  data: function() {
    return {
      saving: false,
      showOriginal: true,
      form:{},
      links:{},
      errors:{},
      original:{},
      fieldsData:{},
      dataId:null,
    };
  },
  watch: {
    fieldsData: function(val, oldVal) {
      
    },
    id: {
      immediate: true,
      async handler(val) {
        this.dataId = val;
        this.showOriginal = val instanceof Array
      }
    },
    dataId: function(val, oldVal) {
      this.$emit('update:id', val);
      if (val > 0) {
        this.getData({id:val})
      }
    },
    form: {
      handler(newVal, oldVal) {
        this.$emit('formValue', newVal)
      },
      deep: true, // Watch nested properties
    },
  },
  computed: {
    
  },
  methods: {
    changeData(field, val){
      this.form[field] = val
    },
    searchData(ind){
      let field = this.fieldsData[ind]
      let where = {}
      if (field.search == '1') {
        where[field.nama_kolom] = this.form[field.nama_kolom]
        this.getData(where, true)
      }   
    },
    getData(where, changeId){
      this.$http.get(this.hrefGet,
        {
          params:where
        }
      )
        .then(result => {
          this.saving = false;
          var psb = result.data;
          if (!this.isEmpty(psb)) {
            this.fillObjectValue(this.form, psb)
            this.fillObjectValue(this.links, psb)
            if (changeId) {
              this.dataId = psb.id
              this.$emit('changeId', this.dataId);
            }
            let fieldsData = Object.values(this.fields)
            fieldsData.forEach(d => {
              if (d.input == 'select-double') {
                // delete vm.form[d.nama_kolom]
                this.fields[d.nama_kolom].parentSelect = psb.parentSelect[d.nama_kolom]
              }
            })
          }
        })
        .catch(err => {
          console.log(err)
          this.saving = false;
          var res = err.response;
          var code = res.status;
          if (this.showNotification)
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right'
            });
        });
    },
    getFileRaw(refName) {
      return (file, fileList) => {
        const rawFile = file.raw; // Access the raw file
        const refInstance = this.$refs[refName]; // Get the ref instance

        this.form[refName] = rawFile
      };
    },
    resetOptions(ind, link){
      this.$http.get(link)
        .then(res => {
          let data = res.data
          this.fieldsData[ind].options = data
        })
    },
    submitForm(){
      this.saving = true;
      this.resetObjectValue(this.errors)
      let vm = this
      let form = this.form
      let backUpForm = JSON.parse(JSON.stringify(vm.form))
      Object.keys(vm.original).forEach(ind => {
        if (vm.original[ind]) 
          delete form[ind]
      });
      form.id = this.dataId
      console.log(form)
      var formData = window.jsonToFormData(form); 

      this.$http.post(this.href, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      } )
        .then(result => {
          this.saving = false;
          var psb = result.data;
          this.dataId = psb.id
          this.$emit('saved', psb);
        })
        .catch(err => {
          this.saving = false;
          console.log(err)
          var res = err.response;
          var code = res.status;
          this.$emit('error', false);
          
          if (code == '400') {
            // Populating error message
            console.log(res.data.messages, this.errors)
            this.fillObjectValue(this.errors, res.data.messages);
            if (this.showNotification)
              this.$notify.error({
                title: 'Gagal',
                message: 'Data belum benar',
                position: 'bottom-right'
              });
          } else {
            if (this.showNotification)
              this.$notify.error({
                title: 'Gagal',
                message: this.errorSubmitText,
                position: 'bottom-right'
              });
          }
          this.form = backUpForm;
        });
    },
    settingFields(){
      let vm = this
      let fieldsData = Object.values(this.fields)
      vm.form, vm.errors, vm.links, vm.original = {}
      fieldsData.forEach(d => {
        if (d.from_user == '1' || d.from_user == undefined) {
          vm.fieldsData[d.nama_kolom] = d
          vm.form[d.nama_kolom] = vm.coalesce([d.default,''])
          vm.errors[d.nama_kolom] = ''
          vm.original[d.nama_kolom] = false
          if (d.input == 'file') {
            // delete vm.form[d.nama_kolom]
            vm.links[d.nama_kolom] = ''
          }
        }
      })
      console.log(vm.form)
    }
  },
  mounted(){
    this.settingFields();
    this.getData({id:this.dataId});
  }
}
</script>

<style lang="scss">
.el-form-item__error {
  margin-top: 6px;
}
</style>