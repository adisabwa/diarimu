<template>
	<div>
		<img :src="`${$baseUrl}/assets/images/dashboard.png`"
			class="absolute w-screen left-0 top-[-150px] z-[0]"/>
		<div id="management" class="flex flex-col justify-center max-w-[1100px] mx-1 md:mx-auto">
			<div class="w-full h-[70px] px-2 mt-2 z-[1]
				text-white leading-[1.3]">
				Assalamu'aialkum,<br/>
				<span class="text-xl font-semibold">{{ user.nama }}</span>
			</div>
			<div class="bg-white rounded-xl shadow-md shadow-emerald-700/[0.2]
				overflow-hidden
				mb-3 pt-0 
				flex flex-col items-center
				h-[180px] w-full">
				<div class="font-montserrat px-10 py-4 pt-10 *:w-fit
					rounded-xl
					bg-emerald-100/[0.3]">Informasi</div>
			</div>
			<div class="relative h-auto w-full
				bg-white/[0.8] shadow-md
					pt-7 pb-8 mb-20">
					<div class="px-6 pb-3
					font-montserrat font-bold text-xl text-emerald-900">Catatan Ibadah</div>
					<el-divider class="mt-0 mb-4 mx-4"/>
					<div class="grid grid-cols-[repeat(auto-fit,_65px)] gap-[25px] gap-y-[15px]
						justify-center
						px-1 md:max-w-[80%] mx-auto">
						<template v-for="(menu, ind) in topMenu">
							<div class="grid-item h-[110px] cursor-pointer"
								@click="$router.push({name:menu.route})">
								<div class="animate pointer duration-500 group/app
									hover:scale-90"	>
									<el-badge :value="menu.before" :offset="['-5','10']"
										:show-zero="false">
										<div :class="`${menu.color} ${menu.textColor} ${menu.shadowColor}
											shadow-md
											h-[65px] w-[65px] rounded-full
											relative overflow-hidden
											flex items-center justify-center align-middle`">
											<img :src="menu.image" height="45px" width="45px"
												class="rounded-full"/>
											<div class="absolute w-[90px] h-[200%] rotate-[-25deg] top-[-50%]
												bg-gradient-to-r from-transparent via-white/[0.2] to-transparent
												animate-[fly-in-absolute_4s_infinite_ease-in-out] [--from-left:-70%] [--left:350%] "
												:style="{animationDelay: ind + 's !important'}"/>
										</div>
									</el-badge>
									<div class="text-center mt-2 text-[15px] text-emerald-800 w-fit
										absolute -translate-x-1/2 left-1/2
										leading-[1.1]">{{ menu.label }}</div>
								</div>
							</div>
						</template>
						
					</div>
			</div>
		</div>
		<!-- <div class="translate-y-[-40px]">
			<div id="bottom" class="bg-cover bg-top bg-repeat
				h-[60px] min-w-[600px] w-full
				filter hue-rotate-[349deg] brightness-[.9]"
				:style="`background-image:url('${$baseUrl}assets/images/bottom.png')`"></div>
			
			<div class="absolute bg-[#f3b76f] h-[100px] w-full" />
		</div> -->
	</div>
</template>

<script>
import { mapGetters } from 'vuex';
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
  computed: {
    ...mapGetters({
      user: 'loggedUser',
    }),
  },
	methods:{
		getBefore(){
			let keys = Object.keys(topMenu)
			for (let i = 0; i < keys.length; i++) {
				const key = keys[i];
				const d = this.topMenu[key]
				this.$http.get(d.url+'/get_before')
					.then(res => {
						d.before = res?.data
					})
			}
		}
	},
	mounted(){
		this.getBefore()
	}
	
}
</script>