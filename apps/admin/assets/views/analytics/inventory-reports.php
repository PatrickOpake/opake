<div class="report-list" ng-controller="AnalyticsCrtl as analyticsVm">
	<div class="headline">Inventory Usage</div>
	<div class="panel-data">
		<div class="data-row">View record of inventory used and associated cost by location, procedure type or staff
		</div>
		<div class="data-row" ng-cloak>
			<div>
				<opk-select ng-model="analyticsVm.report5.user" placeholder="Type"
							options="user.title for user in analyticsVm.inventory_used_types"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report5.period" placeholder="Date Range"
							options="period.title for period in analyticsVm.periods"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report5.format" placeholder="Format"
							options="format.title for format in analyticsVm.formats"></opk-select>
			</div>
			<div class="actions">
				<a href="" ng-click="analyticsVm.reset(analyticsVm.report5)" class="cancel">Clear</a>
			</div>
			<div class="text-right"><a class='btn btn-primary' target="_blank" href='/i/analytic/Personell_spend.jpg'>Generate
					Report</a></div>
		</div>
	</div>
</div>