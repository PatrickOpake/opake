<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/editor.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="export-chart preview-chart">
			<?php if(isset($doc['include_header']) && $doc['include_header']):?>
				<table class="header-form-template">
					<tr>
						<td class="border-right">
							<table>
								<tr><td><?= $org->name?></td></tr>
								<tr><td><?= $org->address?></td></tr>
								<tr><td><?= $_phone($org->contact_phone)?></td></tr>
							</table>
						</td>
					</tr>
				</table>
			<?php endif;?>

			<div class="opk-editor-regular-content">
				<?= isset($doc['own_text']) ? $doc['own_text'] : '' ?>
			</div>
		</div>
	</body>
</html>
