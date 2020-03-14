<div class="report-list" ng-controller="AnalyticsCrtl as analyticsVm">
	<div class="headline">Case Cost</div>
	<div class="panel-data">
		<div class="data-row">View case by procedure, surgeon, or location, including margins around length of case,
			type and inventory
		</div>
		<div class="data-row" ng-cloak>
			<div>
				<opk-select ng-model="analyticsVm.report4.user" placeholder="Type"
							options="user.title for user in analyticsVm.case_cost_types"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report4.period" placeholder="Date Range"
							options="period.title for period in analyticsVm.periods"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report4.format" placeholder="Format"
							options="format.title for format in analyticsVm.formats"></opk-select>
			</div>
			<div class="actions">
				<a href="" ng-click="analyticsVm.reset(analyticsVm.report4)" class="cancel">Clear</a>
			</div>
			<div class="text-right"><a class='btn btn-primary' target="_blank" href='/i/analytic/Personell_spend.jpg'>Generate
					Report</a></div>
		</div>
	</div>
</div>