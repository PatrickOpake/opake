<div class="case-calendar-period-select" calendar="'case-calendar'"></div>
<div class="icon-group">
	<div class="cases-filter tool-item">
		<a href="" class="btn-filters icon" ng-click="calendar.setAction('filters')" uib-tooltip="Filter">
			<i class="icon-filter" ng-class="{active: calendar.getAction() == 'filters'}"></i>
		</a>
	</div>
	<?php if ($_check_access('schedule', 'view_settings')): ?>
		<div class="cases-setting tool-item">
			<a href="" class="btn-setting icon" ng-click="calendar.setAction('setting')" uib-tooltip="Settings">
				<i class="icon-gear" ng-class="{active: calendar.getAction() == 'setting'}"></i>
			</a>
		</div>
	<?php endif ?>
	<?php if ($_check_access('case_blocks', 'create')): ?>
		<div class="cases-blocking tool-item">
			<a href="" class="btn-blocking icon" ng-click="calendar.createBlock();" uib-tooltip="Case Block">
				<i class="icon-case-block" ng-class="{active: calendar.getAction() == 'blocking'}"></i>
			</a>
		</div>
	<?php endif ?>
	<div class="cases-blocking tool-item">
		<a href="" class="btn-blocking icon" print-overview="{{listVm.getPrintUrl()}}" uib-tooltip="Print">
			<i class="icon-circle-print-small"></i>
		</a>
	</div>
</div>
<div ng-if="calendar.getAction() !== 'calendar'" class="actions pull-right cancel-link">
	<a href="" ng-click="calendar.reset();">
		<i class="icon-close-x"></i>
	</a>
</div>