<div class="report-list" ng-controller="AnalyticsCrtl as analyticsVm">
	<div class="headline">User Activity</div>
	<div class="panel-data">
		<div class="data-row">Report of all activities performed by user during set time period</div>
		<div class="data-row" ng-cloak>
			<div>
				<opk-select ng-model="analyticsVm.report1.user" placeholder="User"
							options="user.title for user in analyticsVm.users"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report1.period" placeholder="Date Range"
							options="period.title for period in analyticsVm.periods"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report1.format" placeholder="Format"
							options="format.title for format in analyticsVm.formats"></opk-select>
			</div>
			<div class="actions">
				<a href="" ng-click="analyticsVm.reset(analyticsVm.report1)" class="cancel">Clear</a>
			</div>
			<div class="text-right"><a class='btn btn-primary' target="_blank" href='/i/analytic/Expried_Inventory.jpg'>Generate
					Report</a></div>
		</div>
	</div>

	<div class="headline">Audit Report</div>
	<div class="panel-data">
		<div class="data-row">See a record of every late, canceled or lengthened case</div>
		<div class="data-row" ng-cloak>
			<div>
				<opk-select ng-model="analyticsVm.report2.user" placeholder="Item Type"
							options="user.title for user in analyticsVm.users"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report2.period" placeholder="Date Range"
							options="period.title for period in analyticsVm.periods"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report2.format" placeholder="Format"
							options="format.title for format in analyticsVm.formats"></opk-select>
			</div>
			<div class="actions">
				<a href="" ng-click="analyticsVm.reset(analyticsVm.report2)" class="cancel">Clear</a>
			</div>
			<div class="text-right"><a class='btn btn-primary' target="_blank" href='/i/analytic/Expried_Inventory.jpg'>Generate
					Report</a></div>
		</div>
	</div>
	<div class="headline">Expiring</div>
	<div class="panel-data">
		<div class="data-row">All expired and expiring items and associate costs over time</div>
		<div class="data-row" ng-cloak>
			<div>
				<opk-select ng-model="analyticsVm.report3.user" placeholder="Type"
							options="user.title for user in analyticsVm.users"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report3.period" placeholder="Date Range"
							options="period.title for period in analyticsVm.periods"></opk-select>
			</div>
			<div>
				<opk-select ng-model="analyticsVm.report3.format" placeholder="Format"
							options="format.title for format in analyticsVm.formats"></opk-select>
			</div>
			<div class="actions">
				<a href="" ng-click="analyticsVm.reset(analyticsVm.report3)" class="cancel">Clear</a>
			</div>
			<div class="text-right"><a class='btn btn-primary' target="_blank" href='/i/analytic/Expried_Inventory.jpg'>Generate
					Report</a></div>
		</div>
	</div>
</div>