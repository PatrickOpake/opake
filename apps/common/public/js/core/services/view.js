// App config
(function (opakeCore, angular) {
	'use strict';

	opakeCore.service('View', ['$rootScope', '$timeout', 'config', function ($rootScope, $timeout, config) {

			var marker = '';
			var isPC;
			var isTablet;
			var isPhone;

			var isWaitingRoom = false;

			this.setMarker = function (mrk) {
				marker = mrk;
			};

			this.get = function (view) {
				var path = config.view.src + view;
				if (marker) {
					path += ('?v=' + marker);
				}
				return path;
			};

			function updateScreenSizeConstants() {
				if (window.innerWidth < 720) {
					isPC = false;
					isTablet = true;
					isPhone = true;
				} else if ((window.innerWidth <= 1040) && (window.innerWidth >= 720)) {
					isPC = false;
					isTablet = true;
					isPhone = false;
				} else {
					isPC = true;
					isTablet = false;
					isPhone = false;
				}
			};

			updateScreenSizeConstants();

			var resizePromise;
			window.onresize = function () {
				$timeout.cancel(resizePromise);
				resizePromise = $timeout(function () {
					updateScreenSizeConstants();
					$rootScope.$emit('Screen.resize');
					$rootScope.$apply();
				}, 25);
			};

			this.isPC = function () {
				return isPC;
			};

			this.isTablet = function () {
				return isTablet;
			};

			this.isPhone = function () {
				return isPhone;
			};

		}]);
})(opakeCore, angular);
