// Card list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CardStaffListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$location',
		'View',
		'Permissions',

		function ($scope, $http, $controller, $location, View, Permissions) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			var hasFullAccess = false;

			vm.init = function() {
				hasFullAccess = Permissions.hasAccess('card', 'view');

				if (hasFullAccess) {
					vm.search();
				}
			};

			vm.search = function() {
				$http.get('/cards/ajax/' + $scope.org_id + '/staff/', {params: vm.search_params}).then(function (response) {
					var data = response.data;
					vm.staff = data.items;
					vm.staff_total_count = data.total_count;
				});
			};

			vm.getView = function() {
				if (hasFullAccess && !vm.isStaffSelected()) {
					return View.get('/cards/list/staff.html');
				} else {
					return View.get('/cards/list/staff_cards.html');
				}
			};

			vm.setStaff = function(staff) {
				$location.search('staff', staff.id);
			};

			vm.isStaffSelected = function () {
				return parseInt($location.search().staff, 10);
			};

		}]);

	opakeApp.controller('CardStaffCardsListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$location',
		'$window',
		'Tools',

		function ($scope, $http, $controller, $location, $window, Tools) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			var userId = '';

			vm.isPrinting = false;

			vm.init = function() {
				var params = $location.search();
				userId = angular.isDefined(params.staff) ? params.staff : '';
				vm.user_id = userId;
				vm.search();
			};

			vm.search = function() {
				$http.get('/cards/ajax/' + $scope.org_id + '/staffCards/' + userId, {params: vm.search_params}).then(function (response) {
					var data = response.data;
					vm.full_name = data.full_name;
					vm.items = data.items;
					vm.total_count = data.total_count;
				});
			};

			vm.openCard = function(card) {
				$window.location = '/cards/' + $scope.org_id + '/view/' + card.id;
			};

			vm.createCard = function() {
				$window.location = '/cards/' + $scope.org_id + '/create/' + vm.user_id;
			};

			vm.print = function() {
				if (vm.toSelected.length) {
					var documents = [];
					angular.forEach(vm.toSelected, function (card) {
						documents.push(card.id);
					});
					vm.isPrinting = true;
					$http.post('/cards/ajax/' + $scope.org_id + '/exportStaffPrefCard/', $.param({cards: documents})).then(function (result) {
						if (result.data.success) {
							Tools.print(location.protocol + '//' + location.host + result.data.url);
						}
					}).finally(function() {
						vm.isPrinting = false;
					});
				}
			};

		}]);

})(opakeApp, angular);
