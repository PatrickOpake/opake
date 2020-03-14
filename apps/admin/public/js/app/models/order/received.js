(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('OrderReceived', ['$filter', '$http',  '$rootScope', function ($filter, $http, $rootScope) {

			var OrderReceived = function (data) {

				angular.extend(this, data);

				this.status;

				var STATUS_OPEN = 0;
				var STATUS_INCOMPLETE = 1;
				var STATUS_COMPLETE = 2;

				var statuses = {};
				statuses[STATUS_OPEN] = 'Open';
				statuses[STATUS_INCOMPLETE] = 'Incomplete';
				statuses[STATUS_COMPLETE] = 'Complete';

				this.getTotalItems = function () {
					var total = 0;
					angular.forEach(this.items, function (item) {
						total += +item.ordered;
					});
					return total;
				};

				this.updateStatus = function() {
					this.status = this.getCurrentStatus();
				};

				this.getCurrentStatus = function() {
					if($filter('filter')(this.items, function(value) {
							return value.status === 0 || value.status === 1;
						}, true).length) {
						return STATUS_INCOMPLETE;
					} else {
						return STATUS_COMPLETE;
					}
				};

				this.getCurrentStatusName = function() {
					return statuses[this.getCurrentStatus()];
				};

				this.getStatus = function() {
					return this.status;
				};

				this.getStatusName = function() {
					return statuses[this.status];
				};

				this.save = function(callback) {
					$http.post('/orders/ajax/received/' + $rootScope.org_id + '/saveOrder/' + this.id, $.param({order: this.toJson()})).then(function (resp) {
						if(callback) {
							callback(resp);
						}
					});
				};

				this.getItemsByType = function(status) {
					if(status === 'Open') {
						return $filter('filter')(this.items, {status: '!'+STATUS_COMPLETE});
					} else if(status === 'Complete') {
						return $filter('filter')(this.items, {status: STATUS_COMPLETE});
					}
				};

				this.isComplete = function() {
					return STATUS_COMPLETE == this.status;
				};

				this.isIncomplete = function() {
					return STATUS_INCOMPLETE == this.status;
				};

				this.toJson = function () {
					return {
						id: data.id,
						po_id: this.po_id,
						status: this.getStatus(),
						items: this.items
					};
				};

				this.toOutsideJson = function () {
					return {
						po_id: this.po_id,
						status: this.getStatus(),
						items: this.items,
						vendor_id: this.vendor.id,
						shipping_type: this.shipping_type ? this.shipping_type.name : null
  					};
				};
			};

			return (OrderReceived);
		}]);
})(opakeApp, angular);