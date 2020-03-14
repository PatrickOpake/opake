<div ng-controller="BillingLedgerPaymentActivityCtrl as listVm" class="billing-ledger-payment-activity-page" show-loading="listVm.isExportGenerating" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm" class="patient-statement--filter">
			<div class="data-row">
				<label>Date From</label>
				<div>
					<date-field ng-model="listVm.search_params.date_from"
					            placeholder="mm/dd/yyyy"
					            small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>Date To</label>
				<div>
					<date-field ng-model="listVm.search_params.date_to"
					            placeholder="mm/dd/yyyy"
					            small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>Last Name</label>
				<input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>First Name</label>
				<input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>Payment Source</label>
				<opk-select ng-model="listVm.search_params.payment_source"
				            options="item.id as item.title for item in BillingConst.LEDGER_PAYMENT_ACTIVITY_SOURCE_OPTIONS"></opk-select>
			</div>
			<div class="data-row">
				<label>Payment Method</label>
				<opk-select ng-model="listVm.search_params.payment_method"
				            options="item.id as item.title for item in BillingConst.LEDGER_PAYMENT_METHOD_OPTIONS"></opk-select>
			</div>
		</filters-panel>
		<a href="" class="btn-print icon" ng-click="listVm.export()">
			<i class="icon-export-xls"></i>
		</a>
	</div>

	<div class="content-block" show-loading-list="listVm.isInitLoading">
		<errors src="listVm.errors"></errors>
		<table class="opake">
			<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="date_of_payment">Date of Payment</th>
					<th sort="patient_last_name">Patient Last Name</th>
					<th sort="patient_first_name">Patient First Name</th>
					<th sort="payment_source">Payment Source</th>
					<th sort="payment_method">Payment Method</th>
					<th sort="payment_amount">Payment Amount</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in listVm.items">
					<td ng-bind="::item.date_of_payment"></td>
					<td ng-bind="::item.patient_last_name"></td>
					<td ng-bind="::item.patient_first_name"></td>
					<td ng-bind="::item.payment_source"></td>
					<td ng-bind="::item.payment_method"></td>
					<td ng-bind="::(item.amount|money)"></td>
				</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		       callback="listVm.search()"></pages>
		<h4 class="text-center" ng-if="!listVm.items.length">Items not found</h4>
	</div>

</div>