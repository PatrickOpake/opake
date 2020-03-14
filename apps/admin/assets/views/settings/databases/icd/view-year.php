<div ng-controller="SettingsICDYearViewCtrl as listVm" ng-init="listVm.init(<?= $year_id ?>)" class="content-block" ng-cloak>
	<div class="row cpt-codes-header">
		<div class="col-sm-3">
			<a href="/settings/databases/icd" class="back"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
		</div>
		<div class="col-sm-6">{{ listVm.year }}</div>
	</div>
	<table class="opake">
		<thead>
		<tr>
			<th>Code</th>
			<th>Name</th>
			<th>Status</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
		<tr ng-repeat="item in listVm.items">
			<td>{{::item.code}}</td>
			<td>{{::item.desc}}</td>
			<td>
				<span ng-if="item.active == 1">active</span>
				<span ng-if="item.active == 0">inactive</span>
			</td>
			<td>
				<a ng-if="item.active == 1" href="" ng-click="listVm.deactivate(item.id)">Deactivate</a>
				<a ng-if="item.active == 0" href="" ng-click="listVm.activate(item.id)">Activate</a>
			</td>
		</tr>
		</tbody>
	</table>
	<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
</div>