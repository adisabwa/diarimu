<template>
  <div id="iqab" class="py-6">
    <el-card class="bg-white/[0.7]">
      <form-filter ref="formFilter"
        :fields="filterFields"
        :label-position="labelPosition"
        class="mt-6"
        label-width="180px"
        @form-value="getFilter"
        :show-submit="false"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        ></form-filter>
      <div class="mt-3 flex flex-row justify-between">
        <div class="flex flex-col md:flex-row gap-3 justify-start">
          <el-button type="success" @click="handleActionClick({action:'add'})" class="m-0">
            <icons icon="mdi:plus"/>
            Tambah Baru</el-button>
          <el-dropdown trigger="click" @command="handleActionClick">
            <el-button type="primary"> 
              Ubah Checklist <icons icon="fe:arrow-down" class="el-icon--right" />
            </el-button>
            <template #dropdown>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item class="text-green-600"
                  :command="{action: 'finish-many', status:'1'}">
                  <icons icon="mdi:check"/> Sudah Terlaksana</el-dropdown-item>
                <el-dropdown-item class="text-red-500"
                  :command="{action: 'delete-many'}" >
                  <icons icon="material-symbols:delete-outline"/> Hapus Data</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
          <el-button type="warning" @click="showUpload = true" class="m-0">
            <icons icon="mdi:download"/>
            Download Excel</el-button>
        </div>
        <el-button type="primary"
          @click="searchData"
          ><icons icon="mdi:search"/>Cari</el-button>
      </div>
      <table-data ref="tableData" :href="hrefData" :params="params"
        :show-create="false" :show-search="false" :show-dropdown="false"
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
          <el-table-column class="font-bold"
          width="130" align="center">
          <template #default="scope">
            <el-dropdown trigger="click" @command="handleActionClick"
              class="[&_*]:text-[13px]">
              <el-button type="primary" class="px-3 py-2"> 
                Aksi <icons icon="fe:arrow-down" class="el-icon--right" />
              </el-button>
              <template #dropdown>
                <el-dropdown-menu slot="dropdown" class="[&_*]:text-[13px]">
                  <el-dropdown-item v-if="scope.row.status == '0'" class="text-green-600"
                    :command="{action: 'finish', id: scope.row.id, status:'1'}">
                    <icons icon="mdi:check"/> Sudah Terlaksana</el-dropdown-item>
                  <el-dropdown-item 
                    :command="{action: 'edit', id: scope.row.id}">
                    <icons icon="mdi:edit"/> Ubah Data</el-dropdown-item>
                  <el-dropdown-item 
                    :command="{action: 'delete', id: scope.row.id}">
                    <icons icon="material-symbols:delete-outline"/> Hapus</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </template>
        </el-table-column>
      </table-data>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { setStatusText, setStatusType } from '@/helpers/iqab'
import Form from '@/components/Form.vue'
import TableData from '@/components/TableData.vue'

export default {
  name: "iqab",
  components: {
    'form-filter' : Form,
    TableData,
  },
  data: function() {
    return {
      loading: false,
      showAdd: false,
      filterFields: {
        'nama' : {
          nama_kolom:'nama',
          label:'Nama Santri'
        },
        'kelas' : {
          nama_kolom:'kelas',
          label:'Kelas',
          input:'select',
          options: [],
        },
      },
      fields:{
        tanggal:'',
      },
      addFields:{
        pelanggaran: {
          nama_kolom:'pelanggaran',
          label:'Pelanggaran / Tingkat',
          'min-width':'220px',
          sort:'',
          sortable:'1',
          align:'center',
          hide_content: true,
        },
      },
      filter:{
        nama:'',
        kelas:'',
      },
      params:{
        nama:'-1',
        kelas:'-1',
      },
      editId:-1,
      ids:[],
      sizeWindow:window.innerWidth,
      setStatusText: setStatusText,
      setStatusType: setStatusType,
      hrefData:'iqab/admin',
    };
  },
  watch: {
    'filter.nama': function(val) {
      this.$store.dispatch('saveFilter', {ref:'nama', data: val})
    },
    'filter.kelas': function(val) {
      this.$store.dispatch('saveFilter', {ref:'kelas', data: val})
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
    searchData(){
      this.resetObjectValue(this.params)
      this.fillObjectValue(this.params, this.filter)
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
        await this.$http.get('/data/santri/kelas')
          .then(result => {
            var res = result.data;
            this.filterFields.kelas.options = res
            this.loading = false
          });
      },
    getFilter(filter){
      this.fillObjectValue(this.filter, filter)
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
    this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
    this.filter.kelas = this.isEmpty(filter.kelas) ? '' : filter.kelas
    this.getInitial()
    // console.log(this.$router);
  },
  mounted: function() {
    let vm = this
    vm.sizeWindow = window.innerWidth
    window.addEventListener('resize', () => {
      vm.sizeWindow = window.innerWidth
    });
    this.searchData()
  },
}
</script>
