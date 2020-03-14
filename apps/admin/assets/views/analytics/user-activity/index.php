<?php
$activityParams = [];
$activityParams['isInternal'] = !empty($isInternal);
?>

<div ng-controller="AnalyticsUserActivityCtrl as listVm" ng-init="listVm.init(<?= $_(json_encode($activityParams)) ?>)" ng-cloak class="user-activity">
	<div class="content-block insurance-database-list patient-list">
		<ng-include src="view.get('analytics/user-activity/filters.html')"></ng-include>
		<div class="list-control">
			<div class="loading-wheel" ng-if="listVm.isLoading">
				<div class="loading-spinner"></div>
			</div>
			<a class="btn btn-success" href="" ng-click="listVm.exportToExcel()">Export To Excel</a>
		</div>
	</div>

	<div show-loading-list="listVm.isLoading">
		<div ng-show="listVm.isDataLoaded">
			<table class="opake">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="organization" class="text-center" ng-hide="listVm.isOrgColumnHidden" width="150px">Organization</th>
					<th sort="user" class="text-center" ng-hide="listVm.isUserColumnHidden">User</th>
					<th sort="date" class="text-center">Date</th>
					<th sort="time" class="text-center">Time</th>
					<th sort="action" class="text-center">Activity</th>
					<th sort="case" class="text-center">Case</th>
					<th class="text-center" width="350px">Description</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td class="text-center" ng-hide="listVm.isOrgColumnHidden"><a href="" ng-click="listVm.selectOrganization(item)">{{::item.user_org_name}}</a></td>
					<td class="text-center" ng-hide="listVm.isUserColumnHidden"><a href="" ng-click="listVm.selectUser(item)">{{::item.user_fullname}}</a></td>
					<td class="text-center">{{::item.date}}</td>
					<td class="text-center">{{::item.time}}</td>
					<td class="text-center">{{::item.action}}</td>
					<td class="text-center">
						<span ng-if="!item.case_link">{{::item.case_id}}</span>
						<a ng-href="{{::item.case_link}}" ng-if="item.case_link" target="_blank">{{::item.case_id}}</a>
					</td>
					<td class="text-left">
						<div class="action-details" ng-if="item.details && item.details.length">
							<div ng-repeat="detail in item.details">
								<span class="key">{{::detail.label}}</span>:
								<span class="value" ng-if="!detail.value.type">{{::detail.value}}</span>
								<span class="value" ng-if="detail.value.type === 'link'"><a href="{{::detail.value.url}}" target="_blank">{{::detail.value.title}}</a></span>
								<span class="value" ng-if="detail.value.type === 'link_list'">
									<span ng-repeat="link in detail.value.links">
										<a ng-if="link.url" href="{{::link.url}}" target="_blank" >{{::link.title}}</a><span ng-if="!link.url">{{::link.title}}</span>{{$last ? '' : ','}}
									</span>
								</span>
							</div>
						</div>
						<div class="sep-container"ng-if="(item.details && item.details.length && item.changes && item.changes.length)">
							<div class="sep"></div>
						</div>
						<div class="action-changes" ng-if="item.changes && item.changes.length">
							<div ng-repeat="change in item.changes">
								<span class="key">{{::change.label}}</span>:
								<span class="value" ng-if="!change.value.type">{{::change.value}}</span>
								<span class="value" ng-if="change.value.type === 'link'"><a href="{{::change.value.url}}" target="_blank">{{::change.value.title}}</a></span>
								<div class="value child-changes" ng-if="change.value.type === 'changes'">
									<div class="child-row" ng-repeat="childData in change.value.data">
										<span ng-if="childData.data && childData.data.length"><span class="key">{{::childData.label}}</span>:</span>
										<span ng-if="!childData.data || !childData.data.length"><span class="key">{{::childData.label}}</span></span>
										<div class="child-row" ng-repeat="(changeLabel, changeValue) in childData.data">
											<span class="key">{{::changeLabel}}</span>:
											<span class="value" ng-if="!changeValue.type">{{::changeValue}}</span>
											<span class="value" ng-if="changeValue.type === 'link'"><a href="{{::changeValue.url}}" target="_blank">{{::changeValue.title}}</a></span>
										</div>
									</div>
								</div>
								<div class="value child-changes" ng-if="change.value.type === 'keyValue'">
									<div class="child-row" ng-repeat="(changeLabel, changeValue) in change.value.data">
										<span class="key">{{::changeLabel}}</span>:
										<span class="value" ng-if="!changeValue.type">{{::changeValue}}</span>
										<span class="value" ng-if="changeValue.type === 'link'"><a href="{{::changeValue.url}}" target="_blank">{{::changeValue.title}}</a></span>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
				<tr ng-if="!listVm.items.length">
					<th colspan="{{listVm.getCurrentColumnsCount()}}" class="text-center">
						<h4>Activity not found</h4>
					</th>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.totalCount" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		</div>
	</div>

</div>

