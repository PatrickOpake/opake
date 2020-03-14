<div ng-controller="AnalyticsSmsLogCtrl as listVm" class="content-block" ng-cloak>
	<filters-panel ctrl="listVm">
		<div class="data-row">
			<label>Case ID</label>
			<input type="text" ng-model="listVm.search_params.case_id" class='form-control input-sm' placeholder='Type' />
		</div>
	</filters-panel>

	<div class="table-wrap">
		<table class="opake" ng-if="listVm.items.length">
			<thead callback="listVm.search()">
			<tr>
				<th>Case ID</th>
				<th>Sent Date</th>
				<th>Type</th>
				<th>Phone To</th>
				<th>Status</th>
				<th>Message</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td><a ng-href="/cases/{{::org_id}}/cm/{{ ::item.case_id }}" target="_blank">{{ ::item.case_id }}</a></td>
				<td>{{ ::item.send_date }}</td>
				<td>{{ ::item.type }}</td>
				<td>{{ ::item.phone_to }}</td>
				<td>{{ ::item.status_text }}</td>
				<td>{{ ::item.body }}</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No logs found</h4>
	</div>
</div>
