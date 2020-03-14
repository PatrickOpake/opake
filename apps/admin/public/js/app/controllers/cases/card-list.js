(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CasePrefCardListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$location',
		'Case',
		'Permissions',
		'CardConst',

		function ($scope, $http, $controller, $window, $location, Case, Permissions, CardConst) {

			$scope.cardConst = CardConst;

			var vm = this;
			vm.isShowLoading = false;

			$controller('ListCrtl', {vm: vm});

			vm.hasCaseManagementAccess = Permissions.hasAccess('case_management', 'view');

			vm.search = function () {
				vm.isShowLoading = true;
				var data = angular.copy(vm.search_params);

				return $http.get('/cases/ajax/' + $scope.org_id + '/cards/', {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Case(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};

			vm.search();

			vm.openCard = function (caseId) {
				$window.location = '/cases/' + $scope.org_id + '/cm/' + caseId + '#?phase=item_log&fromCardsQueue=1';
			};

			vm.redirectToCase = function (caseId) {
				$window.location = '/cases/' + $scope.org_id + '/cm/' + caseId + '#?fromCardsQueue=1';
			};

		}]);

})(opakeApp, angular);
