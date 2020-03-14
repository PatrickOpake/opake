<div ng-controller="InventoryInvoiceListCrtl as listVm" ng-init="listVm.init();" show-loading="listVm.isShowLoading" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Invoice Name</label>
				<opk-select ng-model="listVm.search_params.invoice" options="item.name for item in source.getInventoryInvoices($query)"></opk-select>
			</div>
			<div class="data-row">
				<label>Manufacturer</label>
				<opk-select ng-model="listVm.search_params.manufacturer" options="item.name for item in source.getVendors($query, 'manf')"></opk-select>
			</div>
			<div class="data-row">
				<label>Item Name</label>
				<opk-select ng-model="listVm.search_params.item" options="item.full_name for item in source.getInventoryItems($query)"></opk-select>
			</div>
		</filters-panel>

		<a href="" class="btn btn-success inventory-invoice--upload-button" ng-click="listVm.openUploadDialog()">Upload Invoice</a>

		<table class="opake" ng-if="listVm.items.length">
			<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th class="narrow"></th>
					<th sort="name">Invoice Name</th>
					<th sort="date">Invoice Date</th>
					<th>Manunfacturer</th>
					<th>Number of Items</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="item in listVm.items">
					<td><a href="" ng-click="listVm.showPreview(item)"><i class="icon-pdf"></i></a></td>
					<td><a href="/inventory/invoices/{{ ::org_id }}/view/{{ item.id }}">{{ ::item.name }}</a></td>
					<td>{{ ::item.date | date:'M/d/yyyy' }}</td>
					<td>{{ ::item.getManufacturerNames() }}</td>
					<td><a href="" uib-tooltip-html="item.getItemNames()">{{ ::item.items.length }}</a></td>
					<td><a href="" ng-click="listVm.deleteInvoice(item.id)" class="btn btn-danger">Delete</a></td>
				</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
		<h4 ng-if="listVm.items && !listVm.items.length">Invoices not found</h4>
	</div>
</div>

<script type="text/ng-template" id="inventory/invoices/delete-item-modal.html">
	<div>
		<div class="modal-header">
			<h4 class="modal-title">Delete Item</h4>
		</div>
		<div class="modal-body">
			Are you sure you would like to delete the item?
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="ok()">Delete</button>
			<button class="btn btn-grey" ng-click="cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="inventory/invoices/upload-form.html">
	<div class="modal-header">
		<h4 class="modal-title">Upload Invoice</h4>
		<a href="" ng-click="cancel()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
	</div>
	<div class="modal-form-upload" show-loading="listVm.isUploadLoading">
		<div class="modal-body">
			<errors src="listVm.modalErrors"></errors>
			<div ng-if="!listVm.form.uploadedFile" class="file-drop-box"
			     ngf-drop="listVm.uploadFile($files)" ngf-drag-over-class="'drag-over'">
				<div class="file-drop-box--help">
					<i class="icon-file-upload-cloud"></i> <br/>
					<span class="bold-text">Drag and drop a file here</span> <br/>
					or <br/>
					<button class="btn btn-grey btn-file" select-file on-select="listVm.uploadFile(files)"> Select File
						<input type="file" name="fileDoc" />
					</button>
				</div>
			</div>
			<div ng-if="listVm.form.uploadedFile" class="data-row upload-file--filename">
				{{listVm.form.uploadedFile.name}}
				<a ng-click="listVm.removeUploadedFile()" href="" class="remove">
					<i class="glyphicon glyphicon-remove-circle"></i>
				</a>
			</div>
			<div class="inventory-invoice--inputs">
				<div class="data-row">
					<label>Name:</label>
					<input type="text" class="form-control input-sm" ng-model="listVm.form.name">
				</div>
				<div class="data-row">
					<label>Date:</label>
					<date-field ng-model="listVm.form.date" icon="true"></date-field>
				</div>
				<div class="data-row">
					<label>Manufacturer:</label>
					<opk-select ng-model="listVm.form.manufacturers" multiple options="item as item.name for item in source.getVendors($query, 'manf')"></opk-select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-grey" ng-click="cancel()" type="button">Cancel</button>
			<button class="btn btn-success" ng-click="listVm.clickUpload()" ng-disabled="!listVm.isFormValid()">Upload</button>
		</div>
	</div>
</script>
