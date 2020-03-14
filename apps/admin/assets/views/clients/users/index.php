<div ng-controller="UserListCrtl as listVm" ng-init="listVm.init()" ng-cloak>
	<div class="content-block user-list">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>User</label>
				<input type="text" ng-model="listVm.search_params.user" class='form-control input-sm' placeholder='Name or email' />
			</div>
			<div class="data-row">
				<label>Site</label>
				<opk-select ng-model="listVm.search_params.site" options="item.name as item.name for item in source.getSites()"></opk-select>
			</div>
		</filters-panel>

		<?php if ($_check_access('user', 'create')) { ?>
			<div class="list-control">
				<a class='btn btn-success' href='/clients/users/{{ ::org_id }}/create/'>Create New User</a>
			</div>
		<?php } ?>

		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th></th>
					<th sort="full_name">Full Name</th>
					<th sort="username">Username</th>
					<th>Site</th>
					<th class="text-center" sort="status">Status</th>
					<th sort="time_first_login">First Login Date</th>
					<th sort="time_last_login">Last Login Date</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in listVm.items">
					<td><img src="{{ ::item.image }}" class="user-tiny-image" ></td>
					<?php if ($_check_access('user', 'edit')) { ?>
						<td><a href='/clients/users/{{ ::org_id }}/view/{{ item.id }}'>{{ ::item.full_name }}</a></td>
					<?php } else { ?>
						<td>{{ ::item.full_name }}</td>
					<?php } ?>
					<td>{{ ::item.username }}</td>
					<td>
						<span ng-repeat="site in item.sites">
							<a href="/clients/sites/{{ ::org_id }}/view/{{::site.id}}/">{{::site.name}}</a>
						</span>
					</td>
					<td class="text-center">{{ ::item.status }}</td>
					<td>{{ ::item.time_first_login | date:'M/d/yyyy h:mm a' }}</td>
					<td>{{ ::item.time_last_login | date:'M/d/yyyy h:mm a' }}</td>
				</tr>

			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No users found</h4>
	</div>
</div>
