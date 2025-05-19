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
        flex items-center justify-between">
        <div class="leading-[1.5]">
          <div class="font-semibold text-[13px] opacity-70">
            <slot name="subtitle" :data="s" />
          </div>
          <div class="font-bold text-[18px]">
            <slot name="title" :data="s"/>
          </div>
          <div class="font-semibold text-[13px] opacity-70">
            <slot name="content" :data="s" />
          </div>
        </div>
      </div>
		</template>
		<p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
		<p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
	</div>
</template>

<script setup>

</script>

<script>
import { mapGetters } from 'vuex';

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
    }
  },
  computed: {
    ...mapGetters({
      user: 'loggedUser',
      anggotas:'data/anggotas'
    }),
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
		disabledScroll(){
			return this.noMoreScrolling || this.loadingScroll
		}

	},
	methods:{
      getData(reset = true){
        console.log(this.href, this.idAnggota)
        this.loading = true
				if (reset) {
					this.offset = 0
					this.limit = 5
				}
        this.$http.get(this.href, {
            params: {
							in:{
								id_anggota: this.idAnggota.split(','),
							},
							order:['tanggal desc'],
              limit:this.limit,
              offset:this.offset,
            }
          }).then(result => {
            var res = result.data;
            this.datas = reset ? res : [...this.datas, ...res]
            console.log(this.datas)
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
      loadingData(){
        this.loadingScroll = true
        this.getData(false)
      },
	},
	mounted(){
	}
}
</script>