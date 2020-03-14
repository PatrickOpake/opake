<div class="content-block">
	<table class='opake'>
		<thead>
			<tr>
				<th>Code</th>
				<th>Name</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($list as $item) { ?>
				<tr>
					<td><?= $item->code ?></td>
					<td><?= $item->name ?></td>
					<td><?= $item->active ? 'active' : 'inactive' ?></td>
					<td><a href="/settings/fields/activity/<?= $item->id ?>?model=<?= $model_name ?>" onclick="return confirm('Are you sure?')">
						<?= $item->active === '1' ? 'Deactive' : 'Active' ?>
					</a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?= $pages; ?>

	<form method='POST' action="">
		<span>
			<label>Code:</label>
			<input type="text" name="code" value="<?= $model->code ?>" />
		</span>&nbsp;&nbsp;
		<span>
			<label>Name:</label>
			<input type="text" name="name" value="<?= $model->name ?>" />
		</span>&nbsp;&nbsp;
		<input type="submit" value="Add" />
	</form>
</div>