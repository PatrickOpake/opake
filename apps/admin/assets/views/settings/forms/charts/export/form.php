<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/editor.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="export-chart" <?= (isset($pixelWidth) ? 'style="width: ' . $pixelWidth . 'px"' : '')  ?>>
			<?php if($doc->include_header):?>
				<?php include('_header.php'); ?>
			<?php endif;?>

			<div class="opk-editor-regular-content">
				<?= $ownText ?>
			</div>
		</div>
	</body>
</html>
