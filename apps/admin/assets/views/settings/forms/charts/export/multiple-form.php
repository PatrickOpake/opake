<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/editor.css' type='text/css' rel='stylesheet'/>
</head>
<body>
		<?php foreach ($dataSets as $dataSet): ?>
			<?php
				$doc = $dataSet['doc'];
				$org = $dataSet['org'];
				$case = (isset($dataSet['case'])) ? $dataSet['case'] : null;
				$ownText = $dataSet['ownText'];
			?>
			<div style="page-break-before:always; <?= (isset($dataSet['pixelWidth']) ? 'width: ' . $dataSet['pixelWidth'] . 'px' : '')  ?>" class="export-chart">
				<?php if ($doc->include_header):?>
					<?php include('_header.php'); ?>
				<?php endif;?>

				<div class="opk-editor-regular-content">
					<?= $ownText ?>
				</div>
			</div>
		<?php endforeach ?>
</body>
</html>
