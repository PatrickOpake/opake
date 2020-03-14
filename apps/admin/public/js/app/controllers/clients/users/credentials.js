(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('UserCredentialsCtrl', [
		'$scope',
		'$http',
		'$location',
		'View',
		'User',
		'UserCredentials',

		function ($scope, $http, $location, View, User, UserCredentials) {

			var vm = this;
			var user_id = null;

			vm.user = null;
			vm.errors = null;

			vm.init = function(user) {
				var params = $location.search();
				if (angular.isDefined(params.user_id)) {
					user_id = params.user_id;
					$http.get('/users/ajax/' + $scope.org_id + '/user/' + user_id).then(function (result) {
						vm.user = new User(result.data);
					})
				} else {
					user_id = user.id;
					vm.user = user;
				}

				$http.get('/profiles/credentials/ajax/' + $scope.org_id + '/credentials/' + user_id).then(function (result) {
					if (result.data.success) {
						vm.credentials = new UserCredentials(result.data.credentials);
						vm.alert = result.data.alert;
					} else {
						vm.credentials = new UserCredentials();
						vm.alert = [];
					}
					vm.original_credentials = angular.copy(vm.credentials);
				});
			};

			vm.save = function() {
				vm.errors = null;
				$http.post('/profiles/credentials/ajax/' + $scope.org_id + '/save/' + user_id,
					$.param({data: angular.toJson(vm.credentials)})
				).then(function (result) {
						if (result.data.success) {
							vm.init(vm.user);
						} else if (result.data.errors) {
							vm.errors = result.data.errors;
						}
					});
			};

			vm.cancel = function() {
				vm.credentials = angular.copy(vm.original_credentials);
			};

			vm.isChanged = function () {
				return !angular.equals(vm.credentials, vm.original_credentials);
			};

			vm.preview = function(doc) {
				vm.doc = doc;
				$scope.dialog(View.get('/users/credentials/doc_preview.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

			vm.getPrintDocUrl = function() {
				return vm.doc.url + '&to_download=false';
			};

			vm.uploadFile = function(files, fileType) {
				var file = files[0];
				var fd = new FormData();
				fd.append('file', file);

				return $http.post('/profiles/credentials/ajax/' + $scope.org_id + '/uploadFile/' + user_id, fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.file.uploaded_date) {
						result.data.file.uploaded_date = moment(result.data.file.uploaded_date).toDate();
					}

					switch (fileType) {
						case 'npi_file':
							vm.credentials.npi_file = result.data.file;
							break;
						case 'medical_licence_file':
							vm.credentials.medical_licence_file = result.data.file;
							break;
						case 'dea_file':
							vm.credentials.dea_file = result.data.file;
							break;
						case 'cds_file':
							vm.credentials.cds_file = result.data.file;
							break;
						case 'insurance_file':
							vm.credentials.insurance_file = result.data.file;
							break;
						case 'acls_file':
							vm.credentials.acls_file = result.data.file;
							break;
						case 'immunizations_file':
							vm.credentials.immunizations_file = result.data.file;
							break;
						case 'licence_file':
							vm.credentials.licence_file = result.data.file;
							break;
						case 'bls_file':
							vm.credentials.bls_file = result.data.file;
							break;
						case 'cnor_file':
							vm.credentials.cnor_file = result.data.file;
							break;
						case 'malpractice_file':
							vm.credentials.malpractice_file = result.data.file;
							break;
						case 'hp_file':
							vm.credentials.hp_file = result.data.file;
							break;
					}
				});
			};

		}]);

})(opakeApp, angular);
