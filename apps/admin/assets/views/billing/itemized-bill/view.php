<div ng-controller="BillingLedgerViewCtrl as ledgerVm" ng-init="ledgerVm.init(<?= $patientId ?>)" class="billing-ledger-page billing-ledger-view">
	<div class="panel-data verification-header">
		<a href="" class="back"><i class="glyphicon glyphicon-chevron-left"></i> Back</a>
		<div class="patient-info-header">
			<div class="row">
				<div class="col-sm-6">
					<div ng-if="::ledgerVm.ledger.patient.first_name" class="data-row case-notes">
						<label>Patient Name:</label>
						<span>{{ ::(ledgerVm.ledger.patient.first_name + ' ' + ledgerVm.ledger.patient.last_name) }}</span>
					</div>
					<div ng-if="::ledgerVm.ledger.patient.age" class="data-row">
						<label>Age:</label>
						{{ ::ledgerVm.ledger.patient.age }}
					</div>
					<div ng-if="::ledgerVm.ledger.patient.gender" class="data-row">
						<label>Sex:</label>
						{{ ::ledgerVm.ledger.patient.gender }}
					</div>
					<div ng-if="::ledgerVm.ledger.patient.home_phone" class="data-row">
						<label>Phone:</label>
						{{ ::ledgerVm.ledger.patient.home_phone | phone }}
					</div>
				</div>
				<div class="col-sm-6">
					<div ng-if="::ledgerVm.ledger.patient.dob" class="data-row">
						<label>Date of Birth:</label>
						<div>{{ ::ledgerVm.ledger.patient.dob }}</div>
					</div>
					<div ng-if="::ledgerVm.ledger.patient.home_address" class="data-row">
						<label>Address:</label>
						<div>{{ ::ledgerVm.ledger.patient.home_address }}</div>
					</div>
					<div ng-if="::(ledgerVm.ledger.patient.home_city && ledgerVm.ledger.patient.home_state)" class="data-row">
						<label>City, State:</label>
						<div>{{ ::ledgerVm.ledger.patient.home_city.name }}, {{ ::ledgerVm.ledger.patient.home_state.name }}</div>
					</div>
					<div ng-if="::ledgerVm.ledger.patient.home_zip_code" class="data-row">
						<label>Zip:</label>
						<div>{{ ::ledgerVm.ledger.patient.home_zip_code }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>