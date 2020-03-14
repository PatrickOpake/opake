<div ng-controller="CardCrtl as cardVm" ng-init="cardVm.init(null, <?= $_(json_encode($user->toArray())) ?>)" ng-cloak>

	<div ng-include="cardVm.getView()"></div>

</div>