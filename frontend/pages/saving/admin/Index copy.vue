<template>
  <div id="saving" class="py-6">
    <el-card>
      <el-form label-width="80px" label-position="left">
        <el-form-item label="Bulan">
          <div class="w-full flex flex-col md:flex-row gap-y-0 gap-x-3">
            <el-date-picker
              v-model="filter.start"
              type="month"
              value-format="YYYY-MM-DD"
              format="MMMM YYYY"
              placeholder="Pilih Bulan Mulai"
            />
            <div class="text-center"> sampai </div>
            <el-date-picker
              v-model="filter.end"
              type="month"
              value-format="YYYY-MM-DD"
              format="MMMM YYYY"
              placeholder="Pilih Bulan Selesai"
            />
          </div>
        </el-form-item>
        <div class="flex gap-10">
          <el-form-item label="Kelas" class="w-1/3">
            <el-select v-model="filter.kelas" placeholder="Nama Kelas" class="w-full"
              filterable>
              <el-option value="" label="Semua"/>
              <template 
                v-for="item in kelasList"
                :key="item">
                <el-option
                  :value="item"
                  :label="item">
                </el-option>
              </template>
            </el-select>
          </el-form-item>
          <el-form-item label="Santri" class="w-2/3">
            <el-select v-model="filter.santri" placeholder="Nama Santri" class="w-full"
              filterable>
              <el-option value="" label="Semua"/>
              <template v-for="item in santriList"
                :key="item.id">
                <el-option
                  v-if="item.kelas == filter.kelas"
                  :value="item.id"
                  :label="item.kelas + ' - ' + item.nama">
                </el-option>
              </template>
            </el-select>
          </el-form-item>
        </div>
      </el-form>
      <div class="flex flex-col md:flex-row gap-3">
        <div class="w-full md:w-2/3">
          <el-button type="success" size="small" round @click="handleActionClick({action:'add'})">
            <icons icon="mdi:plus"/>
            Buat Baru</el-button>
          <el-button type="primary" size="small" round @click="showUpload = true">
            <icons icon="mdi:upload"/>
            Upload Excel</el-button>
          <el-button type="danger" size="small" round @click="deleteMany">
            <icons icon="mdi:trash"/>
            Delete Checklist</el-button>
        </div>
        <div class="w-full md:w-1/3 flex">
          <el-input placeholder="Cari..." class="min-w-[200px] rounded-full" size="default" type="search" @input="isTyping=true" v-model="searchKeyword" />
        </div>
      </div>
      <el-row>
        <div class="w-full">
          <loading v-if="loading" title="Mengambil data..."
            subtitle="Silahkan tunggu, sedang mengambil data." />
          <loading v-else-if="searching" title="Mencari data..."
            subtitle="Silahkan tunggu, sedang mencari data." />
          <loading v-else-if="savingsFilter.length == 0"
            icon="el-icon-s-order" title="Tidak ada data"
            subtitle="Silahkan ubah santri atau start untuk melihat data yang lain." />
          <div v-else>
            <el-table
              class="w-full mt-8"
              :data="savingsFilter.slice(paging.offset, paging.offset + paging.perPage)"
              :summary-method="getSummaries"
              show-summary
              stripe fit>
              <el-table-column
                width="30">
                <template #header>
                  <el-checkbox  
                    v-model="checkAll"
                    :indeterminate="isIndeterminate"
                    @change="handleCheckAllChange"/>
                </template>
                <template #default="scope">
                  <el-checkbox  
                    v-model="scope.row.checked"/>
                </template>
              </el-table-column>
              <el-table-column type="index" :index="indexMethod" />
              <el-table-column label="Tanggal" min-width="150">
                <template #default="scope">
                  {{ dateIndo(scope.row.tanggal) }}
                </template>
              </el-table-column>
              <el-table-column label="Nama Santri" min-width="170">
                <template #default="scope">
                  {{ scope.row.kelas }} - {{ scope.row.nama  }}
                </template>
              </el-table-column>
              <el-table-column label="Keterangan" min-width="200">
                <template #default="scope">
                  {{ scope.row.keterangan  }}
                </template>
              </el-table-column>
              <el-table-column label="Pemasukan" width="150" align="center">
                <template #default="scope">
                  {{ scope.row.jenis == '1' ? toIDR(scope.row.nominal) : '-' }}
                </template>
              </el-table-column>
              <el-table-column label="Pengeluaran" width="150" align="center">
                <template #default="scope">
                  {{ scope.row.jenis == '-1' ? toIDR(scope.row.nominal) : '-' }}
                </template>
              </el-table-column>
               <el-table-column class="font-bold"
                width="150">
                <template #default="scope">
                  <el-dropdown trigger="click" @command="handleActionClick">
                    <el-button type="primary" size="small"> 
                      Aksi <icons icon="fe:arrow-down" class="el-icon--right" />
                    </el-button>
                    <template #dropdown>
                      <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item 
                          :command="{action: 'edit', id: scope.row.id}">
                          <icons icon="material-symbols:edit-outline"/> Ubah</el-dropdown-item>
                        <el-dropdown-item 
                          :command="{action: 'delete', id: scope.row.id}">
                          <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </div>
      </el-row>
      <el-row type="flex" justify="space-between" style="margin-top: 15px">
        <el-select v-model="paging.perPage" placeholder="Select" style="width:70px;">
          <el-option
            v-for="(item,key) in page"
            :key="key"
            :label="item"
            :value="item">
          </el-option>
        </el-select>
        <el-pagination
          background
          layout="total, prev, pager, next"
          :total="paging.dataTotal"
          v-model:page-size="paging.perPage"
          v-model:current-page="paging.currentPage">
        </el-pagination>
      </el-row>
    </el-card>

  <saving-create v-model:show="showAdd" :data-id="editId" :type="type"
       @saved="onUpdated" />
  <upload-dialog v-model:show="showUpload" title="Pemasukan dan Pengeluaran" link="saving" @saved="onUpdated" />

  </div>
