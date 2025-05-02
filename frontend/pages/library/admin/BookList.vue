<template>
    <div id="library" class="py-6">
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
                    :command="{action: 'edit-all'}">
                    <icons icon="mdi:edit"/> Edit Data</el-dropdown-item>
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
          :show-create="false" :show-search="false" 
          :upload="false" :pass-columns="['penerbit','penulis','terbit','link','status']"
          :title="'Data Buku'"
          vertical-align="baseline" header-vertical-align="middle"
          v-model:checked-id="ids"
          :fields="fields"
          class="p-0">
          <template #judul-inside="el">
            Judul Buku : {{ el.scope.row.judul  }} <br/>
            Penulis : {{ el.scope.row.penulis  }} <br/>
            Penerbit : {{ el.scope.row.penerbit  }}, {{ dateIndo(el.scope.row.terbit) }} <br/>
            <template v-if="!isEmpty(el.scope.row.link)">
              <file class="mt-1" title="Link Buku" :href="el.scope.row.link" />
            </template>
          </template>
          <el-table-column label="Status" width="130" align="center">
            <template #default="scope">
              <el-tag :type="setStatusType(scope.row.status) "  effect="dark">
                {{ setStatusText(scope.row.status) }}
              </el-tag>
            </template>
          </el-table-column>
        </table-data>
      </el-card>
    </div>
  </template>
  
  <script>
    import { mapGetters } from 'vuex';
    import { setStatusText, setStatusType } from '@/helpers/library'
    import Form from '@/components/Form.vue'
    import TableData from '@/components/TableData.vue'
import { isEmpty } from 'lodash';
    
    export default {
      name: "library",
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
              nama_kolom:'keyword',
              label:'Pencarian',
              placeholder:'Judul / Pengarang / Penerbit',
            },
            'jenis' : {
              nama_kolom:'jenis',
              label:'Jenis Buku',
              input:'select',
              options: [],
            },
          },
          fields:{
            ebook:'',
            judul:'',
          },
          filter:{
            keyword:'',
            jenis:'',
          },
          params:{
            keyword:'-1',
            jenis:'-1',
          },
          editId:-1,
          ids:[],
          sizeWindow:window.innerWidth,
          setStatusText: setStatusText,
          setStatusType: setStatusType,
          hrefData:'library/admin',
        };
      },
      watch: {
        'filter.keyword': function(val) {
          this.$store.dispatch('saveFilter', {ref:'keyword', data: val})
        },
        'filter.jenis': function(val) {
          this.$store.dispatch('saveFilter', {ref:'jenis', data: val})
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
            await this.$http.get('/kolom/preparation?table=dalib_buku&grouping=0&input=0')
              .then(result => {
                var res = result.data;
                this.fields = this.fillAndAddObjectValue(this.fields, res)
                this.fields.judul.hide_content = true
                this.loading = false
              });
            await this.$http.get('/library/admin/jenis/reset_options')
              .then(result => {
                var res = result.data;
                this.filterFields.jenis.options = res
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
      },
      created: function() {
        let filter = this.$store.getters.filter
        this.filter.keyword = this.isEmpty(filter.keyword) ? '' : filter.keyword
        this.filter.jenis = this.isEmpty(filter.jenis) ? '' : filter.jenis
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
  