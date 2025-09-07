<!DOCTYPE html>
<html>
<head>
	<title>Ashoi-Mu</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
	<style>
	* {
		margin: 0;
		padding: 0;
	}
	body {
		background-color: #e3b796;
		font-family: 'Roboto';
		font-size: 14px;
	}
	.wrapper {
		height: 100%;
		margin: 0 auto 0 auto;
		max-width: 600px;
		background-color: #fff;
		box-sizing: border-box;
		border-radius: 2px;
	}

	.content {
		padding: 30px;
	}

	p {
		text-align: justify;
		margin-bottom: 15px;
		line-height: 1.5;
	}
	.divider {
		height: 1px;
		background: #efefef;
		margin: 20px 0;
	}
	.note {
		margin-top: 15px;
		text-align: center;
		color: #111;
	}
	.logo{
		height: fit-content;
		margin: 15px auto;
		text-align: center;
	}
	img {
	  width: 70px;
	  height: 70px;
	  margin: 5px auto;
	}
	.top, .bottom {
		margin: 0px auto;
		width: 100%;
		height: 50px;
		background-color: #2e7980;
	}
	.top {
		margin-top: 15px;
	}
	.bottom {
		margin-bottom: 15px;
	}
	.btn-link {
		display: inline-block;
		vertical-align: middle;
		text-decoration: none !important;
		width: 100px;
		height: auto;
		background-color: #00a8db;
		color: #ffffff !important;
		font-weight: bold;
		padding: 10px 15px;
		border-radius: 20px;
		cursor: pointer;
		letter-spacing: 1px;
	}
	h3 {
		line-height: 1.6;
		font-size: 15px;
	}
	.table-tenaga tr, td, thead, th{
		padding: 5px;
		border: 1px solid #000;
	}
	table {
		width: 100%;
		font-size: inherit;
		border-collapse: collapse;
	}
	</style>
</head>
<body>
	<div class="wrapper">
		<div class="top"></div>
		<div class="content">
			<div class="logo">
				<img src="<?php echo base_url();?>/assets/images/logo-kecil.png" alt="Logo">
				<h1 align="center"><b>Ashoi-Mu</b></h1>
				<h2 align="center"><b>Sistem Pencatatan Ibadah PDM Kendal</b></h2>
			</div>
			<div class="divider"></div>
			<h3 align="center">Reset Password</h3>
			<br>
			<p>Anda mengajukan permohonan reset password. Untuk reset password silahkan klik link di bawah ini : 
        <br/>
      <b><a href="<?= site_url()?>/auth/reset_password?id=<?=md5($user->id)?>">
        <?= site_url()?>/auth/reset_password?id=<?=md5($user->id)?>
      </a></b></p>
			<p>Untuk password baru anda adalah : 
        <b><?= substr($user->password, 0, 5) ?></b>
      </p>
		</div>
		<div class="bottom"></div>
	</div>
		<p class="note">
			<small>Fakultas Teknik Universitas Gadjah Mada</small>
		</p>
</body>
</html>
