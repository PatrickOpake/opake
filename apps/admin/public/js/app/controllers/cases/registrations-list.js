// Patient list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseRegistrationListCrtl', [
		'$scope',
		'$http',
		'$window',
		'$controller',
		'$q',
		'CaseRegistration',
		'CaseRegistrationConst',
		'Tools',
		function ($scope, $http, $window, $controller, $q, CaseRegistration, CaseRegistrationConst, Tools) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});
			vm.toDownload = [];
			vm.isDocumentsLoading = false;

			var activeOnly = true;
			vm.caseRegistrationConst = CaseRegistrationConst;


			vm.init = function (active, patient_id) {
				if (angular.isDefined(active)) {
					activeOnly = active;
				}
				vm.patient_id = patient_id || '';
				vm.search();
			};

			vm.search = function () {
				var data = vm.search_params;
				data.active = activeOnly;
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/index/' + vm.patient_id, {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function(data){
						data.case.time_start = new Date(data.case.time_start);
						data.case.time_end = new Date(data.case.time_end);
						items.push(new CaseRegistration(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};

			vm.view = function (id) {
				$window.location = '/cases/registrations/' + $scope.org_id + '/view/' + id;
			};

			vm.cancel = function () {
				$window.history.back();
			};

			vm.downloadRecord = function() {

				vm.isDocumentsLoading = true;

				var promises = [];
				angular.forEach(vm.toDownload, function(item) {
					var url = '/cases/ajax/' + $scope.org_id + '/compileCaseWithForms/' + item.case.id;
					promises.push($http.post(url));
				});

				$q.all(promises).then(function(responses) {
					angular.forEach(responses, function(res) {
						if (res.data.success) {
							Tools.export(res.data.url);
						}
					})
				}).finally(function() {
					vm.isDocumentsLoading = false;
				});
			};

			vm.addToDownload = function(doc) {
				var idx = vm.toDownload.indexOf(doc);
				if (idx > -1) {
					vm.toDownload.splice(idx, 1);
				} else {
					vm.toDownload.push(doc);
				}
				vm.selectAll = vm.items.length === vm.toDownload.length;
			};

			vm.addToDownloadAll = function() {
				vm.toDownload = [];
				if (!vm.selectAll) {
					angular.forEach(vm.items, function (item) {
						vm.toDownload.push(item);
					});
				}
				vm.selectAll = !vm.selectAll;
			};

			vm.isAddedToDownload = function(doc) {
				return vm.toDownload.indexOf(doc) > -1;
			};

			vm.goToCancellationsList = function (item) {
				$window.location = '/cases/' + $scope.org_id + '/canceled/' 
					+ '#?first_name=' + item.first_name + '&last_name=' + item.last_name + '&dos=' + item.case.time_start;
			};
		}]);

})(opakeApp, angular);
