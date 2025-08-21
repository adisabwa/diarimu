<template>
	<div class="flex">
		<div id="management" class="max-w-[1100px] mx-6 sm:mx-auto bg-white bg-opacity-[0.9] 
			border-solid border border-gray-300">
			<div class="flex flex-col items-center align-middle
			h-fit pt-32 pb-6 flex-1 px-5
			text-center text-[20px]">
				<div class="font-montserrat
					text-[40px] sm:text-7xl font-[500] text-emerald-800 leading-[1.3]">
					ASHOI-MU
				</div>
				<div class="font-montserrat
					text-[16px] sm:text-2xl font-[500] text-emerald-800 leading-[1.3]
					">
					Sistem Pencacatan Ibadah Muhammadiyah Kendal
				</div>
				<img id="logo" :src="$baseUrl + 'assets/images/vector.png'" height="230px" 
					class="mt-6
						"/>
			</div>
			<div class="w-auto [&_*]:text-[16px] py-3 px-7 pb-12">
				<el-button class="bg-emerald-900/[0.5] w-full
					shadow-[inset_3px_3px_rgba(#fff,#fff,#fff,0.3)]
					py-5
					active:bg-emerald-800/[0.5]
					text-white text-lg 
					font-bold" 
					@click="$router.push({name:'login'})">Masuk Aplikasi</el-button>
				<div class="w-full text-center mt-2">
					Belum punya akun?
					<a @click="$router.push({name:'register'})" 
						class="p-0 w-auto pointer
						text-emerald-800 font-bold
						active:text-emerald-600">Buat Akun Baru</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
  const authStore = useAuthStore()
</script>

<script>
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "default",
  components: {
  },
  data: function() {
    return {
		topMenu:topMenu,
    };
  },
  methods:{
	async getInitial() {
	}
  },
  created() { 
	const authStore = useAuthStore()
	if (authStore.loggedUser.role != '') {
		this.$router.replace({ name: 'dashboard' });
	}
  },
	
}
</script>