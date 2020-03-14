<div ng-controller="BillingLedgerStatementHistoryListCtrl as listVm" ng-cloak>

	<div class="content-block billing-ledger-history--filter">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Last Name</label>
				<input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>First Name</label>
				<input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="date-range data-row">
				<label>Date Generated</label>
				<label class="from">From</label>
				<date-field ng-model="listVm.search_params.date_generated_from" icon="true"></date-field>
				<label class="to">To</label>
				<date-field ng-model="listVm.search_params.date_generated_to" icon="true"></date-field>
			</div>
		</filters-panel>
	</div>

	<div class="content-block" show-loading-list="listVm.isInitLoading">
		<table class="opake">
			<thead>
			<tr>
				<th>Date of Patient Statement Generated</th>
				<th>Type</th>
				<th>Last Name</th>
				<th>First Name</th>
				<th>MRN</th>
				<th>DOB</th>
				<th>Bulk Print</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>
					<a target="_blank" href="{{::item.url}}">{{::item.date_generated | date:'M/d/yyyy'}}</a>
				</td>
				<td>{{::item.type}}</td>
				<td>{{::item.last_name}}</td>
				<td>{{::item.first_name}}</td>
				<td>{{::item.mrn}}</td>
				<td>{{::(item.dob | date:'M/d/yyyy')}}</td>
				<td>{{::item.is_bulk_print}}</td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		       callback="listVm.search()"></pages>
		<h4 ng-if="!listVm.items.length">Patients not found</h4>
	</div>
</div>