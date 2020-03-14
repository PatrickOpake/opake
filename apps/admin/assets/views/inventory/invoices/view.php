<div class="inventory-invoice--form" ng-controller="InventoryInvoiceCrtl as docVm" ng-init="docVm.init(<?= $model->id; ?>)" ng-cloak>

	<errors src="docVm.errors"></errors>

	<div class="inventory-invoice--inputs">
		<div class="data-row">
			<label>Name*:</label>
			<input ng-model="docVm.form.name" type="text" class="form-control" />
		</div>
		<div class="data-row">
			<label>Date:</label>
			<date-field ng-model="docVm.form.date" icon="true"></date-field>
		</div>
		<div class="data-row">
			<label>Manufacturer:</label>
			<opk-select ng-model="docVm.form.manufacturers" multiple options="item as item.name for item in source.getVendors($query, 'manf')"></opk-select>
		</div>
	</div>
	<div class="inventory-invoice--actions">
		<button class="btn btn-success" ng-click="docVm.save()">Save</button>
		<a href="/inventory/invoices/{{ ::org_id }}">Cancel</a>
	</div>

	<div class="inventory-invoice--preview-items">
		<document-preview ng-if="docVm.loaded"
				  src="{{::docVm.getPreviewUrl()}}" page-count="{{::docVm.form.page_count}}">
		</document-preview>
		<div class="options-list--wrap">
			<span class="options-list--title">Item Names</span>
			<div class="options-list">
				<opk-select ng-model="docVm.addingItem" ng-change="docVm.addItem()"
					    options="item.full_name for item in source.getInventoryItems($query)"></opk-select>
				<ul>
					<li ng-repeat="item in docVm.form.items"
					    class="options-list--option">
						{{::item.name}}
						<a href="" class="options-list--option--remove" ng-click="docVm.removeItem(item)"><i class="glyphicon glyphicon-remove"></i></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>