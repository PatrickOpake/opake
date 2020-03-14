<?php if (sizeof($list)) { ?>
	<table class='opake'>
		<thead>
		<tr>
			<th>Opake ID</th>
			<th>Device</th>
			<th>Product description</th>
			<th><?= trim((($vendor->is_manf ? 'MMIS/' : '') . ($vendor->is_dist ? 'Distributor' : '')), '/'); ?> ID</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($list as $item) { ?>
			<tr>
				<td><?= $item->id ?></td>
				<td><a href='/inventory/<?= $item->organization_id; ?>/view/<?= $item->id; ?>'><?= $item->name; ?></a>
				</td>
				<td><?= $item->desc ?></td>
				<td><?php
					$ids = [];
					if ($vendor->is_manf && $vendor->id == $item->manf_id) {
						$ids[] = $item->mmis;
					}
					if (isset($item->device_id) && $item->device_id) {
						$ids[] = $item->device_id;
					}
					echo implode('<b> / </b>', $ids);
					?></td>
				<td><?= $item->status ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?= $pages; ?>
<?php } else { ?>
	<h4>No inventory found</h4>
<?php } ?>