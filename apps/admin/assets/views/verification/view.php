<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $_($case['id']) ?>, {openInEditMode: false, case: <?= $_(json_encode($case)) ?>} )" ng-cloak>
	<div ng-controller="CaseRegistrationCtrl as regVm" ng-init="regVm.init(<?= $id ?>, <?= $_(json_encode($registration)) ?>, <?= $_(json_encode($case)) ?>)" ng-cloak>
		<div ng-controller="VerificationCtrl as VerificationVm" ng-init="VerificationVm.init(regVm.registration.id, regVm.registration.insurances, regVm.case.additional_cpts, {isVerificationQueue: true})" ng-cloak>
			<ng-include src="view.get('verification/view.html')"></ng-include>
		</div>
	</div>
</div>