<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
		<style type="text/css">
		</style>
	</head>
	<body class="operative-report-export-footer">
		<div class="op-report-footer">
			<table>
				<tr>
					<td>
			    	<?php if ($op_report->time_submitted) : ?>
						Date Submitted: <?= $_date_time($op_report->time_submitted); ?>
				    	<?= date('T'); ?>
			    	<?php endif ?>
					</td>
					<td>
			    	<?php if ($op_report->time_start) : ?>
							Date Created: <?= $_date_time($op_report->time_start); ?>
				    		<?= date('T'); ?>
			    	<?php endif ?>
					</td>
					<td>
						Date Printed: <?= \Opake\Helper\TimeFormat::getDateTime('now'); ?>
			    		<?= date('T'); ?>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>