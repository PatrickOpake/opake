<div ng-controller="PracticeGroupsCtrl as pgVm" class="content-block" ng-cloak>

	<div class="list-control">
		<a class="btn btn-success" href="" ng-click="pgVm.openCreateDialog()">Create New Practice Group</a>
	</div>

	<table class="opake" ng-if="pgVm.items.length">
		<thead sorter="pgVm.searchParams" callback="pgVm.search()">
		<tr>
			<th sort="name">Name</th>
			<th sort="active">Status</th>
			<th>Actions</th>
			<th></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<tr ng-repeat="item in pgVm.items">
			<td>{{ ::item.name }}</td>
			<td>
				<span ng-if="item.active == 1">active</span>
				<span ng-if="item.active == 0">inactive</span>
			</td>
			<td>
				<a ng-if="item.active == 1" href="" ng-click="pgVm.deactivate(item)">Deactivate</a>
				<a ng-if="item.active == 0" href="" ng-click="pgVm.activate(item)">Activate</a>
			</td>
			<td>
				<a href="" ng-click="pgVm.openEditDialog(item)">Edit</a>
			</td>
			<td>
				<button class="btn btn-danger" ng-click="pgVm.delete(item)" type="button">Delete</button>
			</td>
		</tr>
		</tbody>
	</table>
	<div>
		<pages count="pgVm.totalCount" page="pgVm.searchParams.p" limit="pgVm.searchParams.l" callback="pgVm.search()"></pages>
		<h4 ng-if="pgVm.items && !pgVm.items.length">No practice groups found</h4>
	</div>
</div>

<script type="text/ng-template" id="settings/practice-groups/form.html">
	<div class="practice-groups-modal">
		<div class="modal-header" ng-if="modalVm.isCreate">
			Create New Practice Group
		</div>
		<div class="modal-header" ng-if="!modalVm.isCreate">
			Edit Practice Group
		</div>
		<div class="modal-body">

			<errors src="modalVm.errors"></errors>

			<div>
				<label>Name:</label>
				<input type="text" class="form-control" ng-model="modalVm.group.name">
			</div>

		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.save()">Save</button>
			<button class="btn btn-danger" ng-click="modalVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="settings/practice-groups/confirm-activate-modal.html">
	<div class="modal-body">
		<h4>Are you sure you want to activate this practice group?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>

<script type="text/ng-template" id="settings/practice-groups/confirm-deactivate-modal.html">
	<div class="modal-body">
		<h4>Are you sure you want to deactivate this practice group?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>

<script type="text/ng-template" id="settings/practice-groups/confirm-delete-modal.html">
	<div class="modal-body">
		<h4>Are you sure you want to delete this practice group?</h4>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" ng-click="ok()">Yes</button>
		<button class="btn btn-primary" ng-click="cancel()" type="button">Cancel</button>
	</div>
</script>
