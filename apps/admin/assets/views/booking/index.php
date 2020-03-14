<div ng-controller="BookingListCtrl as listVm" ng-cloak>
	<div class="content-block patient-list booking-sheet-list">
		<filters-panel ctrl="listVm" class="booking-filter">
			<div class="row">
				<div class="col-sm-6">
					<div class="date-range data-row">
						<label>Date Range</label>
						<date-field ng-model="listVm.search_params.dateFrom" icon="true"></date-field>
						<label class="to">to</label>
						<date-field ng-model="listVm.search_params.dateTo" icon="true"></date-field>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="data-row">
						<label>Patient</label>

						<div class="group-field">
							<div><input type="text" ng-model="listVm.search_params.patient_last_name" class='form-control input-sm'
								    placeholder='Last Name' /></div>
							<div><input type="text" ng-model="listVm.search_params.patient_first_name" class='form-control input-sm'
								    placeholder='First Name' /></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="data-row">
						<label>Surgeon</label>
						<opk-select class="multiple" ng-model="listVm.search_params.surgeons"
							    options="surgeon.id as surgeon.fullname for surgeon in source.getSurgeons()" multiple></opk-select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="data-row">
						<label>Status</label>
						<opk-select ng-model="listVm.search_params.status" key-value-options="listVm.bookingConst.STATUSES"></opk-select>
					</div>
				</div>
			</div>
		</filters-panel>
		<div class="list-control">
			<a href="" class="icon" ng-click="listVm.printAll()" ng-disabled="!listVm.toSelected.length">
				<i class="icon-print-grey" uib-tooltip="Print"></i>
			</a>
			<div class="loading-wheel" ng-if="listVm.isShowLoading">
				<div class="loading-spinner"></div>
			</div>
			<?php if ($_check_access('booking', 'create')): ?>
				<div class="pull-right">
					<a class="btn btn-success" href="/booking/{{ ::org_id }}/create">Create Booking</a>
				</div>
			<?php endif ?>
		</div>

		<div show-loading-list="listVm.isShowLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th>
						<div class="checkbox">
							<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
							<label for="print_all"></label>
						</div>
					</th>
					<th>Patient Name</th>
					<th>MRN</th>
					<th>Physician</th>
					<th sort="dos">DOS</th>
					<th>Surgery Time</th>
					<th>Duration</th>
					<th></th>
					<th ng-if="::listVm.canDeleteBooking"></th>
					<th></th>
					<th>Status</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td>
						<div class="checkbox">
							<input id="print_{{$index}}"
								   type="checkbox"
								   class="styled"
								   ng-checked="listVm.isAddedToSelected(item)"
								   ng-click="listVm.addToSelected(item)">
							<label for="print_{{$index}}"></label>
						</div>
					</td>
					<td ng-if="item.patient_id">
						<a ng-if="listVm.canEditBooking(item)" href="/booking/{{ ::org_id }}/view/{{ ::item.id }}" uib-tooltip="Open Booking Sheet">{{ ::item.patient_name }}</a>
						<span ng-if="!listVm.canEditBooking(item)">{{ ::item.patient_name }}</span>
					</td>
					<td ng-if="!item.patient_id">
						<a ng-if="listVm.canEditBooking(item)" href="/booking/{{ ::org_id }}/view/{{ ::item.id }}" uib-tooltip="Open Booking Sheet">{{ ::item.booking_patient_name }}</a>
						<span ng-if="!listVm.canEditBooking(item)">{{ ::item.booking_patient_name }}</span>
					</td>
					<td>{{ ::item.mrn }}</td>
					<td>{{ ::item.first_surgeon }}</td>
					<td><a href="/cases/{{ :: org_id}}/#?date={{ ::item.time_start | date:'yyyy-MM-dd'}}">{{ ::item.time_start | date:'M/d/yyyy'  }}</a></td>
					<td>{{ ::item.time_start | date:'h:mm a' }}</td>
					<td>{{ ::item.time_start| timeLength : item.time_end }}</td>
					<td class="booking-charts" ng-controller="BookingChartsCtrl as chartsVm">
						<a href="" ng-click="chartsVm.init(item.id)">
							<i ng-if="!item.charts_count" class="icon-cloud-upload-grey" uib-tooltip="Upload Files"></i>
							<i ng-if="item.charts_count" class="icon-cloud-upload-blue" uib-tooltip="Files Uploaded"></i>
						</a>
					</td>
					<td class="text-center" ng-if="::listVm.canDeleteBooking">
							<a href=""
							   ng-click="listVm.remove(item.id)"
							   uib-tooltip="Delete booking"
							   tooltip-class="red"
							   ng-if="item.status !== 1 || loggedUser.is_internal">
								<i class="icon-remove"></i>
							</a>
					</td>
					<td class="case-notes" ng-controller="BookingNoteCrtl as noteVm">
						<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(item.id)">
							<i ng-class="{'icon-note': !noteVm.bookingNotes.hasFlaggedNotes(item), 'icon-notes-red': noteVm.bookingNotes.hasFlaggedNotes(item)}"></i>
							<span class="badge" ng-if="noteVm.bookingNotes.getNotesCount(item)"
								  ng-class="{'blue': noteVm.bookingNotes.hasUnreadNotes[item.id]}">
								{{ noteVm.bookingNotes.getNotesCount(item) }}
							</span>
						</a>
					</td>
					<td ng-if="!loggedUser.isSatelliteOffice() && !loggedUser.isBiller()">
						<span ng-if="item.status === 1">Scheduled</span>
						<button ng-if="item.status !== 1" ng-disabled="item.scheduleDisabled || !item.is_valid_for_schedule" ng-click="listVm.schedule(item)" class="btn btn-success">
							Schedule
						</button>
					</td>
					<td ng-if="loggedUser.isSatelliteOffice() || loggedUser.isBiller()">
						<span ng-if="item.status === 2">Submitted</span>
						<span ng-if="item.status === 1">Scheduled</span>
						<a ng-if="item.status === 0" ng-if="::listVm.canEditBooking" class="btn btn-grey" href="/booking/{{ ::org_id }}/view/{{ ::item.id }}">
							Edit
						</a>
					</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
				   callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Bookings not found</h4>
		</div>
	</div>
</div>