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
				p-2 mb-1">{{ monthIndo(s.tanggal) }}</div>
			<div class="h-fit px-5 py-3 mb-3
				rounded-[15px]
				bg-sky-50/[0.5] border border-solid border-sky-400
				text-sky-900
				flex items-center justify-between">
				<div class="leading-[1.5]">
					<div class="font-semibold text-[19px] opacity-70">
					{{ dateDayIndo(s.tanggal)}}</div>
					<div class="text-[15px] mt-1"> 
						<div class="flex items-center"
							@click="s.show_detail = !s.show_detail">
							<icons v-if="s.show_detail" icon="fe:arrow-down" class="text-[12px]"/>
							<icons v-else icon="fe:arrow-up" class="text-[12px]"/>
							Sholat Wajib {{ (
								(s.shubuh > 0 ? 2 : 0) + (s.dhuhur > 0 ? 4 : 0) + (s.asar > 0 ? 4 : 0) + (s.maghrib > 0 ? 3 : 0) + (s.isya > 0 ? 4 : 0) 
							) }} Raka'at
						</div>
						<ol v-show="s.show_detail"
							class="pl-[30px] italic mt-0 mb-1">
							<li v-for="ind in ['shubuh','dhuhur','asar','maghrib','isya']"
                class="pl-1">
								Sholat {{ ucFirst(ind) }} ( {{ getLabel(s[ind]) }} ) 
                <template v-if="s[ind] >= 25">
                  <star :id="'3star'+ind+'data'" :class="['scale-100', s[ind] == 100 ? '' : 'grayscale']" width="10px"/>
                  <star :id="'3star'+ind+'data'" :class="['scale-100', s[ind] >= 75 ? '' : 'grayscale']" width="10px"/>
                  <star :id="'3star'+ind+'data'" :class="['scale-100', s[ind] >= 50 ? '' : 'grayscale']" width="10px"/>
                </template>
							</li>
						</ol>
					</div>
				</div>
			</div>
		</template>
		<p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
		<p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
	</div>
</template>

<script setup>
  import { setStatusColor, options, getLabel } from '@/helpers/sholat.js'
</script>

<script>

import Star from '../../components/Star.vue'

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
    Star,
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
      async getData(reset = true){
        this.loading = true
				if (reset) {
					this.offset = 0
					this.limit = 5
				}
        await this.$http.get('sholat/wajib', {
            params: {
							where:{
								id_anggota: this.idAnggota,
							},
							order:['tanggal desc'],
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
						console.log(this.datas)
          });
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