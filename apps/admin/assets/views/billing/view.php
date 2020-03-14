<div ng-controller="BillingViewCtrl as billingVm" ng-cloak>
	<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $case['id'] ?>, {openInEditMode: false, case: <?= $_(json_encode($case)) ?>})">
		<div ng-controller="CaseRegistrationCtrl as regVm" ng-init="regVm.init(<?= $id ?>, <?= $_(json_encode($registration)) ?>, <?= $_(json_encode($case)) ?>)" ng-cloak>
				<div ng-if="topMenuActive === 'registration' || topMenuActive === 'billing'">
					<div ng-controller="CaseCodingCrtl as codingVm" ng-init="codingVm.init(<?= $case['id'] ?>)" ng-cloak>
						<ng-include src="view.get('billing/coding.html')"></ng-include>
					</div>
				</div>
		</div>
	</div>
</div>