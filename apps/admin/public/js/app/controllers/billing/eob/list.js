// EOB Management list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('EobManagementListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$filter',
		'View',
		'Tools',
		'Billing',
		'BillingConst',
		function ($scope, $http, $controller, $window, $filter, View, Tools, Billing, BillingConst) {
			$scope.billingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = false;
			vm.uploadingMode = false;
			vm.docsToUpload = [];
			$controller('ListCrtl', {vm: vm});

			vm.search = function (dontShowLoading) {
				if (!dontShowLoading) {
					vm.isShowLoading = true;
				}
				var paramsData = angular.copy(vm.search_params);
				if (paramsData.insurer) {
					paramsData.insurer = paramsData.insurer.id;
				}
				if (paramsData.charge) {
					paramsData.charge = paramsData.charge.id;
				}
				$http.get('/billings/eob-management/ajax/' + $scope.org_id + '/', {params: paramsData}).then(function (response) {
					var items = [];
					vm.docsToUpload = [];
					angular.forEach(response.data.items, function (data) {
						items.push(data);
					});
					vm.items = items;
					vm.total_count = response.data.total_count;

					if (!dontShowLoading) {
						vm.isShowLoading = false;
					}
				});
			};

			vm.search();

			vm.uploadFiles = function (files) {
				angular.forEach(files, function (file) {
					var doc = {file: file, name: file.name};
					vm.docsToUpload.push(doc);
				});
				vm.uploadingMode = false;
				$scope.$apply();
			};

			vm.removeUploadedDoc = function(doc) {
				if ($filter('filter')(vm.docsToUpload, doc, true).length) {
					var index = vm.docsToUpload.indexOf(doc);
					vm.docsToUpload.splice(index, 1);
				}
			};

			vm.openUploadingMode = function() {
				vm.uploadingMode = true;
			};

			vm.closeUploadingMode = function() {
				vm.uploadingMode = false;
			};

			vm.renameUploadedDoc = function(doc) {
				vm.currentDocName = doc.name;
				$scope.dialog(View.get('/billing/rename_doc.html'), $scope).result.then( function() {
					doc.name = vm.currentDocName;
				});
			};

			vm.saveUploadedDocs = function() {
				angular.forEach(vm.docsToUpload, function (doc) {
					if (doc.file && doc.name) {
						var docData = angular.copy(doc);
						if(docData.insurer) {
							docData.insurer_id = docData.insurer.id;
						}
						if(docData.charge_master) {
							docData.charge_master_id = docData.charge_master.id;
						}
						var fd = new FormData();
						angular.forEach(docData, function (value, key) {
							if(key != 'charge_master' && key != 'insurer') {
								fd.append(key, value);
							}
						});

						return $http.post('/billings/eob-management/ajax/' + $scope.org_id + '/uploadDoc/', fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then(function () {
							vm.search();
						});
					}
				});
			};

			vm.changeChargeCPT = function (doc) {
				doc.charge_master_amount = doc.charge_master.amount;
			};

			vm.previewEOB = function (doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/preview-doc.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};


			vm.print = function() {
				if (vm.toSelected && vm.toSelected.length) {
					vm.isDocumentsLoading = true;
					var documents = [];
					angular.forEach(vm.toSelected, function (item) {
						documents.push(item);
					});
					$http.post('/billings/eob-management/ajax/' + $scope.org_id + '/compileDocs/', $.param({documents: documents})).then(function (res) {
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

		}]);

})(opakeApp, angular);
