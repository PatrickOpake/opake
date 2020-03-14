<div class="inventory-report" ng-controller="InventoryReportListCrtl as listVm" ng-cloak show-loading="listVm.isShowLoading">
	<div class="content-block">
		<filters-panel ctrl="listVm" name-search-btn="Filter Cases" is-hide-buttons="listVm.table === 'inventory'">
			<div class="data-row">
				<label>From</label>
				<div>
					<date-field ng-model="listVm.search_params.start" placeholder="mm/dd/yyyy" small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>To</label>
				<div>
					<date-field ng-model="listVm.search_params.end" placeholder="mm/dd/yyyy" small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>Item Type</label>
				<opk-select ng-model="listVm.search_params.inventory_type" options="item for item in source.getInventoryTypes()"></opk-select>
			</div>
			<div class="data-row">
				<label>Manufacturer</label>
				<opk-select ng-model="listVm.search_params.inventory_manf" options="item.name for item in source.getVendors($query, 'manf')"></opk-select>
			</div>
			<div class="data-row">
				<label>Item Description</label>
				<input type="text" ng-model="listVm.search_params.inventory_desc" class="form-control input-sm" placeholder='Type'/>
			</div>
			<div class="data-row">
				<label>Physician</label>
				<opk-select ng-model="listVm.search_params.doctor" options="doctor.fullname for doctor in source.getSurgeons()"></opk-select>
			</div>
			<div class="data-row">
				<label>Procedure</label>
				<opk-select class="long-options" ng-model="listVm.search_params.procedure"
					    options="type.full_name for type in source.getCaseTypes($query)"></opk-select>
			</div>
			<div class="data-row">
				<label>#/Item Name</label>
				<opk-select ng-model="listVm.search_params.inventory" options="item.full_name for item in source.getInventoryItems($query)"></opk-select>
			</div>
		</filters-panel>

		<ng-include src="view.get('inventory/report/' + listVm.table + '_table.html')"></ng-include>
	</div>
</div>