<div ng-controller="BillingLedgerListCtrl as listVm" class="billing-ledger-page" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Patient Name</label>

				<div class="group-field">
					<div><input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm'
					            placeholder='Last Name'/></div>
					<div><input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm'
					            placeholder='First Name'/></div>
				</div>
			</div>
			<div class="data-row">
				<label>DOB</label>
				<div>
					<date-field ng-model="listVm.search_params.dob" without-calendar="true" placeholder="mm/dd/yyyy"
					            small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>MRN</label>
				<div><input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm'
				            placeholder='#####-##'/></div>
			</div>
		</filters-panel>
	</div>

	<div class="content-block" show-loading-list="listVm.isInitLoading">
		<table class="opake patient-table">
			<thead>
			<tr>
				<th>Patient Name</th>
				<th>DOB</th>
				<th>MRN</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td class="link"><a href="/billings/ledger/{{::org_id}}/view/{{::item.id}}">{{ ::item.name }}</a></td>
				<td class="link"><a href="/billings/ledger/{{::org_id}}/view/{{::item.id}}">{{ ::item.dob }}</a></td>
				<td class="link"><a href="/billings/ledger/{{::org_id}}/view/{{::item.id}}">{{ ::item.mrn }}</a></td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		       callback="listVm.search()"></pages>
		<h4 ng-if="!listVm.items.length">Patients not found</h4>
	</div>
</div>