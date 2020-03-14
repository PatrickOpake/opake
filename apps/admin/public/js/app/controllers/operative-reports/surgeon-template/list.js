// Patient list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportSurgeonTemplateListCrtl', [
		'$scope',
		'$http',
		'$window',
		'$controller',
		'OperativeReportFutureTemplate',
		'User',
		'View',
		function ($scope, $http, $window, $controller, OperativeReportFutureTemplate, User, View) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.template = null;

			vm.init = function(user_id) {
				if (user_id) {
					vm.user_id = user_id;
					$http.get('/users/ajax/' + $scope.org_id + '/user/' + vm.user_id).then(function (response) {
						vm.user = new User(response.data);
					});
				}
				vm.search();
			};

			vm.search = function () {
				if(vm.user_id) {
					vm.search_params.user_id = vm.user_id;
				}
				var data = vm.search_params;

				$http.get('/operative-reports/ajax/' + $scope.org_id + '/futureTemplates', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new OperativeReportFutureTemplate(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};

			vm.createTemplate = function() {
				vm.modal = $scope.dialog(View.get('operative-report/surgeon-templates/create.html'), $scope,  {size: 'md'});
				vm.template = new OperativeReportFutureTemplate;
				vm.template.organization_id = $scope.org_id;
				vm.template.user_id = vm.user_id;
				vm.modal.result.then(function () {
					$http.post('/operative-reports/ajax/save/' + $scope.org_id + '/futureReport/', $.param({data: JSON.stringify(vm.template)})).then(function (result) {
						if (result.data.id) {
							vm.errors = [];
							$window.location = '/operative-reports/' + $scope.org_id + '/view/' + result.data.id;
						} else if (result.data.errors) {
							vm.errors =  result.data.errors.split(';');
						}
						vm.search();
					});
				});
			};

			vm.removeTemplate = function(templateId) {
				$scope.dialog(View.get('operative-report/confirm_delete.html'), $scope).result.then(function () {
					$http.get('/operative-reports/ajax/' + $scope.org_id + '/removeFutureTemplate/' + templateId).then(function () {
						vm.search();
					});
				});
			};

			vm.view = function(item) {
				var params = '';
				if(vm.user_id) {
					params = '/' + vm.user_id;
				}
				$window.location =  '/operative-reports/' + $scope.org_id + '/view/' + item.id + params;
			};

		}]);

})(opakeApp, angular);
