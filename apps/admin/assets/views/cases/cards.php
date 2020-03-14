<div ng-controller="CasePrefCardListCrtl as listVm" ng-cloak show-loading="listVm.isShowLoading">
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>DOS</label>
				<div>
					<date-field ng-model="listVm.search_params.dos" placeholder="mm/dd/yyyy" small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>Patient Name</label>
				<div class="group-field">
					<div><input type="text" ng-model="listVm.search_params.patient_last_name" class='form-control input-sm'
						    placeholder='Last Name'/></div>
					<div><input type="text" ng-model="listVm.search_params.patient_first_name" class='form-control input-sm'
						    placeholder='First Name'/></div>
				</div>
			</div>
			<div class="data-row">
				<label>Surgeon</label>
				<opk-select ng-model="listVm.search_params.doctor"
					    options="doctor.id as doctor.fullname for doctor in source.getSurgeons()"></opk-select>
			</div>
			<div class="data-row">
				<label>MRN</label>
				<div><input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm' placeholder='Type'/></div>
			</div>
		</filters-panel>

		<table class="opake highlight-rows patients-pref-cards-table" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th class="case-patient">Patient Name</th>
				<th class="case-mrn">MRN</th>
				<th class="case-physician">Physician</th>
				<th sort="date" class="case-dos">DOS</th>
				<th class="case-time">Surgery Time</th>
				<th class="case-procedure">Procedure</th>
				<th class="case-status">Status</th>
			</tr>
			</thead>
			<tbody>
				<tr ng-repeat="case in listVm.items">
					<td class="case-patient" ng-click="listVm.redirectToCase(case.id)">
						<a href='' ng-click="listVm.redirectToCase(case.id)"
						   ng-if="listVm.hasCaseManagementAccess">
							{{ ::case.patient.last_name }}, {{ ::case.patient.first_name }}
						</a>
						<span ng-if="!listVm.hasCaseManagementAccess">
							{{ ::case.patient.last_name }}, {{ ::case.patient.first_name }}
						</span>
					</td>
					<td class="case-mrn" ng-click="listVm.redirectToCase(case.id)">
						{{ ::case.patient.full_mrn }}
					</td>
					<td class="case-physician" ng-click="listVm.redirectToCase(case.id)">
						{{ ::case.first_surgeon_for_dashboard }}
					</td>
					<td class="case-dos" ng-click="listVm.redirectToCase(case.id)">
						{{ ::case.time_start | date:'M/d/yyyy' }}
					</td>
					<td class="case-time" ng-click="listVm.redirectToCase(case.id)">
						{{ ::case.time_start | date:'h:mm a' }}
					</td>
					<td class="case-procedure" ng-click="listVm.redirectToCase(case.id)">
						<a ng-if="listVm.hasCaseManagementAccess" href="" ng-click="listVm.redirectToCase(case.id)">
							{{ ::case.type.full_name }}
						</a>
						<span ng-if="!listVm.hasCaseManagementAccess">{{ ::case.type.full_name  }}</span> <br/>
					</td>
					<td class="case-status">
						<button class="btn status-button" ng-click="listVm.openCard(case.id)"
							ng-class="::{
								'btn-primary': case.card_status == cardConst.CARD_STATUSES.STATUS_DRAFT, 
								'btn-success': case.card_status == cardConst.CARD_STATUSES.STATUS_OPEN, 
								'btn-link': case.card_status == cardConst.CARD_STATUSES.STATUS_SUBMITTED
							}">
							{{ ::cardConst.STATUSES[case.card_status] }}
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">Cards not found</h4>
	</div>
</div>