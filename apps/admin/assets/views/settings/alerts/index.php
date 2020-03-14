<div ng-controller="SiteListCrtl as listVm" ng-init="listVm.search_params.logged_user_sites = true;" class="panel-data" ng-cloak>

	<h2 class="headline-title">Sites</h2>

	<div class="table-wrap">
		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th sort="name">Site Name</th>
				<th sort="description">Description</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>
					<a href='/settings/alerts/{{ ::org_id }}/view/{{ ::item.id }}'>{{ ::item.name }}</a>
				</td>
				<td>{{ ::item.description }}</td>
			</tr>
			</tbody>
		</table>
	</div>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
	<h4 ng-if="listVm.items && !listVm.items.length">No sites found</h4>
</div>