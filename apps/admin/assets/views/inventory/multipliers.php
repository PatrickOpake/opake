<div ng-controller="InventoryMultiplierCtrl as multiplierVm" ng-init="multiplierVm.init()" ng-cloak>
	<div class="content-block chargeable">
		<div class="row header">
			<div class="col-sm-2">
				<h3>Chargeable </h3>
			</div>
			<div class="col-sm-10">
				Set the price ceiling for items that will not be billed to the patient 
			</div>
		</div>
		<div class="set-price-row">
			<span>Items that are below </span>
			<span class="dollar">$ </span>
			<input type="text" class="form-control" ng-model="multiplierVm.charge_price" valid-number type-number="float"/>
			<span>will not be charged to the patient </span>
			<button type="button" class="btn btn-success" ng-click="multiplierVm.saveChargeable()" 
				ng-disabled="multiplierVm.charge_price == multiplierVm.original_charge_price">
				Set
			</button>
		</div>
	</div>

	<div class="content-block inventory-multiplier">
		<errors src="multiplierVm.errors"></errors>
		<div class="add-multiplier">
			<div class="row">
				<div class="col-sm-2 radio-column">
					<div class="radio">
						<input id="type-name" type="radio" name="multiplier_type" ng-model="multiplierVm.newItem.type" ng-value="0">
						<label for="type-name">Item Name</label>
					</div>
				</div>
				<div class="col-sm-2 radio-column">
					<div class="radio">
						<input id="type-type" type="radio" name="multiplier_type" ng-model="multiplierVm.newItem.type" ng-value="1">
						<label for="type-type">Item Type</label>
					</div>
				</div>
				<div class="col-sm-2"></div>
				<div class="col-sm-6">Cost Multiplier</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div ng-if="multiplierVm.newItem.typeIsItemName()">
						<opk-select select-options="{appendToBody: true, fixedDropdownWidth: true}"
									ng-model="multiplierVm.newItem.inventory" options="item.full_name for item in source.getInventoryItems($query)" placeholder="Type or Select">
						</opk-select>
					</div>
					<div ng-if="multiplierVm.newItem.typeIsItemType()">
						<opk-select select-options="{appendToBody: true}"
									ng-model="multiplierVm.newItem.inventory_type" options="item.name for item in source.getFullInventoryTypes()" placeholder="Type or Select">
						</opk-select>
					</div>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" ng-model="multiplierVm.newItem.multiplier" valid-number type-number="float"/>
				</div>
				<div class="col-sm-2"></div>
				<div class="col-sm-2">
					<button type="button" class="btn btn-success" ng-click="multiplierVm.AddNewMultiplier(multiplierVm.newItem)" ng-disabled="!multiplierVm.canSaveItem(multiplierVm.newItem)">Add</button>
				</div>
			</div>
		</div>
		<table class="opake multipliers-table" ng-if="multiplierVm.multipliers.length > 0">
			<thead>
			<tr>
				<th class="text-center">Item Name/Type</th>
				<th class="text-center">Item Number</th>
				<th class="text-center">Multiplier</th>
				<th class="text-center">Edit</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in multiplierVm.multipliers">
				<td class="name">
					<div ng-if="item.typeIsItemName()">
						<span ng-if="!item.edit_mode">{{ item.inventory.name }}</span>
						<div ng-if="item.edit_mode">
							<opk-select select-options="{appendToBody: true, fixedDropdownWidth: true}"
										ng-model="item.inventory" options="item.full_name for item in source.getInventoryItems($query)" placeholder="Type or Select">
							</opk-select>
						</div>
					</div>
					<div ng-if="item.typeIsItemType()">
						<span ng-if="!item.edit_mode">{{ item.inventory_type.name }}</span>
						<div ng-if="item.edit_mode">
							<opk-select select-options="{appendToBody: true}"
								ng-model="item.inventory_type" options="item.name for item in source.getFullInventoryTypes()" placeholder="Type or Select">
							</opk-select>
						</div>
					</div>
				</td>
				<td class="number text-center">
					<span ng-if="item.typeIsItemName()">{{ item.inventory.number }}</span>
				</td>
				<td class="multiplier text-center">
					<span ng-if="!item.edit_mode">{{item.multiplier ? item.multiplier : '' }}</span>
					<input ng-if="item.edit_mode" type="text" class="form-control" ng-model="item.multiplier" valid-number type-number="float"/>
				</td>
				<td class="actions text-center" ng-if="!item.edit_mode">
					<a href="" class="edit" ng-click="multiplierVm.editItem(item)"><i class="icon-edit"></i></a>
					<a href="" class="remove" ng-click="multiplierVm.removeItem(item)"><i class="icon-remove"></i></a>
				</td>
				<td class="buttons text-center" ng-if="item.edit_mode">
					<button type="button" class="btn btn-grey" ng-click="multiplierVm.cancelEditItem(item)">Cancel</button>
					<button type="button" class="btn btn-success" ng-click="multiplierVm.saveItem(item)" ng-disabled="!multiplierVm.canSaveItem(item)">Save</button>
				</td>
			</tr>
			</tbody>
		</table>

		<pages count="multiplierVm.total_count" page="multiplierVm.search_params.p" limit="multiplierVm.search_params.l"
			callback="multiplierVm.search()">
		</pages>
	</div>
</div>