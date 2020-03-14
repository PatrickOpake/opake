<div ng-controller="OrganizationListCrtl as listVm" class="content-block" ng-cloak>
	<filters-panel ctrl="listVm">
		<div class="data-row">
			<label>Organization</label>
			<opk-select ng-model="listVm.search_params.organization"
						options="item.name as item.name for item in source.getOrganizations()"></opk-select>
		</div>
		<div class="data-row">
			<label>User</label>
			<opk-select ng-model="listVm.search_params.user" placeholder="Name or email"
						options="item for item in source.getList('/clients/ajax/user/', $query)"></opk-select>
		</div>
		<div class="data-row">
			<label class='dark'><input type='checkbox' ng-model="listVm.search_params.active"/> Active only</label>
		</div>
	</filters-panel>

	<div class="list-control">
		<a class='btn btn-success' href='/clients/create/'>Create New Organization</a>
	</div>

	<div class="table-wrap">
		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th sort="name">Organization Name</th>
				<th class="text-center" sort="sites_count"># Sites</th>
				<th class="text-center" sort="users_count"># Users</th>
				<th sort="time_create">Date Created</th>
				<th class="text-center" sort="status">Status</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td><a href='/profiles/clients/view/{{ ::item.id }}'>{{ ::item.name }}</a></td>
				<td class="text-center">{{ ::item.sites_count }}</td>
				<td class="text-center">{{ ::item.users_count }}</td>
				<td>{{ ::item.time_create | date:'M/d/yyyy h:mm a' }}</td>
				<td class="text-center">{{ ::item.status }}</td>
			</tr>
			</tbody>
		</table>
	</div>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		   callback="listVm.search()"></pages>
	<h4 ng-if="listVm.items && !listVm.items.length">No organizations found</h4>
</div>
