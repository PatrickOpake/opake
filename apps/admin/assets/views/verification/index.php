<div ng-controller="VerificationListCrtl as listVm" ng-cloak show-loading="listVm.isShowLoading">
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>DOS</label>
				<div>
					<date-field ng-model="listVm.search_params.dos" placeholder="mm/dd/yyyy" small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>From</label>
				<div>
					<date-field ng-model="listVm.search_params.start" placeholder="mm/dd/yyyy" small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>To</label>
				<div>
					<date-field ng-model="listVm.search_params.end" placeholder="mm/dd/yyyy" small="true"></date-field>
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
				<label>Status</label>
				<opk-select ng-model="listVm.search_params.verification_status" key-value-options="verificationConst.STATUSES" placeholder="Select"></opk-select>
			</div>
			<div class="data-row">
				<label>Procedure</label>
				<opk-select class="long-options" ng-model="listVm.search_params.procedure"
					    options="type.id as type.full_name for type in source.getCaseTypes($query)"></opk-select>
			</div>
			<div class="data-row">
				<label>Surgeon</label>
				<opk-select ng-model="listVm.search_params.doctor"
					    options="doctor.id as doctor.fullname for doctor in source.getSurgeons()"></opk-select>
			</div>
			<div class="data-row">
				<label>MRN</label>
				<div><input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm' placeholder='#####-##'/></div>
			</div>
		</filters-panel>

		<table class="opake verification-table" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th class="case-patient">Patient Name</th>
				<th class="case-dob">DOB</th>
				<th sort="date" class="case-time">DOS</th>
				<th class="case-physician">Physician</th>
				<th class="case-procedure">Procedure</th>
				<th class="case-status">Status</th>
			</tr>
			</thead>
			<tbody>
				<tr ng-repeat="case in listVm.items">
					<td class="case-patient">
						<a href='/cases/{{ ::org_id }}/cm/{{ ::case.id }}'
						   ng-if="listVm.hasCaseManagementAccess"
						   uib-tooltip-html="listVm.getPatientTooltipStr(case.patient)">
							{{ ::case.patient.last_name }}, {{ ::case.patient.first_name }}
						</a>
						<span ng-if="!listVm.hasCaseManagementAccess"
							  uib-tooltip-html="listVm.getPatientTooltipStr(case.patient)">
							{{ ::case.patient.last_name }}, {{ ::case.patient.first_name }}
						</span>
					</td>
					<td class="case-dob">
						{{ ::case.patient.dob | date:'M/d/yyyy' }}
					</td>
					<td class="case-time">
						{{ ::case.time_start | date:'M/d/yyyy' }}
					</td>
					<td class="case-surgeon">
						{{ ::case.first_surgeon_for_dashboard }}
					</td>
					<td class="case-procedure">
						<a ng-if="listVm.hasCaseManagementAccess" href="/cases/{{ ::org_id }}/cm/{{ ::case.id }}">
							{{ ::case.procedure_name_for_dashboard }}
						</a>
						<span ng-if="!listVm.hasCaseManagementAccess">{{ ::case.type.full_name }}</span> <br/>
						<span class="italicized-text">{{ ::case.description }}</span>
					</td>
					<td class="case-status">
						<button ng-click="listVm.view(case.registration_id)" class="btn status-button"
							ng-class="::{
								'btn-primary': case.isVerificationContinue(), 
								'btn-success': case.isVerificationBegin(), 
								'btn-link': case.isVerificationCompleted()
							}">
							{{ ::verificationConst.STATUSES[case.verification_status] }}
							<span ng-if="::case.isVerificationCompleted()">
								- {{ ::case.verification_completed_date | date:'M/d/yyyy' }}
							</span>
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">Cases not found</h4>
	</div>
</div>