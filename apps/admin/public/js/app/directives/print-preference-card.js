(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('printPreferenceCard', [
		'$timeout',

		function ($timeout) {
			return {
				restrict: "A",
				link: function (scope, elem, attrs) {

					elem.click(function (e) {
						$timeout(function () {
							var prefCard = $('#preferenceCardPrint');
							var $body = $('body');
							$body.addClass('hidden-print-body');
							$body.addClass('pref-card-print-body');
							var $prefCardPrintSection = $("#prefCardPrintSection");
							var domClone = prefCard.get(0).cloneNode(true);
	
							if (!$prefCardPrintSection.length) {
								$prefCardPrintSection = $('<div>').attr('id', 'prefCardPrintSection');
								$body.append($prefCardPrintSection);
							} else {
								$prefCardPrintSection.html('');
							}

							var printSectionTittle = '<title>@page</title>' +
								'<style>' +
								' @media print { ' +
									'@page {' +
										'size: A4;' +
										'margin: 15mm 25mm;' +
									'}' +
								'}' +
								'</style>';
	
							$prefCardPrintSection.append(printSectionTittle);
							$prefCardPrintSection.append(domClone);
	
							window.print();
							$body.removeClass('hidden-print-body');
							$prefCardPrintSection.html('');
						});
					});

				}
			};
		}
	]);

})(opakeApp, angular);
