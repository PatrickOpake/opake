(function (opakeApp, angular) {
	'use strict';

	var UPDATE_USER_STATUS_PERIOD = 45;

	var Message = function (data) {
		angular.extend(this, data);

		if (angular.isDefined(data)) {
			this.send_date = moment(data.send_date).toDate();
		}
	};

	opakeApp.service('Messaging', [
		'$http',
		'$rootScope',
		'$q',
		'$filter',
		'$timeout',
		'$interval',
		'appInitData',
		'MessagingPoller',
		'ViewState',
		function ($http, $rootScope, $q, $filter, $timeout, $interval, appInitData, MessagingPoller, ViewState) {

			var self = this,
				baseUrl = '/messaging/ajax/' + appInitData.orgId,
				loggedUser = appInitData.loggedUser,
				users,
				dialogMessages = {}, // messages for active dialogs {user_id: message array}
				viewState = ViewState.getState('messaging') || {};

			this.showWidget = angular.isDefined(viewState.show_widget) ? viewState.show_widget : false;

			this.init = function () {
				$http.get(baseUrl + '/users').then(function (result) {
					users = result.data.users;

					MessagingPoller.start(result.data.timestamp, applyUpdates);

					$interval(updateUsersState, UPDATE_USER_STATUS_PERIOD * 1000);
				});
			};

			this.isLoaded = function () {
				return angular.isDefined(users);
			};

			this.isActive = function () {
				return loggedUser.is_messaging_active;
			};

			this.toggleActive = function () {
				var active = !loggedUser.is_messaging_active,
					url = baseUrl + (active ? '/activate' : '/deactivate');

				$http.post(url).then(function(){
					loggedUser.is_messaging_active = active;
				});
			};

			this.isShowWidget = function () {
				return self.showWidget;
			};

			this.toggleShowWidget = function () {
				self.showWidget = !self.showWidget;

				if (self.showWidget) {
					for (var i = 0; i < users.length; i++) {
						if (users[i].unread_count) {
							$timeout( function() {
								$rootScope.$broadcast('OpenDialogWindowForUser', users[i]);
							});
							break;
						}
					}
				}

				this.updateViewState('show_widget', self.showWidget);
			};

			this.getViewState = function () {
				return viewState;
			};

			this.updateViewState = function (prop, value) {
				if (angular.isObject(prop)) {
					angular.forEach(prop, function(val, key){
						viewState[key] = val;
					});
				} else {
					viewState[prop] = value;
				}
				ViewState.update('messaging', viewState);
			};

			this.getUsers = function () {
				return users;
			};

			this.getUser = function (id) {
				return $filter('filter')(users, {id: id})[0];
			};

			this.getUnreadSum = function () {
				var sum = 0;
				angular.forEach(users, function (user) {
					if (user.unread_count) {
						sum += user.unread_count;
					}
				});
				return sum;
			};

			this.getMessages = function (userId) {
				var deferred = $q.defer();
				if (angular.isDefined(dialogMessages[userId])) {
					deferred.resolve(dialogMessages[userId]);
				} else {
					$http.get(baseUrl + '/history/' + userId).then(function (result) {
						var messages = [];
						angular.forEach($filter('orderBy')(result.data, 'id'), function (message) {
							messages.push(new Message(message));
						});
						dialogMessages[userId] = messages;
						deferred.resolve(messages);
						checkRead();
					});
				}
				return deferred.promise;
			};

			this.clearHistory = function (userId) {
				delete dialogMessages[userId];
			};

			this.addMessage = function (message) {
				$http.post(baseUrl + '/add/', $.param({data: JSON.stringify(message)})).then(function (result) {
					if (dialogMessages[message.recipient_id]) {
						dialogMessages[message.recipient_id].push(new Message(result.data));
					}
				});
			};

			this.editMessage = function (message) {
				return $http.post(baseUrl + '/edit/', $.param({data: JSON.stringify(message)}));
			};

			this.removeMessage = function (userId, message) {
				$http.post(baseUrl + '/remove/' + message.id).then(function () {
					var messages = dialogMessages[userId];
					if (messages) {
						var idx = messages.indexOf(message);
						if (idx > -1) {
							messages.splice(idx, 1);
						}
					}
				});
			};

			function userPositionRise(id) {
				var user = self.getUser(id),
					idx = users.indexOf(user);
				if (idx > 0) {
					users.splice(idx, 1);
					users.unshift(user);
				}
			}

			var unreadCache = [],
				deleteCache = [];
			function applyUpdates(data) {
				if (data.messages) {
					var needCheckRead = false;
					angular.forEach($filter('orderBy')(data.messages, 'id'), function (message) {
						var message = new Message(message),
							messages = dialogMessages[message.sender_id];
						if (messages) {
							var oldMessage = $filter('filter')(messages, {id: message.id})[0];
							if (oldMessage) {
								messages[messages.indexOf(oldMessage)] = message;
							} else {
								messages.push(message);
								needCheckRead = true;
								userPositionRise(message.sender_id);
							}
						} else if (!message.is_read && unreadCache.indexOf(message.id) === -1) {
							var user = self.getUser(message.sender_id);
							if (user) {
								user.unread_count++;
								userPositionRise(user.id);
								unreadCache.push(message.id);
							} else {
								$http.get(baseUrl + '/user/' + message.sender_id).then(function (result) {
									users.unshift(result.data);
								});
							}
						}
					});
					if (needCheckRead) {
						checkRead();
					}
				}
				if (data.deleted) {
					angular.forEach(data.deleted, function (message) {
						var messages = dialogMessages[message.sender_id];
						if (messages) {
							var deletedMessage = $filter('filter')(messages, {id: message.id})[0];
							if (deletedMessage) {
								messages.splice(messages.indexOf(deletedMessage), 1);
							}
						} else if (!message.is_read && deleteCache.indexOf(message.id) === -1) {
							var user = self.getUser(message.sender_id);
							if (user) {
								user.unread_count--;
								deleteCache.push(message.id);
							}
						}
					});
				}
			}

			function checkRead() {
				$timeout(function () {
					var read = [];
					angular.forEach(dialogMessages, function (messages, userId) {
						var unread = $filter('filter')(messages, {sender_id: userId, is_read: false});
						if (unread.length) {
							read.push(unread[unread.length - 1]);
						}
					});
					if (read.length) {
						$http.post(baseUrl + '/read/', $.param({data: JSON.stringify(read)})).then(function(){
							angular.forEach(read, function(lastRead){
								var user = self.getUser(lastRead.sender_id);
								user.unread_count = 0;

								if (dialogMessages[lastRead.sender_id]) {
									var messages = $filter('filter')(dialogMessages[lastRead.sender_id], {sender_id: lastRead.sender_id});
									angular.forEach(messages, function (message) {
										message.is_read = true;
									});
								}
							});
						});
					}
				});
			}

			function updateUsersState() {
				var usersById = {};
				angular.forEach(users, function(user){
					usersById[user.id] = user;
				});
				$http.get(baseUrl + '/usersState').then(function (result) {
					angular.forEach(result.data, function (userUpdate, id) {
						if (usersById[id]) {
							usersById[id].is_online = userUpdate.is_online;
							usersById[id].is_active = userUpdate.is_active;
						}
					});
				});
			}
		}]);

	opakeApp.service('MessagingPoller', [
		'$http',
		'$q',
		'appInitData',
		function ($http, $q, appInitData) {
			var started = false,
				callback,
				timestamp,
				canceler,
				url = '/messaging/ajax/' + appInitData.orgId + '/poll/',
				iterations = [5,10,10,15,15,20,20,25,25,30],
				reqNumber = 0;

			this.start = function (ts, callbk) {
				if (started) {
					throw new Error("Messaging poller already started");
				}
				if (!angular.isFunction(callbk)) {
					throw new Error("Callback should be function");
				}
				timestamp = ts;
				callback = callbk;
				canceler = $q.defer();
				started = true;

				poll();
			};

			this.stop = function () {
				canceler.resolve();
				started = false;
			};

			function poll() {
				reqNumber++;
				$http.post(url, $.param({timestamp: timestamp, iterations: getIterations()}), {timeout: canceler.promise}).then(function(resp){
					var result = resp.data;
					timestamp = result.timestamp;

					if (result.data) {
						callback(result.data);
					}
					poll();
				}, function(){
					setTimeout(poll, 1000);
				});
			}

			function getIterations() {
				if (iterations[reqNumber-1]) {
					return iterations[reqNumber-1];
				}
				return iterations[iterations.length-1];
			}
		}]);
})(opakeApp, angular);
