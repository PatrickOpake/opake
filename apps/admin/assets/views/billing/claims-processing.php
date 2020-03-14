<div class="claims-processing-page" ng-controller="ClaimsProcessingCtrl as claimsProcessingVm">
	<div ng-if="subTopMenuActive == 'process'">
		<ng-include src="view.get('billing/claims-processing/tabs/process.html')"></ng-include>
	</div>
	<div ng-if="subTopMenuActive == 'processed'">
		<ng-include src="view.get('billing/claims-processing/tabs/processed.html')"></ng-include>
	</div>
	<div ng-if="subTopMenuActive == 'resubmitted'">
		<ng-include src="view.get('billing/claims-processing/tabs/resubmitted.html')"></ng-include>
	</div>
	<div ng-if="subTopMenuActive == 'onHold'">
		<ng-include src="view.get('billing/claims-processing/tabs/on-hold.html')"></ng-include>
	</div>
	<div ng-if="subTopMenuActive == 'exception'">
		<ng-include src="view.get('billing/claims-processing/tabs/exception.html')"></ng-include>
	</div>
</div>