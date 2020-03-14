<div ng-controller="ClaimsManagementCtrl as listVm" ng-cloak class="claims-management-page">
	<div class="content-block billing-list">

		<filters-panel ctrl="listVm" class="claims-management-filters">
			<div class="data-row date-filter">
				<div class="from-field">
					<label class="dos">DOS From</label>
					<div><date-field ng-model="listVm.search_params.dos_from" icon="true"></date-field></div>
				</div>
				<div class="to-field">
					<label>To</label>
					<div><date-field ng-model="listVm.search_params.dos_to" icon="true"></date-field></div>
				</div>
			</div>
			<div class="data-row date-filter">
				<div class="from-field">
					<label>Billing Date From</label>
					<div><date-field ng-model="listVm.search_params.billing_date_from" icon="true"></date-field></div>
				</div>
				<div class="to-field">
					<label>To</label>
					<div><date-field ng-model="listVm.search_params.billing_date_to" icon="true"></date-field></div>
				</div>
			</div>
			<div class="data-row">
				<label>Payer</label>
				<opk-select ng-model="listVm.search_params.payer"
				            options="value.name for value in source.getInsurances($query)"></opk-select>
			</div>
			<div class="data-row">
				<label>Claim Status</label>
				<opk-select class="multiple small" ng-model="listVm.search_params.status"
				            options="value.id as value.title for value in BillingConst.NAV_CLAIM_STATUS_OPTIONS"></opk-select>
			</div>
			<div class="data-row">
				<label>Patient Name</label>
				<div class="group-field">
					<div><input type="text" ng-model="listVm.search_params.patient_last_name" class='form-control input-sm' placeholder='Last Name' /></div>
					<div><input type="text" ng-model="listVm.search_params.patient_first_name" class='form-control input-sm' placeholder='First Name' /></div>
				</div>
			</div>
			<div class="data-row">
				<label>Patient DOB</label>
				<div><date-field ng-model="listVm.search_params.patient_dob" icon="true"></date-field></div>
			</div>
			<div class="data-row number-input">
				<div class="input-container">
					<label>Case Number</label>
					<input type="text" ng-model="listVm.search_params.case_number" class='form-control input-sm' />
				</div>
				<div class="input-container">
					<label>Claim Number</label>
					<input type="text" ng-model="listVm.search_params.claim_number" class='form-control input-sm' />
				</div>
			</div>
			<div class="data-row">
				<label>Type</label>
				<opk-select class="multiple small" ng-model="listVm.search_params.type"
				            options="value.id as value.title for value in BillingConst.NAV_CLAIM_ELECTRONIC_TYPE_OPTIONS"></opk-select>
			</div>
		</filters-panel>

		<div class="list-control">
			<a href="" class="icon" ng-click="listVm.printSelected()" ng-disabled="!listVm.toSelected.length">
				<i class="icon-print-grey" uib-tooltip="Print"></i>
			</a>
			<a class="btn btn-grey" href="" ng-click="listVm.forceUpdate()">Force Update Status</a>
			<div class="loading-wheel" ng-if="listVm.isShowLoading">
				<div class="loading-spinner"></div>
			</div>
		</div>

		<div show-loading-list="listVm.isShowLoading && listVm.isInitLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th>
						<div class="checkbox">
							<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
							<label for="print_all"></label>
						</div>
					</th>
					<th sort="claim_id">Claim #</th>
					<th sort="patient_name">Patient Name</th>
					<th sort="mrn">MRN</th>
					<th sort="case">Case #</th>
					<th sort="dos">DOS</th>
					<th sort="payer">Payer</th>
					<th sort="transaction_date">Transaction Date</th>
					<th sort="type">Type</th>
					<th sort="status">Status</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td class="not-link">
						<div>
							<div class="checkbox">
								<input id="print_{{$index}}"
								       type="checkbox"
								       class="styled"
								       ng-checked="listVm.isAddedToSelected(item)"
								       ng-click="listVm.addToSelected(item)">
								<label for="print_{{$index}}"></label>
							</div>
						</div>
					</td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.id }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.patient_name }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.mrn }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.case_id }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.dos }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.insurance_payer_name }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.transaction_date }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.type_title }}</a></td>
					<td class="link"><a href="/billings/{{::org_id}}/view/{{::item.case_id}}" target="_blank">{{ ::item.status }}</a></td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			       callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Claims not found</h4>
		</div>
	</div>
</div>