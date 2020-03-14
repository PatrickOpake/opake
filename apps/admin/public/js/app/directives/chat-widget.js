(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('chatWidget', ['$rootScope', '$filter', '$interval', 'View', 'ChatMessage', function ($rootScope, $filter, $interval, View, ChatMessage) {
			return {
				restrict: "E",
				replace: true,
				controller: function ($scope, $http) {
					var vm = this;
					var user = $rootScope.loggedUser;
					var messages = null;

					vm.input_text = '';
					vm.open = false;

					var getLastMessageId = function() {
						if (messages && messages.length) {
							return messages[messages.length - 1].id;
						}
						return false;
					};

					var insertMessages = function(dataArr) {
						if (dataArr && dataArr.length) {
							if (!messages) {
								messages = [];
							}
							angular.forEach(dataArr, function(data){
								messages.push(new ChatMessage(data));
							});

							messages = $filter('orderBy')(messages, 'id');
							if (messages.length > 100) {
								messages = messages.slice(-100);
							}

							var messageGroups = [],
								group = null;
							angular.forEach(messages, function (message) {
								if (group && group.user.id === message.user.id) {
									group.messages.push(message);
								} else {
									if (group) {
										messageGroups.push(group);
									}
									group = {
										user: message.user,
										messages: [message]
									};
								}
							});
							if (group) {
								messageGroups.push(group);
							}
							vm.messageGroups = messageGroups;
						}
					};

					vm.addMessages = function () {
						if (vm.input_text) {
							var message = new ChatMessage();
							message.text = vm.input_text;
							vm.input_text = '';
							$http.post('/chat/ajax/' + $scope.org_id + '/addMessage/', $.param({
								data: JSON.stringify(message)
							})).then(function (result) {
								var mess = result.data;
								if (angular.isObject(mess)) {
									user.chat_last_readed_id = mess.id;
									insertMessages([mess]);
								}
							});
						}
					};

					vm.toggleWindow = function () {
						vm.open = !vm.open;
					};

					vm.getUnreadedCount = function() {
						var count = 0;
						angular.forEach(messages, function(message){
							if (message.id > user.chat_last_readed_id) {
								count++;
							}
						});
						return count;
					};

					vm.read = function () {
						if (vm.getUnreadedCount()) {
							var lastId = getLastMessageId();
							if (lastId) {
								$http.get('/chat/ajax/' + $scope.org_id + '/read/' + lastId).then(function (response) {
									user.chat_last_readed_id = lastId;
								});
							}
						}
					};

					vm.getDate = function (message) {
						var messageNumber = messages.indexOf(message);
						if (messageNumber > 0) {
							var previous = messages[messageNumber - 1];
							if (message.date.toDateString() === previous.date.toDateString()) {
								return $filter('date')(message.date, 'h:mm a');
							}
						}
						return $filter('date')(message.date, 'M/d/yyyy, h:mm a');
					};

					$interval(function() {
						var lastId = getLastMessageId();
						$http.get('/chat/ajax/' + $scope.org_id + '/lastMessages/' + (lastId || '')).then(function (response) {
							if (lastId === getLastMessageId()) { // User could send message
								insertMessages(response.data);
							}
						});
					}, 2000);
				},
				controllerAs: 'ctrl',
				templateUrl: function () {
					return View.get('widgets/chat.html');
				},
				link: function (scope, elem, attrs, ctrl) {
					elem.on('click', '.chat-widget--body', function(){
						ctrl.read();
					});
				}
			};
		}]);

})(opakeApp, angular);
