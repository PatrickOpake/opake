<?php
/* @var $this \Opake\View\View */
?>

<div class="report-list user-profile analytics-reports-export-params content-block" ng-controller="AnalyticsReportsCtrl as reportVm" show-loading="reportVm.isReportGenerating" ng-cloak>
	<div class="headline">Analytics</div>

	<errors src="reportVm.errors"></errors>

	<div class="data-row report-type">
		<div class="data-row">
			<label>Report Type</label>
			<opk-select class="small" ng-model="reportVm.selectedParams.reportType" ng-change="reportVm.reportTypeChanged()"
						options="type.id as type.name for type in reportVm.reportTypesList"></opk-select>
			<div class="text-left" ng-if="!!reportVm.selectedParams.parentReportType">
				<a class="btn btn-danger" ng-click="reportVm.deleteCustomReportDlg()">Delete</a>
			</div>
		</div>
	</div>

	<div class="sub-headline">Filters</div>

	<div ng-form name="ReportFilterForm">
	<div ng-if="reportVm.selectedParams.reportType == reportVm.reportTypes.INFECTION.id
		|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.INFECTION.id">

		<div class="report-filters">
			<div>
				<div class="filters-row date-range">
					<label ng-class="{'invalid': !reportVm.selectedParams.dateFrom || !reportVm.selectedParams.dateTo}">Date Range</label>
					<date-field ng-model="reportVm.selectedParams.dateFrom" icon="true" ng-required="true"></date-field>
					<label class="to">to</label>
					<date-field ng-model="reportVm.selectedParams.dateTo" icon="true" ng-required="true"></date-field>
				</div>
				<div class="filters-row">
					<label>Surgeon</label>
					<opk-select multiple
								ng-model="reportVm.selectedParams.surgeons"
								placeholder="Type or select"
								options="value.id as value.fullname for value in source.getSurgeons()"></opk-select>
				</div>
			</div>
		</div>

		<div class="analytics-reports data-row buttons">
			<div class="export-param-field">
				<div class="text-right">
					<a class='btn btn-primary' ng-click="reportVm.generateMonthlyIPC()">Monthly IPC</a>
					<a class='btn btn-primary' ng-click="reportVm.generatePostOpInfection()">Post-Op Infection</a>
				</div>
			</div>
		</div>

	</div>

	<div ng-if="reportVm.selectedParams.reportType == reportVm.reportTypes.CASE_BLOCK_UTILIZATION.id
		|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.CASE_BLOCK_UTILIZATION.id">

		<div class="report-filters">
			<div>
				<div class="filters-row date-range">
					<label ng-class="{'invalid': !reportVm.selectedParams.dateFrom || !reportVm.selectedParams.dateTo}">Date Range</label>
					<date-field ng-model="reportVm.selectedParams.dateFrom" icon="true" ng-required="true"></date-field>
					<label class="to">to</label>
					<date-field ng-model="reportVm.selectedParams.dateTo" icon="true" ng-required="true"></date-field>
				</div>
				<div class="filters-row">
					<label>Surgeon</label>
					<opk-select multiple
								ng-model="reportVm.selectedParams.surgeons"
								placeholder="Type or select"
								options="value.id as value.fullname for value in source.getSurgeons()"></opk-select>
				</div>
			</div>
			<div>
				<div class="filters-row">
					<label>Practice Name</label>
					<opk-select multiple
								select-options="{appendToBody: false}"
								ng-model="reportVm.selectedParams.practiceGroups" placeholder="Type or select"
								options="value.id as value.name for value in source.getPracticeGroups(org_id)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Location</label>
					<opk-select multiple
								select-options="{appendToBody: false}"
								ng-model="reportVm.selectedParams.locations"
								placeholder="Type or select"
								options="value.name for value in source.getLocations()"></opk-select>
				</div>
			</div>
		</div>

		<div class="analytics-reports data-row">
			<div class="export-param-field">
				<div class="text-right"><a class='btn btn-primary' ng-click="reportVm.generateReport()">Generate Report</a></div>
			</div>
		</div>
	</div>

	<div ng-if="reportVm.selectedParams.reportType == reportVm.reportTypes.PROCEDURES_REPORT.id
		|| reportVm.selectedParams.reportType == reportVm.reportTypes.CASES_REPORT.id
		|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.PROCEDURES_REPORT.id
		|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.CASES_REPORT.id">

		<div class="report-filters">
			<div>
				<div class="filters-row date-range">
					<label ng-class="{'invalid': !reportVm.selectedParams.dateFrom || !reportVm.selectedParams.dateTo}">Date Range</label>
					<date-field ng-model="reportVm.selectedParams.dateFrom" icon="true" ng-required="true"></date-field>
					<label class="to">to</label>
					<date-field ng-model="reportVm.selectedParams.dateTo" icon="true" ng-required="true"></date-field>
				</div>
				<div class="filters-row">
					<label>Insurance</label>
					<opk-select multiple ng-model="reportVm.selectedParams.insurances" placeholder="Type or select"
								options="value.name for value in source.getInsurances($query, false, true)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Surgeon</label>
					<opk-select multiple
								ng-model="reportVm.selectedParams.surgeons"
								placeholder="Type or select"
								options="value.id as value.fullname for value in source.getSurgeons()"></opk-select>
				</div>
			</div>
			<div>
				<div class="filters-row">
					<label>Procedure</label>
					<opk-select multiple
								class="opk-codes--select"
								select-options="{appendToBody: true, fixedDropdownWidth: true, autocompleteOnly: true, searchFilter: 'opkSelectCpt', reorder: true}"
								ng-model="reportVm.selectedParams.procedures"
								placeholder="Type or select"
								options="type.full_name for type in source.getCaseTypes($query)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Practice Name</label>
					<opk-select multiple
								select-options="{appendToBody: false}"
								ng-model="reportVm.selectedParams.practiceGroups" placeholder="Type or select"
								options="value.id as value.name for value in source.getPracticeGroups(org_id)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Item Type</label>
					<opk-select multiple ng-model="reportVm.selectedParams.inventoryItemTypes" placeholder="Type or select"
								options="value as value for value in source.getInventoryTypes()"></opk-select>
				</div>
			</div>
		</div>

		<div class="analytics-reports data-row">
			<div class="export-param-field">
				<div class="text-right"><a class='btn btn-primary' ng-click="reportVm.generateReport()">Generate Report</a></div>
			</div>
		</div>
	</div>

	<div ng-if="reportVm.selectedParams.reportType != reportVm.reportTypes.CASE_BLOCK_UTILIZATION.id
		&& reportVm.selectedParams.reportType != reportVm.reportTypes.INFECTION.id
		&& reportVm.selectedParams.reportType != reportVm.reportTypes.PROCEDURES_REPORT.id
		&& reportVm.selectedParams.reportType != reportVm.reportTypes.CASES_REPORT.id
		&& reportVm.selectedParams.parentReportType != reportVm.reportTypes.CASE_BLOCK_UTILIZATION.id
		&& reportVm.selectedParams.parentReportType != reportVm.reportTypes.INFECTION.id
		&& reportVm.selectedParams.parentReportType != reportVm.reportTypes.PROCEDURES_REPORT.id
		&& reportVm.selectedParams.parentReportType != reportVm.reportTypes.CASES_REPORT.id">

		<div class="report-filters">
			<div>
				<div class="filters-row date-range">
					<label ng-class="{'invalid': !reportVm.selectedParams.dateFrom || !reportVm.selectedParams.dateTo}">Date Range</label>
					<date-field ng-model="reportVm.selectedParams.dateFrom" icon="true" ng-required="true"></date-field>
					<label class="to">to</label>
					<date-field ng-model="reportVm.selectedParams.dateTo" icon="true" ng-required="true"></date-field>
				</div>
				<div class="filters-row">
					<label>Insurance Company</label>
					<opk-select multiple ng-model="reportVm.selectedParams.insurances" placeholder="Type or select"
								options="value.name for value in source.getInsurances($query, false, true)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Surgeon</label>
					<opk-select multiple ng-model="reportVm.selectedParams.surgeons" placeholder="Type or select"
								options="value.id as value.fullname for value in source.getSurgeons()"></opk-select>
				</div>
				<div class="filters-row">
					<label>Item Number/Name</label>
					<opk-select multiple
								class="opk-codes--select"
								select-options="{appendToBody: true, fixedDropdownWidth: true, autocompleteOnly: true, searchFilter: 'opkSelectInventoryItemNumber', reorder: true}"
								ng-model="reportVm.selectedParams.inventoryItems"
								placeholder="Type or select"
								options="type.full_name for type in source.getInventoryItems($query)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Practice Name</label>
					<opk-select multiple ng-model="reportVm.selectedParams.practiceGroups" placeholder="Type or select"
								options="value.id as value.name for value in source.getPracticeGroups(org_id)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Item Type</label>
					<opk-select multiple ng-model="reportVm.selectedParams.inventoryItemTypes" placeholder="Type or select"
								options="value as value for value in source.getInventoryTypes()"></opk-select>
				</div>
				<div class="filters-row">
					<label>Procedure</label>
					<opk-select multiple
								class="opk-codes--select"
								select-options="{appendToBody: true, fixedDropdownWidth: true, autocompleteOnly: true, searchFilter: 'opkSelectCpt', reorder: true}"
								ng-model="reportVm.selectedParams.procedures"
								placeholder="Type or select"
								options="type.full_name for type in source.getCaseTypes($query)"></opk-select>
				</div>
				<div class="filters-row">
					<label>Manufacturer</label>
					<opk-select multiple ng-model="reportVm.selectedParams.manufacturers" placeholder="Type or select"
								options="value.name for value in source.getVendors($query, 'manf')"></opk-select>
				</div>
				<div class="filters-row">
					<label>Primary Insurance Type</label>
					<opk-select multiple
								class="insurance-types"
								ng-model="reportVm.selectedParams.insuranceTypes"
								options="item.id  as item.name disable when reportVm.isDisabledInsuranceTypeItem(item) for item in reportVm.insuranceTypes"
								placeholder="Type or select"></opk-select>
				</div>
				<div class="filters-row">
					<label>Billing Status</label>
					<opk-select multiple
							ng-model="reportVm.selectedParams.billing_status"
							key-value-options="reportVm.manualBillingStatuses"
							placeholder="Type or select">
					</opk-select>
				</div>
			</div>
		</div>

	    </div>
	</div>

		<div class="sub-headline">Display Columns <a href="" ng-click="reportVm.clearAllColumns()" class="clear-all-columns">(Clear All)</a></div>

		<div class="columns report-columns">

			<div ng-if="permissions.hasAccess('analytics', 'view_billing')">
				<label>Billing</label>
				<ul class="columns-list">
					<li ng-repeat="column in reportVm.columnGroups.billing">
						<span class="checkbox">
							<input id="report-column-{{column}}" type="checkbox" ng-model="reportVm.selectedParams.selectedColumns[column]"/>
							<label for="report-column-{{column}}">{{reportVm.columnLabels[column] || column}}</label>
						</span>
					</li>
				</ul>
			</div>

			<label>Demographics</label>
			<ul class="columns-list">
				<li ng-repeat="column in reportVm.columnGroups.demographic">
				<span class="checkbox">
					<input id="report-column-{{column}}" type="checkbox" ng-model="reportVm.selectedParams.selectedColumns[column]"/>
					<label for="report-column-{{column}}">{{reportVm.columnLabels[column] || column}}</label>
				</span>
				</li>
			</ul>

			<label>Case Details</label>
			<ul ng-if="reportVm.selectedParams.reportType == reportVm.reportTypes.CANCELED_CASES.id
				|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.CANCELED_CASES.id" class="columns-list">
				<li ng-repeat="column in reportVm.columnGroups.caseDetailsForCancelledCases">
				<span class="checkbox">
					<input id="report-column-{{column}}" type="checkbox" ng-model="reportVm.selectedParams.selectedColumns[column]"/>
					<label for="report-column-{{column}}">{{reportVm.columnLabels[column] || column}}</label>
				</span>
				</li>
			</ul>
			<ul ng-if="reportVm.selectedParams.reportType != reportVm.reportTypes.CANCELED_CASES.id
				&& reportVm.selectedParams.parentReportType != reportVm.reportTypes.CANCELED_CASES.id" class="columns-list">
				<li ng-repeat="column in reportVm.columnGroups.caseDetails">
				<span class="checkbox">
					<input id="report-column-{{column}}" type="checkbox" ng-model="reportVm.selectedParams.selectedColumns[column]"/>
					<label for="report-column-{{column}}">{{reportVm.columnLabels[column] || column}}</label>
				</span>
				</li>
			</ul>

			<div ng-if="reportVm.selectedParams.reportType == reportVm.reportTypes.INVENTORY.id
				|| reportVm.selectedParams.parentReportType == reportVm.reportTypes.INVENTORY.id">
				<label>Inventory</label>
				<ul class="columns-list">
					<li ng-repeat="column in reportVm.columnGroups.inventory">
				<span class="checkbox">
					<input id="report-column-{{column}}" type="checkbox" ng-model="reportVm.selectedParams.selectedColumns[column]"/>
					<label for="report-column-{{column}}">{{reportVm.columnLabels[column] || column}}</label>
				</span>
					</li>
				</ul>
			</div>
		</div>

		<div class="analytics-reports data-row">
			<div class="export-param-field">
				<div class="text-right">
					<a class="btn btn-grey" ng-click="reportVm.showSaveReportDialog()">Save New Report Type</a>
					<a class='btn btn-primary' ng-disabled="ReportFilterForm.$invalid" ng-click="reportVm.generateReport()">Generate Report</a>
				</div>
			</div>
		</div>
	</div>

</div>