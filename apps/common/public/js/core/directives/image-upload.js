(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive("imageUpload", function() {
		return {
			restrict: "A",
			require: 'ngModel',
			scope: {
				ngModel: "="
			},
			link: function(scope, element, attrs, ngModel) {
				var target = attrs.target;
				var size = attrs.size;
				var uploadType = attrs.uploadType;

				var $body = element.closest('body');
				var $formUploadInput = $('#fileupload');

				if (!$formUploadInput.length) {
					$formUploadInput = $("<input id='fileupload' type='file' name='file' />");
					$body.append($formUploadInput);
				}

				var fileUploadParams = {
					dataType: 'json',
					url: '/image/ajax/upload/?r=' + Math.random(),
					formData: function() {

						var params = [];
						if (uploadType) {
							params.push({
								name: 'upload_type',
								value: uploadType
							});
						}

						return params;
					},
					add: function (e, data) {
						var jqXHR = data.submit();
						jqXHR.success(function (result, textStatus, jqXHR) {
							if (result.success) {
								var $target = $(target);

								var url = result.result;
								if (size && result.thumbnails[size]) {
									url = result.thumbnails[size]
								} else {
									url = result.original;
								}
								if ($target.length) {
									$target.attr('src', url);
								}

								if (attrs.onComplete) {
									$scope.$eval(attrs.onComplete);
								}

								ngModel.$setViewValue(result.image_id);

							} else {
								alert("Can't upload this file");
								console.log('error: ' + result.result);
							}
						});
						jqXHR.error(function (jqXHR, textStatus, errorThrown) {
							alert("Can't upload this file");
							console.log('error: ' + textStatus);
						});

					}
				};

				element.on('click', function(e) {
					$('#fileupload').trigger('click');
					e.preventDefault();
				});
				$formUploadInput.fileupload(fileUploadParams);
			}
		}
	});

})(opakeCore, angular);
