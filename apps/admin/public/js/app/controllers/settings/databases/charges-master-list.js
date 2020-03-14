(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ChargesMasterListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$q',
		function ($scope, $http, $controller, $q) {

			var vm = this;

			vm.siteId = null;
			vm.errors = null;
			vm.isLoading = false;

			$controller('ListCrtl', {vm: vm});

			vm.action = 'view';

			vm.init = function (siteId) {
				vm.siteId = siteId;
				vm.search();
			};

			vm.search = function () {
				vm.isLoading = true;
				$http.get('/clients/sites/ajax/' + $scope.org_id + '/charges-master/list/' + vm.siteId + '/', {
					params: vm.search_params
				}).then(function (response) {
					vm.items = response.data.items;
					angular.forEach(vm.items, function (item) {
						if (angular.isDefined(item.last_edited_date) && item.last_edited_date) {
							item.last_edited_date = moment(item.last_edited_date).toDate();
						}
					});
					vm.total_count = response.data.total_count;
				}).finally(function() {
					vm.isLoading = false;
				});
			};

			vm.edit = function(){
				vm.original_items = angular.copy(vm.items);
				vm.action = 'edit';
			};

			vm.cancel = function () {
				vm.items = vm.original_items;
				vm.action = 'view';
			};

			vm.save = function() {
				var originalItemsWithoutNotes = [];
				angular.forEach(vm.original_items, function (item) {
					delete item.notes;
					originalItemsWithoutNotes.push(item);
				});
				var itemsWithoutNotes = angular.copy(vm.items);
				angular.forEach(itemsWithoutNotes, function (item) {
					delete item.notes;
				});

				for (var i = 0; i < originalItemsWithoutNotes.length; i++) {
					if (!angular.equals(originalItemsWithoutNotes[i], itemsWithoutNotes[i])) {
						if (!vm.items[i].last_edited_date || moment(originalItemsWithoutNotes[i].last_edited_date).isSame(vm.items[i].last_edited_date)) {
							vm.items[i].last_edited_date = moment(new Date());
						}
					}
				}

				$http.post('/clients/sites/ajax/' + $scope.org_id + '/charges-master/save/', $.param({data: JSON.stringify(vm.items)})).then(function (result) {
					vm.errors = null;
					if (result.data.result == 'ok') {
						vm.action = 'view';
						vm.search();
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.addRow = function () {
				vm.items.unshift({site_id: vm.siteId});
			};

			vm.uploadChargeMaster = function(files) {
				vm.isLoading = true;
				vm.errors = null;
				var chargeMasterFile = files[0];
				var fd = new FormData();
				fd.append('file', chargeMasterFile);
				$http.post('/clients/sites/ajax/' + $scope.org_id + '/charges-master/uploadChargeMaster/' + vm.siteId + '/', fd, {
					withCredentials: true,
					headers: {
						'Content-Type': undefined
					},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.errors) {
						vm.errors = result.data.errors;
					} else {
						vm.init(vm.siteId);
					}
				}).finally(function() {
					vm.isLoading = false;
				});
			};

			vm.downloadChargeMaster = function () {
				window.location = '/clients/sites/' + $scope.org_id + '/charges-master/downloadChargeMaster/' + vm.siteId;
			};

			vm.data = {};

			vm.searchCPT = function (q) {
				var deferred = $q.defer();
				var src = '/clients/sites/ajax/' + $scope.org_id + '/charges-master/searchCPT';
				var key = src;
				var params = {siteId: vm.siteId, query: q};
				if (params) {
					key += JSON.stringify(params);
				}
				$http.get(src, {params: params}).then(function (result) {
					// inject currently selected value into the options list
					if (vm.search_params.cpt) {
						let addToList = vm.search_params.cpt;
						angular.forEach(result.data, function(item) {
							if (vm.search_params.cpt == item.cpt) {
								addToList = null;
								return;
							}
						});
						if (addToList) {
							result.data.push({cpt: addToList});
						}
					}

					vm.data[key] = result.data;
					deferred.resolve(result.data);
				});
				return deferred.promise;
			};

		}]);

})(opakeApp, angular);
