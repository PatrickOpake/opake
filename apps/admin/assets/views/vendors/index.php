<div ng-controller="VendorListCrtl as listVm" ng-cloak>
	<div class="content-block">
		<ng-include src="view.get('vendors/filters.html')"></ng-include>

		<div class="list-control" ng-if="org_id">
			<a class='btn btn-success' href='/vendors/{{::org_id}}/create/'>Create Vendor</a>
		</div>

		<table class="opake" ng-if="listVm.items.length">
			<thead>
			<tr>
				<th class="narrow"></th>
				<th>Vendor</th>
				<th>Vendor Type</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td><img src="{{ ::item.image }}"/></td>
				<td><a href="/vendors/{{::item.organization_id}}/view/{{ ::item.id }}">{{ ::item.name }}</a></td>
				<td>{{ ::item.getTypes() }}</td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No vendors found</h4>
	</div>
</div>