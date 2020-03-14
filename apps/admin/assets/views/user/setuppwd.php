<div class='container setuppwd-page'>
	<?php if ($this->pixie->session->get('flash.message')) { ?>
		<?= $this->getMessageHtml() ?>
		<a href="/" class='btn btn-lg btn-success'>Ok</a>
	<?php } else { ?>

		<?= $this->getErrorsHtml() ?>

		<form class='form-horizontal enter-submit' action='/user/setuppwd/?hash=<?= $hash ?>' method='POST'>
			<span class='help-block'>
				Password must be at least 8 characters long and include at least one capital
				letter, one lowercase letter, one special (symbol) character.
			</span>
			<div class='input-group input-group-lg'>
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-lock"></span>
			</span>
				<input class='form-control' name='password' type='password' placeholder="Enter password"/>
			</div>

			<div class='input-group input-group-lg'>
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-lock"></span>
			</span>
				<input class='form-control' name='password_confirm' type='password' placeholder="Re-enter password"/>
			</div>

			<div class='input-group'>
				<input class='btn btn-lg btn-success' type='submit' value='Set password'/>
			</div>
		</form>

	<?php } ?>
</div>
