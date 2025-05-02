<template>
  <div id="pengurus" class="py-6">
    <el-card class="bg-white/[0.7]">
      <form-filter ref="formFilter"
        :fields="filterFields"
        :label-position="labelPosition"
        class="mt-6"
        label-width="250px"
        @form-value="getFilter"
        :show-submit="false"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        :show-columns="['tingkat',...(filter.tingkat == 'cabang' ? ['id_pcm'] : (filter.tingkat == 'ranting' ? ['id_prm'] : []))]"
        >
      </form-filter>
      <div class="mt-3 flex flex-row justify-between">
        <div class="flex flex-col md:flex-row gap-3 justify-start">
          <el-dropdown trigger="click" @command="handleActionClick">
            <el-button type="primary"> 
              Ubah Checklist <icons icon="fe:arrow-down" class="el-icon--right" />
            </el-button>
            <template #dropdown>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item class="text-green-600"
                  :command="{action: 'pay-many', status:'1'}">
                  <icons icon="mdi:money"/> Sudah Dibayar</el-dropdown-item>
                <el-dropdown-item class="text-sky-600"
                  :command="{action: 'pay-many', status:'2'}">
                  <icons icon="mdi:check"/> Verifikasi</el-dropdown-item>
                <el-dropdown-item class="text-red-500"
                  :command="{action: 'delete-many'}" >
                  <icons icon="material-symbols:delete-outline"/> Hapus Data</el-dropdown-item>
                <el-dropdown-item
                  :command="{action: 'download-many'}" >
                  <icons icon="mdi:download"/> Unduh Kartu Pendaftaran</el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
          <el-button type="success" @click="showUpload = true" class="m-0">
            <icons icon="mdi:download"/>
            Download Excel</el-button>
        </div>
        <el-button type="primary"
          @click="searchData"
          ><icons icon="mdi:search"/>Cari</el-button>
      </div>
      <table-data ref="tableData" :href="hrefData" :params="params"
        :show-create="false" :show-search="false" :upload="false" 
        title="Data Pengurus"
        v-model:checked-id="ids"
        :fields="fields"
        class="p-0">
        <template #nama-inside="el">
          {{ el.scope.row.nama  }} <br/>
          {{ el.scope.row.nisn  }} 
        </template>
        <template #ayah_nama-inside="el">
            Ayah : {{ el.scope.row.ayah_nama  }} ( {{ el.scope.row.ayah_nik  }} ) <br/>
            Ibu : {{ el.scope.row.ibu_nama  }} ( {{ el.scope.row.ibu_nik  }} ) <br/>
            Wali : {{ el.scope.row.wali_nama  }} ( {{ el.scope.row.wali_nik  }}  )
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
    name: "pengurus",
    components: {
      'form-filter' : Form,
      TableData,
    },
    data: function() {
      return {
        loading: false,
        filterFields: {
          tingkat: {
            nama_kolom:'tingkat',
            label:'Tingkat',
            input:'radio',
            default:'daerah',
            options:[
              {value:'daerah',label:'Daerah'},
              {value:'cabang',label:'Cabang'},
              {value:'ranting',label:'Ranting'},
            ]
          },
          id_pcm: {
            nama_kolom: 'id_pcm',
            label:'Pimpinan Cabang',
            input:'select',
          },
          id_prm: {
            nama_kolom: 'id_prm',
            label:'Pimpinan Ranting',
            input:'select',
          },
        },
        fields:{
          no_pendaftaran: {
            nama_kolom:'no_pendaftaran',
            label:'No. Pendaftaran',
            'min-width':'200px',
            sort:'',
            sortable:'1',
            align:'left',
          },
          nama: {
            nama_kolom:'nama',
            label:'Nama Calon Santri / NISN',
            'min-width':'300px',
            sort:'',
            sortable:'1',
            align:'left',
            hide_content: true,
          },
          ayah_nama: {
            nama_kolom:'ayah_nama',
            label:'Data Orang Tua',
            'min-width':'250px',
            sort:'',
            sortable:'1',
            align:'left',
            hide_content: true,
          },
        },
        filter:{
          tingkat:'',
          id_pcm:'',
          id_prm:'',
        },
        params:{
          tingkat:'-1',
          id_pcm:'-1',
          id_prm:'-1',
        },
        editId:-1,
        ids:[],
        sizeWindow:window.innerWidth,
        hrefData:'pengurus/admin',
      };
    },
    watch: {
      'filter.tingkat': function(val) {
        this.$store.dispatch('saveFilter', {ref:'tingkat', data: val})
      },
      'filter.id_pcm': function(val) {
        this.$store.dispatch('saveFilter', {ref:'id_pcm', data: val})
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
      },
    },
    methods: {
      findOptions: async function() {
        await this.$http.get('data/pcm/options')
          .then(result => {
            var res = result.data;
            this.filterFields.id_pcm.options = res
          });
        await this.$http.get('data/prm/options')
          .then(result => {
            var res = result.data;
            this.filterFields.id_prm.options = res
          });
      },
      searchData(){
        this.resetObjectValue(this.params)
        this.fillObjectValue(this.params, this.filter)
      },
      getFilter(filter){
        this.fillObjectValue(this.filter, filter)
      },
      handleActionClick: function(obj) {
        var action = obj.action;
        var id = obj.id;
        var nisn = obj.nisn;
        var status = obj.status;
        var index = obj.index;
        if (action == 'edit') {
          this.$router.push({name:'pengurus-view'})
          this.$store.dispatch('saveFilter', {ref:'keyword', data: nisn})
        } else if (action == 'pay') {
          this.statusPsb(id, status);
        } else  if (action == 'pay-many') {
          this.statusMany(status);
        } else  if (action == 'download') {
          this.downloadPsb(id);
        } else  if (action == 'download-many') {
          this.downloadMany();
        } else {
          this.$refs.tableData.handleActionClick(obj);
        } 
      },
      statusPsb: function(id, status) {
        this.$confirm('Yakin mengubah status data ini?', 'Konfirmasi', {
          confirmButtonText: 'Ubah',
          cancelButtonText: 'Batal',
          type: 'warning'
        }).then(() => {
          this.$http.post('/pengurus/admin/status/' + id + '/' + status)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData()
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
          let data = window.jsonToFormData({ id: this.ids , status : status})
  
          this.$http.post(`pengurus/admin/status_many`, data)
            .then(result => {
              this.$notify.success({
                title: 'Berhasil',
                message: 'Data berhasil diubah',
                position: 'bottom-right'
              });
              this.searchData()
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
      },
      downloadPsb: function(id, index) {
        window.open(this.$siteUrl + '/pengurus/admin/download/'+id,'_blank')
      },
      downloadMany: function() {
          let url = ''
          url = this.$siteUrl + '/pengurus/admin/download_many'
          // Replace with your URL
  
          // Create a form
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = url;
          form.target = '_blank'; // Open in a new tab
  
          // Add data as hidden input fields
          this.ids.forEach((s, key) => {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = `id[${key}]`;
            input.value = s;
            form.appendChild(input);
          })
  
          // Append the form to the body and submit it
          document.body.appendChild(form);
          form.submit();
  
          // Remove the form after submission
          document.body.removeChild(form);
      },
    },
    created: function() {
      let filter = this.$store.getters.filter
      let array = Object.keys(this.filter);
      for (let i = 0; i < array.length; i++) {
        let index = array[i];
        this.filter[index] = this.isEmpty(filter[index]) ? '' : filter[index]
      }
      this.findOptions();
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