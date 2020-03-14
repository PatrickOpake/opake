// Patient list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingListCtrl', [
		'$scope',
		'$http',
		'$window',
		'$controller',
		'$filter',
		'Booking',
		'Permissions',
		'View',
		'BookingConst',
		'Tools',

		function ($scope, $http, $window, $controller, $filter, Booking, Permissions, View, BookingConst, Tools) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.bookingConst = BookingConst;
			vm.isShowLoading = false;

			$scope.$on('BookingChartsUpdated', function () {
				vm.search();
			});

			vm.canDeleteBooking = Permissions.hasAccess('booking', 'delete');

			vm.search = function () {
				vm.isShowLoading = true;
				var data = angular.copy(vm.search_params);

				if(data.dateFrom) {
					data.dateFrom = moment(data.dateFrom).format('YYYY-MM-DD');
				}
				if(data.dateTo) {
					data.dateTo = moment(data.dateTo).format('YYYY-MM-DD');
				}
				if (data.surgeons) {
					data.surgeons = JSON.stringify(data.surgeons);
				}
				$http.get('/booking/ajax/' + $scope.org_id + '/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						data.time_start = new Date(data.time_start);
						data.time_end = new Date(data.time_end);
						items.push(new Booking(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};
			vm.search();

			vm.remove = function (id) {
				vm.modalDelete = $scope.dialog(View.get('patients/confirm_delete_zero.html'), $scope, {windowClass: 'alert'});
				vm.modalDelete.result.then(function () {
					$http.post('/booking/ajax/' + $scope.org_id + '/remove/' + id).then(function () {
						vm.search();
					});
				});
			};

			vm.schedule = function (booking) {
				if (!booking.scheduleDisabled) {
					booking.scheduleDisabled = true;
					$window.location = '/cases/' + $scope.org_id + '#?booking_id=' + booking.id + '&date=' + $filter('date')(booking.time_start, 'yyyy-MM-dd');
				}
			};

			vm.canEditBooking = function (booking) {
				return Permissions.hasAccess('booking', 'edit', booking);
			};

			vm.printAll = function() {
				if (vm.toSelected && vm.toSelected.length) {
					var documents = [];
					angular.forEach(vm.toSelected, function (item) {
						documents.push(item.id);
					});
					vm.isShowLoading = true;
					$http.post('/booking/ajax/' + $scope.org_id + '/compileBookings/', $.param({bookings: documents})).then(function (res) {
						vm.isShowLoading = false;
						if (res.data.success) {
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						}
					}, function() {
						vm.isShowLoading = false;
					});
				}
			};
		}]);

})(opakeApp, angular);
