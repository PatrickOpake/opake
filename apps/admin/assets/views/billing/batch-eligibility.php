<div ng-controller="BatchEligibilityCtrl as batchVm" class="billing-reports" ng-cloak>
	<div class="content-block">
		<h4>Batch Eligibility</h4>
		<ng-include src="view.get('billing/batch-eligibility/filters.html')"></ng-include>
	</div>

	<div class="content-block">
		<table class="opake">
			<thead callback="batchVm.searchBatchEligibilities()">
			<tr>
				<th>Batch ID</th>
				<th>Date Received</th>
				<th>Entries Sent</th>
				<th>Eligible</th>
				<th>Not Eligible</th>
				<th>Insufficient Data</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in batchVm.batchEligibilities">
				<td><a ng-click="batchVm.viewBatch(item)" href="">{{item.id}}</a></td>
				<td>{{item.date_received | date:'M/d/yyyy'}}</td>
				<td>{{item.entries_sent}}</td>
				<td>{{item.eligible}}</td>
				<td>{{item.not_eligible}}</td>
				<td>{{item.insufficient_data}}</td>
			</tr>
			</tbody>
		</table>
		<h4 ng-if="batchVm.batchEligibilities && !batchVm.batchEligibilities.length">Items not found</h4>
	</div>
</div>