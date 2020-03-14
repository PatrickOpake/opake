<div ng-controller="InsurancePayorsListCtrl as listVm" class="insurance-payors-list" ng-cloak>

	<div class="content-block filter-panel">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Insurance Company</label>
				<input type="text" ng-model="listVm.search_params.name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>Phone Number</label>
				<input type="text" ng-model="listVm.search_params.phone" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>Address 1</label>
				<input type="text" ng-model="listVm.search_params.address" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>Zip Code</label>
				<input type="text" ng-model="listVm.search_params.zip" class='form-control input-sm' placeholder='Type' />
			</div>
		</filters-panel>
		<div class="list-control">
			<a class='btn btn-success pull-left' href='' ng-click="listVm.downloadDatabase()">Download Database</a>
			<a class='btn btn-success btn-file pull-left' select-file on-select="listVm.uploadDatabase(files)">
				Upload Database
				<input type="file" name="file" />
			</a>

			<a class='btn btn-success' href='' ng-click="listVm.addRow()">Add</a>

			<div class="loading-wheel" ng-if="listVm.isLoading">
				<div class="loading-spinner"></div>
			</div>
		</div>
	</div>

	<div class="table-wrap content-block">
		<errors src="listVm.errors"></errors>
		<div show-loading-list="listVm.isLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th class="edit-items">&nbsp;</th>
					<th class="insurance-company" sort="name">Insurance Company</th>
					<th sort="ub04_payer_id">Navicure UB04 Code</th>
					<th sort="cms1500_payer_id">Navicure CMS1500 Code</th>
					<th sort="navicure_eligiblity_id">Navicure Eligibility ID</th>
					<th sort="carrier_code">Insurance Card Payer ID</th>
					<th sort="insurance_type">Insurance Type</th>
					<th sort="last_change_date">Last Edited Date</th>
					<th sort="last_change_user_name">User Name</th>
					<th ng-repeat-start="i in listVm.addressNumbers">Address {{::i}}</th>
					<th>City {{::i}}</th>
					<th>State {{::i}}</th>
					<th>Zip Code {{::i}}</th>
					<th ng-repeat-end>Phone {{::i}}</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td class="delete-items">
						<a class='btn btn-success' href='' ng-click="listVm.edit(item.id)">Edit</a>
						<a class='btn btn-danger' href='' ng-click="listVm.delete(item.id)">Delete</a>
					</td>
					<td class="insurance-company">{{::item.name}}</td>
					<td>{{::item.ub04_payer_id}}</td>
					<td>{{::item.cms1500_payer_id}}</td>
					<td>{{::item.navicure_eligibility_payor_id}}</td>
					<td>{{::item.carrier_code}}</td>
					<td>{{::item.insurance_type}}</td>
					<td>{{::item.last_change_date}}</td>
					<td>{{::item.last_change_user_name}}</td>
					<td ng-repeat-start="i in listVm.addressNumbers">{{::item.addresses[i-1].address}}</td>
					<td>{{::item.addresses[i-1].city_name}}</td>
					<td>{{::item.addresses[i-1].state_name}}</td>
					<td>{{::item.addresses[i-1].zip_code}}</td>
					<td ng-repeat-end>{{::item.addresses[i-1].phone}}</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.totalCount" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">No insurances found</h4>
		</div>
	</div>
	<div>
	</div>
</div>
