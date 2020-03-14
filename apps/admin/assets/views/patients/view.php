<div ng-controller="PatientCrtl as patientVm" ng-init="patientVm.init(<?= $id ?>)" ng-cloak>
	<div class="panel-data patient-main-info">
		<div class="patient-panel--ids">
			<div><b>MRN#:</b> {{ patientVm.patient.mrn }}<span ng-if="patientVm.patient.mrn_year">-{{patientVm.patient.mrn_year}}</span></div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="data-row">
					<label>Patient Name:</label>
					{{ patientVm.patient.last_name + ', ' + patientVm.patient.first_name +
							(patientVm.patient.suffix ? (' ' + patientConst.SUFFIXES[patientVm.patient.suffix]) : '') }}
				</div>
				<div ng-if="patientVm.patient.dob" class="data-row">
					<label>Age:</label>
					{{ patientVm.patient.dob | age }}
				</div>
				<div ng-if="patientVm.patient.gender" class="data-row">
					<label>Sex:</label>
					{{ patientConst.GENDERS[patientVm.patient.gender] }}
				</div>
				<div ng-if="patientVm.patient.home_phone" class="data-row">
					<label>Phone:</label>
					{{ patientVm.patient.home_phone | phone }}
				</div>
			</div>
			<div class="col-sm-6">
				<div ng-if="patientVm.patient.dob" class="data-row">
					<label>Date of Birth:</label>

					<div>{{ patientVm.patient.dob | date:'M/d/yyyy' }}</div>
				</div>
				<div ng-if="patientVm.patient.home_address" class="data-row">
					<label>Address:</label>

					<div>{{ patientVm.patient.home_address }}</div>
				</div>
				<div ng-if="patientVm.patient.home_city && patientVm.patient.home_state" class="data-row">
					<label>City, State:</label>

					<div>{{ patientVm.patient.home_city.name }}, {{ patientVm.patient.home_state.name }}</div>
				</div>
				<div ng-if="patientVm.patient.home_zip_code" class="data-row">
					<label>Zip:</label>

					<div>{{ patientVm.patient.home_zip_code }}</div>
				</div>
			</div>
		</div>
	</div>


	<ng-include class="patient-form" ng-if="patientVm.patient" src="patientVm.getView()"
				onLoad="hideCreateBooking = <?= json_encode($_check_access('booking', 'create') === false) ?>;"></ng-include>
</div>
