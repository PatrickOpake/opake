<div ng-controller="SettingsLogsNavicureLogList as listVm" class="content-block" ng-cloak>
	<filters-panel ctrl="listVm">
		<div class="data-row">
			<label>Claim</label>
			<input type="text" ng-model="listVm.search_params.claim_id" class='form-control input-sm' placeholder='Type' />
		</div>
		<div class="data-row">
			<label>Transaction</label>
			<opk-select ng-model="listVm.search_params.transaction"
			            options="tr.id as tr.title for tr in BillingConst.NAV_TRANSACTION_LIST_OPTIONS"></opk-select>
		</div>
		<div class="data-row">
			<label>Errors only</label>
			<div class="checkbox">
				<input id="errors-checkbox" type="checkbox" ng-model="listVm.search_params.only_with_errors" />
				<label for="errors-checkbox"></label>
			</div>
		</div>
	</filters-panel>

	<div class="table-wrap">
		<table class="opake" ng-if="listVm.items.length">
			<thead callback="listVm.search()">
			<tr>
				<th>Claim ID</th>
				<th>Case ID</th>
				<th>Date</th>
				<th>Transaction</th>
				<th>Error</th>
				<th>Content</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>{{ ::item.claim_id }}</td>
				<td>{{ ::item.case_id }}</td>
				<td>{{ ::item.time }}</td>
				<td>{{ ::item.transaction_description }}</td>
				<td>{{ ::item.error }}</td>
				<td>
					<a target="_blank" ng-href="/settings/logs/navicure/viewContent/{{::item.id}}">Show</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No logs found</h4>
	</div>
</div>
