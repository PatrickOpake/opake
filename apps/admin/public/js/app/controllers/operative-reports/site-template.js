// Operative Report Template
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportSiteTemplateCtrl', [
		'$scope',
		'$http',
		'$q',
		'$filter',
		'OpReports',
		'OperativeReportTemplate',
		'View',
		'OperativeReportTemplateConst',

		function ($scope, $http, $q, $filter, OpReports, OperativeReportTemplate, View, OperativeReportTemplateConst) {

			var vm = this;
			vm.action = 'view';
			vm.templateConst = OperativeReportTemplateConst;
			vm.allowedTypes = [];
			vm.isShowLoading = false;
			angular.forEach(OperativeReportTemplateConst.GROUPS, function(group_id, key) {
				if (key !== 'CASEINFO') {
					vm.allowedTypes.push(group_id);
				}
			});

			vm.init = function() {
				var def = $q.defer();
				$http.get('/operative-reports/ajax/' + $scope.org_id + '/template/').then(function (response) {
					vm.template = response.data;
					divideSurgeonsToColumn();
					def.resolve();

				});
				return def.promise;
			};

			vm.edit = function() {
				vm.original_template = angular.copy(vm.template);
				vm.action = 'edit';
			};

			vm.cancel = function() {
				vm.template = vm.original_template;
				vm.action = 'view';
				vm.errors = [];
			};

			vm.reindexSurgeonColumns = function () {
				mergeSurgeonsFromColumns();
				divideSurgeonsToColumn();
			};

			vm.save = function() {
				vm.isShowLoading = true;
				OpReports.saveTemplate(vm.template, function(result) {
					if (result.data.status === 'ok') {
						vm.action = 'view';
						vm.errors = [];
						vm.isShowLoading = false;
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.addCustomField = function(group_id) {
				vm.custom_field_name = '';
				$scope.ctrl = vm;
				$scope.dialog(View.get('operative-report/add_custom_field.html'), $scope, {windowClass: 'alert forms'}).result.then(function () {
					vm.template[group_id].push({
							name: vm.custom_field_name,
							group_id: group_id,
							field: 'custom',
							active: true
						});
				});
			};

			vm.removeCustomField = function(field) {
				$scope.dialog(View.get('operative-report/confirm_delete.html'), $scope).result.then(function () {
					var idx = vm.template[field.group_id].indexOf(field);
					if(idx > -1) {
						vm.template[field.group_id].splice(idx, 1);
					}
					if(field.id) {
						$http.get('/operative-reports/ajax/' + $scope.org_id + '/removeSiteCustomField/' + field.id);
					}
				});
			};

			function divideSurgeonsToColumn() {
				if(vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO]) {
					var countOfItemsInColumn = Math.ceil(vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO].length / 2);
					vm.chunkedSurgeons = chunk(vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO], countOfItemsInColumn);
				}
			}

			function mergeSurgeonsFromColumns() {
				if(vm.template) {
					vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO] = [].concat.apply([], vm.chunkedSurgeons);
				}
			}

			function chunk(arr, size) {
				var newArr = [];
				for (var i=0; i<arr.length; i+=size) {
					newArr.push(arr.slice(i, i+size));
				}
				return newArr;
			}
		}]);

})(opakeApp, angular);
