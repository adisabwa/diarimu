<template>
  <div id="iqab" class="py-2">
    <el-card class="bg-white/[0.7]">
      <form-filter ref="formFilter"
        :fields="filterFields"
        :label-position="labelPosition"
        class="mt-2"
        label-width="180px"
        :pass-columns="['start','end']"
        @form-value="getFilter"
        :show-submit="false"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        >
        <template #default="{ errors, form}">
          <el-form-item label="Bulan" :errors="errors.start + ' ' + errors.end">
            <div class="w-full flex flex-col md:flex-row gap-y-0 gap-x-3">
              <el-date-picker
                v-model="form.start"
                type="month"
                value-format="YYYY-MM-DD"
                format="MMMM YYYY"
                placeholder="Pilih Bulan Mulai"
                size="large"
              />
              <div class="text-center"> sampai </div>
              <el-date-picker
                v-model="form.end"
                type="month"
                value-format="YYYY-MM-DD"
                format="MMMM YYYY"
                placeholder="Pilih Bulan Selesai"
                size="large"
              />
            </div>
          </el-form-item>
        </template>
      </form-filter>
      <table-data ref="tableData" href="saving/admin" :params="params"
        :show-search="false"
        :title="'Data Tabungan Santri'"
        v-model:checked-id="ids"
        :fields="fields"
        :pass-columns="['jenis','nominal']"
        class="p-0">
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
        <template #menu>
          <!-- <el-button type="warning" @click="showUpload = true" class="m-0">
            <icons icon="mdi:download"/>
            Download Data</el-button> -->
          <el-button type="primary" class="float-right"
            @click="searchData"
            ><icons icon="mdi:search"/>Cari</el-button>
        </template>
      </table-data>
    </el-card>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
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
        'start' : {
          nama_kolom:'start',
          label:'Tanggal Mulai'
        },
        'end' : {
          nama_kolom:'end',
          label:'Tanggal Selesai'
        },
        'kelas' : {
          nama_kolom:'kelas',
          label:'Kelas',
          input:'select',
          options: [],
        },
      },
      fields:{},
      filter:{
        nama:'',
        kelas:'',
        start:'',
        end:'',
      },
      params:{
        nama:'-1',
        kelas:'-1',
        start:'',
        end:'',
      },
      editId:-1,
      ids:[],
      sizeWindow:window.innerWidth,
    };
  },
  watch: {
    'filter.nama': function(val) {
      this.$store.dispatch('saveFilter', {ref:'nama', data: val})
    },
    'filter.kelas': function(val) {
      this.$store.dispatch('saveFilter', {ref:'kelas', data: val})
    },
    'filter.start': function(val) {
      this.$store.dispatch('saveFilter', {ref:'start', data: val})
    },
    'filter.end': function(val) {
      this.$store.dispatch('saveFilter', {ref:'end', data: val})
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
        await this.$http.get('/kolom/preparation?table=das_tabungan&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fields = this.fillAndAddObjectValue(this.fields, res)
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
  },
  created: function() {
    let filter = this.$store.getters.filter
    this.filter.nama = this.isEmpty(filter.nama) ? '' : filter.nama
    this.filter.kelas = this.isEmpty(filter.kelas) ? '' : filter.kelas
    this.filter.start = this.isEmpty(filter.start) ? '' : filter.start
    this.filter.end = this.isEmpty(filter.end) ? '' : filter.end
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
