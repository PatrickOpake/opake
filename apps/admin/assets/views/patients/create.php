<div ng-controller="CreatePatientCtrl as patientVm" ng-init="patientVm.init()"
	 class="patient-form content-block" ng-cloak>
	<errors src="patientVm.saveBlockingErrors"></errors>
	<div ng-if="patientVm.patient">
		<div class="main-control">
			<div class="patient-mrn--field" ng-if="!loggedUser.isSatelliteOffice()">
				<label class="control-label" ng-class="{'invalid': patientVm.errors[error_model].mrn}">MRN*</label>
				<input type="text" ng-model="patientVm.patient.mrn" class='form-control input-sm' placeholder='Type' />
				<span class="mrn-year-sep">-</span>
				<input type="text" ng-model="patientVm.patient.mrn_year" class='form-control input-sm mrn-year-field' placeholder='' />
			</div>
			<a class="btn btn-grey" href="" ng-click="patientVm.cancel()">Cancel</a>
			<a class="btn btn-success" href="" ng-disabled="!patientVm.canCreate()" ng-click="patientVm.create()">Save</a>
		</div>
		<div class="form-horizontal patient-view-info">
			<h3>Patient Details</h3>
			<ng-include src="view.get('patients/patient_info/edit.html')" onLoad="error_model='patient';"
				warning-unsaved-form="patientVm.patient"
				warning-msg="Are you sure you want to continue without saving your changes?"
				warning-ini-timeout
			></ng-include>
			<ng-include src="view.get('patients/insurances/edit.html')"></ng-include>
		</div>
	</div>
</div>
