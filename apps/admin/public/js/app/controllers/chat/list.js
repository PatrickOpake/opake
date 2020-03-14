// Chat list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ChatListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'ChatMessage',

		function ($scope, $http, $controller, ChatMessage) {
			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.date = new Date();

			vm.search = function () {
				var data = angular.copy(vm.search_params);
				data.date = moment(vm.date).format('YYYY-MM-DD');

				return $http.get('/chat/ajax/' + $scope.org_id + '/index/', {params: data}).then(function (response) {
					var messages = [];
					angular.forEach(response.data.messages, function (data) {
						messages.push(new ChatMessage(data));
					});
					vm.messages = messages;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

			vm.isToday = function () {
				return ((new Date()).toDateString() === vm.date.toDateString());
			};

			vm.today = function () {
				vm.date = new Date();
				vm.search();
			};

			vm.previous = function () {
				vm.date = moment(vm.date).add(-1, 'days').toDate();

				vm.search();
			};

			vm.next = function () {
				vm.date = moment(vm.date).add(1, 'days').toDate();

				vm.search();
			};

			vm.getDateDisplay = function () {
				return moment(vm.date).format('MMMM D, YYYY');
			};

		}]);

})(opakeApp, angular);
