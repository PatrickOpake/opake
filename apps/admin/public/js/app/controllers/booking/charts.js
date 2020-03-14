(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingChartsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'View',
		'Tools',
		'Booking',
		'CaseChart',

		function ($scope, $http, $filter, View, Tools, Booking, CaseChart) {

			var vm = this;

			vm.isDocumentsLoading = false;
			vm.charts = [];

			vm.init = function(bookingId, bookingCharts) {
				if (bookingId) {
					vm.bookingId = bookingId;
					$http.get('/booking/ajax/charts/' + $scope.org_id + '/list/' + bookingId).then(function (result) {
						var charts = [];
						angular.forEach(result.data.charts, function (chart) {
							var chartObject = new CaseChart(chart);
							charts.push(chartObject);
						});
						vm.charts = charts;
					});
				} else if (bookingCharts) {
					if (bookingCharts.length) {
						vm.charts = bookingCharts;
					}
				}
				else {
					if (!vm.charts.length) {
						vm.charts = [];
					}
				}

				vm.modal = $scope.dialog(View.get('booking/charts.html'), $scope, {windowClass: 'alert forms upload'});
				vm.modal.result.then(function () {
				}, function () {
					$scope.$emit('BookingChartsUpdated');
				});
			};

			vm.uploadFiles = function (files) {
				vm.errors = [];
				angular.forEach(files, function (file) {
					if (file.type == 'application/pdf') {
						var chart = {file: file, name: file.name};
						vm.charts.push(chart);
					} else {
						vm.errors = ['Only PDF files are supported'];
					}

				});
			};

			vm.uploadFileChanged = function(files) {
				vm.errors = [];
				angular.forEach(files, function (file) {
					if (file.type == 'application/pdf') {
						var chart = {file: file, name: file.name};
						vm.charts.push(chart);
					} else {
						vm.errors = ['Only PDF files are supported'];
					}

				});
				$scope.$apply();
			};

			vm.saveUploadedCharts = function() {
				if (vm.bookingId) {
					vm.errors = [];
					angular.forEach(vm.charts, function (chart) {
						if (chart.name) {
							var fd = new FormData();
							angular.forEach(chart, function (value, key) {
								fd.append(key, value);
							});
							var bookingId = vm.bookingId;
							return $http.post('/booking/ajax/charts/' + $scope.org_id + '/upload/' + bookingId, fd, {
								withCredentials: true,
								headers: {'Content-Type': undefined},
								transformRequest: angular.identity
							}).then(function () {
								$scope.$emit('BookingChartsUpdated', vm.charts);
								vm.modal.close();
							});
						}
					});
				} else {
					$scope.$emit('BookingChartsUpdatedForUnsavedBooking', vm.charts);
					vm.modal.close();
				}
			};

			vm.renameMode = function(chart) {
				chart.new_name = chart.name;
				chart.rename_mode = true;
			};

			vm.renameChart = function(chart) {
				chart.name = chart.new_name;
				chart.rename_mode = false;
			};

			vm.cancelRenameChart = function(chart) {
				chart.rename_mode = false;
			};

			vm.removeChart = function (chart) {
				if ($filter('filter')(vm.charts, chart, true).length) {
					var index = vm.charts.indexOf(chart);
					vm.charts.splice(index, 1);
				}

				if (chart.id) {
					$http.post('/booking/ajax/charts/' + $scope.org_id + '/remove/' + chart.id + '?booking=' + vm.bookingId);
				}
			};

			vm.preview = function (doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/preview-doc.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

		}]);

})(opakeApp, angular);
