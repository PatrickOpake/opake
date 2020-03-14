<div ng-controller="AlertsSettingCtrl as alertVm" ng-init="alertVm.init(<?= $siteId ?>)" class="panel-data alert-site--setting" show-loading="alertVm.isShowLoading" ng-cloak>
	<div class="data-row header">
		<a href="/settings/alerts/{{:: org_id}}" class="back"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
		<h2>{{alertVm.site.name}}</h2>
	</div>

	<div class="data-row checkbox enable-alerts-site">
		<input ng-click="alertVm.save()" id="enable_alerts_for_site" type="checkbox" class="styled" ng-model="alertVm.site.alert.enable_for_site">
		<label for="enable_alerts_for_site">Enable Alerts for this site</label>
	</div>

	<div class="data-row">
		<h4>Cases</h4>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="cases_report_completed_48hrs_case_end" type="checkbox" class="styled" ng-model="alertVm.site.alert.cases_report_completed_48hrs_case_end">
		<label for="cases_report_completed_48hrs_case_end">Alert if Operative Reports has not been completed within 48hrs of case end</label>
	</div>

	<div class="data-row">
		<h4>Schedule</h4>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="not_insurance_verified" type="checkbox" class="styled" ng-model="alertVm.site.alert.not_insurance_verified">
		<label for="not_insurance_verified">Alert if patient has not had their insurance verified</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="not_completed_preauthorized" type="checkbox" class="styled" ng-model="alertVm.site.alert.not_completed_preauthorized">
		<label for="not_completed_preauthorized">Alert if patient has not completed preauthorized</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_pre_certification_required" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_pre_certification_required">
		<label for="has_pre_certification_required">Alert if patient has pre-certification required</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_not_been_pre_certified" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_not_been_pre_certified">
		<label for="has_not_been_pre_certified">Alert if patient has not been precertified</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="is_self_funded" type="checkbox" class="styled" ng-model="alertVm.site.alert.is_self_funded">
		<label for="is_self_funded">Alert if patient is self-funded</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_oon_benefits" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_oon_benefits">
		<label for="has_oon_benefits">Alert if patient has OON benefits cap</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_asc_benefits" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_asc_benefits">
		<label for="has_asc_benefits">Alert if patient has ASC benefits cap</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_clauses_under_medicare_entitlement" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_clauses_under_medicare_entitlement">
		<label for="has_clauses_under_medicare_entitlement">Alert if patient has any clauses under medicare entitlement</label>
	</div>
	<div class="data-row checkbox">
		<input ng-click="alertVm.save()" ng-disabled="!alertVm.site.alert.enable_for_site" id="has_clauses_under_patient_policy" type="checkbox" class="styled" ng-model="alertVm.site.alert.has_clauses_under_patient_policy">
		<label for="has_clauses_under_patient_policy">Alert if patient has any pre-existing clauses under patient’s policy</label>
	</div>

	<div class="data-row">
		<h4>Registration</h4>
	</div>
	<div class="data-row"></div>
	<div class="data-row"></div>
	<div class="data-row"></div>
	<div class="data-row">
		<h4>Inventory</h4>
	</div>
	<div class="data-row"></div>
	<div class="data-row"></div>
	<div class="data-row"></div>
	<div class="data-row">
		<h4>Billing</h4>
	</div>

</div>
