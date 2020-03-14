<div ng-controller="SmsTemplateCtrl as templateVm" ng-init="templateVm.init()" class="panel-data sms-template--setting" ng-cloak>
	<ng-include src="view.get('settings/sms-template/' + templateVm.action +'.html')"></ng-include>
</div>
