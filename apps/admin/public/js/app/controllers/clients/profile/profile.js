(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClientProfileCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',
		'Organization',
		'Permissions',
		'Source',

		function ($scope, $http, $q, View, Organization, Permissions, Source) {

			var vm = this;
			vm.isShowForm = false;
			vm.canEditOrganization = false;
			vm.canEditNuanceOrgId = false;
			vm.canEditServiceCodes = false;
			vm.profileImageOptions = null;
			vm.profileImageControl = {};
			vm.errors = null;
			vm.isSaveFormSent = false;

			vm.init = function(organizationId) {

				if (organizationId) {
					$http.get('/organizations/ajax/' + organizationId + '/organizationProfile/').then(function (result) {
						vm.org = new Organization(result.data);
						changePermissions(vm.org);

						vm.profileImageOptions = {
							imageSrc: vm.org.logo_src
						};
					});
				} else {
					$http.get('/organizations/ajax/defaultSitePermissions/').then(function (result) {
						vm.org = new Organization({
							logo_src: '/i/default-logo_default.png',
							status: 'active',
							permissions: result.data
						});

						vm.isShowForm = true;
						changePermissions(vm.org);

						vm.profileImageOptions = {
							imageSrc: vm.org.logo_src
						};
					});
				}
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalOrg = angular.copy(vm.org);
			};

			vm.cancel = function() {
				if (vm.org.id) {
					vm.org = vm.originalOrg;
					vm.isShowForm = false;
					vm.errors = null;
				} else {
					history.back();
				}

			};

			vm.save = function() {
				var def = $q.defer();

				if (!vm.isSaveFormSent) {
					vm.isSaveFormSent = true;
					vm.errors = null;

					vm.profileImageControl.saveImage({
						imageType: 'user'
					}).then(function(uploadedFile) {
						var isCreate = !vm.org.id;
						if (uploadedFile) {
							vm.org.logo_id = uploadedFile.image_id;
						}
						$http.post('/organizations/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.org)}))
							.then(function (result) {
								if (result.data.id) {
									if (isCreate) {
										window.location = '/clients/';
										def.resolve();
									} else {
										window.location.reload();
										def.resolve();
									}
								} else if (result.data.errors) {
									vm.errors =  angular.fromJson(result.data.errors);
									def.reject();
								}

								vm.isSaveFormSent = false;
							}
						);
					}, function(errors) {
						vm.errors = errors;
						vm.isSaveFormSent = false;
					});
				}

				return def.promise;
			};

			vm.getView = function () {
				var view = 'organizations/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.activateOrganization = function() {
				vm.org.is_active = true;
			};

			vm.deactivateOrganization = function() {
				vm.org.is_active = false;
			};

			vm.formatMultipleValues = function(values) {
				var result = [];
				angular.forEach(values, function(value) {
					result.push(value.name);
				});

				return result.join(', ');
			};

			function changePermissions(organization) {
				vm.canEditOrganization = Permissions.hasAccess('organization', 'edit', organization);
				vm.canEditNuanceOrgId = Permissions.hasAccess('organization', 'edit_nuance_org_id', organization);
				vm.canEditServiceCodes = Permissions.hasAccess('organization', 'edit_service_codes', organization);
			}

		}]);


	opakeApp.filter('opkSelectPracticeGroupSearchFilter', ['$filter', function($filter) {
		return function(label, query, object, select, elem) {
			if (!object.active) {
				label = '<span class="inactive-practice-group">' + label + '*</span>';
			}
			return $filter('oiSelectCloseIcon')(label, query, object, select, elem);
		}
	}]);

})(opakeApp, angular);
