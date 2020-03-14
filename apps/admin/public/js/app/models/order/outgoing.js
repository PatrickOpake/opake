(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('OrderOutgoing', ['$filter', function ($filter) {

			var OrderOutgoing = function (data) {

				angular.extend(this, data);

				this.getTotalItems = function () {
					var total = 0;
					angular.forEach(this.groups, function (group) {
						angular.forEach(group.items, function (item) {
							total += item.count;
						});
					});
					return total;
				};

				this.getUniqueItems = function () {
					var total = 0;
					angular.forEach(this.groups, function (group) {
						total += group.items.length;
					});
					return total;
				};

			};

			return (OrderOutgoing);
		}]);
})(opakeApp, angular);