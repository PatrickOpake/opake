<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $_($case['id']) ?>, {openInEditMode: true, case: <?= $_(json_encode($case)) ?>} )" ng-cloak>
	<div ng-controller="CaseRegistrationCtrl as regVm" ng-init="regVm.init(<?= $id ?>, <?= $_(json_encode($registration)) ?>, <?= $_(json_encode($case)) ?>); regVm.initSubMenu();"  ng-cloak>
		<div ng-controller="VerificationCtrl as VerificationVm" ng-init="VerificationVm.init(regVm.registration.id, regVm.registration.insurances, regVm.case.additional_cpts, {})" ng-cloak>
			<div ng-if="!topMenu || topMenuActive === 'registration'" ng-switch="subTopMenuActive">
				<div ng-switch-when="charts">
					<ng-include class="content-block" src="view.get('cases/registrations/view/additional_info/form.html')"></ng-include>
				</div>
				<div ng-switch-default>
					<ng-include src="view.get('cases/registrations/view/' + subTopMenuActive + '.html')"></ng-include>
				</div>
			</div>
		</div>
	</div>
</div>