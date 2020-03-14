<div class="content-block">
	<table class='opake'>
		<thead>
			<tr>
				<th>Name</th>
				<?= isset($model->active) ? '<th>Status</th>' : '' ?>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($list as $item) { ?>
				<tr>
					<td><?= $item->name ?></td>
					<?php
					if (isset($item->active)) {
						echo '<td>', ($item->active ? 'active' : 'inactive'), '</td>';
					}
					?>
					<td><a href="/settings/fields/activity/<?= $item->id ?>?model=<?= $model_name ?>" onclick="return confirm('Are you sure?')">
						<?= isset($model->active) ? ($item->active === '1' ? 'Deactive' : 'Active') : 'Delete' ?>
					</a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?=$pages?>

	<form method='POST' action="">
		<input type="text" name="name" value="<?= $model->name ?>" />
		<input type="submit" value="Add" />
	</form>
</div>