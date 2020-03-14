(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FormsListCtrl', [
		'$scope',
		'$http',
		'View',
		'CaseDocument',
		'MultipleFilesDownload',
		function ($scope, $http, View, CaseDocument, MultipleFilesDownload) {

			var vm = this;
			vm.documents = [];
			vm.searchParams = null;
			vm.totalCount = null;
			vm.isLoaded = false;

			vm.documentsToDownload = [];
			vm.isAllSelected = false;

			vm.init = function () {
				vm.reset();
				vm.load();
			};

			vm.load = function () {
				vm.documentsToDownload = [];
				vm.isAllSelected = false;
				$http.get('/api/documents/myDocuments', {params: vm.searchParams}).then(function (res) {
					var items = [];
					angular.forEach(res.data.items, function (data) {
						items.push(new CaseDocument(data));
					});
					vm.documents = items;

					vm.totalCount = res.data.total_count;
					vm.isLoaded = true;
				});
			};

			vm.reset = function () {
				vm.searchParams = {
					p: 0,
					l: 20,
					sort_by: 'dos',
					sort_order: 'DESC'
				};
			};

			vm.addToDownload = function (doc) {
				var idx = vm.documentsToDownload.indexOf(doc);
				if (idx > -1) {
					vm.documentsToDownload.splice(idx, 1);
					vm.isAllSelected = !(vm.documents.length !== vm.documentsToDownload.length);
				} else {
					vm.documentsToDownload.push(doc);
					vm.isAllSelected = vm.documents.length === vm.documentsToDownload.length;
				}
			};

			vm.addAllToDownload = function () {
				vm.documentsToDownload = [];
				if (!vm.isAllSelected) {
					angular.forEach(vm.documents, function (item) {
						vm.documentsToDownload.push(item);
					});
				}
				vm.isAllSelected = !vm.isAllSelected;
			};

			vm.download = function() {
				var urls = [];
				angular.forEach(vm.documentsToDownload, function(form) {
					urls.push(form.url);
				});

				if (urls.length) {
					MultipleFilesDownload.download(urls);
				}
			};

			vm.isAddedToDownload = function(doc) {
				return vm.documentsToDownload.indexOf(doc) > -1;
			};

			vm.preview = function(doc) {
				$scope.doc = doc;
				$scope.dialog(View.get('/app/forms/preview.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

			vm.init();

		}]);

})(opakeApp, angular);
