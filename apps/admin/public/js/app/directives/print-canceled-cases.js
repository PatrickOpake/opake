(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('printCanceledCases', [
		'$timeout',

		function ($timeout) {
			return {
				restrict: "A",
				link: function (scope, elem, attrs) {

					elem.click(function (e) {
						$timeout(function () {
							var overview = $('#canceledCasesPrint');
							var $body = $('body');
							$body.addClass('hidden-print-body');
							$body.addClass('canceled-cases-print-body');
							var $canceledCasesPrintSection = $("#canceledCasesPrintSection");
							var domClone = overview.get(0).cloneNode(true);
	
							if (!$canceledCasesPrintSection.length) {
								$canceledCasesPrintSection = $('<div>').attr('id', 'canceledCasesPrintSection');
								$body.append($canceledCasesPrintSection);
							} else {
								$canceledCasesPrintSection.html('');
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
	
							$canceledCasesPrintSection.append(printSectionTittle);
							$canceledCasesPrintSection.append(domClone);
	
							window.print();
							$body.removeClass('hidden-print-body');
							$canceledCasesPrintSection.html('');
						});
					});

				}
			};
		}
	]);

})(opakeApp, angular);
