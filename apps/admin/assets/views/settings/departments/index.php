<div ng-controller="DepartmentCrtl as depVm" class="content-block" ng-cloak>

	<div class="list-control">
		<a class="btn btn-success" href="" ng-click="depVm.openCreateDialog()">Create New Department</a>
	</div>

	<table class="opake" ng-if="depVm.items.length">
		<thead sorter="depVm.search_params" callback="depVm.search()">
			<tr>
				<th sort="name">Name</th>
				<th sort="active">Status</th>
				<th>Actions</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in depVm.items">
				<td>{{ ::item.name }}</td>
				<td>
					<span ng-if="item.active == 1">active</span>
					<span ng-if="item.active == 0">inactive</span>
				</td>
				<td>
					<a ng-if="item.active == 1" href="" ng-click="depVm.deactivate(item.id)">Deactivate</a>
					<a ng-if="item.active == 0" href="" ng-click="depVm.activate(item.id)">Activate</a>
				</td>
				<td>
					<a href="" ng-click="depVm.openEditDialog(item)">Edit</a>
				</td>
				<td>
					<button class="btn btn-danger" ng-click="depVm.delete(item.id)" type="button">Delete</button>
				</td>
			</tr>
		</tbody>
	</table>
	<div>
		<pages count="depVm.total_count" page="depVm.search_params.p" limit="depVm.search_params.l" callback="depVm.search()"></pages>
		<h4 ng-if="depVm.items && !depVm.items.length">No departments found</h4>
	</div>
</div>

<script type="text/ng-template" id="settings/departments/create.html">
	<div class="department-edit--modal">
		<div class="modal-header">
			Create Department
		</div>
		<div class="modal-body" ng-form name="DepartmentForm">
			<div>
				<label>Name:</label>
				<input ng-required="true" type="text" class="form-control" ng-model="depVm.department.name">
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="depVm.clickSave(DepartmentForm)" ng-disabled="DepartmentForm.$invalid">Save</button>
			<button class="btn btn-danger" ng-click="depVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="settings/departments/edit.html">
	<div class="department-edit--modal">
		<div class="modal-header">
			Edit Department
		</div>
		<div class="modal-body" ng-form name="DepartmentForm">
			<div>
				<label>Name:</label>
				<input ng-required="true" type="text" class="form-control" ng-model="depVm.department.name">
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="depVm.clickSave(DepartmentForm)" ng-disabled="DepartmentForm.$invalid">Save</button>
			<button class="btn btn-danger" ng-click="depVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="settings/departments/confirm_activate.html">
	<div class="modal-body">
		<h4>Are you sure you want to activate this department?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>

<script type="text/ng-template" id="settings/departments/confirm_deactivate.html">
	<div class="modal-body">
		<h4>Are you sure you want to deactivate this department?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>

<script type="text/ng-template" id="settings/departments/confirm_delete.html">
	<div class="modal-body">
		<h4>Are you sure you want to delete this department?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>
