(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientPortalSettingsCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',

		function ($scope, $http, $q, View) {

			var vm = this;
			vm.isShowForm = false;
			vm.portalSettings = null;
			vm.originalPortalSettings = null;
			vm.profileImageOptions = null;
			vm.profileImageControl = {};
			vm.isSaveFormSent = false;
			vm.errors = null;

			vm.init = function(organizationId) {
				$http.get('/settings/patient-portal/ajax/' + organizationId + '/getPortalSettings/').then(function (result) {
					vm.portalSettings = result.data;
					vm.profileImageOptions = {
						imageSrc: vm.portalSettings.icon
					}
				});
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalPortalSettings = angular.copy(vm.portalSettings);
			};

			vm.cancel = function() {
				vm.portalSettings = vm.originalPortalSettings;
				vm.isShowForm = false;
				vm.errors = null;
			};

			vm.save = function() {
				var def = $q.defer();

				if (!vm.isSaveFormSent) {
					vm.isSaveFormSent = true;
					vm.errors = null;

					vm.profileImageControl.saveImage({
						imageType: 'user'
					}).then(function(uploadedFile) {
						if (uploadedFile) {
							vm.portalSettings.icon_file_id = uploadedFile.image_id;
						}

						$http.post(
							'/settings/patient-portal/ajax/' + vm.portalSettings.organization_id + '/save/',
							$.param({data: JSON.stringify(vm.portalSettings)})
						).then(function (result) {
								vm.errors = null;
								if (result.data.id) {
									window.location.reload();
									def.resolve();
								} else if (result.data.errors) {
									vm.errors = result.data.errors.split(';');
									def.reject();
								}
							});

					}, function(errors) {
						vm.errors = errors;
						vm.isSaveFormSent = false;
					});

				}

				return def.promise;
			};

			vm.getView = function () {
				var view = 'settings/patient-portal/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.publishPortal = function() {
				$http.post('/settings/patient-portal/ajax/' + vm.portalSettings.organization_id + '/publishPortal').then(function (result) {
					vm.portalSettings.active = true;
				});
			};

			vm.unpublishPortal = function() {
				$http.post('/settings/patient-portal/ajax/' + vm.portalSettings.organization_id + '/unpublishPortal').then(function (result) {
					vm.portalSettings.active = false;
				});
			};

		}]);

})(opakeApp, angular);
