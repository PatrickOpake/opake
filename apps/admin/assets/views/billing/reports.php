<div ng-controller="BillingReportsListCtrl as listVm" class="billing-reports" ng-cloak>
	<div class="content-block">
		<ng-include src="view.get('billing/reports/filters.html')"></ng-include>

		<div class="row list-control">
			<div class="col-sm-6">
				<a class="btn btn-success" href="" ng-click="listVm.export()">Export to CSV</a>
			</div>
			<div class="col-sm-6">
				<div class="pull-right">
					<a class="btn btn-success" href="" ng-click="listVm.addNew()">Create New Entry</a>
				</div>
			</div>
		</div>
	</div>

	<div class="billing-reports-tabs">
		<uib-tabset class="opk-tabs form-horizontal patient-view-info" active="listVm.activeTab">
			<uib-tab index="0" heading="Cases" select="listVm.deselectTab($event, $selectedIndex, 'cases')">
				<ng-include src="view.get('billing/reports/cases.html')"></ng-include>
			</uib-tab>
			<uib-tab index="1" heading="Procedures" select="listVm.deselectTab($event, $selectedIndex, 'procedures')">
				<ng-include src="view.get('billing/reports/procedures.html')"></ng-include>
			</uib-tab>
		</uib-tabset>
	</div>
</div>