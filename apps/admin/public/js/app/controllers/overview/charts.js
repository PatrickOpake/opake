(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OverviewChartsCtrl', [
		'$scope',
		'$http',
		'View',
		'Tools',
		function ($scope, $http, View, Tools) {

			var vm = this;

			vm.openPrintModal = function(caseItem) {

				$scope.dialog(View.get('cases/dashboard/charts_print.html'), $scope,  {
					size: 'md',
					controller: [
						'$scope',
						'$uibModalInstance',
						function($scope, $uibModalInstance) {

							var printVm = this;
							printVm.errors = null;
							printVm.isLoading = true;

							printVm.charts = [];
							printVm.chartGroups = [];
							printVm.prefCards = [];

							printVm.selectedCharts = [];
							printVm.selectedChartGroup = null;
							printVm.selectedPrefCards = [];

							$http.get('/overview/ajax/dashboard/' + $scope.org_id + '/chartGroupOptions?case=' + caseItem.id).then(function (response) {
								if (response.data.success) {
									printVm.chartGroups = [{
										id: null,
										name: ''
									}];
									printVm.chartGroups = printVm.chartGroups.concat(response.data.chart_groups);
									printVm.charts = response.data.charts;
									printVm.prefCards = response.data.pref_cards;
								}
							}).finally(function() {
								printVm.isLoading = false;
							});

							printVm.printSelected = function() {
								printVm.errors = null;

								if (!printVm.selectedChartGroup && !printVm.selectedCharts.length && !printVm.selectedPrefCards.length) {
									printVm.errors = ['You must select at least one chart or group or preference card'];
									return;
								}


								if (printVm.selectedChartGroup) {
									var selectedChartGroup = getSelectedChartGroup();
									if (!selectedChartGroup.documents || !selectedChartGroup.documents.length) {
										printVm.errors = ['Selected group must have at least one form'];
										return;
									}
								}

								printVm.isLoading = true;
								var params = {};
								params.charts = printVm.selectedCharts;
								params.chart_group = printVm.selectedChartGroup;
								params.pref_cards = printVm.selectedPrefCards;
								$http.post('/overview/ajax/dashboard/' + $scope.org_id + '/compileChartGroupsPrint/' + caseItem.registration_id, $.param(params)).then(function(res) {
									if (res.data.success) {
										if (res.data.print) {
											Tools.print(location.protocol + '//' + location.host + res.data.url);
										} else {
											location.href = res.data.url;
										}
									}
								}).finally(function() {
									printVm.isLoading = false;
								});
							};

							printVm.printLabels = function() {
								printVm.isLoading = true;

								$http.post('/overview/ajax/dashboard/' + $scope.org_id + '/compilePatientLabels/' + caseItem.registration_id).then(function(res) {
									if (res.data.success) {
										Tools.print(location.protocol + '//' + location.host + res.data.url);
									}
								}).finally(function() {
									printVm.isLoading = false;
								});
							};

							printVm.getChartGroupDocumentsList = function() {

								var selectedChartGroup = getSelectedChartGroup();

								if (selectedChartGroup) {
									var names = [];
									if (selectedChartGroup.documents) {
										angular.forEach(selectedChartGroup.documents, function(document) {
											names.push(document.name);
										});
									}

									return names.join(', ');
								}

								return '';

							};

							printVm.close = function() {
								$uibModalInstance.dismiss('cancel');
							};

							function getSelectedChartGroup() {
								var selectedChartGroup = null;
								angular.forEach(printVm.chartGroups, function(chartGroup) {
									if (chartGroup.id == printVm.selectedChartGroup) {
										selectedChartGroup = chartGroup;
										return false;
									}
								});

								return selectedChartGroup;
							}

						}
					],
					controllerAs: 'printVm'
				});
			}

		}]);

})(opakeApp, angular);
