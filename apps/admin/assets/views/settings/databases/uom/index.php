<div ng-controller="SettingsUomListCtrl as listVm" class="content-block" ng-cloak>
	<div class="filters-panel--content">
		<div class="data-row">
			<label>Search Unit</label>
			<input class="form-control input-sm" ng-model="listVm.name_search" placeholder="Type" />
		</div>
	</div>
	<errors src="listVm.errors"></errors>
	<table class="opake">
		<thead>
			<tr>
				<th>Unit of measure</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in listVm.items | filter: listVm.searchFilter">
				<td>
					<span ng-if="!listVm.isEditable(item)">{{item.name}}</span>
					<input ng-if="listVm.isEditable(item)" type="text" ng-model="item.name" class="form-control" placeholder='Type'/>
				</td>
				<td>
					<div ng-if="!listVm.isEditable(item)">
						<a href="" ng-click="listVm.edit(item)" ng-disabled="!item.name"><i class="icon-edit-case"></i></a>
						<a href="" ng-click="listVm.delete(item)"><i class="icon-remove"></i></a>
					</div>
					<div ng-if="listVm.isEditable(item)">
						<a class="btn btn-success" href="" ng-click="listVm.save(item)" ng-disabled="!item.name">Save</a>
						<a class="btn btn-grey" href="" ng-click="listVm.cancelEdit(item)">Cancel</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>

	<a class="btn btn-success" ng-if="listVm.showAddNewButton()" ng-click="listVm.addNewItem()">Add Unit</a>
</div>
