<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST'] ?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<h4><?= $org->name; ?></h4>
		<div><?= $org->address; ?></div>
		<?php
		$contact = [];
		if ($org->contact_name) { $contact[] = $org->contact_name; }
		if ($org->contact_phone) { $contact[] = $org->contact_phone; }
		if ($org->contact_email) { $contact[] = $org->contact_email; }
		echo $contact ? ('<div>' . implode(', ', $contact) . '</div>') : '';
		?>
		<div><?= $vendor->acc_number; ?></div>

		<br />
		<h4>Order Details</h4>
		<table class="border">
			<thead>
				<tr>
					<th>Item #</th>
					<th>Description</th>
					<th>Total Qty</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $item) { ?>
					<tr>
						<td><?= $item->supply_chain->device_id; ?></td>
						<td><?= $item->inventory->desc; ?></td>
						<td><?= $item->count ? $item->count : 0; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<body>
</html>