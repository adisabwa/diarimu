<template>
    <div id="iqab" class="py-16">
      <el-card class="bg-white/[0.8] relative">
				<div class="flex flex-col md:flex-row
          justify-start gap-2
          md:items-center align-middle px-3">
					<div class="w-[180px] shrink-0">Nama Santri</div>
					<el-select v-model="filter.santri" placeholder="Pilih Santri" class="w-full"
						filterable clearable size="large" @change="getData">
						<template 
							v-for="item in optionsSantri"
							:key="item">
							<el-option
								:value="item.id"
								:label="item.kelas + ' - ' + item.nama">
							</el-option>
						</template>
					</el-select>
				</div>
        <div v-show="!empty">
					<view-table ref="viewSantri"
						:fields="santriField" 
						:key="'from'+active"
						:keyword="inputKeyword"
            :label-position="labelPosition"
						class="mt-6 px-4
              flex flex-col md:flex-row
              items-center
              gap-7 [&>*]:flex-0" 
						label-width="180px"
						href-get="/data/santri/search"
						:search-columns="['id']"
            :pass-columns="['foto']"
						v-model:empty="empty"
						v-model:loading="loading">
            <template #outside-form="{ form }">
              <!-- <div class="h-full w-1/2 flex-1 bg-contain bg-center bg-no-repeat"
                :style="{ backgroundImage:`url('${form.foto}')` }"></div> -->
              <div 
                class="w-[180px] h-[240px] text-center shrink-0 order-first md:order-last
                  border-3 box-border border-solid border-teal-800 overflow-hidden">
                <img v-if="!isEmpty(form.foto)" :src="form.foto" class="h-full object-cover
                  " />
                <div v-else class="h-full
                  flex items-center">
                  <div class="m-auto">Belum ada Foto</div>
                </div>
              </div>
            </template>
          </view-table>
        </div>
        <el-collapse class="mt-8 px-4" v-model="active">
          <el-collapse-item  name="statistik" >
            <template #title>
              <div class="text-xl font-semibold text-center py-4">
                <span>Statistik Pelanggaran</span>
              </div>
            </template>
            <Line ref="line" v-if="loaded" :data="statistic" :options="options" class="w-full" />
          </el-collapse-item>
          <el-collapse-item  name="data" >
            <template #title>
              <div class="text-xl font-semibold text-center py-4">
                <span>Detail Pelanggaran</span>
              </div>
            </template>
            <table-data ref="tableData" :href="hrefData" :params="params"
              :show-create="false" :show-search="false" :show-dropdown="false" :checked="false"
              :upload="false" :pass-columns="['id_pelanggaran-id_iqab','status']"
              :title="'Data Calon Santri'"
              v-model:checked-id="ids"
              :fields="fields"
              class="p-0">
              <template #id_pelanggaran-inside="el">
                {{ el.scope.row.tingkat  }} - {{ el.scope.row.pelanggaran  }} 
              </template>
              <el-table-column label="Status" min-width="130" align="center">
                <template #default="scope">
                  <el-tag :type="setStatusType(scope.row.status) "  effect="dark">
                    {{ setStatusText(scope.row.status) }}
                  </el-tag>
                </template>
              </el-table-column>
            </table-data>
          </el-collapse-item>
        </el-collapse>
      </el-card>
    </div>
  </template>
  
  <script>
  import { mapGetters } from 'vuex';
  import { setStatusText, setStatusType } from '@/helpers/iqab'
  import Form from '@/components/Form.vue'
  import TableData from '@/components/TableData.vue'
  import ViewTable from '@/components/ViewTable.vue'
  import { isEmpty } from 'lodash';
  import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip,  Legend} from 'chart.js'
  import { Line } from 'vue-chartjs'
  
  ChartJS.register( CategoryScale, LinearScale, PointElement, LineElement, Title,  Tooltip, Legend)

  export default {
    name: "rekap-iqab",
    components: {
      'form-filter' : Form,
      TableData,
			ViewTable,
      Line,
    },
    data: function() {
      return {
        loading: false,
        empty: true,
				optionsSantri:[],
				santriField:{},
				inputKeyword:'',
        fields:{
          tanggal:'',
        },
        filter:{
          santri:'',
        },
        params:{},
        editId:-1,
        ids:[],
        sizeWindow:window.innerWidth,
        setStatusText: setStatusText,
        setStatusType: setStatusType,
        hrefData:'iqab/admin/rekapitulasi',
        active:['statistik', 'data'],
        loaded:false,
        statistic: {},
        options : {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 20,
              right: 20,
              top: 20,
              bottom: 20,
            },
          },
          scales: {
            y: {
              suggestedMin:-1,
              suggestedMax:5,
              ticks: {
                stepSize: 1
              }
            },
            x: {
              // Apply offset to create padding at the start and end
              offset: true,
            },
          },
          plugins: {
            legend: {
              position: 'top',
              labels: {
                font: {
                  size: 14,
                  family:'montserrat'
                },
              },
              title: {
                padding: {
                  bottom: 100,
                },
              },
            },
            tooltip: {
              callbacks: {
                label: (tooltipItem) => {
                  const label = tooltipItem.dataset.label || '';
                  const value = tooltipItem.raw || 0;
                  return `${label}: ${value}`;
                },
              },
              titleFont: {
                size: 13,
                family:'montserrat'
              },
              footerFont: {
                size: 12,
                family:'montserrat'
              },
            },
          }
        }  
      };
    },
    watch: {
      'filter.santri': function(val) {
        this.$store.dispatch('saveFilter', {ref:'santri', data: val})
      },
      'paging.currentPage': function(val) {
        this.paging.offset = val * this.paging.perPage - this.paging.perPage;
      },
    },  
    computed: {
      ...mapGetters({
        user: 'loggedUser',
      }),
      labelPosition(){
        return this.sizeWindow < 800 ? 'top' : 'left'
      }
    },
    methods: {
      searchData: async function(){
				this.params = {
					and: {
						id_santri:this.filter.santri,
					}
				};
        await this.$http.get('iqab/admin/rekapitulasi/summary', {
          params: {
            id_santri: this.filter.santri
          }
        })
          .then(res => {
            let data = res.data
            this.statistic = data
            this.options.scales.y.suggestedMax = parseInt(data.max) + 1
            this.options.scales.y.suggestedMin = parseInt(data.min) - 1
            this.loaded = true
          })
          .catch(err => {
            this.$notify({
              type:'error',
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right',
            });
          })
          let chart = this.$refs.line.chart;
          chart.options = this.options;
          chart.update();
      },
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=daiq_list_iqab&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = this.fillAndAddObjectValue(this.fields, res)
              this.fields.id_pelanggaran.hide_content = true
              this.loading = false
            });
          await this.$http.get('/kolom/preparation?table=pdm_santri&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.santriField = this.fillAndAddObjectValue(this.santriField, res)
              this.loading = false
            });
					await this.$http.get('/data/santri')
						.then(result => {
							var res = result.data;
							this.optionsSantri = res
							this.loading = false
						});
        },
      getData(data){
        this.inputKeyword = data
        this.searchData()
      },
      handleActionClick: function(obj) {
        var action = obj.action;
        var id = obj.id;
        var status = obj.status;
        var index = obj.index;
        if (action == 'finish') {
          this.statusIqab(id, status);
        } else  if (action == 'finish-many') {
          this.statusMany(status);
        } else {
          this.$refs.tableData.handleActionClick(obj);
        } 
      },
      statusIqab: function(id, status) {
        this.$confirm('Yakin mengubah status data ini?', 'Konfirmasi', {
          confirmButtonText: 'Ubah',
          cancelButtonText: 'Batal',
          type: 'warning'
        }).then(() => {
          this.$http.post('/iqab/admin/status/' + id + '/' + status)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData(0)
            })
            .catch(err => {
              this.$notify.error({
                title: 'Gagal',
                message: 'Tidak dapat mengubah status',
                position: 'bottom-right'
              });
            });        
          }).catch(() => {
          // Do nothing          
        });
      },
      statusMany: function(status) {
        this.$confirm('Yakin mengubah status data yang di checklist?', 'Konfirmasi', {
          confirmButtonText: 'Ubah',
          cancelButtonText: 'Batal',
          type: 'warning'
        }).then(() => {
          let data = window.jsonToFormData({ id:this.ids , status : status})
  
          this.$http.post(`iqab/admin/status_many`, data)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData(0)
            })
            .catch(err => {
              console.log(err)
              this.$notify.error({
                title: 'Gagal',
                message: 'Tidak dapat mengubah status',
                position: 'bottom-right'
              });
            });        
          }).catch(err => {
            console.log(err)
          // Do nothing          
        });
      }
    },
    created: function() {
      let filter = this.$store.getters.filter
      this.filter.santri = this.isEmpty(filter.santri) ? '' : filter.santri
      this.getInitial()
      // console.log(this.$router);
    },
    mounted: function() {
      let vm = this
      vm.sizeWindow = window.innerWidth
      window.addEventListener('resize', () => {
        vm.sizeWindow = window.innerWidth
      });
    },
  }
  </script>
  