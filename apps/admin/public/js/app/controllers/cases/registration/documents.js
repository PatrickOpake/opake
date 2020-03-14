(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseRegistrationDocsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'$q',
		'View',
		'Tools',
		'FormDocuments',
		'CaseRegistration',

		function ($scope, $http, $filter, $q, View, Tools, FormDocuments, CaseRegistration) {

			var vm = this;

			vm.toPrint = [];
			vm.selectAll = false;
			vm.form_docs = [];
			vm.faceSheetChecked = false;
			vm.useFaceSheet = false;
			vm.isDocumentsLoading = false;
			vm.errors = null;

			vm.init = function(opts) {
				if ($scope.regVm) {
					vm.reg = $scope.regVm.registration;

					if (vm.reg) {
						FormDocuments.getForms({segment: 'intake', caseId: vm.reg.case_id}, function(response) {
							vm.form_docs = response.data.items;
						});
					}
				}

				if (opts && opts.useFaceSheet) {
					vm.useFaceSheet = true;
				}

			};

			vm.openFormsPrintDialog = function (case_item) {
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/registration/' + case_item.registration_id).then(function (result) {
					vm.reg = new CaseRegistration(result.data);
					vm.addToPrintAll();
					$scope.dialog(View.get('cases/dashboard/forms_print.html'), $scope, {size: 'md'}).result.then(function () {});
				});
			};

			vm.addToPrint = function (doc) {
				var forms = $filter('filter')(vm.reg.documents, {name: doc.name });
				if(forms.length) {
					var form = forms[0];
					var idx = vm.toPrint.indexOf(form);
					if (idx > -1) {
						vm.toPrint.splice(idx, 1);
						vm.selectAll = !(vm.reg.documents.length !== vm.toPrint.length);
					} else {
						vm.toPrint.push(form);
						vm.selectAll = vm.reg.documents.length === vm.toPrint.length;
					}
				}
			};

			vm.addToPrintAll = function () {
				vm.toPrint = [];
				if (!vm.selectAll) {
					if (vm.useFaceSheet) {
						vm.faceSheetChecked = true;
					}
					angular.forEach(vm.reg.documents, function (item) {
						vm.toPrint.push(item);
					});
				} else {
					if (vm.useFaceSheet) {
						vm.faceSheetChecked = false;
					}
				}
				vm.selectAll = !vm.selectAll;
			};

			vm.print = function (regVm) {
				if ((vm.toPrint && vm.toPrint.length) || (vm.useFaceSheet && vm.faceSheetChecked)) {
					vm.isDocumentsLoading = true;
					var documents = [];
					if (vm.useFaceSheet && vm.faceSheetChecked) {
						documents.push('facesheet');
					}
					angular.forEach(vm.toPrint, function (form) {
						documents.push(form.id);
					});

					$http.post('/cases/forms/ajax/' + $scope.org_id + '/compileForms/' + vm.reg.id, $.param({documents: documents})).then(function (res) {
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

			vm.printLabels = function() {
				if (vm.reg) {
					vm.isDocumentsLoading = true;

					$http.post('/overview/ajax/dashboard/' + $scope.org_id + '/compilePatientLabels/' + vm.reg.id).then(function(res) {
						if (res.data.success) {
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						}
					}).finally(function() {
						vm.isDocumentsLoading = false;
					});
				}
			};

			vm.isExistForm = function(doc) {
				return $filter('filter')(vm.reg.documents, {name: doc.name }).length;
			};

			vm.isAddedToPrint = function(doc) {
				var forms = $filter('filter')(vm.reg.documents, {name: doc.name });
				if(forms.length) {
					var form = forms[0];
					return vm.toPrint.indexOf(form) > -1;
				}
				return false;
			};

			vm.removeFile = function (id) {
				vm.toPrint = [];
				vm.selectAll = false;
				var data = vm.reg;
				if (confirm('Are you sure you want to delete this form, it will be removed from this registration')) {
					$http.post('/cases/registrations/ajax/' + $scope.org_id + '/removeFile/' + data.case_id, $.param({docid: id})).then(function (result) {
						vm.refreshDocuments();
						$scope.$emit('RegistrationFormDocumentsUpdated');
					});
				}
			};

			vm.refreshDocuments = function() {
				var reg = vm.reg;
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/registration/' + reg.id).then(function (result) {
					if (result.data.documents) {
						reg.updateDocuments(result.data.documents);
					}
				});
			};

			vm.openUploadDocumentModal = function(doc) {
				vm.currentDoc = doc;
				vm.modal = $scope.dialog(View.get('/cases/registrations/view/additional_info/upload_document_modal.html'), $scope, {windowClass: 'alert forms upload'})
				vm.modal.result.then(function () {
					vm.refreshDocuments();
				});
			};

			vm.uploadFile = function (file, doc) {
				doc.file = file[0];
				$scope.$apply();
			};

			vm.uploadFileChanged = function(files, doc) {
				vm.currentDoc.file = files[0];
				$scope.$apply();
			};

			vm.removeUploadedFile = function(doc) {
				doc.file = null;
			};

			vm.uploadNewDocument = function(doc) {
				vm.errors = null;
				var fd = new FormData();
				angular.forEach(doc, function(value, key) {
					fd.append(key, value);
				});
				fd.append('document_type', doc.type);

				var caseId;
				if (vm.reg && vm.reg.case_id) {
					caseId = vm.reg.case_id;
				} else {
					caseId = $scope.regVm.registration.case_id;
				}

				return $http.post('/cases/registrations/ajax/' + $scope.org_id + '/upload/' + caseId, fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.error) {
						vm.errors = [result.data.error];
					} else {
						vm.refreshDocuments();
						vm.modal.close();
					}
				});
			};

			vm.uploadDocument = function(doc) {
				vm.errors = null;
				vm.uploadedFile = null;
				vm.selected_doc = null;
				vm.changeUploadType('select_existing_form');
				$scope.doc = doc;
				$scope.dialog(View.get('/cases/registrations/view/additional_info/upload_modal.html'), $scope, {windowClass: 'alert forms upload'}).result.then(function () {
					if (vm.upload_type === 'upload_new_form' && vm.uploadedFile && vm.uploadedFile.name) {
						var fd = new FormData();
						fd.append('file', vm.uploadedFile);
						fd.append('document_type', doc.type);

						$http.post('/cases/registrations/ajax/' + $scope.org_id + '/upload/' + vm.reg.case_id, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function (result) {
							if (result.data.error) {
								vm.errors = [result.data.error];
							} else {
								vm.refreshDocuments();
								$scope.$emit('RegistrationFormDocumentsUpdated');
							}
						});
					} else if(vm.upload_type === 'select_existing_form' && vm.selected_doc && vm.selected_doc.name) {
						var param = {from: vm.selected_doc.id, to: doc.id};
						$http.post('/cases/registrations/ajax/' + $scope.org_id + '/copyExistedDoc/' + vm.reg.case_id,
							$.param(param)
						).then(function (result) {
								if (result.data.error) {
									vm.errors = [result.data.error];
								} else {
									vm.refreshDocuments();
									$scope.$emit('RegistrationFormDocumentsUpdated');
								}

						});
					}
				});
			};

			vm.uploadFormFileChanged = function(files) {
				vm.uploadedFile = files[0];
				vm.changeUploadType('upload_new_form');
				$scope.$apply();
			};

			vm.changeUploadType = function(type) {
				vm.upload_type = type;
			};

			vm.onUploadComplete = function() {
				vm.refreshDocuments();
				$scope.$emit('RegistrationFormDocumentsUpdated');
			};

			vm.getDocumentUploadData = function(doctype) {
				return [
					{
						name: 'document_type',
						value: doctype
					}
				];
			};

			vm.additionalForm = function() {
				vm.uploadFormFileName = '';
				vm.modal = $scope.dialog(View.get('/cases/registrations/view/additional_info/new_document_modal.html'), $scope, {windowClass: 'alert forms upload'});
				vm.modal.result.then(function () {
					vm.refreshDocuments();
				});
			};

			vm.getPrintDocUrl = function(doc) {
				return doc.url + '&to_download=false';
			};

			vm.preview = function(doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/cases/registrations/view/additional_info/doc_preview.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

			vm.getExistedDocs = function(doc) {
				var deferred = $q.defer();
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/existedDocs/' + vm.reg.case_id, {params: {doc_type: doc.type}}).then(function (response) {
					var docs = [];
					angular.forEach(response.data, function(item) {
						if(item.uploaded_date) {
							item.uploaded_date = moment(item.uploaded_date).toDate();
							docs.push(item);
						}
					});
					deferred.resolve(docs);
				});
				return deferred.promise;
			};

		}]);

})(opakeApp, angular);
