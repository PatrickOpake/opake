<div class='row'>
	<div class='col-sm-3'>
		<select size="30" class="log_list">
			<?php
			foreach ($logs as $log) {
				echo '<option>', $log, '</option>';
			}
			?>
		</select>
		<br/>
		<button class="log_clear">Clear</button>
	</div>
	<div class='col-sm-9'>
		<div class="log_view"></div>
	</div>
</div>