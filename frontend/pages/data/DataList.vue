<template>
  <div id="data-list" class="py-6">
    <table-data ref="tableData" :fields="fields" :href="data.href"
      :checked="data.checked" :upload="data.upload" :pass-columns="[...coalesce([data.pass,[]]), ...(type == 'pengguna' ? ['photo'] : []) ]"
      :params="{offset:0, limit:0}"
      :title="data.title">
      <el-table-column v-if="type == 'pengguna'" label="Foto Anggota" width="200px">
        <template #default="scope">
          <div v-if="isEmpty(scope.row.photo)"
            class="text-center">
            <el-button type="primary" @click="$refs.tableData.handleActionClick({action:'edit', id:scope.row.id}, ['photo'])"
              size="small">
              Upload Foto
            </el-button>
          </div>
          <div v-else
            class="text-center">
            <img :src="scope.row.photo" height="40px" />
          </div>
        </template>
      </el-table-column>
    </table-data>
  </div>
</template>

<script setup>
  import { isEmpty } from 'lodash';
import components from './components'
</script>

<script>
  import TableData from '@/components/TableData.vue'

export default {
  name: "data-list",
  props:{
    type:'',
    component:'',
    showCreate:{
      type:Boolean,
      default: true,
    },
    showSearch:{
      type:Boolean,
      default: true,
    },
  },
  components: {
    TableData,
  },
  data: function() {
    return {
      data:{},
      fields:[],
    };
  },
  watch: {
   
    
  },
  computed: {
    
  },
  methods: {
    getInitial: async function() {
        this.loading = true;
        await this.$http.get('/kolom/preparation?table=' + this.data.table + '&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.fields = res
            this.loading = false
            this.$refs.tableData.getData()
          });
      },
  },
  created: function() {
    console.log('created')
    this.data = this.coalesce([components[this.type], this.component, {}])
    console.log(this.data )
    this.getInitial();
    this.$store.dispatch('changePageTitle', `<b>Data ${this.data.title} </b>`)
  }
}
</script>
