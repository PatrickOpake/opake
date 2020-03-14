<div class="operative-report--list" ng-controller="OperativeReportListCrtl as listVm" ng-init="listVm.init(<?php echo isset($user_id) && $user_id ? $user_id : '' ?>)" ng-cloak>
	<div class="opk-tabs">
		<ul class="nav nav-tabs nav-justified">
			<li ng-class="{active: listVm.type === 'open'}">
				<a ng-click="listVm.setType('open')" href="">Open
					<span class="badge" ng-if="listVm.alerts_count.open">{{ listVm.alerts_count.open }}</span>
				</a>
			</li>
			<li ng-class="{active: listVm.type === 'submitted'}">
				<a ng-click="listVm.setType('submitted')" href="">Submitted
					<span class="badge" ng-if="listVm.alerts_count.submitted">{{ listVm.alerts_count.submitted }}</span>
				</a>
			</li>
		</ul>
	</div>
	<div class="content-block">
		<div ng-include="view.get('/operative-report/my/' + listVm.type + '.html')"></div>
	</div>
</div>