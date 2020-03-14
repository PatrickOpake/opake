// Discharge docs
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseDischargeCrtl', [
		'$scope',
		'$http',
		'Tools',
		'Document',

		function ($scope, $http, Tools, Document) {

			var vm = this;

			vm.docs = [];

			vm.init = function (id) {
				vm.controlLinks = {
					list: '/cases/ajax/discharge/' + $scope.org_id + '/list/' + id,
					upload: '/cases/ajax/discharge/' + $scope.org_id + '/upload/' + id,
					remove: '/cases/ajax/discharge/' + $scope.org_id + '/remove/'
				};

				vm.updateList();
			};

			vm.updateList = function () {
				$http.get(vm.controlLinks.list).then(function (result) {
					vm.docs = [];
					angular.forEach(result.data, function(item){
						vm.docs.push(new Document(item));
					});
				});
			};
			

			vm.upload = function (files) {
				Tools.uploadFile(vm.controlLinks.upload, files[0]).then(function(){
					vm.updateList();
				});
			};

			vm.remove = function (id) {
				if (confirm('Are you sure you want to delete this document, it will be removed from this case and patient record')) {
					$http.get(vm.controlLinks.remove + id).then(function (result) {
						vm.updateList();
					});
				}
			};

			vm.changed = function () {
				return vm.docs.length;
			};

			vm.print = function (doc) {
				Tools.print(doc.path);
			};

		}]);

})(opakeApp, angular);
