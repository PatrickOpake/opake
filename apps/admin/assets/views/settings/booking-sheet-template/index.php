<div class="panel-data booking-sheet-template-page" ng-controller="BookingSheetTemplateListCtrl as templatesListVm" ng-init="templatesListVm.init()" show-loading="templatesListVm.isShowLoading" ng-cloak>
	<div>
		<errors src="templatesListVm.errors"></errors>
		<div>
			<div class="header-title">
				<span class="right-buttons">
					<a href="/settings/booking-sheet-templates/{{::org_id}}/create" class="btn btn-success">Create Booking Sheet Template</a>
				</span>
			</div>

			<table ng-if="templatesListVm.templates" class="opake templates-table">
				<thead>
				<tr>
					<th class="column-name">Name</th>
					<th class="column-sites">Sites</th>
					<th class="column-settings">Settings</th>
				</tr>
				</thead>
				<tbody>

				<tr ng-repeat="template in templatesListVm.templates">
					<td class="column-name">{{ ::template.name }}</td>
					<td class="column-sites">
						<span ng-if="templatesListVm.isAllSitesChecked(template)">All</span>
						<span ng-if="templatesListVm.isNoOneSitesChecked(template)">None</span>
					<span ng-if="!templatesListVm.isAllSitesChecked(template) && !templatesListVm.isNoOneSitesChecked(template)" ng-repeat="site in template.sites">
						{{ ::site.name }} {{ $last ? '' : ', ' }}
					</span>
					</td>
					<td class="control form-settings">
					<span class="dropdown" uib-dropdown dropdown-append-to-body>
						<a href="#" class="upload-forms--control-link" id="simple-dropdown" uib-dropdown-toggle>
							<i class="icon-grey-gear"></i>
							<span class="glyphicon glyphicon-triangle-bottom"></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-right form-settings-dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="simple-dropdown">
							<li role="menuitem">
								<a href="#" ng-click="templatesListVm.renameTemplate(template)">Rename</a>
							</li>
							<li role="menuitem">
								<a href="#" ng-click="templatesListVm.assignTemplate(template)">Assign</a>
							</li>
							<li role="menuitem" ng-if="template.type == 1">
								<a href="/settings/booking-sheet-templates/{{::org_id}}/edit/default">Edit</a>
							</li>
							<li role="menuitem" ng-if="template.type != 1">
								<a href="/settings/booking-sheet-templates/{{::org_id}}/edit/{{::template.id}}">Edit</a>
							</li>
							<li role="menuitem" ng-if="template.type != 1">
								<a href="#" ng-click="templatesListVm.deleteTemplate(template)">Delete</a>
							</li>
						</ul>
					</span>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/ng-template" id="booking-sheet-template/rename-template.html">
	<div >
		<div class="modal-header">
			<h4 class="modal-title">Rename</h4>
			<a href="" ng-click="modalVm.close()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<errors src="modalVm.errors"></errors>
			<div class="data-row">
				<input type="text" class="form-control" ng-model="modalVm.template.name" />
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.rename()">Rename</button>
			<button class="btn btn-grey" ng-click="modalVm.close()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="booking-sheet-template/assign-template.html">
	<div>
		<div class="modal-header">
			<h4 class="modal-title">Assign</h4>
			<a href="" ng-click="modalVm.close()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-sm-12">
					<span class="checkbox">
					<input id="assign-all-sites" type="checkbox" ng-model="modalVm.template.is_all_sites" />
					<label for="assign-all-sites">All sites</label>
				</span>
				</div>
			</div>
			<div class="row" ng-if="!modalVm.template.is_all_sites">
				<div class="col-sm-2">
					<label>Sites:</label>
				</div>
				<div class="col-sm-10">
					<opk-select ng-model="modalVm.template.sites"
					            multiple
					            options="site.name for site in source.getSites()"></opk-select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success text-center " ng-click="modalVm.save()">Save</button>
			<button class="btn btn-grey" ng-click="modalVm.close()">Cancel</button>
		</div>
	</div>
</script>


<script type="text/ng-template" id="booking-sheet-template/delete-template.html">
	<div>
		<div class="modal-header">
			<h4 class="modal-title">Delete Template</h4>
			<a href="" ng-click="modalVm.close()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<b>Are you sure you would like to delete the template?</b>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.delete()">Delete</button>
			<button class="btn btn-grey" ng-click="modalVm.close()">Cancel</button>
		</div>
	</div>
</script>
