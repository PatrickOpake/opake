(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('efaxWidget', ['$rootScope', '$http', 'View', 'EfaxWidgetService', function ($rootScope, $http, View, EfaxWidgetService) {
		return {
			restrict: "E",
			replace: true,
			scope: {},
			controller: ['$scope', function ($scope) {

				$scope.view = View;

				var vm = this;
				vm.activeTab = 'inbox';
				vm.isInitialized = false;
				vm.isShowWidget = false;
				vm.isContentCollapsed = false;
				vm.totalUnreadCount = 0;

				EfaxWidgetService.assign(vm);

				vm.init = function() {
					$http.get('/efax/ajax/getUnreadCount/').then(function(response) {
						vm.totalUnreadCount = parseInt(response.data.count, 10);
					});
				};

				vm.setActiveTab = function(name) {
					vm.activeTab = name;
				};

				vm.toggleShowWidget = function() {
					if (!vm.isShowWidget) {
						vm.isShowWidget = true;
						if (!vm.isInitialized) {
							vm.init();
							vm.isInitialized = true;
						}
					} else {
						vm.isShowWidget = false;
					}
				};

				vm.collapseContent = function() {
					vm.isContentCollapsed = true;
				};

				vm.expandContent = function() {
					vm.isContentCollapsed = false;
				};

			}],
			controllerAs: 'widgetVm',
			templateUrl: function () {
				return View.get('widgets/efax.html');
			},
			link: function (scope, elem, attrs, ctrl) {

			}
		};
	}]);


	opakeApp.controller('EfaxInboundList', [
		'$scope',
		'$rootScope',
		'$controller',
		'$http',
		'View',
		'Tools',
		function($scope, $rootScope, $controller, $http, View, Tools) {

			var displayInOnePage = 8;
			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.sites = [];
			vm.currentSelectedFilterName = '';
			vm.currentPage = 1;
			vm.totalPages = 1;
			vm.viewProcessActivated = false;

			$controller('ListCrtl', {vm: vm, options: {
				defaultParams: {
					l: displayInOnePage
				}
			}});

			vm.init = function() {
				$http.get('/efax/ajax/getSitesList/').then(function(response) {
					vm.sites = response.data;
					vm.search();
				});
			};

			vm.search = function () {
				vm.toSelected = [];
				vm.selectAll = false;
				vm.isShowLoading = true;
				var paramsData = angular.copy(vm.search_params);
				$http.get('/efax/ajax/list', {params: paramsData}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.totalPages = Math.ceil(vm.total_count / displayInOnePage);

					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.selectAllFaxes = function() {
				vm.toSelected = [];
				angular.forEach(vm.items, function (item) {
					vm.toSelected.push(item);
				});
				vm.selectAll = true;
			};

			vm.selectNoneFaxes = function() {
				vm.toSelected = [];
				vm.selectAll = false;
			};

			vm.selectReadFaxes = function() {
				vm.toSelected = [];
				vm.selectAll = false;
				angular.forEach(vm.items, function (item) {
					if (item.is_read) {
						vm.addToSelected(item);
					}
				});
			};

			vm.selectUnreadFaxes = function() {
				vm.toSelected = [];
				vm.selectAll = false;
				angular.forEach(vm.items, function (item) {
					if (!item.is_read) {
						vm.addToSelected(item);
					}
				});
			};

			vm.resetFilters = function() {
				vm.search_params.filter = null;
				vm.search_params.p = 0;
				vm.currentSelectedFilterName = '';
				vm.currentPage = 1;
				vm.search();
			};

			vm.filterReadOnly = function() {
				vm.search_params.filter = 'read-only';
				vm.currentSelectedFilterName = 'Read Only';
				vm.search_params.p = 0;
				vm.currentPage = 1;
				vm.search();
			};

			vm.filterUnreadOnly = function() {
				vm.search_params.filter = 'unread-only';
				vm.currentSelectedFilterName = 'Unread Only';
				vm.search_params.p = 0;
				vm.currentPage = 1;
				vm.search();
			};

			vm.filterSite = function (site) {
				vm.search_params.filter = 'site';
				vm.search_params.site_id = site.id;
				vm.currentSelectedFilterName = site.name;
				vm.search_params.p = 0;
				vm.currentPage = 1;
				vm.search();
			};

			vm.hasNextPage = function() {
				return (vm.currentPage <= (vm.totalPages - 1));
			};

			vm.hasPrevPage = function() {
				return (vm.currentPage > 1)
			};

			vm.prevPage = function() {
				if (vm.hasPrevPage()) {
					vm.currentPage -= 1;
					vm.search_params.p = (vm.currentPage - 1);
					vm.search();
				}
			};

			vm.nextPage = function() {
				if (vm.hasNextPage()) {
					vm.currentPage += 1;
					vm.search_params.p = (vm.currentPage - 1);
					vm.search();
				}
			};

			vm.preview = function(item) {
				$rootScope.dialog(View.get('/widgets/efax/inbox/preview.html'), $scope, {
					size: 'lg',
					windowClass: 'preview-doc',
					controller: [
						'$scope',
						'$uibModalInstance',
						function ($scope, $uibModalInstance) {
							var vm = this;
							vm.fax = item;
							vm.isShowLoading = true;
							vm.fileInfo = null;

							$http.get('/efax/ajax/getDocumentLink/' + item.id).then(function (response) {
								if (response.data.success) {
									vm.fileInfo = {};
									vm.fileInfo.url = response.data.preview_url;
									vm.fileInfo.mimeType = response.data.mimeType;
									setEfaxRead(item);
								}
								vm.isShowLoading = false;
							});

							vm.cancel = function() {
								$uibModalInstance.close('');
							};
						}
					],
					controllerAs: 'modalVm'
				});
			};

			vm.printSelected = function() {
				vm.viewSelected(false);
			};

			vm.downloadSelected = function() {
				vm.viewSelected(true);
			};

			vm.viewSelected = function(download) {
				if (!vm.viewProcessActivated) {
					if ((vm.toSelected && vm.toSelected.length)) {

						vm.isShowLoading = true;
						vm.viewProcessActivated = true;

						var documents = [];
						angular.forEach(vm.toSelected, function (item) {
							setEfaxRead(item);
							documents.push(item.id);
						});

						$http.post('/efax/ajax/compileFaxes/', $.param({
							documents: documents
						})).then(function (response) {
							vm.isShowLoading = false;
							vm.viewProcessActivated = false;
							if (response.data.success) {
								if (download) {
									location.href = response.data.url + '&download=1';
								} else {
									Tools.print(location.protocol + '//' + location.host + response.data.url);
								}
							}
						}, function() {
							vm.viewProcessActivated = false;
							vm.isShowLoading = false;
						});

					}
				}
			};

			vm.markAsUnreadSelected = function() {
				var items = vm.toSelected;
				if (items.length) {
					vm.isShowLoading = true;
					var faxIds = [];
					angular.forEach(items, function(item) {
						faxIds.push(item.id);
					});
					$http.post('/efax/ajax/markAsUnread', $.param({data: angular.toJson({
						faxIds: faxIds
					})})).then(function (response) {
						if (response.data.success) {
							angular.forEach(items, function(item) {
								setEfaxUnread(item);
							});
						}

						vm.isShowLoading = false;
					});
				}
			};

			function setEfaxRead(item) {
				if (!item.is_read) {
					item.is_read = true;
					if ($scope.widgetVm.totalUnreadCount > 0) {
						$scope.widgetVm.totalUnreadCount--;
					}
				}
			}

			function setEfaxUnread(item) {
				if (item.is_read) {
					item.is_read = false;
					$scope.widgetVm.totalUnreadCount++;
				}
			}
		}
	])


})(opakeApp, angular);
