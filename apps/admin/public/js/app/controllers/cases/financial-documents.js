(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseFinancialDocumentsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'View',
		'Tools',
		'CaseFinancialDocument',

		function ($scope, $http, $filter, View, Tools, CaseFinancialDocument) {

			var vm = this;

			vm.isDocumentsLoading = false;
			vm.typeDoc = 'financial_document';
			vm.charts = null;
			vm.docsCount = null;

			vm.init = function(caseId, docsCount) {
				vm.caseId = caseId;
				vm.docsCount = docsCount;
			};

			vm.open = function() {
				loadDocs();

				vm.modal = $scope.dialog(View.get('booking/charts.html'), $scope, {windowClass: 'alert forms upload'});
				vm.modal.result.then(function () {

				}, function () {

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
				vm.errors = [];
				angular.forEach(vm.charts, function (doc) {
					if (doc.name) {
						var fd = new FormData();
						angular.forEach(doc, function (value, key) {
							fd.append(key, value);
						});
						fd.append('doc_type', vm.typeDoc);
						fd.append('doc_name', doc.name);
						var caseId = vm.caseId;
						return $http.post('/cases/ajax/' + $scope.org_id + '/uploadDoc/' + caseId, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function () {
							loadDocs();
							vm.modal.close();
						});
					}
				});
			};

			vm.renameMode = function(doc) {
				doc.new_name = doc.name;
				doc.rename_mode = true;
			};

			vm.renameChart = function(doc) {
				doc.name = doc.new_name;
				doc.rename_mode = false;
			};

			vm.cancelRenameChart = function(doc) {
				doc.rename_mode = false;
			};

			vm.removeChart = function (doc) {
				if ($filter('filter')(vm.charts, doc, true).length) {
					var index = vm.charts.indexOf(doc);
					vm.charts.splice(index, 1);
				}

				if (doc.id) {
					$http.post('/cases/ajax/' + $scope.org_id + '/removeDoc/' + doc.id + '?case=' + vm.caseId, $.param({doc_type: vm.typeDoc})).then(function () {
						vm.docsCount -= 1;
					});
				}
			};

			vm.preview = function(doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/preview-doc.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

			function loadDocs() {
				$http.get('/cases/ajax/' + $scope.org_id + '/financialDocList/' + vm.caseId).then(function (result) {
					var docs = [];
					angular.forEach(result.data.charts, function (doc) {
						var docObject = new CaseFinancialDocument(doc);
						docs.push(docObject);
					});
					vm.charts = docs;
					vm.docsCount = docs.length;
				});
			}

		}]);

})(opakeApp, angular);
