<div class='container restore-page'>
	<?php if ($this->pixie->session->get('flash.message')) { ?>
		<?= $this->getMessageHtml() ?>
		<a href="/" class='btn btn-lg btn-success'>Ok</a>
	<?php } else { ?>

		<form class='form-horizontal enter-submit' role='form' method='POST' action="">

			<h2>Password restore</h2>

			<?= $this->getErrorsHtml() ?>

			<div class="input-group input-group-lg">
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-envelope"></span>
			</span>
				<input class='form-control' name='email' type='text' value='<?= isset($email) ? $email : '' ?>'
					   placeholder="Email address"/>
			</div>

			<div class="input-group">
				<input class='btn btn-lg btn-success' type='submit' value='Restore'/>
			</div>
		</form>

	<?php } ?>
</div>