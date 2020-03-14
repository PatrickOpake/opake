// Outgoing Order
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrderOutgoingCrtl', [
		'$scope',
		'$http',
		'OrderOutgoing',
		'View',
		function ($scope, $http, OrderOutgoing, View) {

			var vm = this;

			vm.order = {items: []};
			vm.modal;

			vm.init = function (id) {
				return $http.get('/orders/ajax/outgoing/' + $scope.org_id + '/order/' + id).then(function (resp) {
					vm.order = new OrderOutgoing(resp.data);
				});
			};

			vm.updateCount = function (item, data) {
				return $http.get('/orders/ajax/outgoing/' + $scope.org_id + '/updateCount/' + item.id, {params: {count: data}});
			};

			vm.delete = function (item, items) {
				if (confirm('Are you sure?')) {
					$http.get('/orders/ajax/outgoing/' + $scope.org_id + '/deleteItem/' + item.id).then(function (resp) {
						var idx = items.indexOf(item);
						if (idx > -1) {
							items.splice(idx, 1);
						}
					});
				}
			};

			vm.place = function () {
				vm.emails = [];
				if (vm.order.messages && vm.order.messages.length) {
					angular.forEach(vm.order.messages, function(msg, key) {
						vm.emails.push({
							to: (msg.receivers.to && msg.receivers.to.length) ? msg.receivers.to : null,
							cc: (msg.receivers.cc && msg.receivers.cc.length) ? msg.receivers.cc : null,
							bcc: (msg.receivers.bcc && msg.receivers.bcc.length) ? msg.receivers.bcc : null,
							body: msg.body,
							subject: msg.subject,
							vendor: vm.order.groups[key]
						});
					});
				} else {
					angular.forEach(vm.order.groups, function (group) {
						vm.emails.push({to:group.vendor.email, vendor: group.vendor});
					});
				}

				vm.modal = $scope.dialog(View.get('/orders/outgoing/mail.html'), $scope, {windowClass: 'outgoing-order', size: 'md'});
			};

			vm.saveWithoutSending = function () {
				if (vm.validEmailForms()) {
					vm.sending = true;
					$http.post('/orders/ajax/outgoing/' + $scope.org_id + '/saveWithoutSending/' + vm.order.id, $.param({
						data: JSON.stringify(vm.emails)
					})).then(function () {
						vm.modal.close();
						vm.init(vm.order.id);
						vm.sending = false;
					});
				}
			};

			vm.sendEmails = function () {
				vm.sending = true;
				$http.post('/orders/ajax/outgoing/' + $scope.org_id + '/place/' + vm.order.id, $.param({
					data: JSON.stringify(vm.emails)
				})).then(function (result) {
					if (result.data.errors) {
						var errors = result.data.errors.split(';');
						if (angular.isArray(vm.errors)) {
							vm.errors = vm.errors.concat(errors);
						} else {
							vm.errors = errors;
						}
					} else {
						vm.modal.close();
						vm.init(vm.order.id);
					}
					vm.sending = false;
				});
			};

			vm.validEmailForms = function () {
				var valid = true;
				angular.forEach(vm.emails, function (email, i) {
					var form = vm['email_form_' + i] ? vm['email_form_' + i] : null;
					if (form && form.$invalid) {
						valid = false;
					}
				});
				return valid;
			};


			vm.export = function (id) {
				var iframe = document.createElement("iframe");
				iframe.setAttribute("src", '/orders/ajax/outgoing/' + $scope.org_id + '/export/' + vm.order.id + '?vendor_id=' + id);
				iframe.setAttribute("style", "display: none");
				document.body.appendChild(iframe);
			};

		}]);

})(opakeApp, angular);
