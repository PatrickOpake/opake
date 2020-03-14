(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('documentPreview', ['$compile', function ($compile) {
			return {
				restrict: "E",
				replace: true,
				transclude: true,
				bindToController: {
					src: '@',
					pageCount: '@'
				},
				controller: [
					'$http',
					'$q',
					function ($http, $q) {
						var vm = this,
							previews = {};

						// VM Objects
						vm.currentPage = 1;
						vm.isPageLoading = false;
						vm.pagePreview = null;

						// VM Actions
						vm.openPage = openPage;

						function loadPreview(page) {
							var def = $q.defer();
							if (angular.isUndefined(previews[page])) {
								var img = new Image();
								img.onload = function () {
									previews[page] = img;
									def.resolve(img);
								};
								$http.get(vm.src, {params: {page: page}}).then(function (result) {
									if (result.data.success) {
										img.src = result.data.url;
									}
								});
							} else {
								def.resolve(previews[page]);
							}
							return def.promise;
						}
						function openPage(page) {
							var def = $q.defer();
							vm.isPageLoading = true;
							vm.currentPage = page;
							loadPreview(page).then(function (result) {
								vm.pagePreview = result;
								vm.isPageLoading = false;
								def.resolve();
							});
							return def.promise;
						}
					}
				],
				controllerAs: 'previewVm',
				template: '<div class="document-preview" show-loading="previewVm.isPageLoading">' +
					'<div class="document-preview--pages">' +
					'<a href="" ng-click="prevPage()" ng-disabled="previewVm.currentPage <= 1"><i class="glyphicon glyphicon-menu-left"></i>Previous</a>' +
					'{{previewVm.currentPage}} / {{::previewVm.pageCount}}' +
					'<a href="" ng-click="nextPage()" ng-disabled="previewVm.currentPage >= previewVm.pageCount">Next<i class="glyphicon glyphicon-menu-right"></i></a>' +
					'</div>' +
					'</div>',
				link: function (scope, elem, attrs, ctrl, transclude) {
					
					transclude(scope, function (clone, scope) {
						elem.append(clone);
					});

					var preview = angular.element('<div class="document-preview--preview-area"></div>'),
						previewArea = attrs.previewArea ? elem.find('.' + attrs.previewArea) : elem;

					previewArea.append(preview);

					ctrl.openPage(ctrl.currentPage).then(function () {
						updatePreviewImage();
					});

					scope.nextPage = function () {
						var next = ctrl.currentPage + 1;
						if (next <= ctrl.pageCount) {
							ctrl.openPage(next).then(function () {
								updatePreviewImage();
							});
						}
					};

					scope.prevPage = function () {
						var prev = ctrl.currentPage - 1;
						if (prev > 0) {
							ctrl.openPage(prev).then(function () {
								updatePreviewImage();
							});
						}
					};

					function updatePreviewImage() {
						preview.html(ctrl.pagePreview);
						preview.css('width', ctrl.pagePreview.width + 'px');
						preview.css('height', ctrl.pagePreview.height + 'px');
					}

				}
			};
		}]);

})(opakeApp, angular);
