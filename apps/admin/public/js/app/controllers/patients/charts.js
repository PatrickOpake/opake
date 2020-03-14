(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientChartsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'$window',
		'$controller',
		'View',
		'Tools',
		'Case',
		'PatientChart',

		function ($scope, $http, $filter, $window, $controller, View, Tools, Case, PatientChart) {

			var vm = this;
			vm.typeDoc = 'chart';
			vm.typeDocName = 'Chart';

			$controller('AbstractDocumentsCtrl', {vm: vm, $scope: $scope});

			vm.init = function(patientId) {
				vm.uploadingMode = false;
				vm.patientId = patientId;

				$http.get('/patients/ajax/' + $scope.org_id + '/charts/' + patientId).then(function (result) {
					var cases = [];
					var casesDocsLength = 0;
					vm.docsToUpload = [];
					vm.foldersChoiceList = [];
					var generalFolder = {folder_id: 'general', text: 'General Charts'};
					vm.foldersChoiceList.push(generalFolder);

					angular.forEach(result.data.cases, function (case_item) {
						var caseObject = new Case(case_item);
						if(caseObject.report) {
							caseObject.report.name = 'Operative Report';
							caseObject.report.uploaded_date = moment(caseObject.time_start).toDate();
							caseObject.report.type = 'report';
							casesDocsLength++;
						}
						casesDocsLength += caseObject.charts.length;
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
					angular.forEach(result.data.patientCharts, function (doc) {
						var docObject = new PatientChart(doc);
						general_docs.push(docObject);
					});
					vm.general_docs = general_docs;

					vm.fullDocLength = vm.general_docs.length + casesDocsLength;
				});
			};

		}]);

})(opakeApp, angular);
