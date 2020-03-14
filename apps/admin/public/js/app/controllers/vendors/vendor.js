(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('VendorCrtl', [
		'$scope',
		'$http',
		'$q',
		'$filter',
		'View',
		'Vendor',
		'VendorContact',

		function ($scope, $http, $q, $filter, View, Vendor, VendorContact) {

			var vm = this;
			vm.isShowForm = false;

			vm.errors = null;

			vm.init = function(id) {
				if (id) {
					$http.get('/vendors/ajax/' + $scope.org_id + '/vendor/' + id).then(function (result) {
						vm.vendor = new Vendor(result.data);
					});
				} else {
					vm.vendor = new Vendor();
					vm.isShowForm = true;
				}
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalVendor = angular.copy(vm.vendor);
			};

			vm.cancel = function() {
				if (vm.vendor.id) {
					vm.vendor = vm.originalVendor;
					vm.isShowForm = false;
					vm.errors = null;
				} else {
					history.back();
				}

			};

			vm.save = function() {
				var def = $q.defer();
				var isCreate = !vm.vendor.id;
				$http.post('/vendors/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.vendor)})).then(function (result) {
					vm.errors = null;
					if (result.data.id) {
						var savingDone = function() {
							if (isCreate) {
								window.location = '/vendors/' + $scope.org_id + '/view/' + result.data.id;
								def.resolve();
							} else {
								window.location.reload();
								//vm.isShowForm = false;
								//vm.init(result.data.id);
								def.resolve();
							}
						};

						savingDone();

					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
						def.reject();
					}
				});

				return def.promise;
			};

			vm.getView = function () {
				var view = 'vendors/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.contactAddDialog = function() {
				vm.contact = new VendorContact();

				vm.modal = $scope.dialog('opake/vendor/contact.html', $scope, {size: 'md'});
				vm.modal.result.then(function () {
					if (vm.contact) {
						vm.vendor.contacts.push(vm.contact);
						vm.contact = null;
					}
				});
			};

			vm.contactEditDialog = function(contact) {
				vm.contact = contact;
				vm.modal = $scope.dialog('opake/vendor/contact.html', $scope, {size: 'md'});
				vm.modal.result.then(function () {
				});
			};

			vm.removeContact = function(contact) {
				var index = vm.vendor.contacts.indexOf(contact);
				vm.vendor.contacts.splice(index, 1);
			};

		}]);

})(opakeApp, angular);
