<template>
    <div id="register" class="max-w-[1100px] mx-6 md:mx-auto bg-white bg-opacity-[0.9]
      border-solid border border-gray-300">
      <div class="h-full md:px-10">
        <div class="flex flex-col items-center align-middle
           pt-[90px] px-5 pb-20">
          <div class="w-full
            text-center">
            <h2 class="mt-0 mb-0 text-center text-2xl
              font-[600] font-montserrat ">Buat Akun Baru</h2>
            <div class="mt-0 mb-0 text-center text-[12px]
            font-montserrat ">Silahkan Isi Data Diri Terlebih Dahulu</div>
            <el-divider class="my-4 mx-0"/>
            <div class="flex flex-col mt-6">
              <form-comp ref="formRegistrasi"
                class="[&_*]:text-center [&_*]:rounded-full"
                :key="'form-registrasi-'+formKey"
                :fields="fields" 
                :id="dataId"
                href="data/anggota/store"
                href-get="data/anggota/get"
                @saved="submitted"  
                @error="saving=false"
                size="large"
                :show-submit="false"
                :show-label="false"
                label-position="top"
                :show-required-text="false"
              ></form-comp>  
              <el-divider class="m-0 mb-5 "/>
              <form-comp ref="formAkun"
                class="[&_*]:text-center [&_*]:rounded-full"
                :key="'form-akun-'+formKey"
                :fields="fieldsAkun" 
                :id="dataId"
                href="pengguna/store"
                @saved="submitted"  
                @error="saving=false"
                size="large"
                :show-submit="false"
                :show-label="false"
                label-position="top"
                :show-required-text="false"
              ></form-comp> 
              <el-button 
                type="primary" 
                size="large" 
                @click="$refs.formRegistrasi.submitForm();
                $refs.formAkun.submitForm();
                saving=true"
                :loading="saving" 
                class="mt-2 w-full bg-teal-700 font-bold
                  rounded-full">Buat Akun</el-button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
  
<script>
  import Form from '@/components/Form.vue'
  
export default {
  name: 'register',
  components:{
    'form-comp' : Form,
  },
  data() {
    return {
      saving: false,
      formKey:1,
      fields:{},
      fieldsAkun:{
        id_anggota:{
          nama_kolom:'id_anggota',from_user:'0'
        },
        role:{
          nama_kolom:'role',from_user:'0',default:'user',
        },
        password:{
          nama_kolom:'password',input:'password',label:'Password Baru'
        },
        passwordconf:{
          nama_kolom:'passwordconf',input:'password',label:'Konfirmasi Password'
        }
      },
      dataId:'',
    };
  },
  methods: {
    getInitial: async function() {
      this.saving = true;
      await this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          this.fields = res
          this.formKey++
          this.saving = false
        });
    },
  },
  created() {
    this.getInitial();
  }
};
</script>
  