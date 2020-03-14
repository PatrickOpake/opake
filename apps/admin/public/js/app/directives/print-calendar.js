(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('printCalendar', [
		'$http',
		'$rootScope',
		'$compile',
		'$timeout',
		'$filter',
		'uiCalendarConfig',

		function ($http, $rootScope, $compile, $timeout, $filter, uiCalendarConfig) {
			return {
				restrict: "A",
				link: function (scope, elem, attrs) {

					elem.click(function (e) {
						$timeout(function () {
							var calendar = $('.cases-calendar');
							var $body = $('body');
							$body.addClass('hidden-print-body');
							$body.addClass('calendar-body');
							var $printSection = $("#printSection");
							var domClone = calendar.get(0).cloneNode(true);

							var calendarItems = uiCalendarConfig.calendars['case-calendar'].fullCalendar('clientEvents');
							var rooms = [];

							angular.forEach(calendarItems, function (item) {
								if (angular.isString(item.location.name) && (!$filter('filter')(rooms, item.location.name).length)) {
									rooms.push(item.location.name)
								} else if (!$filter('filter')(rooms, item.location).length && angular.isString(item.location)) {
									rooms.push(item.location)
								}
							});

							var roomsString = rooms.join(', ');
							var printHeader =
								'<div class="case-calendar--print-header">' +
								'Room: ' + roomsString +
								'</div>';


							if (!$printSection.length) {
								$printSection = $('<div>').attr('id', 'printSection');
								$body.append($printSection);
							} else {
								$printSection.html('');
							}

							var printSectionTittle = '<title>@page</title>' +
								'<style>' +
								' @media print { ' +
									'@page {' +
										'size: A4;' +
									'}' +
								'}' +
								'</style>';

							$printSection.append(printSectionTittle);
							$printSection.append(printHeader);
							$printSection.append(domClone);

							var calendarTitle = calendar.find('.fc-toolbar .fc-center h2').text();
							var scopeSearchParams = null;
							if (scope.listVm) {
								scopeSearchParams = scope.listVm.search_params;
							}

							var logParams = {
								title: calendarTitle,
								searchParams: scopeSearchParams
							};
							$http.post('/cases/ajax/' + $rootScope.org_id + '/logPrintSchedule', $.param({
								data: JSON.stringify(logParams)
							}));

							window.print();
							$body.removeClass('hidden-print-body');
							$printSection.html('');
						});
					});

				}
			};
		}
	]);

})(opakeApp, angular);
