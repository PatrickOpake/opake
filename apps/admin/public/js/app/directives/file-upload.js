(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive("fileUpload", function() {
		return {
			restrict: "A",
			scope: {
				uploadUrl: '=',
				formData: '&',
				onComplete: '&',
				onError: '&'
			},
			link: function($scope, element, attrs) {

				var body = element.closest('body');
				var formUploadInput = $("<input type='file' class='fileupload' name='file' />");
				body.append(formUploadInput);

				var fileUploadParams = {
					dataType: 'json',
					url: $scope.uploadUrl,
					formData: ($scope.formData) ? $scope.formData() : null,
					dropZone: null,
					add: function (e, data) {
						var jqXHR = data.submit();
						jqXHR.success(function (result, textStatus, jqXHR) {
							if ($scope.onComplete) {
								$scope.onComplete({result: result});
							}
						});
						jqXHR.error(function (jqXHR, textStatus, errorThrown) {
							if ($scope.onError) {
								$scope.onError({error: errorThrown});
							}
						});

					}
				};

				element.on('click', function(e) {
					formUploadInput.trigger('click');
					e.preventDefault();
				});
				formUploadInput.fileupload(fileUploadParams);
			}
		}
	});

})(opakeApp, angular);
