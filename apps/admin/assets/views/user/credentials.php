<div ng-controller="UserCredentialsCtrl as crVm" ng-init="crVm.init(loggedUser)" ng-cloak>
	<div class="content-block user-credentials">
		<div class="top-buttons top-buffer">
			<div class="pull-right">
				<a class="btn btn-success" href="" ng-click="crVm.save()" ng-disabled="!crVm.isChanged()">Save</a>
				<a class="btn btn-grey" href="" ng-click="crVm.cancel()">Cancel</a>
			</div>
		</div>
		<errors src="crVm.errors"></errors>
		<div ng-if="crVm.user.isMedicalStaffType()" ng-include="view.get('users/credentials/medical.html')"></div>
		<div ng-if="!crVm.user.isMedicalStaffType()" ng-include="view.get('users/credentials/non_surgical.html')"></div>
	</div>
</div>

