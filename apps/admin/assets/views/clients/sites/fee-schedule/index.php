<div class="fee-schedule-list" ng-controller="FeeScheduleListCtrl as listVm" class="content-block" ng-init="listVm.init(<?= $siteId ?>)" ng-cloak show-loading="listVm.isLoading">
	<div class="list-control">
		<errors src="listVm.errors"></errors>
		<div class="fee-schedule-top-block">
			<div class="site-name">{{listVm.siteName}}</div>
			<div class="fee-schedule-buttons">
				<div>
					<a class='btn btn-grey btn-file' ng-disabled="!listVm.search_params.type" select-file on-select="listVm.uploadFeeSchedule(files)">
						Upload Fee Schedule
						<input type="file" name="file" />
					</a>
					<a ng-disabled="!listVm.search_params.type" class='btn btn-grey' href='/clients/sites/{{::org_id}}/fee-schedule/downloadFeeSchedule/{{::listVm.siteId}}?type={{listVm.search_params.type}}'>
						Download Template
					</a>
				</div>
			</div>
		</div>
		<div class="fee-schedule-filters">
			<filters-panel is-hide-buttons="true" ctrl="listVm">
				<div class="data-row">
					<opk-select ng-model="listVm.search_params.type" key-value-options="listVm.feeScheduleConst.TYPE_FIELDS_LIST" placeholder="Type of Fee Schedule"></opk-select>
				</div>
				<div class="data-row">
					<input type="text" ng-model="listVm.search_params.hcpcs"
						   class="form-control input-sm search-hcpcs"
						   placeholder="Search HCPCS">
				</div>
			</filters-panel>
		</div>
	</div>
	<table class="opake" ng-if="listVm.search_params.type">
		<thead>
			<tr>
				<th>HCPCS/CPT</th>
				<th>Description</th>
				<th>Contracted Rate</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>{{::item.hcpcs}}</td>
				<td>{{::item.description}}</td>
				<td>{{::item.contracted_rate}}</td>
			</tr>
		</tbody>
	</table>
	<h4 class="list-not-found" ng-if="listVm.items && !listVm.items.length">Fee schedule is not found</h4>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
</div>

