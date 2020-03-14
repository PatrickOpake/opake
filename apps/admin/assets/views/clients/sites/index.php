<div ng-controller="ClientProfileCtrl as profileVm" ng-init="profileVm.init(<?= $org->id() ?>)">

	<div ng-if="!profileVm.isShowForm" class="profile-page organization-info">
		<div class="block-panel">
			<div ng-controller="SiteListCrtl as listVm" ng-init="listVm.search_params.logged_user_sites = true;" ng-cloak>

				<div class="heading-row clearfix">
					<div class="headline-container">
						<div class="headline-title">
							Sites
						</div>
					</div>
					<div class="controls">
						<?php if ($_check_access('sites', 'create')) { ?>
							<a class="btn btn-success" href='/clients/sites/<?= $org->id() ?>/create/'>Create New Site</a>
						<?php } ?>
					</div>
				</div>

				<div class="table-wrap">
					<table class="opake" ng-if="listVm.items.length">
						<thead sorter="listVm.search_params" callback="listVm.search()">
						<tr>
							<th sort="name">Site Name</th>
							<th sort="description">Description</th>
							<th sort="departments_count" class="text-center"># Departments</th>
							<th sort="users_count" class="text-center"># Users</th>
						</tr>
						</thead>
						<tbody>
						<tr ng-repeat="item in listVm.items">
							<td>
								<a href='/clients/sites/{{ ::org_id }}/view/{{ ::item.id }}'>{{ ::item.name }}</a>
							</td>
							<td>{{ ::item.description }}</td>
							<td class="text-center">{{ ::item.departments_count }}</td>
							<td class="text-center">{{ ::item.users_count }}</td>
						</tr>
						</tbody>
					</table>
				</div>
				<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
				<h4 ng-if="listVm.items && !listVm.items.length">No sites found</h4>
			</div>

		</div>
	</div>

</div>
