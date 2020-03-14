(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClientSiteCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',
		'Site',
		'Permissions',

		function ($scope, $http, $q, View, Site, Permissions) {

			var vm = this;
			vm.isShowForm = false;
			vm.canEditSite = false;

			vm.init = function(siteId) {

				if (siteId) {
					$http.get('/sites/ajax/' + $scope.org_id + '/site/' + siteId).then(function (result) {
						vm.site = new Site(result.data);
						vm.hasFeeSchedule = vm.site.has_fee_schedule;
						changePermissions(vm.site);
					});
				} else {
					vm.site = new Site({});
					vm.isShowForm = true;
					changePermissions(vm.site);
				}
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalSite = angular.copy(vm.site);
			};

			vm.delete = function(siteId) {
				$scope.dialog(View.get('sites/confirm_delete.html'), $scope).result.then(function () {
					$http.get('/sites/ajax/' + $scope.org_id + '/delete/' + siteId).then(function () {
						window.location.replace('/clients/sites/' + $scope.org_id);
					});
				});
			};

			vm.cancel = function() {
				if (vm.site.id) {
					vm.site = vm.originalSite;
					vm.isShowForm = false;
					vm.errors = null;
				} else {
					history.back();
				}

			};

			vm.save = function() {
				var def = $q.defer();
				var isCreate = !vm.site.id;
				$http.post('/sites/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.site)})).then(function (result) {
					vm.errors = null;
					if (result.data.id) {
						vm.saveFeeSchedule(result.data.id).then(function() {
							if (isCreate) {
								history.back();
								def.resolve();
							} else {
								vm.isShowForm = false;
								vm.init(result.data.id);
								def.resolve();
							}
						});
					} else if (result.data.errors) {
						vm.errors =  angular.fromJson(result.data.errors);
						def.reject();
					}
				});

				return def.promise;
			};

			vm.roomTypeaheadChange = function($event) {
				if ($event.keyCode == 13) {
					if (!vm.site.room_names) {
						vm.site.room_names = [];
					}
					if(vm.site.room_names.indexOf(vm.currentRoomName) === -1) {
						vm.site.room_names.push(vm.currentRoomName);
						vm.currentRoomName = '';
					}
				}
			};

			vm.storageTypeaheadChange = function($event) {
				if ($event.keyCode == 13) {
					if (!vm.site.storage_names) {
						vm.site.storage_names = [];
					}
					if(vm.site.storage_names.indexOf(vm.currentStorageName) === -1) {
						vm.site.storage_names.push(vm.currentStorageName);
						vm.currentStorageName = '';
					}
				}
			};

			vm.removeRoomTypeahead = function(index) {
				vm.site.room_names.splice(index, 1);
			};

			vm.removeStorageTypeahead = function(index) {
				vm.site.storage_names.splice(index, 1);
			};

			vm.getView = function () {
				var view = 'sites/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.selectFeeSchedule = function(files) {
				vm.feeScheduleUploadedFile = files[0];
			};

			vm.saveFeeSchedule = function(siteId) {
				var def = $q.defer();
				if (vm.feeScheduleUploadedFile) {
					var fd = new FormData();
					fd.append('file', vm.feeScheduleUploadedFile);
					$http.post('/sites/ajax/' + $scope.org_id + '/uploadFeeSchedule/' + siteId + '/', fd, {
						withCredentials: true,
						headers: {'Content-Type': undefined},
						transformRequest: angular.identity
					}).then(function (result) {
						if (result.data.errors) {
							vm.errors = result.data.errors;
							def.reject();
						} else {
							def.resolve();
						}
					}, function() {
						def.reject();
					});
				} else {
					def.resolve();
				}

				return def.promise;
			};

			vm.formatMultipleValues = function(values) {
				var result = [];
				angular.forEach(values, function(value) {
					result.push(value.name);
				});

				return result.join(', ');
			};

			function changePermissions(site) {
				vm.canEditSite = Permissions.hasAccess('sites', 'edit', site);
			}

		}]);

})(opakeApp, angular);
