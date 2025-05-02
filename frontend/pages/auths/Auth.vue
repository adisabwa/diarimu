<template>
  <div id="auth" class="max-w-[1100px] mx-6 md:mx-auto bg-white bg-opacity-[0.9]
    border-solid border border-gray-300">
    <div class="h-full md:px-10">
      <div class="flex flex-col items-center align-middle">
        <div class="p-5 pt-36 
          text-center">
          <img id="logo" :src="$baseUrl + 'assets/images/vector.png'" height="250px" 
            class="my-4 mt-8
              "/>
          <!-- Error message -->
          <h2 class="mt-0 mb-0 text-center text-2xl
            font-[600] font-montserrat ">Masuk Sekarang</h2>
          <div class="mt-0 mb-0 text-center text-[12px]
          font-montserrat ">Masuk untuk memulai menggunakan aplikasi</div>
          <template v-if="errorLogin" >
            <el-divider class="m-0 mt-3"/>
            <p class="text-danger error-message my-1" v-html="errorMessage"></p>
          </template>
          <template v-else>
            <el-divider class="m-3 mx-0"/>
          </template>
          <div class="flex flex-col gap-y-3 mt-2">
            <el-input 
              size="large" 
              class="w-full"
              v-model="form.no_hp" 
              placeholder="Nomor HP / Email"
              @keypress.enter.native="onEnter">
              <template #prefix>
                <icons icon="line-md:phone" />
              </template>
            </el-input>
            <el-input 
              size="large" 
              v-model="form.password" 
              placeholder="Kata Sandi" 
              type="password" 
              show-password
              class="w-full in-password"
              @keypress.enter.native="onEnter">
              <template #prefix>
                <icons icon="material-symbols:lock-outline" />
              </template>
            </el-input>    
            <el-checkbox v-model="saveAuth"
              class="h-fit p-1 hidden md:flex">Ingat Saya</el-checkbox>     
            <el-button 
              type="primary" 
              size="large" 
              @click="doLogin()"
              :loading="loading" 
              class="mt-2 w-full bg-teal-700">Masuk</el-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'auth',
  data() {
    return {
      loading: false,
      errorLogin: false,
      errorMessage: 'Username atau password salah.',
      nextUrl: null,
      prodiOptions: [],
      form: {
        no_hp: '',
        password: '',
      },
      saveAuth:true,
    };
  },
  methods: {
    onEnter(event) {
      console.log(event, this.form);
      if (!this.isEmpty(this.form.no_hp) && !this.isEmpty(this.form.password)) {
        this.doLogin();
      }
    },
    doLogin() {
      this.loading = true;
      this.$store.dispatch('login', this.form, this.saveAuth)
        .then(res => {
          this.loading = false;
          this.redirect();
        }).catch(err => {
          this.loading = false;
          const res = err.response;
          console.log(err)
          if (res.status == 401) {
            this.errorMessage = res.data.message;
            this.errorLogin = true;
          } else {
            this.$notify.error({
              title: 'Gagal',
              message: 'Terjadi kesalahan pada server',
              position: 'bottom-right'
            });
          }
        });
    },
    redirect(){
      this.$store.dispatch('checkUser');
      const loggedUser = this.$store.getters.loggedUser;
      if (loggedUser.role != '') {
        if (this.nextUrl) {
          this.$router.replace({ path: this.nextUrl });
        } else {
          this.$router.replace({ name: 'dashboard' });
        }
      }
    }
  },
  created() {
    // this.initial();
    this.nextUrl = this.$route.query.nextUrl;
    this.redirect();
  }
};
</script>
