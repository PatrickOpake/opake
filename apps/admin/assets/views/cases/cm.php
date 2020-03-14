<?php
$cmParams = [];
if (isset($cmActiveTab)) {
	$cmParams['activeTab'] = $cmActiveTab;
}
?>

<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $case->id ?>, {openInEditMode: true, case: <?= $_(json_encode($caseObj)) ?>})" class="cases-management" ng-cloak>
	<div ng-controller="CaseManagementCrtl as cmVm" ng-init="cmVm.init(<?= $_(json_encode($cmParams)) ?>)">
		<div ng-if="cmVm.case" ng-controller="CaseRegistrationCtrl as regVm" ng-init="regVm.initFromCase(<?= $_(json_encode($case->registration->toArray())) ?>, <?= $_(json_encode($caseObj)) ?>); regVm.initSubMenu();">
			<div ng-controller="VerificationCtrl as VerificationVm" ng-init="VerificationVm.init(regVm.registration.id, regVm.registration.insurances, regVm.case.additional_cpts, {})" ng-cloak>

				<div class="case-timeline" ng-show="cmVm.isActiveTimeline()"
					 start="cmVm.case.time_start" end="cmVm.case.time_end"
					 start-fact="cmVm.case.time_start_in_fact" end-fact="cmVm.case.time_end_in_fact">
				</div>

				<errors class="case-errors" src="caseVm.errors"></errors>

				<div ng-if="cmVm.isActiveCasePanel()"
				     ng-init="caseVm.edit()"
				     class="case-panel content-block"
					 ng-class="{'with-collapse': caseVm.action == 'view'}"
					 ng-include="view.get(cmVm.getPathToCase() + caseVm.action + '.html')">
				</div>

				<div class="cases-management--phases">
					<?php if ($_check_access('registration', 'view')): ?>
						<div ng-if="topMenuActive === 'intake'" ng-switch="subTopMenuActive">
							<div ng-switch-when="charts">
								<ng-include class="content-block" src="view.get('cases/registrations/view/additional_info/form.html')"></ng-include>
							</div>
							<div ng-switch-default>
								<ng-include src="view.get('cases/registrations/view/' + subTopMenuActive + '.html')"></ng-include>
							</div>
						</div>
					<?php endif ?>
					<?php if ($_check_access('case_management_clinical', 'view')): ?>
						<ng-include src="view.get('cases/cm/clinical/main.html')"
									ng-if="topMenuActive === 'clinical'"></ng-include>
					<?php endif ?>
					<?php if ($_check_access('billing', 'view')): ?>
						<div ng-if="topMenuActive === 'billing'">
							<div>
								<ng-include src="view.get('cases/cm/billing/main.html')"></ng-include>
							</div>
						</div>
					<?php endif ?>
					<?php if ($_check_access('case_management_item_log', 'view')): ?>
						<ng-include src="view.get('cases/cm/item_log/' + subTopMenuActive + '.html')"
									ng-if="topMenuActive === 'item_log'"></ng-include>
					<?php endif ?>
					<?php if ($_check_access('case_management_time_log', 'view')): ?>
						<ng-include src="view.get('cases/cm/time_log/main.html')"
									ng-if="topMenuActive === 'time_log'"></ng-include>
					<?php endif ?>
					<?php if ($_check_access('case_management_audit', 'view')): ?>
						<ng-include src="view.get('cases/cm/audit/main.html')"
						            ng-if="topMenuActive === 'audit'"></ng-include>
					<?php endif ?>
				</div>

			</div>
		</div>
	</div>
</div>