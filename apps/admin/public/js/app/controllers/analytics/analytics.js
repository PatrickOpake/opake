// Vendor save
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AnalyticsCrtl', [
		'$scope',

		function ($scope) {

			var vm = this;

			vm.report1 = {};
			vm.report2 = {};
			vm.report3 = {};
			vm.report4 = {};
			vm.report5 = {};

			vm.users = [
				{id: 0, title: 'Example A'},
				{id: 1, title: 'Example B'}
			];
			vm.case_cost_types = [
				{id: 'procedure', title: 'By procedure'},
				{id: 'doctor', title: 'By doctor'}
			];
			vm.inventory_used_types = [
				{id: 'total', title: 'Total usage'},
				{id: 'cost', title: 'Cost'},
				{id: 'savings', title: 'Savings'}
			];

			vm.periods = [
				{key: 1, title: 'Yesterday'},
				{key: 7, title: 'Last 7 days'},
				{key: 30, title: 'Last 30 days'},
				{key: 180, title: 'Last 6 months'},
				{key: 'custom', title: 'Custom range'}
			];
			vm.formats = [
				{id: 'html', title: 'HTML'},
				{id: 'pdf', title: 'PDF'}
			];

			vm.reset = function(report) {
				angular.forEach(report, function(val, key){
					report[key] = null;
				});
			};
		}]);
})(opakeApp, angular);
