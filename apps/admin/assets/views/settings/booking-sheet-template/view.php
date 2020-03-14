<script type="application/javascript" src="/vendors/gridstack/lodash.min.js"></script>
<script type="application/javascript" src="/vendors/gridstack/jquery-ui.min.js"></script>
<script type="application/javascript" src="/vendors/gridstack/gridstack.all.js"></script>
<link rel="stylesheet" href="/vendors/gridstack/jquery-ui.min.css" />
<link rel="stylesheet" href="/vendors/gridstack/gridstack.min.css" />
<div class="booking-sheet-template-fields-page" ng-controller="BookingSheetTemplateViewCtrl as templateVm" ng-init="templateVm.init(<?= $_(json_encode($id)) ?>)" show-loading="!templateVm.isConfigLoaded" ng-cloak>
	<ng-include src="templateVm.getView()"></ng-include>
</div>