<div ng-controller="ItemizedBillListCtrl as listVm" class="billing-patient-statement--page" show-loading-list="listVm.isDocumentsLoading" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm" class="patient-statement--filter">
			<div class="data-row">
				<label>Last Name</label>
				<input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>First Name</label>
				<input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>MRN</label>
				<input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm'
							placeholder='#####-##'/>
			</div>
		</filters-panel>
	</div>

	<div class="content-block" show-loading-list="listVm.isInitLoading">
		<div class="list-control">
			<a href="" class="icon" ng-click="listVm.multiplePrint()" ng-disabled="!listVm.toSelected.length">
				<i class="icon-print-grey" uib-tooltip="Print"></i>
			</a>
		</div>
		<table class="opake">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th>
					<div class="checkbox">
						<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
						<label for="print_all"></label>
					</div>
				</th>
				<th sort="last_name">Last Name</th>
				<th sort="first_name">First Name</th>
				<th sort="mrn">MRN</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>
					<div>
						<div class="checkbox">
							<input id="print_{{$index}}"
								   type="checkbox"
								   class="styled"
								   ng-checked="listVm.isAddedToSelected(item.id)"
								   ng-click="listVm.addToSelected(item.id)">
							<label for="print_{{$index}}"></label>
						</div>
					</div>
				</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.last_name}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.first_name}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.mrn}}</td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 class="text-center" ng-if="!listVm.items.length">Items not found</h4>
	</div>

	<script type="text/ng-template" id="billing/itemized-bill/select-date-range.html">
		<div ng-form name="itemizedBillForm" class="modal-body itemized-bill-form" show-loading="listVm.isPrintLoading || listVm.isDocumentsLoading">
			<div class="top-line">
				<label>Select Date Range of Itemized Bill</label>
				<a href="" ng-click="cancel()" class="pull-right"><i class="glyphicon glyphicon-remove "></i></a>
			</div>
			<div>
				<div class="date-range">
					<date-field ng-model="listVm.statementForm.dateRangeFrom" ng-required="true" icon="true"></date-field>
					<label class="to">to</label>
					<date-field ng-model="listVm.statementForm.dateRangeTo" ng-required="true" icon="true"></date-field>
				</div>
				<div class="totals-buttons">
					<button ng-if="listVm.typeOfStatement == 'individual'" class="btn btn-grey" ng-disabled="itemizedBillForm.$invalid" ng-click="listVm.generateIndividualStatement()">Generate Itemized Bill</button>
					<button ng-if="listVm.typeOfStatement == 'multiple'" class="btn btn-grey" ng-disabled="itemizedBillForm.$invalid" ng-click="listVm.generateMultipleStatements()">Generate Itemized Bill</button>
				</div>
			</div>
		</div>
		<div class="modal-footer"></div>
	</script>
</div>