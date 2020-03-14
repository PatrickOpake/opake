<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="export-chart" <?= (isset($pixelWidth) ? 'style="width: ' . $pixelWidth . 'px"' : '')  ?>>
			<?php if($doc->include_header):?>
				<?php include('_header.php'); ?>
			<?php endif;?>
		</div>
	</body>
</html>
