<div ng-controller="InternalOrderListCrtl as listVm" class="content-block" ng-cloak>
	<filters-panel ctrl="listVm">
		<div class="data-row">
			<label>Date from</label>
			<div class='input-group'>
				<date-field ng-model="listVm.search_params.date_from"  icon="true" placeholder="mm/dd/yyyy" small="true"></date-field>
			</div>
		</div>
		<div class="data-row">
			<label>Date to</label>
			<div class='input-group'>
				<date-field ng-model="listVm.search_params.date_to"  icon="true" placeholder="mm/dd/yyyy" small="true"></date-field>
			</div>
		</div>
		<div class="data-row">
			<label># items from</label>
			<div class='input-group'>
				<input type="text" ng-model="listVm.search_params.count_from" valid-number class='form-control input-sm' placeholder='Type' />
			</div>
		</div>
		<div class="data-row">
			<label># items to</label>
			<div class='input-group'>
				<input type="text" ng-model="listVm.search_params.count_to" valid-number class='form-control input-sm' placeholder='Type' />
			</div>
		</div>
		<div class="data-row">
			<label>PO number</label>
			<div class='input-group'>
				<input type="text" ng-model="listVm.search_params.order_id" valid-number class="form-control input-sm" placeholder='Type' />
			</div>
		</div>
		<div class="data-row">
			<label>Client</label>
			<opk-select ng-model="listVm.search_params.org" options="item.name as item.name for item in source.getOrganizations()"></opk-select>
		</div>
	</filters-panel>

	<table class="opake" ng-if="listVm.items.length">
		<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th sort="date">Date</th>
				<th class="text-center" sort="id">PO number</th>
				<th>Image Link</th>
				<th class="text-center" sort="item_count"># Unique Items</th>
				<th class="text-center" sort="org">Client</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>{{ ::item.date | date:'M/d/yyyy' }}</td>
				<td class="text-center"><a href='/orders/internal/view/{{ item.id }}'>{{ item.id }}</a></td>
				<td>
					<a ng-repeat="image in item.images" href="{{ image.image }}" data-lightbox="roadtrip">{{ image.index }}</a>
				</td>
				<td class="text-center">{{ item.item_count }}</td>
				<td class="text-center">{{ item.organization_name }}</td>
			</tr>
		</tbody>
	</table>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
	<h4 ng-if="listVm.items && !listVm.items.length">No purchase orders found</h4>
</div>
