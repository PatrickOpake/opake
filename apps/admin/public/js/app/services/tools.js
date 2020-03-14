// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Tools', ['$http', function ($http) {

			this.export = function (url, params) {
				if (angular.isObject(params)) {
					url += '?' + $.param(params);
				}
				var iframe = document.createElement("iframe");
				iframe.setAttribute("src", url);
				iframe.setAttribute("style", "display: none");
				document.body.appendChild(iframe);
			};

			this.print = function (url, params) {
				if (angular.isObject(params)) {
					url += '?' + $.param(params);
				}

				var w = window.open(url);
				w.print();
			};

			this.uploadFile = function (url, file, filename) {
				var fd = new FormData();
				var name = filename || 'file';
				fd.append(name, file);

				return $http.post(url, fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				});
			};

			this.windowOpenInPost = function(action, data){
				var form = document.createElement("form");
				form.setAttribute("style", "display: none");
				form.target = "_blank";    
				form.method = "POST";
				form.action = action;

				var input = document.createElement("input");
				input.type = "text";
				input.name = "data";
				input.value = data;

				form.appendChild(input);

				document.body.appendChild(form);

				form.submit();
			};
		}]);
})(opakeApp, angular);
