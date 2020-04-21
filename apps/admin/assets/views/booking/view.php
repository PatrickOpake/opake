<div ng-controller="BookingSheetCtrl as bookingVm" ng-init="bookingVm.init(<?= $id ?>)"
     class="patient-form content-block booking-sheet--form" ng-cloak>
	<div>
		<div class="booking-template-selection" ng-if="bookingVm.isCreate && !bookingVm.template">
			<div class="available-templates-list" ng-if="bookingVm.availableTemplates && bookingVm.availableTemplates.length">
				<h4>Select a template for the booking</h4>
				<div class="template" ng-repeat="template in bookingVm.availableTemplates">
					<button class="btn btn-success" ng-click="bookingVm.selectTemplate(template)">{{::template.name}}</button>
				</div>
			</div>
		</div>
		<div ng-if="bookingVm.template">
			<div class="row">
				<div class="col-sm-2">
					<a ng-if="bookingVm.availableTemplates.length > 1" href="" class="back" ng-click="bookingVm.clearTemplateSelection()"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
				</div>
				<div class="col-sm-7">
					<h4 class="text-right">Search for existing patients or create a new patient below</h4>
				</div>
			</div>

			<errors src="bookingVm.errors"></errors>
			<div ng-if="bookingVm.booking" warning-unsaved-form="bookingVm.booking">
				<div>
					<div class="form-horizontal booking-sheet preloader" ng-show="!bookingVm.isFormContentLoaded">
						<div>
							<h3>Patient Information
								<div class="loading-wheel">
									<div class="loading-spinner"></div>
								</div>
							</h3>
						</div>
						<div show-loading-list="true"></div>
						<div>
							<h3>Case Information
								<div class="loading-wheel">
									<div class="loading-spinner"></div>
								</div>
							</h3>
						</div>
						<div show-loading-list="true"></div>
						<div>
							<h3>Insurance Information
								<div class="loading-wheel">
									<div class="loading-spinner"></div>
								</div>
							</h3>
						</div>
						<div show-loading-list="true"></div>
					</div>
					<div class="form-horizontal booking-sheet" ng-show="bookingVm.isFormContentLoaded">
						<div class="row patient-information-section--header">
							<div class="col-sm-6">
								<h3>Patient Information</h3>
							</div>
							<div class="col-sm-6 pull-right">
								<div class="patient-mrn--field text-right" ng-if="bookingVm.booking.patient.mrn && !loggedUser.isSatelliteOffice()">
									<label class="control-label">MRN* </label>
									<input type="text" ng-model="bookingVm.booking.patient.mrn" class='form-control input-sm' placeholder='Type' />
									<span class="mrn-year-sep">-</span>
									<input type="text" ng-model="bookingVm.booking.patient.mrn_year" class='form-control input-sm mrn-year-field' placeholder='' />
								</div>
								<div class="case-note-block" ng-controller="BookingNoteCrtl as noteVm">
									<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(bookingVm.booking.id, bookingVm.booking.patient)">
										<span ng-class="{'icon-note': !noteVm.bookingNotes.hasFlaggedNotes(bookingVm.booking), 'icon-notes-red': noteVm.bookingNotes.hasFlaggedNotes(bookingVm.booking)}"></span>
									<span class="badge" ng-if="bookingVm.bookingNotes.getNotesCount(bookingVm.booking) > 0" ng-class="{'blue': bookingVm.bookingNotes.hasUnreadNotes[bookingVm.booking.id]}">
										{{ bookingVm.bookingNotes.getNotesCount(bookingVm.booking) }}
									</span>
									</a>
								</div>
								<div class="booking-charts" ng-controller="BookingChartsCtrl as chartsVm">
									<a href="" ng-click="chartsVm.init(bookingVm.booking.id, bookingVm.bookingCharts)">
										<i ng-if="!chartsVm.charts.length && !bookingVm.booking.charts_count" class="icon-cloud-upload-grey" uib-tooltip="Upload Files"></i>
										<i ng-if="chartsVm.charts.length || bookingVm.booking.charts_count" class="icon-cloud-upload-blue" uib-tooltip="Files Uploaded"></i>
									</a>
								</div>
							</div>
						</div>
						<booking-form-widget
							template="bookingVm.template.fields"
							booking-vm="bookingVm">
						</booking-form-widget>
						<div class="row insurance-information-section--header">
							<div class="col-sm-3">
								<h3>Insurance Information</h3>
							</div>
							<div class="col-sm-9">
								<div class="booking-charts" ng-controller="BookingChartsCtrl as chartsVm">
									<a href="" ng-click="chartsVm.init(bookingVm.booking.id, bookingVm.bookingCharts)">
										<i ng-if="!chartsVm.charts.length && !bookingVm.booking.charts_count" class="icon-cloud-upload-grey" uib-tooltip="Upload Files"></i>
										<i ng-if="chartsVm.charts.length || bookingVm.booking.charts_count" class="icon-cloud-upload-blue" uib-tooltip="Files Uploaded"></i>
									</a>
								</div>
							</div>
						</div>

						<ng-include  src="view.get('booking/insurances.html')"></ng-include>
					</div>
				</div>
				<div class="bottom-buttons top-buffer">
					<a class="btn btn-grey" href="/booking/{{ ::org_id }}/" >Booking Queue</a>
					<div class="pull-right">
						<a href="" class="icon print-icon" ng-click="bookingVm.print()"><i class="icon-print-grey" uib-tooltip="Print"></i></a>
						<a class="btn btn-grey" href="" ng-click="bookingVm.cancel()">Cancel</a>
						<a class="btn btn-success" href=""  ng-click="bookingVm.save()">Save</a>
						<a ng-if="!loggedUser.isSatelliteOffice() && !loggedUser.isBiller()" class="btn btn-primary" href=""  ng-click="bookingVm.schedule()">Schedule</a>
						<a ng-if="loggedUser.isSatelliteOffice()" class="btn btn-primary" href="" ng-disabled="!bookingVm.isRequiredFieldsForSubmitFilled() || bookingVm.actionButtonsDisabled" ng-click="bookingVm.save(true)">Submit</a>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