</template>

<script>
import SavingCreate from './Create.vue';
import UploadDialog from '@/components/UploadDialog.vue'
import { mapGetters } from 'vuex';

export default {
  name: "saving",
  components: {
    SavingCreate,
    UploadDialog,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      showUpload: false,
      searching: false,
      saving: false,
      filter: {
        santri: '',
        kelas: '',
        start: '',
        end: '',
      },
      editId:-1,
      type:'create',
      santriList: [],
      kelasList:[],
      savings:[],
      searchKeyword: '',
      isInitial: true,
      page: [10,20,30,40,50],
      checkAll:false,
      paging: {
        offset: 0,
        perPage: 10, // [10,20,30,40,50,100]
        currentPage: 1,
        dataTotal: 0,
      },
    };
  },
  watch: {
    'filter.santri': function(val) {
      this.$store.dispatch('saveFilter', {ref:'santri', data: val});
      if (!this.isInitial) {
        this.getSaving();
      }
    },
    'filter.kelas': function(val) {
      this.$store.dispatch('saveFilter', {ref:'kelas', data: val});
      if (!this.isInitial) {
        this.getSaving();
      }
    },
    'filter.start': function(val) {
      if (val == null) return this.filter.start = '';
      this.$store.dispatch('saveFilter', {ref:'start', data: val});
      if (!this.isInitial) {
        this.getSaving();
      }
    },
    'filter.end': function(val) {
      if (val == null) return this.filter.end = '';
      this.$store.dispatch('saveFilter', {ref:'end', data: val});
      if (!this.isInitial) {
        this.getSaving();
      }
    },
    'paging.currentPage': function(val) {
      this.paging.offset = val * this.paging.perPage - this.paging.perPage;
    },
    savingsFilter(val){
      this.paging.dataTotal = val.length
    }
  },  
  computed: {
    savingsFilter: function() {
      var q = this.searchKeyword.toLowerCase();
      if (q.length > 0) {
        return this.savings.filter(sch => {
            return sch.nama.toLowerCase().includes(q) || sch.kelas.toLowerCase().includes(q) || sch.keterangan.toLowerCase().includes(q);
        });
      }
      return this.savings;
        // this.searching = false;
    },
    totalPemasukan(){
      let total = 0;
      this.savings.forEach(d => {
        if (d.jenis == '1') 
          total += parseInt(d.nominal)
      });
      return total
    },
    totalPengeluaran(){
      let total = 0;
      this.savings.forEach(d => {
        if (d.jenis == '-1') 
          total += parseInt( d.nominal)
      });
      return total
    },
    isIndeterminate(){
      let count = this.savingsFilter.filter(d => d.checked).length
      if (count == 0 || count == this.savingsFilter.length)
        return false
      return true
    },
    ...mapGetters({
      user: 'loggedUser',
    }),

  },
  methods: {
    indexMethod(index){
      return index + this.paging.offset + 1
    },
    handleCheckAllChange(val){
      this.savingsFilter.forEach(d => {
        d.checked = val
      });
    },
    getInitial: async function() {
      this.loading = true;
      let filter = this.$store.getters.filter
      this.filter.start = this.isEmpty(filter.start) ? this.dateNow() : filter.start
      this.filter.end = this.isEmpty(filter.end) ? this.dateNow() : filter.end
      await this.$http.get('/data/santri')
        .then(result => {
          var res = result.data;
          this.santriList = res;
          let kelas = [];
          res.forEach(d => {
            if (!kelas.includes(d.kelas))
              kelas.push(d.kelas)
          });
          this.kelasList = kelas
          this.filter.santri = this.isEmpty(filter.santri) ? '' : filter.santri
          this.filter.kelas = this.isEmpty(filter.kelas) ? '' : filter.kelas
        });
      await this.getSaving();
    },
    getSaving: function(){
      this.loading = true;
      var urlParams = new URLSearchParams();
      urlParams.append('santri', this.filter.santri);
      urlParams.append('kelas', this.filter.kelas);
      urlParams.append('start', this.filter.start);
      urlParams.append('end', this.filter.end);

      this.$http.get('/saving?'+ urlParams)
        .then(result => {
          this.loading = false;
          this.isInitial = false;
          var res = result.data;
          this.savings = res;
        })
        .catch(err => {
          this.loading = false;
          this.$notify.error({
            title: 'Gagal',
            message: 'Tidak dapat mengambil data',
            position: 'bottom-right'
          });
        });
    },
    getSummaries(param){
      const { columns, data } = param
      let sums = []
      let vm = this;
      columns.forEach((column, index) => {
        if (index === 2) {
          sums[index] = 'Total Data'
          return
        } else if (index === 5) {
          sums[index] = vm.toIDR(vm.totalPemasukan)
        } else if (index === 6) {
          sums[index] = vm.toIDR(vm.totalPengeluaran)
        } else if (index === 7) {
          sums[index] = vm.toIDR(vm.totalPemasukan - vm.totalPengeluaran)
        } else {
          sums[index] = '';
        }
      })
      this.jquery('.el-table__footer-wrapper').addClass('font-bold')
      return sums
    },
    handleActionClick: function(obj) {
      var action = obj.action;
      var id = obj.id;
      var index = obj.index;
      if (action == 'add') {
        this.showAdd = true;
        this.type = 'create';
        this.editId = -1;
      } if (action == 'edit') {
        this.showAdd = true;
        this.type = 'update';
        this.editId = id;
      } else  if (action == 'delete') {
        this.deleteSchedule(id, index);
      }
    },
    deleteSchedule: function(id, index) {
      this.$confirm('Yakin menghapus jadwal ini?', 'Konfirmasi', {
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        type: 'error'
      }).then(() => {
        this.$http.post('/saving/delete/' + id)
          .then(result => {
            this.$notify.success({
              title: 'Berhasil',
              message: 'Jadwal berhasil dihapus',
              position: 'bottom-right'
            });
            this.savings.splice(index,1);
          })
          .catch(err => {
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat menghapus',
              position: 'bottom-right'
            });
          });        
        }).catch(() => {
        // Do nothing          
      });
    },
    deleteMany: function() {
      this.$confirm('Yakin menghapus data yang di checklist?', 'Konfirmasi', {
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        type: 'error'
      }).then(() => {
        let id = [];
        this.savingsFilter.forEach(s => {
          if (s.checked)
            id.push(s.id)
        })
        let data = window.jsonToFormData({ id:id })

        this.$http.post(`saving/delete_many`, data)
          .then(result => {
            this.$notify.success({
              title: 'Berhasil',
              message: 'Data berhasil dihapus',
              position: 'bottom-right'
            });
            this.getInitial();
          })
          .catch(err => {
            console.log(err)
            this.$notify.error({
              title: 'Gagal',
              message: 'Tidak dapat menghapus',
              position: 'bottom-right'
            });
          });        
        }).catch(err => {
          console.log(err)
        // Do nothing          
      });
    },
    onUpdated(){
      this.showAdd = false;
      this.showUpload = false;
      this.getSaving();
    }
  },
  created: function() {
    this.getInitial();
    // console.log(this.$router);
  },
  mounted: function() {
  },
}
</script>