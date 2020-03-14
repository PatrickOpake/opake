<div ng-controller="CaseCanceledListCrtl as listVm" ng-cloak class="canceled-cases">
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>DOS</label>
				<div><date-field ng-model="listVm.search_params.dos" placeholder="mm/dd/yyyy" small="true" icon="true"></date-field></div>
			</div>
			<div class="data-row">
				<div class="data-row date-cancelled">
					<label>From</label>
					<div><date-field ng-model="listVm.search_params.cancel_date_from" placeholder="mm/dd/yyyy" small="true" icon="true"></date-field></div>
				</div>
				<div class="data-row date-cancelled">
					<label>To</label>
					<div><date-field ng-model="listVm.search_params.cancel_date_to" placeholder="mm/dd/yyyy" small="true" icon="true"></date-field></div>
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
				<opk-select ng-model="listVm.search_params.cancel_status" key-value-options="caseRegistrationConst.CANCEL_STATUSES" placeholder="Select"></opk-select>
			</div>
		</filters-panel>

		<div class="row">
			<div class="col-sm-1 canceled-cases-print">
				<a href="" class="btn-print icon" print-canceled-cases>
					<i class="icon-circle-print" uib-tooltip="Print"></i>
				</a>
			</div>
			<div class="col-sm-1 canceled-cases-export">
				<a href="" class="btn-print icon" ng-click="listVm.export()">
					<i class="icon-export-csv"></i>
				</a>
			</div>
			<div class="loading-wheel" ng-if="listVm.isShowLoading">
				<div class="loading-spinner"></div>
			</div>
		</div>

		<div show-loading-list="listVm.isShowLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="patient_name">Patient Name</th>
					<th sort="mrn">MRN</th>
					<th>Physician</th>
					<th>Practice</th>
					<th sort="dos">DOS</th>
					<th sort="cancel_date">Date Canceled</th>
					<th>Reason</th>
					<th>Staff</th>
					<th ng-if="permissions.hasAccess('cancellation', 'reschedule')"  sort="rescheduled_date">Rescheduled Date</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td>
						<a href='/cases/{{ ::org_id }}/cm/{{ ::item.case.id }}'>
							{{ ::item.case.patient.last_name }}, {{ ::item.case.patient.first_name }}
						</a>
					</td>
					<td>{{ ::item.case.patient.full_mrn }}</td>
					<td>{{ ::item.case.first_surgeon_for_dashboard }}</td>
					<td>{{ ::item.case.first_surgeon_practice_name }}</td>
					<td>{{ ::item.dos | date:'M/d/yyyy' }}</td>
					<td>{{ ::item.cancel_time | date:'M/d/yyyy' }}</td>
					<td ng-if="item.cancel_status != 4">
						<div class="change-reason" ng-click="listVm.cancelAppointment(item)">
							{{ caseRegistrationConst.CANCEL_STATUSES[item.cancel_status] }}
							<span class="italic-text">{{ item.getCancelReason() }}</span>
						</div>
					</td>
					<td ng-if="item.cancel_status == 4"><a href="" ng-click="listVm.cancelAppointment(item)">No Show</a></td>
					<td>{{ ::item.canceled_user.full_name }}</td>
					<td ng-if="!item.rescheduled_date && permissions.hasAccess('cancellation', 'reschedule')">
						<a href="" ng-click="listVm.rescheduleCase(item)">Reschedule</a>
					</td>
					<td ng-if="item.rescheduled_date">{{ ::item.rescheduled_date | date:'M/d/yyyy' }}</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
				   callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Canceled Cases not found</h4>
		</div>
	</div>

	<div id="canceledCasesPrint" ng-include="view.get('cases/canceled_cases_print.html')"></div>
</div>