(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientFinancialDocumentsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'$window',
		'$controller',
		'View',
		'Tools',
		'Case',
		'PatientFinancialDocument',

		function ($scope, $http, $filter, $window, $controller, View, Tools, Case, PatientFinancialDocument) {

			var vm = this;
			vm.typeDocName = 'Financial document';
			vm.typeDoc = 'financial_document';

			$controller('AbstractDocumentsCtrl', {vm: vm, $scope: $scope});

			vm.init = function(patientId) {
				vm.uploadingMode = false;
				vm.patientId = patientId;

				$http.get('/patients/ajax/' + $scope.org_id + '/financialDocuments/' + patientId).then(function (result) {
					var cases = [];
					var casesDocsLength = 0;
					vm.docsToUpload = [];
					vm.foldersChoiceList = [];
					var generalFolder = {folder_id: 'general', text: 'General Financial Documents'};
					vm.foldersChoiceList.push(generalFolder);

					angular.forEach(result.data.cases, function (case_item) {
						var caseObject = new Case(case_item);
						casesDocsLength += caseObject.financial_documents.length;
						cases.push(caseObject);
						var caseFolder = {
							folder_id: caseObject.id,
							text: moment(caseObject.time_start).format('M/D/YYYY') + ' - ' +
								caseObject.first_surgeon_name + ' - ' +
								caseObject.type_name
							};
						vm.foldersChoiceList.push(caseFolder);
					});
					vm.cases = cases;

					var general_docs = [];
					angular.forEach(result.data.financialDocuments, function (doc) {
						var docObject = new PatientFinancialDocument(doc);
						general_docs.push(docObject);
					});
					vm.general_docs = general_docs;

					vm.fullDocLength = vm.general_docs.length + casesDocsLength;
				});
			};

		}]);

})(opakeApp, angular);
