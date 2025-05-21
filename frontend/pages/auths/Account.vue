<template>
	<div id="account-page">
		<el-card class="mx-2 bg-white pt-[50px] pb-10">
			<div class="relative">
				<div class="h-[120px] w-[120px] mx-auto mb-6
          rounded-full overflow-hidden relative
					flex items-center justify-center
					border-3 border-solid border-teal-700"
					@click="showEdit = true;showColumns=['photo']">
          <div v-if="!isEmpty(viewValue.photo)"
            class="w-full h-full bg-cover bg-top"
            :style="`background-image:url('${viewValue.photo}')`"
            />
					<icons v-else 
            icon="mdi:user" class="mr-0 text-teal-700 text-[100px]"/>
				</div>
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
					:show-columns="showColumns"
					href="data/anggota/store"
					href-get="data/anggota/get"
					@saved="saving = false; showEdit = false;
            $store.dispatch('resetAccount')"  
					@error="saving=false"
          :pass-columns="['password','passwordconf','role']"
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
            :pass-columns="['photo','password','passwordconf','role']"
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
						@click="showEdit = true;showColumns=[]">
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
import { unset } from 'lodash';

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
			showColumns:[],
			fields:{},
			empty:false,
			loading:false,
			dataId:-1,
      formValue:{},
      viewValue:{},
		};
	},
	computed:{
		...mapGetters({
			loggedUser: 'loggedUser',
			// pegawai: 'data/employee',
		}),
	},
	methods: {
		getInitial(){
			this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          delete res.password
          delete res.passwordconf
          this.fields = res
          this.formKey++
          this.saving = false
        });
		}
		
	},
	created() {
		this.dataId = this.loggedUser.id_anggota;
		console.log(this.dataId)
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