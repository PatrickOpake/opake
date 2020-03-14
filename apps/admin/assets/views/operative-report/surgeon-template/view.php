<div class="operative-report operative-report--surgeon-template panel-data" ng-controller="OperativeReportSurgeonTemplateCrtl as reportVm" ng-init="reportVm.init(<?= $id ?> <?= isset($user_id)? ',' . $user_id : '' ?>)" ng-cloak>
	<errors src="reportVm.errors"></errors>
	<div class="main-control">
		<div class="data-row">
			<a href="" class="back" ng-click="reportVm.doTheBack()">Back</a>
		</div>
		<div class="row">
			<div class="col-sm-8">
				<label>Template Name</label>
				<div ng-if="reportVm.action == 'view'" class="form-control template-name input-sm">{{ reportVm.report.name }}</div>
				<input ng-if="reportVm.action == 'edit'" class="form-control template-name input-sm" ng-model="reportVm.report.name" />
			</div>
			<div class="col-sm-4">
				<div class="operative-report-buttons">
					<?php if ($loggedUser->isInternal() || $_check_access('surgeon_templates', 'edit', $template)): ?>
						<button ng-if="reportVm.action === 'view'" class="btn btn-success" ng-click="reportVm.edit()">Edit Template</button>
					<?php endif ?>
					<button ng-if="reportVm.action === 'edit'" class="btn btn-grey" ng-click="reportVm.cancel()">Cancel</button>
					<button ng-if="reportVm.action === 'edit'" class="btn btn-success" ng-click="reportVm.save()">Save Template</button>
				</div>
			</div>
		</div>

	</div>
	<h4 class="section-header">Case Details</h4>
	<ng-include src="view.get('operative-report/surgeon-templates/case-info.html')"></ng-include>

	<div>
		<a href="#" ng-click="showDetails = ! showDetails" class="add-data-tags">Add data tags</a>
		<div id="surgeon-tooltip" class="dynamic-fields-tooltip" ng-show="showDetails">
			<a ng-click="showDetails = ! showDetails" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
			<div class="tooltip-header">
				To add data tags that will prefill with patient data from case details use the following tags in template text
				<br/>
				<br/>
			</div>
			<div class="tooltip-fields">
				<div class="tooltip-field">
					%LastName% - Patient's First Name <br/>
					%FirstName% - Patient's Last Name <br/>
					%Account% -  Patient's Account # <br/>
					%Age% - Patient's Age <br/>
					%DOB% - Patient's Date of Birth	<br/>
					%Gender% - Patient's Gender (Male/Female) <br/>
					%Gender2% - Patient's Gender (He/She) <br/>
					%Street% - Patient's Street Address <br/>
					%Apt% - Patient's Apt # <br/>
					%City% - City that the patient lives in <br/>
					%State% - State patient lives in <br/>
					%Country% - Country patient lives in <br/>
					%Zip% - Zip code patient lives in <br/>
				</div>
				<div class="tooltip-field">
					%MRN% - MRN of patient <br/>
					%Physician% - Name of the Physician assigned to the patient's case <br/>
					%DOS% - Date of Service for the patient's case <br/>
					%Insurance% - Name of the Primary Insurance company for patient <br/>
					%SiteName% - Name of the surgery center <br/>
					%SiteAddress% - Street address of surgery center <br/>
					%SiteCity% - City of the surgery center <br/>
					%SiteState% - State of the surgery center <br/>
					%SiteCountry% - Country of the surgery center <br/>
					%SiteZip% - Zip of the surgery center <br/>
					%SitePhone% - Phone number of the surgery center <br/>
				</div>
			</div>
		</div>
		<a ng-if="reportVm.action === 'edit'" class="additional-field-link" ng-click="reportVm.addAdditionalField()" href="" >+ Add Additional Field</a>
	</div>
	<ng-include src="view.get('operative-report/surgeon-templates/' +  reportVm.action + '.html')" class="report-fields"></ng-include>

	<div class="bottom-control">
		<a ng-if="reportVm.action === 'edit'" class="additional-field-link" ng-click="reportVm.addAdditionalField()" href="" >+ Add Additional Field</a>
	</div>
</div>
