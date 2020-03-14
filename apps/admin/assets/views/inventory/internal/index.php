
<div class="content-block">

	<filters-panel-with-params params="<?= $_(json_encode($filters, JSON_NUMERIC_CHECK)) ?>" ng-cloak>
		<div class="data-row">
			<label>Keyword</label>
			<input type="text" ng-model="flt.params.name" class='form-control input-sm' placeholder='Keyword Search' />
		</div>
		<div class="data-row">
			<label>Type</label>
			<opk-select ng-model="flt.params.type" options="item for item in source.getList('/inventory/ajax/1/types', $query)"></opk-select>
		</div>
		<div class="data-row">
			<label>Manufacturer</label>
			<opk-select ng-model="flt.params.manf" options="item.name as item.name for item in source.getVendors($query, 'manf')"></opk-select>
		</div>
		<div class="data-row">
			<label>Site</label>
			<opk-select ng-model="flt.params.site" options="item for item in source.getList('/clients/ajax/site/', $query)"></opk-select>
		</div>
		<div class="data-row">
			<label>Room</label>
			<opk-select ng-model="flt.params.location" options="item.name as item.name for item in source.getStorage()"></opk-select>
		</div>
	</filters-panel-with-params>

	<?php if (sizeof($list)) { ?>

		<table class='opake'>
			<thead>
				<tr>
					<th>Item ID</th>
					<th>Item Name</th>
					<th>Description</th>
					<th>Type</th>
					<th>Manufacturer</th>
					<th class='text-center'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($list as $item) { ?>
					<tr>
						<td><?= $item->id; ?></td>
						<td><a href='/inventory/<?= $item->organization_id; ?>/view/<?= $item->id; ?>'><?= $item->name ?></a></td>
						<td><?= $item->desc ?></td>
						<td><?= $item->type ?></td>
						<td><?= $item->manufacturer->name ?></td>
						<td class='text-center'><?= $item->status ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?= $pages; ?>
	<?php } else { ?>
		<h4>No inventory found</h4>
	<?php } ?>
</div>