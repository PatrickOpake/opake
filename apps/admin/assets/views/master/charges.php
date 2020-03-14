<div ng-controller="ChargesMasterListCtrl as listVm" ng-init="listVm.init(<?= $siteId ?>)" show-loading="listVm.isLoading" ng-cloak class="charge-master">
	<div class="content-block master-control">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Keyword</label>
				<input type="text" ng-model="listVm.search_params.name" class="form-control input-sm" placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>Charge Code</label>
				<input type="text" ng-model="listVm.search_params.cdm" class="form-control input-sm" placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>Department</label>
				<input type="text" ng-model="listVm.search_params.department" class="form-control input-sm" placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>Charge min</label>
				<input type="text" ng-model="listVm.search_params.amount_from" valid-number class='form-control input-sm'
					   placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>Charge max</label>
				<input type="text" ng-model="listVm.search_params.amount_to" valid-number class='form-control input-sm'
					   placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>CPT/HCPCS</label>
				<opk-select ng-model="listVm.search_params.cpt"
							options="item.cpt as item.cpt for item in listVm.searchCPT($query)"></opk-select>
			</div>
		</filters-panel>

		<div class="list-control">
			<a class='btn btn-success pull-left' href='' ng-click="listVm.downloadChargeMaster()">Download Charge Master</a>
			<a class='btn btn-success btn-file pull-left' select-file on-select="listVm.uploadChargeMaster(files)">
				Upload Charge Master
				<input type="file" name="file" />
			</a>

			<a ng-if="listVm.action === 'view'" class='btn btn-primary' href='' ng-click="listVm.edit()">Edit</a>
			<a ng-if="listVm.action === 'edit'" class='btn btn-grey' href='' ng-click="listVm.cancel()">Cancel</a>
			<a ng-if="listVm.action === 'edit'" class='btn btn-success' href='' ng-click="listVm.save(ChargeMasterForm)">Save</a>
			<a ng-if="listVm.action === 'edit'" class='btn btn-success' href='' ng-click="listVm.addRow()">Add Row</a>
		</div>

		<errors src="listVm.errors"></errors>
	</div>

	<div class="content-block charges-list" ng-form name="ChargeMasterForm">
		<table class='opake master' horizontal-scroll>
			<thead>
			<tr>
				<th>Charge <br> Code</th>
				<th>Description</th>
				<th>Charge <br> Amount</th>
				<th>Revenue <br> Code</th>
				<th>Department No.</th>
				<th>CPT<span class="registered-sign"></span>/HCPCS</th>
				<th>CPT<span class="registered-sign"></span> <br> Modifier 1</th>
				<th>CPT<span class="registered-sign"></span> <br> Modifier 2</th>
				<th>Unit Cost</th>
				<th>National Drug <br> Code (NDC)</th>
				<th>Active <br>(Y/N)</th>
				<th>GL-Code</th>
				<th>Notes</th>
				<th>Last Edited Date</th>
				<th>Historical Price</th>
			</tr>
			</thead>
			<tbody ng-include="view.get('master/charges/' + listVm.action + '.html')"></tbody>
		</table>
	</div>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
</div>
