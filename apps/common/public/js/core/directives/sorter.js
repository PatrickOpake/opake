(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('sorter', [function () {
			return {
				restrict: 'A',
				scope: {
					params: '=sorter',
					callback: '&'
				},
				link: function (scope, elem, attrs) {

					scope.$watchGroup(['params.sort_by', 'params.sort_order'], function(newVal, oldVal) {
						elem.find('i.sorter').remove();
						if (newVal[0] && newVal[1]) {
							elem.find('[sort="' + newVal[0] + '"]').append('<i class="glyphicon sorter glyphicon-triangle-' + (newVal[1] === 'ASC' ? 'top' : 'bottom') + '"></i>');
						}
					});

					elem.find('[sort]').click(function () {
						var by = $(this).attr('sort');
						var order = scope.params.sort_order !== 'ASC' ? 'ASC' : 'DESC';

						scope.$apply(function(){
							scope.params.sort_by = by;
							scope.params.sort_order = order;
						});
						scope.callback();
					});

				}
			};
		}]);

})(opakeCore, angular);
