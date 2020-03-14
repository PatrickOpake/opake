(function (opakeApp, angular) {
	'use strict';

	var AccessLevel = function (level) {

		this.level = level;

		this.isAllowed = function() {
			return level === true;
		};

		this.isDisallowed = function() {
			return level === false;
		};

		this.isSelfAllowed = function() {
			return level === 'self';
		}

	};

	opakeApp.service('Permissions', [
		'appInitData',
		'User',

		function (appInitData, User) {

			var self = this;

			this.user = new User(appInitData.loggedUser);
			this.permissions = appInitData.loggedUserPermissions;

			this.getAccessLevel = function (section, action) {
				return new AccessLevel(getAllowed(section, action));
			};

			this.hasAccess = function (section, action, model) {
				var accessLevel = this.getAccessLevel(section, action);
				if (accessLevel.isAllowed()) {
					return true;
				}

				if (accessLevel.isSelfAllowed()) {
					if (!model) {
						return false;
					}

					if (!angular.isFunction(model.isSelf)) {
						return false;
					}

					return model.isSelf(this.user);
				}

				return false;
			};

			function getAllowed(section, action) {
				if (self.user.is_internal) {
					return true;
				}

				if (section in self.permissions) {
					if (action in self.permissions[section]) {
						return self.permissions[section][action];
					}
				}

				return false;
			}

		}]);
})(opakeApp, angular);
