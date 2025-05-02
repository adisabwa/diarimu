<template>
	<div id="account-page">
		<el-card class="page m-auto mt-10 bg-white pt-40 pb-10">
			<div class="center-text flex flex-col items-center">
				<el-avatar class="icon" icon="el-icon-user-solid" size="large"></el-avatar>
				<div class="text-xl my-3 font-bold">{{user.nama}}</div>
				<el-divider ontent-position="left">
					<b>Data Akun</b>
				</el-divider>
				<table class="table-event-detail w-[80%]">
					<tr>
						<td width="130">
							<b>Username</b>
						</td>
						<td class="w-[3px]">:</td>
						<td v-if="edit.username">
							<el-form>
								<el-form-item :error="errors['username']">
									<el-input v-model="form.username" size="default"/>
								</el-form-item>
							</el-form>
						</td>
						<td v-else>
							{{ user.username }}
						</td>
						<td >
							<el-tooltip :content="edit.username ? 'Simpan data' : 'Edit data'" placement="bottom" effect="light">
								<el-button v-if="edit.username"
									type="success" :disabled="saving" @click="saveData('username')" circle size="default">
									<icons icon="mdi:check" class="m-0"/>
								</el-button>
								<el-button v-else
									type="primary" :disabled="saving" @click="editData('username')" circle size="default">
									<icons icon="mdi:edit" class="m-0"/>
								</el-button>
							</el-tooltip>
						</td>
					</tr>
					<tr>
						<td>
							<b>Password</b>
						</td>
						<td >:</td>
						<td v-if="edit.password">
							<el-form>
								<el-form-item :error="errors['old_password']">
									<el-input v-model="form.old_password" size="default" placeholder="Password lama" 
										type="password" show-password class="mb-2"/>
								</el-form-item>
								<el-form-item :error="errors['password']">
									<el-input v-model="form.password" size="default" placeholder="Password baru" 
										type="password" show-password class="mb-2" />
								</el-form-item>
								<el-form-item :error="errors['passconf']">
								<el-input v-model="form.passconf" size="default" placeholder="Masukkan ulang Password baru" 
									type="password" show-password />
								</el-form-item>
							</el-form>
						</td>
						<td v-else>
							*******
						</td>
						<td >
							<el-tooltip :content="edit.password ? 'Simpan data' : 'Edit data'" placement="bottom" effect="light">
								<el-button v-if="edit.password"
									type="success" :disabled="saving" @click="saveData('password')" circle size="default">
									<icons icon="mdi:check" class="m-0"/>
								</el-button>
								<el-button v-else
									type="primary" :disabled="saving" @click="editData('password')" circle size="default">
									<icons icon="mdi:edit" class="m-0"/>
								</el-button>
								</el-tooltip>
						</td>
					</tr>
					<tr>
						<td>
							<b>Akses Aplikasi</b>
						</td>
						<td>:</td>
						<td>
							{{ ucFirst(user.role) }}
						</td>
						<td></td>
					</tr>
					<tr>
						<td> <b>Email</b> </td>
						<td>:</td>
						<td>
							{{ user.email }}
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</el-card>
	</div>
</template>

<script>

import { mapGetters } from 'vuex';

export default {
	name: 'account-page',
	data: function() {
		return {
			saving: false,
			edit: {
				username: false,
				password: false,
				pegawai: false,
			},
			form: {
				username: '',
				password: '',
				passconf: '',
				old_password: '',
				pegawai: [],
			},
			errors: {},

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
		editData(key) {
			this.edit[key] = true;
			if (key != 'password')
				this.form[key] = this.user[key];
		},
		saveData(key) {
			this.saving = true;
			var id = this.user.id;
			var data = {};
			data[key] = this.form[key] ;
			if (key == 'password') {
				data['old_password'] = this.form.old_password;
				data['passconf'] = this.form.passconf;
			}
			data.id = id

			 this.$store.dispatch('pengguna/store', data)
				.then(res => {
					this.saving = false;
					this.edit[key] = false;
					this.$store.dispatch('pengguna/get',{id:id});
				}).catch(err => {
					var res = err.response;
					var code = res.status;
					
					this.saving = false;

					if (code == '422') {
						// Populating error message
						this.errors = res.data.message;
						this.$notify.error({
							title: 'Gagal',
							message: 'Data belum benar',
							position: 'bottom-right'
						});
					} else {
						this.show = false;
						this.$notify.error({
							title: 'Gagal',
							message: 'Tidak dapat mengganti data',
							position: 'bottom-right'
						});
					}
				});
		},
		
	},
	created() {
		this.$store.dispatch('pengguna/get', {id:this.loggedUser.id} );
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