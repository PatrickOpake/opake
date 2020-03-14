<div class="panel-data upload-forms chart-groups" ng-controller="FormChatGroupsCtrl as chartGroupsVm" ng-init="chartGroupsVm.init()" ng-cloak>
	<errors src="chartGroupsVm.errors"></errors>
	<div class="upload-forms--docs" show-loading="chartGroupsVm.isGroupPrinting">
		<div class="chart-groups-title">
			<a href="" class="btn btn-success" ng-click="chartGroupsVm.createGroup()">Create Chart Group</a>
		</div>

		<div class="chart-groups-container" ng-show="chartGroupsVm.isResultsLoaded" >
			<table class="opake chart-groups-table">
				<thead>
				<tr>
					<th class="name-column">Group Name</th>
					<th class="charts-column">Charts</th>
					<th class="button-column">Edit</th>
					<th class="button-column">Print</th>
					<th class="button-column">Delete</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-if="chartGroupsVm.isGroupCreate()">
					<td class="name-column">
						<input type="text" ng-model="chartGroupsVm.newGroup.name" class='form-control' />
					</td>
					<td class="charts-column">
						<opk-select multiple
									ng-model="chartGroupsVm.newGroup.document_ids"
									options="item.id as item.name for item in chartGroupsVm.documents"></opk-select>
					</td>
					<td colspan="3" class="edit-buttons-column">
						<div class="buttons">
							<button class="btn btn-primary" ng-click="chartGroupsVm.save(chartGroupsVm.newGroup)">Save</button>
							<button class="btn" ng-click="chartGroupsVm.cancel()" type="button">Cancel</button>
						</div>
					</td>
				</tr>
				<tr ng-if="!chartGroupsVm.items.length">
					<td colspan="5">
						<h4>No chart groups found</h4>
					</td>
				</tr>

				<tr ng-repeat="group in chartGroupsVm.items">
					<td ng-if="!chartGroupsVm.isGroupEdit(group)" class="name-column">
						{{::group.name}}
					</td>
					<td ng-if="!chartGroupsVm.isGroupEdit(group)" class="charts-column">
						{{::chartGroupsVm.getDocumentNamesList(group)}}
					</td>
					<td ng-if="!chartGroupsVm.isGroupEdit(group)" class="button-column">
						<a href="" class="icon" ng-click="chartGroupsVm.editGroup(group)">
							<i class="icon-edit-case"></i>
						</a>
					</td>
					<td ng-if="!chartGroupsVm.isGroupEdit(group)" class="button-column">
						<a href="" class="icon" ng-click="chartGroupsVm.printGroup(group)">
							<i class="icon-circle-print-small"></i>
						</a>
					</td>
					<td ng-if="!chartGroupsVm.isGroupEdit(group)" class="button-column">
						<a href="" class="icon" ng-click="chartGroupsVm.deleteGroup(group)">
							<i class="icon-circle-remove"></i>
						</a>
					</td>

					<td ng-if="chartGroupsVm.isGroupEdit(group)" class="name-column">
						<input type="text" ng-model="group.name" class='form-control' />
					</td>
					<td ng-if="chartGroupsVm.isGroupEdit(group)" class="charts-column">
						<opk-select multiple select-options="{reorder: true}"
									ng-model="group.document_ids"
									options="item.id as item.name for item in chartGroupsVm.documents"></opk-select>
					</td>
					<td colspan="3" ng-if="chartGroupsVm.isGroupEdit(group)" class="edit-buttons-column">
						<div class="buttons">
							<button class="btn btn-primary" ng-click="chartGroupsVm.save(group)">Save</button>
							<button class="btn" ng-click="chartGroupsVm.cancel()" type="button">Cancel</button>
						</div>
					</td>

				</tr>
				</tbody>
			</table>
			<div>
				<pages count="chartGroupsVm.totalCount" page="chartGroupsVm.searchParams.p" limit="chartGroupsVm.searchParams.l" callback="chartGroupsVm.search()"></pages>
			</div>
		</div>
	</div>
</div>

<script type="text/ng-template" id="settings/forms/chart-groups/confirm_delete.html">
	<div class="modal-body">
		<h4>Are you sure you want to delete this chart group?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>
