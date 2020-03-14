<div ng-controller="SettingsHCPCYearViewCtrl as listVm" ng-init="listVm.init(<?= $year_id ?>)" class="content-block" ng-cloak>
	<div class="row cpt-codes-header">
		<div class="col-sm-3">
			<a href="/settings/databases/hcpc" class="back"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
		</div>
		<div class="col-sm-6">{{ listVm.year }}</div>
	</div>
	<errors src="listVm.errors"></errors>
	<div show-loading-list="listVm.isLoading">
		<table class="opake">
			<thead>
			<tr>
				<th>HCPC</th>
				<th>SEQNUM</th>
				<th>RECID</th>
				<th>Long Description</th>
				<th>Short Description</th>
				<th>Price</th>
				<th>ABU</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>{{::item.code}}</td>
				<td>{{::item.seqnum}}</td>
				<td>{{::item.recid}}</td>
				<td>{{::item.long_description}}</td>
				<td>{{::item.short_description}}</td>
				<td>{{::item.price}}</td>
				<td>{{::item.abu}}</td>
			</tr>
			</tbody>
		</table>
		<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
	</div>
</div>