(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('printIframe', [
		function () {
			return {
				restrict: "A",
				scope: {
					printIframe: '@'
				},
				link: function (scope, elem, attrs) {

					elem.click(function (e) {
						$('iframe[name="printPDF"]').remove();

						var iframe = document.createElement("iframe");
						iframe.setAttribute("src", scope.printIframe);
						iframe.setAttribute("name", 'printPDF');
						iframe.setAttribute("style", "display: none");
						document.body.appendChild(iframe);
						window.frames.printPDF.focus();
						window.frames.printPDF.print();
					});

				}
			};
		}
	]);

})(opakeCore, angular);
