<div ng-controller="ItemMasterListCtrl as listVm" ng-cloak>
	<div class="content-block master-control">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Keyword</label>
				<input type="text" ng-model="listVm.search_params.name" class="form-control input-sm"
					   placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>HCPCS</label>

				<div class='input-group'>
					<input type="text" ng-model="listVm.search_params.hcpcs" class="form-control input-sm"
						   placeholder='Type'/>
				</div>
			</div>
			<div class="data-row">
				<label>Catalog Num</label>

				<div class='input-group'>
					<input type="text" ng-model="listVm.search_params.catalog_num" class="form-control input-sm"
						   placeholder='Type'/>
				</div>
			</div>
			<div class="data-row">
				<label>CDM</label>

				<div class='input-group'>
					<input type="text" ng-model="listVm.search_params.cdm" class="form-control input-sm"
						   placeholder='Type'/>
				</div>
			</div>
		</filters-panel>

		<div class="list-control">
			<button class='btn btn-grey btn-file pull-left'>
				Upload Item Master
				<form method='post' enctype='multipart/form-data'>
					<input type="file" name="file_import" onchange="this.form.submit()"/>
				</form>
			</button>
			<a class='btn btn-grey pull-left' href='/master/inventory/{{::org_id}}/download/'>Download Template</a>
			<a ng-if="listVm.action === 'view'" class='btn btn-primary' href='' ng-click="listVm.edit()">Edit Item
				Master</a>
			<a ng-if="listVm.action === 'edit'" class='btn btn-grey' href='' ng-click="listVm.cancel()">Cancel</a>
			<a ng-if="listVm.action === 'edit'" class='btn btn-success' href='' ng-click="listVm.save()">Save Item
				Master</a>
		</div>
	</div>
	<errors src="listVm.errors"></errors>
	<table class='opake master' ng-if="listVm.items.length">
		<thead sorter="listVm.search_params" callback="listVm.search()">
		<tr>
			<th>Item #</th>
			<th>Item Name</th>
			<th>Item <br> Description</th>
			<th>Substitutes</th>
			<th>HCPCS</th>
			<th>Quantity <br> Per Unit</th>
			<th>Unit of <br> Measure</th>
			<th>Unit Price</th>
			<th>Cost Multiple</th>
			<th>Charge <br> Amount</th>
			<th>Status</th>
			<th>National Drug <br> Code (NDC)</th>
			<th>Manufacturer</th>
			<th>Manufacturer <br> Catalog #</th>
			<th>Distributor <br> Name</th>
			<th>Distributor <br> Catalog #</th>
			<th>GLN #</th>
			<th>GTIN #</th>
			<th>Barcode #</th>
			<th>Barcode <br> Type</th>
			<th>Image</th>
			<th>Shipping <br> Type</th>
			<th>Unit <br> Weight</th>
			<th>Par Min</th>
			<th>Par Max</th>
			<th>Units <br> in Stock</th>
			<th>Type</th>
			<th>Remanufacturable</th>
			<th>Resterilizable</th>
			<th>Reusable</th>
			<th>Generic</th>
			<th>Implantable</th>
			<th>Latex</th>
			<th>Hazardous</th>
			<th>HIMS Indicator</th>
			<th>UNSPSC</th>
			<th>Opake ID</th>
		</tr>
		</thead>
		<tbody ng-include="view.get('master/inventory/' + listVm.action + '.html')"></tbody>
	</table>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		   callback="listVm.search()"></pages>
	<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>

	<a class='btn btn-grey top-buffer' href='' ng-click="listVm.downloadItemMaster()">Download Item Master</a>
</div>
