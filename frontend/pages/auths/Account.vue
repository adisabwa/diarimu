<template>
	<div id="account-page">
		<el-card class="mx-2 bg-white pt-[50px] pb-10">
			<div>
				<div class="h-[120px] w-[120px] mx-auto mb-6
          rounded-full overflow-hidden relative
					flex items-center justify-center
					border border-solid border-teal-600/[0.4]">
          <div v-if="!isEmpty(viewValue.photo)"
            class="w-full h-full bg-cover bg-top"
            :style="`background-image:url('${viewValue.photo}')`"
            />
					<icons v-else 
            icon="mdi:user" class="mr-0 text-teal-700 text-[100px]"/>
				</div>
				<div class="text-xl my-3 font-bold">{{user.nama}}</div>
				<el-divider class="w-full [&>*]:w-[max-content] my-4">
					<div class="text-xl text-center font-bold">Profil Anggota</div>
				</el-divider>
				<form-comp v-if="showEdit"
					ref="formRegistrasi"
					class="mt-3 px-1"
					:key="'form-registrasi-'+formKey"
					:fields="fields" 
					:id="dataId"
					v-model:form-value="formValue"
					href="data/anggota/store"
					href-get="data/anggota/get"
					@saved="saving = false; showEdit = false;
            $store.dispatch('resetAccount')"  
					@error="saving=false"
					size="large"
					submit-text="Simpan Data"
					label-position="top"
					:show-required-text="false"
				></form-comp>  
				<template v-else>
					<view-table
						ref="viewAccount"
						class="mt-2 w-full"
						:fields="fields" 
						:key="'from'+active"
						label-position="top"
            v-model:form-value="viewValue"
						label-width="80px"
            :pass-columns="['photo']"
						href-get="/data/anggota/get_where"
						:keyword="dataId"
						:search-columns="['id']"
						v-model:empty="empty"
						v-model:loading="loading"
						v-model:id="dataId"
						/>
					<el-button class="mt-1 flex items-center
						w-full rounded-full bg-teal-700 
						text-teal-100 font-bold"
						@click="showEdit = true">
						<icons icon="mdi:edit"/>
						Ubah Profil
					</el-button>
				</template>
			</div>
		</el-card>
	</div>
</template>

<script>

import { mapGetters } from 'vuex';
import ViewTable from '../../components/ViewTable.vue';
import Form from '../../components/Form.vue';

export default {
	name: 'account-page',
	components:{
		ViewTable,
		'form-comp' : Form,
	},
	data: function() {
		return {
			saving: false,
			showEdit: false,
			fields:{},
      fieldsAkun:{
        id_anggota:{
          nama_kolom:'id_anggota',default:'',
        },
        role:{
          nama_kolom:'role',default:'user',
        },
        password:{
          nama_kolom:'password',input:'password',label:'Password Baru'
        },
        passwordconf:{
          nama_kolom:'passwordconf',input:'password',label:'Konfirmasi Password'
        }
      },
			empty:false,
			loading:false,
			dataId:-1,
      formValue:{},
      viewValue:{},
		};
	},
	computed:{
		...mapGetters({
			user: 'pengguna/pengguna',
			loggedUser: 'loggedUser',
			// pegawai: 'data/employee',
		}),
	},
	methods: {
		getInitial(){
			this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          this.fields = res
          this.formKey++
          this.saving = false
        });
		}
		
	},
	created() {
		this.$store.dispatch('pengguna/show', {id:this.loggedUser.id} );
		this.dataId = this.loggedUser.id;
		this.getInitial()
		// this.$store.dispatch('data/getEmployee', {id:this.loggedUser.id_pegawai} );
	},
}
</script>

<style lang="sass" scoped>
.page
	width: 50%
.icon 
	font-size: 80px
	height: 80px
	width: 80px
	margin-bottom: 10px
.table-event-detail
	td 
		padding: 10px 10px
		height: 30px
		vertical-align: top
		&:last-child
			min-width: 30px

</style>