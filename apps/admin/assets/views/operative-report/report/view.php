<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $report->getCase()->id ?>, {isOperativeReport: true})" class="cases-management" ng-cloak>
	<div class="operative-report panel-data" ng-controller="OperativeReportCtrl as reportVm" ng-init="reportVm.init(<?= $report->id ?> , <?= isset($user_id)? $user_id : 'null' ?>, null, true, caseVm)" ng-cloak>
		<div class="card-management--phases-control">
			<a href="" class="back" ng-click="reportVm.doTheBack()">Back</a>
			<div class="buttons">
				<div ng-if="reportVm.action === 'edit'">
					<button class="btn btn-grey" ng-click="reportVm.cancel(); caseVm.cancel();">Cancel</button>
					<button ng-if="!reportVm.isSigned()" class="btn btn-success" ng-click="reportVm.saveWithCase('draft', caseVm)">Save & Finish Later</button>
					<button ng-if="!reportVm.isSigned()" class="btn btn-success" ng-click="reportVm.saveWithCase('submitted', caseVm);">Submit</button>
					<button ng-if="reportVm.isSigned()" class="btn btn-success" ng-click="reportVm.saveWithCase('signed', caseVm);">Sign</button>
					<a ng-if="!reportVm.isSigned()" ng-click="reportVm.addAdditionalField()" href="" >+ Add Additional Field</a>
				</div>
				<a href="" print-iframe="{{reportVm.getPrintUrl(reportVm.report.id)}}" class="print-icon"></a>
				<button ng-show="permissions.user.is_internal ||  permissions.hasAccess('operative_reports', 'edit', reportVm.report)"
					ng-if="reportVm.action === 'view' && reportVm.isShowEdit" class="btn btn-success"
					ng-click="reportVm.editMyReport(caseVm);">Edit</button>
			</div>
		</div>
		<div class="row" ng-if="caseVm.action == 'edit'">
			<div class="col-sm-6">
				<div class="data-row">
					<label>Template:</label>
					<opk-select ng-model="reportVm.future_template_picker"
						    placeholder="Choose a template from the list"
						    options="item.name for item in reportVm.future_templates"
						    ng-change="reportVm.selectFutureTemplate(caseVm.case)"
						    ng-disabled="reportVm.isSigned()"
						    select-options="{listFilter: 'opkSelectEmptyFieldIdName'}"></opk-select>
				</div>
			</div>
		</div>
		<div class="case-ids"><b>ACCOUNT#: {{ caseVm.case.id || caseVm.case.acc_number }} | MRN#: {{ caseVm.case.patient.full_mrn }}</b></div>
		<errors src="reportVm.errors"></errors>
		<errors src="caseVm.errors"></errors>
		<h4 class="section-header">Case Details</h4>
		<ng-include class="case-data" src="view.get('cases/report/case_info/form_' + caseVm.action + '.html')" onLoad="case = caseVm.toedit || caseVm.case;"></ng-include>
		<ng-include src="view.get('cases/report/' + reportVm.action + '.html')" class="report-fields"></ng-include>
		<div class="text-right top-buffer bottom-buttons" ng-if="reportVm.action === 'edit'">
			<button ng-if="reportVm.action === 'edit'" class="btn btn-grey" ng-mousedown="reportVm.cancel(); caseVm.cancel();">Cancel</button>
			<button ng-if="!reportVm.isSigned()" class="btn btn-success" ng-mousedown="reportVm.saveWithCase('draft', caseVm);">Save & Finish Later</button>
			<button ng-if="!reportVm.isSigned()" class="btn btn-success" ng-mousedown="reportVm.saveWithCase('submitted', caseVm);">Submit</button>
			<button ng-if="reportVm.isSigned()" class="btn btn-success" ng-mousedown="reportVm.saveWithCase('signed', caseVm);">Sign</button>
		</div>
	</div>
</div>