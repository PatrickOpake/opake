(function (opakeCore, angular) {
	'use strict';

	var template = '<div class="profile-image-upload">' +
		'<div class="crop-image-container">' +
		'<div class="cropit-preview image-preview"></div>' +
		'<input type="file" class="cropit-image-input fileupload" />' +
		'<a href="#" class="open-file-dialog-link">' +
		'<span class="camera-container" ng-show="!isViewOnly"><span class="icon-photo"></span></span>' +
		'</a>' +
		'</div>' +
		'</div>';

	opakeCore.directive('profileImageUpload', [
		'$rootScope',
		'$q',
		'$http',
		function ($rootScope, $q, $http) {
			return {
				restrict: "E",
				replace: true,
				scope: {
					options: '=?',
					control: '=?',
					isViewOnly: '=?'
				},
				template: template,
				link: function (scope, element, attrs) {

					var defaultOptions = {
						isNewImage: false,
						imageSrc: '/common/i/opake_logo_default.png'
					};

					scope.options = scope.options || {};
					scope.options = angular.extend(defaultOptions, scope.options);

					var imageContainer = element.find('.crop-image-container');

					imageContainer.cropit({
						height: 125,
						width: 125,
						smallImage: 'stretch'
					});

					var imagePreview = element.find('.image-preview');
					imagePreview.addClass('enable-moving');

					if (scope.options.imageSrc) {
						imageContainer.cropit('imageSrc', scope.options.imageSrc);
					}

					if (!scope.options.isNewImage || scope.isViewOnly) {
						imageContainer.cropit('disabled');
						imagePreview.removeClass('enable-moving');
					}

					element.find('.open-file-dialog-link').on('click', function(e) {
						element.find('.fileupload').trigger('click');
						imageContainer.cropit('reenable');
						imagePreview.addClass('enable-moving');
						scope.options.isNewImage = true;
						e.preventDefault();
					});

					element.find('.fileupload').on('click', function(e) {
						e.stopPropagation();
					});

					if (scope.control) {
						scope.control.saveImage = function(options) {
							var def = $q.defer();

							if (scope.options.isNewImage) {
								options = angular.extend({
									imageType: 'default',
									exportOptions: {
										originalSize: false
									}
								}, options);

								imageContainer.cropit('exportZoom', 2);
								var data = imageContainer.cropit('export', options.exportOptions);
								imageContainer.cropit('exportZoom', 1);

								$http.post('/image/ajax/uploadContent/', $.param({
									upload_type: options.imageType,
									data: data
								})).then(function(res) {
									if (res.data.success) {
										def.resolve(res.data);
									} else {
										def.reject(res.errors);
									}
								});
							} else {
								def.resolve(null);
							}

							return def.promise;
						}
					}
				}
			};
		}
	]);

})(opakeCore, angular);