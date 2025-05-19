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
        text-slate-400 text-[15px]
        p-2 py-1 mb-3">{{ monthIndo(s.tanggal) }}</div>
      <div class="h-fit px-5 py-3 mb-3
        rounded-[15px]
        bg-[var(--bg-color)] border border-solid border-[var(--border-color)]
        text-[var(--text-color)]
        flex items-center justify-between
        relative overflow-hidden"
				draggable="true"
				@click="(event) => editData(event, key)"
				v-click-outside="() => addClass('#listData'+key, 'translate-x-[120px]')">
        <div class="leading-[1.5] w-full">
          <div class="font-semibold text-[13px] opacity-70">
            <slot name="subtitle" :data="s" />
          </div>
          <div class="font-bold text-[17px]">
            <slot name="title" :data="s"/>
          </div>
          <div class="font-semibold text-[13px] opacity-70" ref="contentData">
            <slot name="content" :data="s" />
          </div>
          <div v-if="showName" class="font-normal italic text-[14px] 
            mt-1 opacity-70" ref="contentData">
            {{ s.nama }}
          </div>
        </div>
				<div :id="'listData'+key"
					class="absolute bg-[var(--bg-button-color)] right-0 translate-x-[120px]
					px-4
					animate
					h-full w-fit flex items-center" 
					v-if="['user','super-admin'].includes(user.role)">
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-[var(--border-color)]
						bg-[var(--button-color)]"
						@click="$emit('editData',{id:s.id, tanggal:s.tanggal})">
						<icons icon="mdi:edit" class="m-0 text-[20px] text-[var(--text-color)]" />
					</el-button>
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-[var(--border-color)]
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
  name: "list-data",
	emits:['editData'],
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    },
    href:{
      type:[String, Number],
      default:'',
    },
    groupBy:{type:Array, default:[]}
  },
  computed: {
    
  },
  data: function() {
    return {
			datas:[],
			loadingScroll:true,
			noMoreScrolling:false,
			limit:5,
			offset:null,
      user:this.$store.getters.loggedUser,
      showName:false,
		}
	},
	computed:{
		disabledScroll(){
			return this.noMoreScrolling || this.loadingScroll
		}

	},
	methods:{
    getData(reset = true){
      // console.log(this.href, this.idAnggota)
      this.loading = true
      if (reset) {
        this.offset = 0
        this.limit = 5
        this.noMoreScrolling = false
      }
      this.$http.get(this.href, {
          params: {
            in:{
              id_anggota: this.idAnggota.split(','),
            },
            order:['tanggal desc','nama','id desc'],
            limit:this.limit,
            offset:this.offset,
            grouping:this.groupBy,
          }
        }).then(result => {
          var res = result.data;
          this.datas = reset ? res : [...this.datas, ...res]
          // console.log(this.datas)
          this.loading = false
          this.showName = this.idAnggota.split(',').length > 1
          this.loadingScroll = false
          if (this.isEmpty(res)) {
            this.noMoreScrolling = true
          } else {
            this.offset += 5
          }
          // console.log(this.datas)
        });
    },
    loadingData(){
      this.loadingScroll = true
      this.getData(false)
    },
    editData(event, key){
      const parent = this.$refs.contentData[0];
      // console.log(parent, event.target)
      if (parent?.contains(event.target)) {
        return; // Ignore clicks on the excluded element
      }
      this.removeClass('#listData'+key, 'translate-x-[120px]')
    }
	},
	mounted(){
	}
}
</script>