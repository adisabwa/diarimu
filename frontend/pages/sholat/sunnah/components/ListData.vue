<template>
	
	<div 
		v-infinite-scroll="loadingData"
		class="min-h-[200px] max-h-[50vh] overflow-auto px-6 "
		:infinite-scroll-disabled="noMoreScrolling"
		infinite-scroll-delay="1000"
		infinite-scroll-distance="10">
		<template v-for="(s, key) in datas">
			<div v-if="s.tanggal.slice(0, 7) != datas[key - 1]?.tanggal?.slice(0,7)" 
				class="bg-slate-100
				text-slate-400
				p-2 mb-3">{{ monthIndo(s.tanggal) }}</div>
			<div class="h-fit px-5 py-3 mb-3
				rounded-[15px]
				bg-rose-50/[0.5] border border-solid border-rose-400
				text-rose-900
				flex items-center justify-between">
				<div class="leading-[1.5]">
					<div class="font-semibold text-[19px] opacity-70">
					{{ dateDayIndo(s.tanggal)}}</div>
					<div class="text-[15px] mt-1"> 
						<div class="flex items-center"
							@click="s.show_detail = !s.show_detail">
							<icons v-if="s.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
							<icons v-else icon="fe:arrow-up" class="text-[12px]"/>
							Sholat Sunnah {{ s.total_rakaat }} Raka'at
						</div>
						<ol v-show="s.show_detail"
							class="pl-[30px] italic mt-0 mb-1">
							<li v-for="(j) in s.daftar_sholat.split('/')"
								class="pl-1">
								{{ getLabelSholat(j) }}
							</li>
						</ol>
					</div>
				</div>
			</div>
		</template>
		<p class="my-0 text-center text-[13px]">
			<template>
				<template v-if="loadingScroll">Menggambil Data...</template>
				<template v-if="noMoreScrolling && showLabel">Data Selesai</template>
			</template>
		</p>
	</div>
</template>

<script>

export default {
  name: "data",
	emits:['editData'],
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    }
  },
  components: {
  },
  data: function() {
    return {
			datas:[],
			loadingScroll:true,
			noMoreScrolling:false,
			showLabel:true,
			limit:5,
			offset:null,
		}
	},
	computed:{
		disabledScroll(){
			return this.noMoreScrolling || this.loadingScroll
		}

	},
	methods:{
      async getData(reset = true){
        this.loading = true
				if (reset) {
					this.offset = 0
					this.limit = 5
				}
        await this.$http.get('sholat/sunnah', {
            params: {
							where:{
								id_anggota: this.idAnggota,
							},
							order:['tanggal desc'],
              limit:this.limit,
              offset:this.offset,
							grouping:['tanggal']
            }
          }).then(result => {
            var res = result.data;
            this.datas = reset ? res : [...this.datas, ...res]
            this.loading = false
            this.loadingScroll = false
            if (this.isEmpty(res)) {
              this.noMoreScrolling = true
            } else {
              this.offset += 5
            }
						console.log(this.datas)
          });
      },

      async loadingData(){
				let vm = this
        vm.loadingScroll = true
				vm.showLabel = true 
        await vm.getData(false)
				setTimeout(() => {
					vm.showLabel = false
				},2000)
      },

			getLabelSholat(sholat){
				// console.log(sholat)
				let data = sholat.split('-')
				return `Sholat ${data[0]} - ${data[1]} Raka'at`
			}
	},
	mounted(){
	}
}
</script>