<div ng-controller="PatientListCrtl as listVm" ng-cloak>
	<div class="content-block patient-list">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Patient</label>

				<div class="group-field">
					<div><input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm'
								placeholder='Last Name'/></div>
					<div><input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm'
								placeholder='First Name'/></div>
				</div>
			</div>
			<div class="data-row">
				<label>Phone</label>
				<phone ng-model="listVm.search_params.home_phone"></phone>
			</div>
			<div class="data-row">
				<label>DOB</label>

				<div>
					<date-field ng-model="listVm.search_params.dob" without-calendar="true" placeholder="mm/dd/yyyy"
								small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>SSN</label>
				<ssn ng-model="listVm.search_params.ssn"></ssn>
			</div>
			<div class="data-row">
				<label>MRN</label>
				<div><input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm'
					    placeholder='#####-##'/></div>
			</div>
			<div class="data-row" ng-show="permissions.hasAccess('cases', 'edit_assigned_users')">
				<label>Surgeon</label>
				<opk-select class="multiple" ng-model="listVm.search_params.surgeons"
						options="surgeon.id as surgeon.fullname for surgeon in source.getSurgeons()" multiple></opk-select>
			</div>
		</filters-panel>

		<div class="list-control">
			<div class="loading-wheel" ng-if="listVm.isShowLoading">
				<div class="loading-spinner"></div>
			</div>
			<?php if ($_check_access('patients', 'create')): ?>
				<div class="pull-right"
					 uib-tooltip="Please search to ensure patient doesn't exist before creating"
					 tooltip-enable="listVm.isCreateDisabled()">
					<a class="btn btn-success" href="/patients/{{ ::org_id }}/create"
					   ng-disabled="listVm.isCreateDisabled()">New Patient</a>
				</div>
			<?php endif ?>
		</div>

		<div show-loading-list="listVm.isShowLoading">
			<table class="opake highlight-rows" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="id">MRN</th>
					<th sort="last_name">Last Name</th>
					<th sort="first_name">First Name</th>
					<th sort="dob">DOB</th>
					<th sort="home_phone">Phone Number</th>
					<th ng-if="permissions.hasAccess('user', 'delete')"></th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td ng-click="listVm.openPatient(item)">{{ ::item.full_mrn }}</td>
					<td ng-click="listVm.openPatient(item)" ng-class="{'strikethrough': !item.status}">{{ ::item.last_name }}</td>
					<td ng-click="listVm.openPatient(item)" ng-class="{'strikethrough': !item.status}">{{ ::item.first_name }}</td>
					<td ng-click="listVm.openPatient(item)">{{ ::item.dob | date:'M/d/yyyy' }}</td>
					<td ng-click="listVm.openPatient(item)">{{ ::item.home_phone | phone }}</td>
					<td ng-if="permissions.hasAccess('user', 'delete')" class="text-center">
						<div class="remove-icons">
							<a href="" class="remove-link" ng-click="listVm.removePatient(item)" uib-tooltip="Delete patient" tooltip-class="red">
								<i class="icon-delete"></i></a>
							<a href="" ng-click="listVm.archivePatient(item)"><i uib-tooltip="Archive patient" tooltip-class="white" class="icon-archive"></i></a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
				   callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Patient not found</h4>
		</div>
	</div>
</div>