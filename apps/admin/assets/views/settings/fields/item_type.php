<div ng-controller="SettingItemTypeCrtl as itemVm" class="content-block">
	<errors src="itemVm.errors"></errors>
	<table class='opake'>
		<thead>
			<tr>
				<th>Name</th>
				<th>Image</th>
				<?= isset($model->active) ? '<th>Status</th>' : '' ?>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($list as $item) { ?>
				<tr>
					<td><?= $item->name ?></td>
					<td ng-form name="uploadForm"><?= $item->getImage('default') ? '<img src="' . $item->getImage('default') . '" alt="' . $item->name . '">' : '' ?>
						<a href="" select-file on-select="itemVm.reupload(files, <?=$item->id?>)"> Reupload
							<input type="file" name="image" />
							<input type="hidden" name="item_id" value="<?=$item->id?>" />
						</button>
					</td>
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

	<br />
	<form method='POST' action="">
		<div class="row">
			<div class='col-sm-3 upload-logo'>
					<img src='<?= $model->getImage('default') ? $model->getImage('default') : '/i/default-logo.png' ?>' data-size='default'/>
					<a class='logo upload' href='#'>Upload</a>
					<input id='fileupload' type='file' name='file' data-target='#logoupload' />
					<input id='logoupload' data-target='form .upload-logo img' name='image_id' value='<?= $model->image_id ?>' type='hidden'/>
			</div>
			<div class='col-sm-4'>
				<span>
					<label>Name:</label>
					<input type="text" name="name" value="<?= $model->name ?>" />
				</span>
				<br /><br /><br />
				<input type="submit" value="Add" />
			</div>
		</div>
	</form>
</div>