(function (opakeCore, angular, Math) {
	'use strict';

	opakeCore.directive('pages', ['$timeout', function ($timeout) {
			return {
				restrict: 'E',
				replace: true,
				scope: true,
				template: '<div class="paging" ng-show="pg.count > 10">' +
						'Entries per page: <select class="form-control" ng-model="pg.limit" ng-options="o as o for o in pg.limits"></select>' +
						'<span class="paging--pages-container"><ul class="paging--pages"></ul></span>' +
					'</div>',
				bindToController: {
					count: '=',
					page: '=',
					limit: '='
				},
				controller: function () {
					var vm = this;

					vm.limits = [10, 20, 30, 40, 50];

					vm.setPage = function(p){
						vm.page = p;
					};
				},
				controllerAs: 'pg',
				link: function(scope, elem, attrs, ctrl) {
					var pagesHtml = elem.find(".paging--pages");

					var callback = function() {
						if (attrs.callback) {
							scope.$eval(attrs.callback)
						}
					};

					scope.$watchGroup(['pg.count', 'pg.page', 'pg.limit'], function(newVal, oldVal) {

						if (!newVal[0]) {
							return;
						}

						var pages =  Math.ceil(newVal[0] / newVal[2]),
							page = newVal[1];

						if (scope.pg.page >= pages) {
							ctrl.setPage(0);
						}

						if (pages < 2) {
							pagesHtml.html("");
							return;
						}

						var navigate = '',
							visible = attrs.visible || 5,
							half_visible = Math.ceil(visible / 2);
						if (page === 0) {
							navigate += '<li class="prev"><span class="inactive"></span></li>';
						}
						if (page > 0) {
							navigate += '<li class="prev"><a href="' + (page - 1) + '"></a></li>';
						}

						for (var i = 0; i < pages; i++) {
							var active = i == page ? 'active' : '';
							// первая и последняя страницы
							if (i === 0 || i === pages - 1) {
								navigate += "<li class='" + active + "'><a href='" + i + "'>" + (i+1) + "</a></li>";
							}
							// $showPage от начала
							else if (page < half_visible && i < visible) {
								navigate += "<li class='" + active + "'><a href='" + i + "'>" + (i+1) + "</a>";
							}
							// $showPage от конца
							else if (page > pages - half_visible && i > pages - visible - 1) {
								navigate += "<li class='" + active + "'><a href='" + i + "'>" + (i+1) + "</a>";
							}
							// номера страниц по середине
							else if (i > page - half_visible && i < page + half_visible) {
								navigate += "<li class='" + active + "'><a href='" + i + "'>" + (i+1) + "</a></li>";
							}
							// многоточия от начала
							else if (page < half_visible && i === visible) {
								navigate += "<li><span>&#8230;</span></li>";
							}
							// многоточия от конца
							else if (page > pages - half_visible && i === pages - visible - 1) {
								navigate += "<li><span>&#8230;</span></li>";
							}
							// многоточия по середине
							else if (i == page - half_visible || i == page + half_visible) {
								navigate += "<li><span>&#8230;</span></li>";
							}
						}
						if (page < pages - 1) {
							navigate += '<li class="next"><a href="' + (page + 1) + '"></a></li>';
						}
						if (page === pages - 1) {
							navigate += '<li class="next"><span class="inactive"></a></li>';
						}
						pagesHtml.html(navigate);
					});

					pagesHtml.on('click', 'a', function(){
						var p = parseInt($(this).attr('href'), 10);
						scope.$apply(function(){
							ctrl.setPage(p);
						});
						callback();
						return false;
					});

					elem.find('select').change(function(){
						callback();
					});

					// Костыль для старых страниц(не ангуляровских)
					if (scope.pg.count === parseInt(scope.pg.count, 10)) {
						$timeout(function () {
							elem.find('a').click(function (e) {
								window.location = updateParam('p', $(this).attr('href'));
								e.preventDefault();
							});
							elem.find('select').change(function (e) {
								var loc = updateParam('p', '' + scope.pg.page);
								window.location = updateParam('l', scope.pg.limit, loc);
							});
						});
					}
				}
			};
		}]);

})(opakeCore, angular, Math);
