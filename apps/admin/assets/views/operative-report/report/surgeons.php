<div class="operative-report--list" ng-controller="OperativeReportSurgeonsCrtl as listVm" ng-cloak>
	<div class="panel-data staff-list">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Surgeon: </label>
				<opk-select ng-model="listVm.search_params.user_id"
					    options="user.id as user.fullname for user in source.getUsers() | filter:{is_enabled_op_report: true}">
				</opk-select>
			</div>
		</filters-panel>

		<table class="opake highlight-rows" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th>Surgeon Name</th>
				<th>Site</th>
				<th class="text-center">Open Operative Reports</th>
				<th class="text-center">Submitted Operative Reports</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items" ng-click="listVm.viewSurgeonReports(item, 'my')">
				<td>
					<img ng-src="{{ ::item.image }}" class="user-tiny-image">
					<a href="/operative-reports/my/{{ ::org_id }}/index/{{:: item.id}}">{{ ::item.full_name }}</a>
				</td>
				<td><span ng-repeat="site in item.sites">{{ ::site.name }}{{ $last ? '' : ', ' }}</span></td>
				<td class="text-center"><a href="/operative-reports/my/{{ ::org_id }}/index/{{:: item.id}}">{{ ::item.report_count }}</a></td>
				<td class="text-center"><a href="/operative-reports/my/{{ ::org_id }}/index/{{:: item.id}}">{{ ::item.submitted_report_count }}</a></td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No surgeons found</h4>
	</div>
</div>