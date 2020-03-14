// Form  docs
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FormDocumentsCrtl', [
		'$scope',
		'$http',
		'$filter',
		'$window',
		'Source',
		'Tools',
		'FormDocumentsConst',
		'FormDocuments',
		'Permissions',

		function ($scope, $http, $filter, $window, Source, Tools, FormDocumentsConst, FormDocuments, Permissions) {
			$scope.source = Source;
			$scope.formDocuments = FormDocuments;
			$scope.formDocumentsConst = FormDocumentsConst;

			var vm = this;
			vm.form = {};
			vm.docsSegments = getAllowedDocsSegments();
			vm.isUploadLoading = false;
			vm.isShowLoading = false;
			vm.isInitLoading = true;
			vm.printQueue = {};
			vm.allChartGroups = [];
			vm.modalErrors = null;

			vm.uploadFile = function (file) {
				vm.form.uploadedFile = file[0];
				$scope.$apply();
			};

			var getCaseTypesCount = function () {
				$http.post('/settings/case-types/ajax/' + $scope.org_id + '/caseTypesCount/').then(function (result) {
					vm.caseTypesCount = result.data.count;
				});
			};

			var getSitesCount = function () {
				$http.get('/clients/ajax/site/', {params: {org: $scope.org_id}}).then(function (result) {
					vm.sitesCount = result.data.length;
				});
			};

			getCaseTypesCount();
			getSitesCount();

			vm.init = function () {
				vm.isShowLoading = true;
				vm.errors = [];
				FormDocuments.getForms(null, function (response) {
					vm.documents = response.data.items;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});

				$http.get('/settings/forms/charts/ajax/' + $scope.org_id + '/allChartGroups/').then(function (result) {
					vm.allChartGroups = result.data;
				});
			};

			vm.isAllCaseTypesChecked = function (doc) {
				return doc.is_all_case_types || doc.case_types.length == vm.caseTypesCount;
			};

			vm.isAllSitesChecked = function (doc) {
				return doc.is_all_sites || doc.sites.length == vm.sitesCount;
			};

			vm.isNoOneCaseTypesChecked = function (doc) {
				return !doc.is_all_case_types && !doc.case_types.length;
			};

			vm.isNoOneSitesChecked = function (doc) {
				return !doc.is_all_sites && !doc.sites.length;
			};

			vm.openUploadDialog = function (segment) {
				vm.modalErrors = null;
				vm.form = {};
				vm.form.segment = segment;
				vm.isUploadLoading = false;
				vm.modal = $scope.dialog('forms/upload-form.html', $scope, {windowClass: 'alert forms upload'});
			};

			vm.renameForm = function (doc) {
				vm.modalErrors = null;
				vm.form = angular.copy(doc);
				$scope.dialog('forms/rename-form.html', $scope, {
					windowClass: 'alert forms',
					controller: [
						'$scope',
						'$uibModalInstance',
						function ($scope, $uibModalInstance) {

							var modalVm = this;

							modalVm.rename = function() {
								FormDocuments.update(vm.form, 'rename', function (result) {
									if (result.data.success) {
										vm.init();
										$uibModalInstance.dismiss('ok');
									} else {
										vm.modalErrors = result.data.errors;
									}
								});
							};

							modalVm.close = function() {
								$uibModalInstance.dismiss('close');
							};
						}
					],
					controllerAs: 'modalVm'
				})
			};

			vm.assignForm = function (doc) {
				vm.modalErrors = null;
				vm.form = angular.copy(doc);
				$scope.dialog('forms/assign-form.html', $scope, {
					windowClass: 'assign',
					size: 'lg'
				}).result.then(function () {
					FormDocuments.update(vm.form, 'assign', function (result) {
						if (result.data.success) {
							vm.init();
						} else {
							vm.modalErrors = result.data.errors;
						}
					});
				});
			};

			vm.deleteForm = function (doc) {
				vm.modalErrors = null;
				vm.form = doc;
				$scope.dialog('forms/delete-form.html', $scope, {windowClass: 'alert forms'}).result.then(function () {
					FormDocuments.delete(doc.id, function (result) {
						if (result.data.success) {
							vm.init();
						} else {
							vm.modalErrors = result.data.errors;
						}
					})
				});
			};

			vm.moveForm = function (doc) {
				vm.modalErrors = null;
				vm.form = doc;
				$scope.dialog('forms/move-form.html', $scope, {windowClass: 'alert forms'}).result.then(function () {
					FormDocuments.update(vm.form, 'move', function (result) {
						if (result.data.success) {
							vm.init();
						} else {
							vm.modalErrors = result.data.errors;
						}
					});
				});
			};

			vm.reupload = function (doc) {
				vm.modalErrors = null;
				vm.form = angular.copy(doc);
				vm.modal = $scope.dialog('forms/upload-form.html', $scope, {windowClass: 'alert forms upload'});
			};

			vm.filesChanged = function (files) {
				vm.form.uploadedFile = files[0];

				var parts = vm.form.uploadedFile.name.split('.');
				if (parts.length > 1) {
					parts.pop();
				}
				vm.form.name = parts.join('.');

				$scope.$apply();
			};

			vm.removeUploadedFile = function () {
				vm.form.uploadedFile = null;
			};

			vm.clickUpload = function (form) {
				vm.isUploadLoading = true;
				form.$pristine = false;
				if (form.$valid) {
					FormDocuments.save(vm.form, function (result) {
						if (result.data.success) {
							vm.init();
							vm.modal.close();
							if (result.data.id) {
								$window.location = '/settings/forms/charts/' + $scope.org_id + '/uploadedView/' + result.data.id;
							}
						} else {
							vm.modalErrors = result.data.errors;
						}

						vm.isUploadLoading = false;

					});
				}
			};

			vm.getTypeName = function (doc) {
				if (doc.uploaded_file_id) {
					return 'Uploaded PDF';
				} else {
					return 'Manual Form';
				}
			};

			vm.getRequiredDocs = function (segment, type) {
				return $filter('filter')(vm.documents, {segment: segment, type: type})[0];
			};

			vm.changeTypeForm = function () {
				vm.form.name = "";
				if (vm.form.type !== 'other') {
					vm.form.name = FormDocumentsConst.TYPE_OF_FORMS[vm.form.segment][vm.form.type];
				}
			};

			vm.toggleSelection = function toggleSelection(site) {
				var idx = vm.form.sites.indexOf(site.id);

				if (idx > -1) {
					vm.form.sites.splice(idx, 1);
				} else {
					vm.form.sites.push(site.id);
				}
			};

			vm.getPrintFileUrl = function (doc) {
				return doc;
			};

			vm.printAllFiles = function (segment) {
				var documents = $filter('filter')(vm.documents, {segment: segment});
				angular.forEach(documents, function (doc) {
					if (doc.url) {
						Tools.print(doc.url);
					}
				});
			};

			vm.isCheckedSite = function (site) {
				var sites = $filter('filter')(vm.form.sites, {id: site.id});
				if (!angular.isUndefined(sites)) {
					return sites.length;
				}
			};

			vm.addChartToPrintQueue = function (segment, doc) {
				var allSegmentCharts = $filter('filter')(vm.documents, {segment: segment.NAME});
				var printQueue = getPrintQueueForSegment(segment);
				var idx = printQueue.charts.indexOf(doc);
				if (idx > -1) {
					printQueue.charts.splice(idx, 1);
				} else {
					printQueue.charts.push(doc);
				}

				printQueue.isAllSelected = (allSegmentCharts.length == printQueue.charts.length);
			};

			vm.isAddedToPrintQueue = function (segment, doc) {
				var printQueue = getPrintQueueForSegment(segment);
				if (printQueue.isAllSelected) {
					return true;
				}

				var idx = printQueue.charts.indexOf(doc);
				return (idx > -1);
			};

			vm.isPrintingEnabled = function (doc) {
				return true;
			};

			vm.addAllToPrintQueue = function (segment) {
				var printQueue = getPrintQueueForSegment(segment);
				printQueue.isAllSelected = !printQueue.isAllSelected;
				if (!printQueue.isAllSelected) {
					printQueue.charts = [];
				} else {
					printQueue.charts = [];
					var allSegmentCharts = $filter('filter')(vm.documents, {segment: segment.NAME});
					angular.forEach(allSegmentCharts, function (chart) {
						printQueue.charts.push(chart);
					});
				}
			};

			vm.isPrintAllSelected = function (segment) {
				var printQueue = getPrintQueueForSegment(segment);
				return printQueue.isAllSelected;
			};

			vm.printSelectedCharts = function (segment) {
				var printQueue = getPrintQueueForSegment(segment);
				if (printQueue.charts.length || printQueue.isAllSelected) {
					var documents = [];
					vm.isShowLoading = true;
					angular.forEach(printQueue.charts, function (chart) {
						documents.push(chart.id);
					});

					$http.post('/settings/forms/charts/ajax/' + $scope.org_id + '/compileCharts/', $.param({documents: documents})).then(function (res) {
						vm.isShowLoading = false;
						if (res.data.success) {
							if (res.data.print) {
								Tools.print(location.protocol + '//' + location.host + res.data.url);
							} else {
								location.href = res.data.url;
							}

						}
					}, function () {
						vm.isShowLoading = false;
					});
				}

			};

			vm.isPDF = function (fileName) {
				if (fileName) {
					return fileName.split('.').pop().toLowerCase() === 'pdf';
				}
				return false;
			};

			function getPrintQueueForSegment(segment) {
				if (!vm.printQueue[segment.NAME]) {
					vm.printQueue[segment.NAME] = {
						isAllSelected: false,
						charts: []
					}
				}

				return vm.printQueue[segment.NAME];
			}

			function getAllowedDocsSegments() {
				var segments = FormDocumentsConst.SEGMENTS;
				for (var i = 0, count = segments.length; i < count; ++i) {
					if (segments[i].KEY === 'billing' && !Permissions.hasAccess('billing', 'view_forms')) {
						segments.splice(i, 1);
					}
				}
				return segments;
			}

		}]);

	opakeApp.filter('formAssignCaseTypeFilter', ['$filter', function ($filter) {
		return function (name, query, item) {
			var limit = 60;
			if (name.length > limit) {
				return name.substring(0, limit) + '...';
			} else {
				return name;
			}
		};
	}]);

})(opakeApp, angular);
