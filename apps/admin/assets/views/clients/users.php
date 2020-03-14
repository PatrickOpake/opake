<div class="content-block">
	<filters-panel-with-params params="<?= $_(json_encode($filters)) ?>">
		<div class="data-row">
			<label>User</label>
			<opk-select ng-model="flt.params.user"
						options="item for item in source.getList('/clients/ajax/user/', $query)"></opk-select>
		</div>
		<div class="data-row">
			<label>Organization</label>
			<opk-select ng-model="flt.params.org"
						options="item.name as item.name for item in source.getOrganizations()"></opk-select>
		</div>
	</filters-panel-with-params>

	<div class="list-control">
		<a class='btn btn-success' href='/clients/create/'>Create New Organization</a>
	</div>

	<?php if (sizeof($list)) { ?>
		<table class='opake'>
			<thead>
			<tr>
				<th></th>
				<th>Organization</th>
				<th>Full Name</th>
				<th>Email</th>
				<th>Site</th>
				<th class="text-center">Status</th>
				<th>First Login Date</th>
				<th>Last Login Date</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($list as $user): ?>
				<tr>
					<td><?= $user->id ?></td>
					<td><a href='/clients/view/<?= $user->organization->id ?>/'><?= $user->organization->name ?></a>
					</td>
					<td>
						<a href='<?= sprintf('/clients/users/%d/view/%d', $user->organization->id, $user->id) ?>'><?= $user->getFullName() ?></a>
					</td>
					<td><?= $user->getEmail() ?></td>
					<td>
						<a href='<?= sprintf('/clients/sites/%d/view/%d', $user->organization->id, $user->getDefaultSite()->id) ?>'><?= $user->getDefaultSite()->name ?></a>
					</td>
					<td class="text-center"><?= $user->getStatus() ?></td>
					<td><?= $_date_time($user->time_first_login) ?></td>
					<td><?= $_date_time($user->time_last_login) ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
		<?= $pages; ?>

	<?php } else { ?>
		<h4>No users found</h4>
	<?php } ?>
</div>