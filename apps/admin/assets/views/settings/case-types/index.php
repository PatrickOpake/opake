<div ng-controller="CaseTypeListCrtl as listVm" class="content-block case-types--list" show-loading="listVm.isExportGenerating" ng-cloak>
	<filters-panel ctrl="listVm">
		<div class="data-row">
			<label>Description</label>
			<input type="text" ng-model="listVm.search_params.name" class='form-control input-sm' placeholder='Type procedure description' />
		</div>
		<div class="data-row">
			<label>HCPCS/CPT</label>
			<input type="text" ng-model="listVm.search_params.code" class='form-control input-sm' placeholder='Type HCPCS/CPT code' />
		</div>
		<div class="data-row">
			<label>Status</label>
			<opk-select ng-model="listVm.search_params.active" key-value-options="listVm.procedureConst.STATUSES"></opk-select>
		</div>
	</filters-panel>

	<div class="list-control">
		<a class='btn btn-success btn-file pull-left' select-file on-select="listVm.upload(files)">
			Upload Procedures
			<input type="file" name="file" />
		</a>
		<a class='btn btn-success pull-left' href='/settings/case-types/{{org_id}}/download'>Download Procedures</a>

		<?php if ($_check_access('case_types', 'create')): ?>
			<a class="btn btn-success" href="" ng-click="listVm.openCreateDialog()">Create New Procedure</a>
		<?php endif ?>
	</div>

	<errors src="listVm.errors"></errors>

	<div class="table-wrap">
		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th sort="name">Description</th>
					<th sort="code">HCPCS/CPT Code</th>
					<th sort="length">Case Length</th>
					<th sort="active">Status</th>
					<th>Actions</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in listVm.items">
					<td>{{ ::item.name }}</td>
					<td ng-if="!item.cpt.code">{{ item.code }}</td>
					<td ng-if="item.cpt.code" class="opk-codes--select"><a href="" uib-tooltip="{{ item.cpt.name }}" tooltip-placement="bottom">
							{{ item.cpt.code }}
						</a></td>
					<td>{{ ::item.length | date:'H:mm' }}</td>
					<td>
						<span ng-if="item.active == 1">active</span>
						<span ng-if="item.active == 0">inactive</span>
					</td>
					<td>
						<?php if ($_check_access('case_types', 'create')): ?>
							<a ng-if="item.active == 1" href="" ng-click="listVm.deactivate(item.id)">Deactivate</a>
							<a ng-if="item.active == 0" href="" ng-click="listVm.activate(item.id)">Activate</a>
						<?php endif ?>
					</td>
					<td>
						<?php if ($_check_access('case_types', 'create')): ?>
							<a href="" ng-click="listVm.openEditDialog(item)">Edit</a>
						<?php endif ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">No procedures found</h4>
	</div>
</div>
