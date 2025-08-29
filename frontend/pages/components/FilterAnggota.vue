<template>
	<div
    class="bg-white/[0.9] rounded-[10px] shadow-md
    mb-3 p-4">
    <form-comp ref="formFilterAnggota"
      :key="'formFilterAnggota'+formKey"
      class=""
      :fields="fields" 
      v-model:form-value="formValue" 
      size="large"
      :show-columns="showColumns"
      :show-label="$windowWidth < 640 ? false : true"
      :show-submit="false"
      label-position="top"
      :show-required-text="false">
    </form-comp>  
  </div>
</template>

<script setup>

</script>

<script>
import { mapState, mapActions } from 'pinia';

export default {
  name: "filter-id_anggota",
	emits:['update:idAnggota','change'],
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    },
  },
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    showColumns(){
      // return []
      let cols = Object.keys(this.showRoles)
      let roles = Object.values(this.showRoles)
      return cols.filter((d, key) => roles[key].includes(this.user.role))
    }
  },
  data: function() {
    return {
      block:false,
      formKey:1,
      formValue:{
        bidang:'',
        id_unit:'',
        id_group:'',
        id_anggota:'',
      },
      showRoles:{
        bidang:['super-admin'],
        id_unit:['super-admin','admin-bidang'],
        id_group:['super-admin','admin-bidang','admin'],
        id_anggota:['super-admin','admin-bidang','admin','mentor'],
      },
      options:{
        bidang:[
          {value:'pendidikan', label:'Pendidikan'},
          {value:'kesehatan', label:'Kesehatan'}, 
          {value:'ekonomi', label:'Ekonomi'},
          {value:'kepengurusan', label:'Kepengurusan'},
        ],
        id_unit:[],
        id_group:[],
        id_anggota:[],
      },
      fields:{
        bidang:{
          nama_kolom:'bidang',
          label:'Bidang',
          input:'select',
          options:[],
        },
        id_unit:{
          nama_kolom:'id_unit',
          label:'Unit',
          input:'select',
          options:[],
        },
        id_group:{
          nama_kolom:'id_group',
          label:'Kelompok',
          input:'select',
          options:[],
        },
        id_anggota:{
          nama_kolom:'id_anggota',
          label:'Anggota',
          input:'select',
          options:[],
        },
      },
      id:-1,
		}
	},
  watch:{
    idAnggota:{
      immediate:true,
      handler(val){ 
        // console.log('idAnggota', val)
        this.setOptionsFromAnggota(val)
      }
    },
    id(val){
      this.$emit('update:idAnggota', val)
    },
    'formValue.bidang'(val){
      if (!this.block) {
        this.setOptions('id_unit','bidang')
        this.setOptions('id_group','id_unit')
        this.setOptions('id_anggota','id_group','id_unit')
        this.formKey++
      }
    },
    'formValue.id_unit'(val){
      if (!this.block) {
        this.setOptions('id_group','id_unit')
        this.setOptions('id_anggota','id_group','id_unit')
        this.formKey++
      }
    },
    'formValue.id_group'(val){
      if (!this.block) {
        this.setOptions('id_anggota','id_group','id_unit')
        this.formKey++
      }
    },
    'formValue.id_anggota'(val){
      console.log('anggota', val)
      this.id = val == 'all' ? this.getAllValue('id_anggota').join(',') : val
    }
  },
	methods:{
    setOptionsFromAnggota(val){
      console.log('setOptions')
      let anggota = this.options.id_anggota.filter(d => d.value == val)[0] ?? {}
      // if (anggota.length > 0)
      //   this.formValue.id_anggota = anggota[0]?.id ?? ''
      let group = this.options.id_group.filter(d => d.value == anggota?.id_group)[0] ?? {}
      let unit = this.options.id_unit.filter(d => d.value == group?.id_unit || d.value == anggota?.id_unit)[0] ?? {}
      let bidang = this.options.bidang.filter(d => d.value == unit?.bidang)[0] ?? {}
      
    },
    getAllValue(col){
      let opt = this.fields[col].options.filter(d => d.value !== 'all')
      // console.log(opt)
      return opt.map(val => val.value)
    },
    setOptions(optionType, searchValue = false, anotherSearchValue = false){
      let getFromUser = !this.showColumns.includes(optionType)
      // console.log(optionType, searchValue, getFromUser)
      let opt = []

      let selected = this.formValue[searchValue] ?? '-1'
      let arr = selected == 'all' ? this.getAllValue(searchValue) : [selected]
      // console.log(arr, arr.length)
      let selectedAnother = this.formValue[anotherSearchValue] ?? '-1'
      let arrAnother = selectedAnother == 'all' ? this.getAllValue(anotherSearchValue) : [selectedAnother]
      // console.log(arrAnother)
      if (searchValue) {
        opt = this.options[optionType].filter(d => {
          // console.log(arr, arr.includes(d[searchValue]), arr.length, arrAnother.includes(d[anotherSearchValue]))
          return arr.includes(d[searchValue]) ||
            (arr.length > 0 ?
            false :
            arrAnother.includes(d[anotherSearchValue]))
        })
      } else {
        opt = this.options[optionType]
      }

      // console.log(this.options[optionType], opt)
      this.fields[optionType].options = [
        ...[{
          value:'all',
          label:'Semua ' + this.fields[optionType].label,
        }],
        ...opt,
      ]
      // console.log(all)
      this.formValue[optionType] = getFromUser ? this.user[optionType] : 'all'
    },
    async getInitial(){
      await this.$http.get('/data/unit/options')
        .then(res => {
          this.options.id_unit = res.data
        })
      await this.$http.get('/data/group/options')
        .then(res => {  
          this.options.id_group = res.data
        })  
      await this.$http.get('/data/anggota/options')
        .then(res => {  
          this.options.id_anggota = res.data
          this.setOptions('bidang', false)        
          this.setOptions('id_unit','bidang')
          this.setOptions('id_group','id_unit')
          this.setOptions('id_anggota','id_group','id_unit')
        })
    }
	},
  created(){
    this.getInitial()
  },
	mounted(){
	}
}
</script>