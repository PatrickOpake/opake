<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="staff-pref-card-export">
			<div>
				<span class="title">PREFERENCE CARD</span><span class="template-name"> - <?= $card->name; ?></span>
			</div>
			<div class="subtitle"><span class="bold-text">Surgeon:</span> <?= ((isset($user)) ? $user->getFullName() : $card->user->getFullName()) ?> </div>
			<div class="subtitle">
				<span class="bold-text">Procedure:</span>
				<?php
				$case_types = $card->case_types->find_all()->as_array();
				foreach ($case_types as $key => $item): ?>
					<?= $item->code; ?> - <?= $item->name; ?><?= count($case_types) - 1 === $key ? '' : ', '?>
				<?php endforeach; ?>
			</div>
			<div class="subtitle">
				<span class="bold-text">Patient Name:</span>
				<?php if (isset($case)): ?>
					<span class="value"><?= $case->registration->last_name ?>, <?= $case->registration->first_name ?></span>
				<?php endif ?>
			</div>
			<div class="subtitle">
				<span class="bold-text">Age/Sex:</span>
				<?php if (isset($case)): ?>
					<span class="value"><?= $case->registration->getAge() ?>/<?= $case->registration->getGender() ?></span>
				<?php endif ?>
			</div>
			<div class="subtitle">
				<span class="bold-text">DOB:</span>
				<?php if (isset($case)): ?>
					<span class="value"><?= (($case->registration->dob) ? \Opake\Helper\TimeFormat::getDate($case->registration->dob) : '') ?></span>
				<?php endif ?>
			</div>
			<div class="subtitle">
				<span class="bold-text">MRN:</span>
				<?php if (isset($case)): ?>
					<span class="value"><?= (($case->registration->patient) ? $case->registration->patient->getFullMrn() : '') ?></span>
				<?php endif ?>
			</div>
			<div class="subtitle">
				<span class="bold-text">DOS:</span>
				<?php if (isset($case)): ?>
					<span class="value"><?= \Opake\Helper\TimeFormat::getDate($case->time_start) ?></span>
				<?php endif ?>
			</div>

			<div class="subtitle notes-title"><span class="bold-text">Instructions / Notes:</span></div>
			<table class="notes">
				<tbody>
				<?php foreach ($notes as $note): ?>
					<tr>
						<td class="square-td"><div class="square"></div></td>
						<td><span class="bold-text"><?= $note->name ?>:</span> <?= $note->text ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<div class="subtitle items-title"><span class="bold-text">Items:</span></div>
			<table class="items">
				<thead>
				<tr>
					<td class="stage">Stage</td>
					<td class="number">Item #</td>
					<td class="name">Item Name</td>
					<td class="manufacturer">Manufacturer</td>
					<td class="description">Description</td>
					<td class="default-qty text-center">Default Qty</td>
					<td class="uom">Unit of Measure</td>
					<td class="actual-use text-center">Actual Use</td>
				</tr>
				</thead>
			</table>
			<?php foreach ($stages_with_items as $stageWithItems): ?>
				<?php if (count($stageWithItems['items'])): ?>
					<div class="items-for-stage">
						<div class="stage-title"><?= $stageWithItems['stage_name'] ?></div>
						<table class="items">
							<tbody>
							<?php foreach ($stageWithItems['items'] as $item): ?>
								<tr>
									<td class="stage"></td>
									<?php if ($item->inventory_id) { ?>
										<td class="number"><?= $item->inventory->item_number ?></td>
										<td class="name"><?= $item->inventory->name ?></td>
										<td class="manufacturer"><?= $item->inventory->manufacturer->name ?></td>
										<td class="description"><?= $item->inventory->desc ?></td>
									<?php } else { ?>
										<td class="no-inventory" colspan="4">Item number does not exist</td>
									<?php }; ?>
									<td class="default-qty text-center"><?php if ($item->default_qty) { echo $item->default_qty; }; ?></td>
									<td class="uom"><?= $item->inventory_id ? $item->inventory->uom->name : '' ?></td>
									<td class="actual-use text-center">_____</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</body>
</html>