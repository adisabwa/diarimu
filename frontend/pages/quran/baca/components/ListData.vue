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
			<div class="h-fit px-4 py-3 mb-3
				relative
				rounded-[15px]
				bg-lime-50/[0.5] border border-solid border-lime-400
				text-lime-900
				flex items-center justify-between
				overflow-hidden
				cursor-grab"
				draggable="true"
				@click="removeClass('#quranBacaData'+key, 'translate-x-[120px]')"
				v-click-outside="() => addClass('#quranBacaData'+key, 'translate-x-[120px]')">
				<div class="leading-[1.3]">
					<div class="font-semibold text-[13px] opacity-70">
					{{ dateDayIndo(s.tanggal)}}</div>
					<div class="text-[16px]">Dari : 
						<span class="font-bold">
						{{ s.nama_surat_mulai }} ({{ s.surat_mulai }}) : {{ s.ayat_mulai }}
						</span>
					</div>
					<div class="text-[16px]">Sampai : 
						<span class="font-bold">
						{{ s.nama_surat_selesai }} ({{ s.surat_selesai }}) : {{ s.ayat_selesai }}
						</span>
					</div>
					<div class="font-semibold text-[13px] opacity-70">{{ s.keterangan }}</div>
				</div>
				<div :id="'quranBacaData'+key"
					class="absolute bg-lime-200/[0.4] right-0 translate-x-[120px]
					px-4
					animate
					h-full w-fit flex items-center" 
					v-if="$store.getters.loggedUser.role == 'user'">
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-lime-600/[0.5]
						bg-lime-200"
						@click="$emit('editData',{id:s.id})">
						<icons icon="mdi:edit" class="m-0 text-[20px] text-lime-500" />
					</el-button>
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-lime-600/[0.5]
						bg-red-200"
						@click="deleteData(s.id)">
						<icons icon="mdi:delete" class="m-0 text-[20px] text-red-500" />
					</el-button>
				</div>
			</div>
		</template>
		<p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
		<p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
	</div>
</template>

<script>

export default {
  name: "data",
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    }
  },
	emits:['editData'],
  components: {
  },
  data: function() {
    return {
			datas:[],
			loadingScroll:true,
			noMoreScrolling:false,
			limit:5,
			offset:null,
		}
	},
	computed:{
		
	},
	methods:{
      async getData(reset = true){
        this.loading = true
				if (reset) {
					this.offset = 0
					this.limit = 5
				}
        await this.$http.get('quran/baca', {
            params: {
							where:{
								id_anggota: this.idAnggota,
							},
							order:['tanggal desc','id desc'],
              limit:this.limit,
              offset:this.offset,
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
						// console.log(this.datas)
          });
      },
			deleteData(id){
				this.$store.dispatch('data/deleteData',{
					href:'quran/baca/delete',
					id:id,
				}).then(() => {
					this.getData()
				})
			},
      async loadingData(){
        this.loadingScroll = true
        await this.getData(false)
      },
	},
	mounted(){
	}
}
</script>