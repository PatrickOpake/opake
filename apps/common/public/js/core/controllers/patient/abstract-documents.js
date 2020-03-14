(function (opakeCore, angular) {
	'use strict';

	opakeCore.controller('AbstractDocumentsCtrl', [
		'$scope',
		'$filter',
		'$http',
		'$window',
		'vm',
		'View',
		'Tools',
		function ($scope, $filter, $http, $window, vm, View, Tools) {

			vm.toPrint = [];
			vm.selectAll = false;
			vm.viewType = 'grid';
			vm.uploadingMode = false;
			vm.isDocumentsLoading = false;


			vm.docsToUpload = [];

			// Uploading mode

			vm.openUploadingMode = function() {
				vm.uploadingMode = true;
			};

			vm.closeUploadingMode = function() {
				vm.uploadingMode = false;
			};

			vm.uploadFiles = function (files) {
				angular.forEach(files, function (file) {
					var doc = {file: file, name: file.name};
					vm.docsToUpload.push(doc);
				});
				vm.uploadingMode = false;
				$scope.$apply();
			};

			vm.saveUploadedDocs = function() {
				angular.forEach(vm.docsToUpload, function (doc) {
					if (doc.file && doc.name) {
						var fd = new FormData();
						angular.forEach(doc, function (value, key) {
							fd.append(key, value);
						});
						fd.append('doc_name', doc.name);
						fd.append('doc_type', vm.typeDoc);
						if (doc.folder_id == 'general') {
							var patientId = vm.patientId;
							return $http.post('/patients/ajax/' + $scope.org_id + '/uploadDoc/' + patientId, fd, {
								withCredentials: true,
								headers: {'Content-Type': undefined},
								transformRequest: angular.identity
							}).then(function () {
								vm.init(vm.patientId);
							});
						} else {
							var caseId = doc.folder_id;
							return $http.post('/cases/ajax/' + $scope.org_id + '/uploadDoc/' + caseId, fd, {
								withCredentials: true,
								headers: {'Content-Type': undefined},
								transformRequest: angular.identity
							}).then(function () {
								vm.init(vm.patientId);
							});
						}
					}
				});
			};

			vm.renameUploadedDoc = function(doc) {
				vm.currentDocName = doc.name;
				$scope.dialog(View.get('/patients/documents/rename_doc.html'), $scope).result.then( function() {
					doc.name = vm.currentDocName;
				});
			};

			vm.removeUploadedDoc = function(doc) {
				if ($filter('filter')(vm.docsToUpload, doc, true).length) {
					var index = vm.docsToUpload.indexOf(doc);
					vm.docsToUpload.splice(index, 1);
				}
			};

			// Upload New modal

			vm.openUploadNewModal = function(doc) {
				vm.currentDoc = doc;
				vm.modal = $scope.dialog(
					View.get('/patients/documents/upload_new_modal.html'),
					$scope, {
						windowClass: 'alert forms upload',
						controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

							var vm = this;
							vm.currentDoc = doc;

							vm.cancel = function() {
								$uibModalInstance.close();
							};

						}],
						controllerAs: 'modalVm'
					});
			};

			vm.uploadNewFile = function (file, doc) {
				doc.file = file[0];
				$scope.$apply();
			};

			vm.uploadNewFileChanged = function(files, doc) {
				vm.currentDoc.file = files[0];
				$scope.$apply();
			};

			vm.removeUploadedNewFile = function(doc) {
				doc.file = null;
			};

			vm.uploadNewFileSave = function(doc) {
				if (doc.name) {
					var fd = new FormData();
					angular.forEach(doc, function(value, key) {
						fd.append(key, value);
					});
					fd.append('doc_id', doc.id);
					fd.append('doc_name', doc.file.name);
					fd.append('doc_type', vm.typeDoc);

					if (doc.patient_id) {
						var patientId = doc.patient_id;
						return $http.post('/patients/ajax/' + $scope.org_id + '/uploadDoc/' + patientId, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function () {
							vm.init(vm.patientId);
							vm.modal.close();
						});
					} else if (doc.case_id) {
						var caseId = doc.case_id;
						return $http.post('/cases/ajax/' + $scope.org_id + '/uploadDoc/' + caseId, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function () {
							vm.init(vm.patientId);
							vm.modal.close();
						});
					}
				}
			};

			vm.removeDoc = function (doc) {
				vm.toPrint = [];
				vm.selectAll = false;

				$scope.dialog(View.get('/patients/documents/confirm_doc_delete.html'), $scope, {windowClass: 'alert forms upload'}).result.then( function () {
					if (doc.patient_id) {
						$http.post('/patients/ajax/' + $scope.org_id + '/removeDoc/' + doc.id, $.param({doc_type: vm.typeDoc})).then(function () {
							vm.init(vm.patientId);
						});
					} else if (doc.case_id) {
						$http.post('/cases/ajax/' + $scope.org_id + '/removeDoc/' + doc.id, $.param({doc_type: vm.typeDoc})).then(function () {
							vm.init(vm.patientId);
						});
					}
				});
			};

			// Printing

			vm.addToPrint = function (doc) {
				var idx = vm.toPrint.indexOf(doc);
				if (idx > -1) {
					vm.toPrint.splice(idx, 1);
				} else {
					vm.toPrint.push(doc);
				}
				vm.selectAll = vm.fullDocLength === vm.toPrint.length;
			};

			vm.addToPrintAll = function () {
				vm.toPrint = [];
				if (!vm.selectAll) {
					angular.forEach(vm.general_docs, function (doc) {
						vm.toPrint.push(doc);
					});
					angular.forEach(vm.cases, function (case_item) {
						vm.toPrint.push(case_item.report);
						if(vm.typeDoc === 'chart') {
							angular.forEach(case_item.charts, function (chart) {
								vm.toPrint.push(chart);
							});
						}
						if(vm.typeDoc === 'financial_document') {
							angular.forEach(case_item.financial_documents, function (doc) {
								vm.toPrint.push(doc);
							});
						}

					});
				}
				vm.selectAll = !vm.selectAll;
			};


			vm.print = function() {
				if (vm.toPrint && vm.toPrint.length) {
					vm.isDocumentsLoading = true;
					var documents = [];
					angular.forEach(vm.toPrint, function (item) {
						documents.push(item);
					});
					$http.post('/patients/ajax/' + $scope.org_id + '/compileDocs/', $.param({documents: documents, doc_type: vm.typeDoc})).then(function (res) {
						vm.isDocumentsLoading = false;
						if (res.data.success) {
							if (res.data.print) {
								Tools.print(location.protocol + '//' + location.host + res.data.url);
							} else {
								location.href = res.data.url;
							}
						}
					}, function() {
						vm.isDocumentsLoading = false;
					});
				}
			};

			vm.isAddedToPrint = function(doc) {
				return vm.toPrint.indexOf(doc) > -1;
			};

			vm.viewOperativeReport = function (report) {
				var type = 'open';
				if(report.status == 3) {
					type = 'submitted';
				}
				var params = '#?type=' + type;
				$window.location = '/operative-reports/my/' + $scope.org_id + '/view/' + report.id + params;
			};

			vm.preview = function(doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/preview-doc.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

		}]);

})(opakeCore, angular);
