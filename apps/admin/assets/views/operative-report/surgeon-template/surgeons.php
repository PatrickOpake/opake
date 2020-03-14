<div class="operative-report--list" ng-controller="OperativeReportSurgeonsCrtl as listVm" ng-cloak>
	<div class="panel-data">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Surgeon: </label>
				<opk-select ng-model="listVm.search_params.user_id"
					    options="user.id as user.fullname for user in source.getUsers()">
				</opk-select>
			</div>
		</filters-panel>

		<table class="opake highlight-rows" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th>Surgeon Name</th>
				<th>Site</th>
				<th class="text-center">Operative Report Templates</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items" ng-click="listVm.viewSurgeonReports(item, 'setting')" >
				<td>
					<img ng-src="{{::item.image}}" class="user-tiny-image" alt="User Image"/>
					<a href="/operative-reports/{{ ::org_id }}/index/{{:: item.id}}">{{ ::item.full_name }}</a>
				</td>
				<td><span ng-repeat="site in item.sites">{{ ::site.name }}{{ $last ? '' : ', ' }}</span></td>
				<td class="text-center"><a href="/operative-reports/{{ ::org_id }}/index/{{:: item.id}}">{{ ::item.template_count}}</a></td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No surgeons found</h4>
	</div>
</div>