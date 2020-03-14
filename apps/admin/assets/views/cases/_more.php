<div class="header row">
	<div class="col-sm-11">More Actions</div>
	<div class="col-sm-1">
		<a href="" ng-click="listVm.showMoreActions = false">
			<i class="icon-close-x"></i>
		</a>
	</div>
</div>
<div class="body">
	<div class="case-calendar-period-select" calendar="'case-calendar'" is-ipad-calendar="true"></div>
	<ul class="options">
		<li ng-click="calendar.setAction('filters'); listVm.showMoreActions = false;" class="pointer">
			<div class="icon"><i class="icon-filter" ng-class="{active: calendar.getAction() == 'filters'}"></i></div>
			<div>Filter</div>
		</li>
		<?php if ($_check_access('schedule', 'view_settings')): ?>
		<li ng-click="calendar.setAction('setting'); listVm.showMoreActions = false;" class="pointer">
			<div class="icon"><i class="icon-gear" ng-class="{active: calendar.getAction() == 'setting'}"></i></div>
			<div>Settings</div>
		</li>
		<?php endif ?>
		<?php if ($_check_access('case_blocks', 'create')): ?>
		<li ng-click="calendar.createBlock(); listVm.showMoreActions = false;" class="pointer">
			<div class="icon"><i class="icon-case-block" ng-class="{active: calendar.getAction() == 'blocking'}"></i></div>
			<div>Case Block</div>
		</li>
		<?php endif ?>
		<li ng-click="listVm.showMoreActions = false" class="pointer" print-overview="{{listVm.getPrintUrl()}}">
			<div class="icon"><i class="icon-circle-print-small"></i></div>
			<div>Print</div>
		</li>
	</ul>
</div>
