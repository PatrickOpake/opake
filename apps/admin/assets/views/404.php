<?php include('_partial/header.php'); ?>

	<style>
		body {
			background-color: #58a7c8;
			background-size: cover;
			font-family: "Avenir Next","Helvetica Neue",Helvetica,Arial,sans-serif
			height: 100%;
		}

		.error-page-body {
			position: relative;
		}

		.message-box {
			position: fixed;
			width: 400px;
			height: 450px;
			z-index: 15;
			top: 50%;
			left: 50%;
			margin: -225px 0 0 -200px;
			text-align: center;
		}

		.message-box .logo {
			margin-bottom: 20px;
			text-align: center;
		}

		.message-box .logo img {
			vertical-align: middle;
			display: inline-block;
		}

		.message-box .message {
			color: #ffffff;
			font-size: 22px;
			text-align: center;
		}

		.opake-logo {
			display: inline-block;
			margin-top: 20px;
			text-align: center;
			height: 50px;
			position: relative;
			font-size: 18px;
			line-height: 20px;
		}

		.opake-logo .logo-title {
			color: #ffffff;
			font-size: 11px;
			font-weight: bold;
			letter-spacing: 2px;
			margin-left: 2px;
			line-height: 1.5;
		}

		.opake-logo:after {
			color: #ffffff;
			font-family: Arial, sans-serif;
			content: "\00ae";
			font-size: 18px;
			font-weight: normal;
			position: absolute;
			top: 5px;
			right: -15px;
		}

		.site-breadcrumbs {
			display: none;
		}

		.site-header:after {
			display: none;
		}

	</style>

<div class="error-page-body">
	<div class="message-box">
		<div class="logo">
			<img src="/common/i/heart.png">
		</div>
		<div class="message">
			We try to be transparent,<br>
			but when we're working on the site<br>
			it's really OPAKE.
		</div>
		<div class="opake-logo">
			<img src="/common/i/logo.png">
			<div class="logo-title">OPAKE</div>
		</div>
	</div>
</div>
<?php $disableSiteFooter = true; ?>
<?php include('_partial/footer.php'); ?>