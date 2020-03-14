(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('printOverview', [
		'$timeout',
		'$http',

		function ($timeout, $http) {
			return {
				restrict: "A",
				scope: {
					printOverview: '@'
				},
				link: function (scope, elem, attrs) {

					elem.click(function (e) {
						$timeout(function () {
							var $body = $('body');
							$body.addClass('hidden-print-body');
							$body.addClass('overview-print-body');
							var $overviewPrintSection = $("#overviewPrintSection");
	
							if (!$overviewPrintSection.length) {
								$overviewPrintSection = $('<div>').attr('id', 'overviewPrintSection');
								$body.append($overviewPrintSection);
							} else {
								$overviewPrintSection.html('');
							}

							var printSectionTittle = '<title>@page</title>' +
								'<style>' +
								' @media print { ' +
									'@page {' +
										'size: A4;' +
										'margin: 5mm;' +
									'}' +
								'}' +
								'</style>';
							$overviewPrintSection.append(printSectionTittle);

							$http.get(scope.printOverview).then(function (response) {
								$overviewPrintSection.append(response.data);

								window.print();
								$body.removeClass('hidden-print-body');
								$overviewPrintSection.html('');
							});
						});
					});

				}
			};
		}
	]);

})(opakeApp, angular);
