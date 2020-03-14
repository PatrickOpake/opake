<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/site.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="card-slide">
			<div class="card-slide--head">
				<div class="type"><?= $title ?></div>
			</div>
			<div class="card-slide--content">
				<div class="card-slide--list">
					<div class="title">Checklist</div>
					<?php if ($notes) { ?>
						<div class="card-slide--container">
							<?php foreach ($notes as $note) { ?>
								<div class="checklist-item card-slide--item">
									<div class="icon"><i class="icon-note"></i></div>
									<div class="pre-wrap"><?= $note->text ?></div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<?php if (isset($item_groups)) { ?>
					<?php foreach ($item_groups as $key => $group) { ?>
						<div class="card-slide--list">
							<div class="title"><?= $key ?>:</div>
							<div class="card-slide--container">
								<?php foreach ($group as $item) { ?>
									<div class="checklist-item card-slide--item">
										<div class="icon"><img src="<?= $item->inventory->getImage(true, 'tiny') ?>" alt="Image" /></div>
										<div><?= $item->inventory->name ?></div>
										<div class="pull-right"><?= $item->quantity ?></div>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				<?php } elseif ($items) { ?>
					<div class="card-slide--list">
						<div class="title">Items:</div>
						<div class="card-slide--container">
							<?php foreach ($items as $item) { ?>
								<div class="checklist-item card-slide--item">
									<div class="icon"><img src="<?= $item->inventory->getImage(true, 'tiny') ?>" alt="Image" /></div>
									<div><?= $item->inventory->name ?></div>
									<div class="pull-right"><?= $item->quantity ?></div>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	<body>
</html>