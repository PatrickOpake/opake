<div ng-controller="CaseRegistrationListCrtl as listVm" ng-init="listVm.init();" ng-cloak>
	<div class="content-block case-registration-list">
		<ng-include src="view.get('cases/registrations/filters.html')"></ng-include>

		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th class="text-center" sort="appointment">Appointment</th>
					<th class="text-center" sort="last_name">Last Name</th>
					<th class="text-center" sort="first_name">First Name</th>
					<th class="text-center" sort="dob">DOB</th>
					<th class="text-center" sort="dos">DOS</th>
					<th class="text-center" sort="procedure">Procedure</th>
					<th class="text-center" sort="status">Status</th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in listVm.items">
					<td class="text-center">{{ ::item.case.time_start | date:'h:mm a' }}</td>
					<td class="text-center"><a href="/patients/{{ ::org_id }}/view/{{ item.patient.id }}">{{ ::item.last_name }}</a></td>
					<td class="text-center"><a href="/patients/{{ ::org_id }}/view/{{ item.patient.id }}">{{ ::item.first_name }}</a></td>
					<td class="text-center">{{ ::item.patient.dob | date:'M/d/yyyy' }}</td>
					<td class="text-center">{{ ::item.case.time_start | date:'M/d/yyyy' }}</td>
					<td class="text-center">{{ ::item.case.type.full_name }}</td>
					<td class="text-center control">
						<button ng-if="item.case.appointment_status != 1" ng-click="listVm.view(item.id)" class="btn" ng-class="{'btn-success': item.status == 0, 'btn-primary': item.status == 1, 'btn-link': item.status == 2}">
							{{ ::listVm.caseRegistrationConst.STATUSES[item.status] }}
						</button>
						<span ng-if="item.case.appointment_status == 1">Canceled</span>
					</td>
				</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">Case registrations not found</h4>
	</div>
</div>