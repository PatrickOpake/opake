// Abstract Report
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AbstractReportCrtl', [
		'$sce',
		'$filter',
		'vm',
		'OperativeReportTemplateConst',
		function ($sce, $filter,  vm, OperativeReportTemplateConst) {

			vm.trustAsHtml = function(string) {
				return $sce.trustAsHtml(string);
			};

			vm.addNewListItem = function (column) {
				column.push(vm.getMasterListItem());
			};

			vm.getMasterListItem = function() {
				return {active: false, text: '', description: ''};
			};

			vm.changeTypeField = function () {
				if(vm.type_field == vm.templateConst.TYPE_FIELDS.LIST) {
					vm.newListItem = {name: '', field: 'list', active: true, confirmed_active: true, list_value: {count_columns: '1'}};
				}
			};

			vm.movedField =function () {
				vm.toedit = angular.copy(vm.template);
			};

			vm.hasActiveFields = function (group_id) {
				return $filter('filter')(vm.template[group_id], {'confirmed_active': true}).length;
			};

			vm.chunk = function(arr, size) {
				var newArr = [];
				for (var i=0; i<arr.length; i+=size) {
					newArr.push(arr.slice(i, i+size));
				}
				return newArr;
			};

			vm.divideCaseInfoToColumn = function () {
				if(vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO]) {
					var caseInfo = vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO];
					var caseInfoLength = caseInfo.length;
					var countOfItemsInColumn = Math.ceil(caseInfoLength / 2);
					vm.chunkedCaseInfo = vm.chunk(caseInfo, countOfItemsInColumn);
				}
			};

			vm.mergeCaseInfoFromColumns = function() {
				if(vm.template) {
					vm.template[OperativeReportTemplateConst.GROUPS.CASEINFO] = [].concat.apply([], vm.chunkedCaseInfo);
				}
			};

			vm.reindexCaseInfoColumns = function () {
				vm.mergeCaseInfoFromColumns();
				vm.divideCaseInfoToColumn();
			};

		}]);

})(opakeApp, angular);
