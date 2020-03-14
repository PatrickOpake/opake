<div ng-controller="CardCrtl as cardVm" ng-init="cardVm.init(<?= $card->id ?>)" show-loading="cardVm.isCardPrinting">

	<div ng-include="cardVm.getView()"></div>

	<div id="preferenceCardPrint" ng-include="view.get('cards/staff/print.html')"></div>
</div>