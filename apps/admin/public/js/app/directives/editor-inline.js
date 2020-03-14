(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('editorInline', ['$compile', 'Dictation', function ($compile, Dictation) {
			return {
				restrict: "E",
				replace: true,
				require: 'ngModel',
				scope: {
					ngModel: "=",
					ngDisabled: "="
				},
				template: '<div class="opk-editor-inline"></div>',
				link: function (scope, elem, attrs) {
					var editor = angular.element('<div ng-disabled="ngDisabled" ui-tinymce="options" ng-model="ngModel" ng-style="{}" class="form-control"></div>'),
						dictationEnabled = angular.isDefined(attrs.dictation);


					var imageUploadInput = $('#uiMceImageUpload');
					if (!imageUploadInput.length) {

						var fileUploadParams = {
							dataType: 'json',
							url: '/image/ajax/upload/?r=' + Math.random(),
							formData: function() {

								var params = [];
								params.push({
									name: 'upload_type',
									value: 'editor'
								});

								return params;
							},
							add: function (e, data) {
								var jqXHR = data.submit();
								jqXHR.success(function (result, textStatus, jqXHR) {
									if (result.success) {
										var url = result.original;
										var fullUrl = location.protocol + '//' + location.host + url;

										$('.mce-btn.mce-open')
											.parent()
											.find('.mce-textbox')
											.val(fullUrl);

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

						imageUploadInput = $("<input id='uiMceImageUpload' type='file' name='file' />");
						$('body').append(imageUploadInput);
						imageUploadInput.fileupload(fileUploadParams);
					}

					scope.options = {
						inline: true,
						menubar: false,
						//needed for image plugin and printing
						remove_script_host : false,
						convert_urls : false,
						readonly: scope.ngDisabled,
						plugins: ["image", "autoresize", "charmap", "paste"],
						toolbar1: 'undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image charmap',
						charmap_append: [
							['9633', 'white square'],
						],
						paste_as_text: true,
						paste_filter_drop: false,
						file_browser_callback: function(field_name, url, type, win) {
							if (type == 'image') {
								$('#uiMceImageUpload').trigger('click');
							}
						}
					};

					if (dictationEnabled) {
						editor.attr('data-nusa-enabled', true);

						scope.options.init_instance_callback = function (ed) {
							Dictation.reload();
						};
						/*scope.options.setup = function (ed) {
							//navigator.getUserMedia({audio: true}, function () {
								ed.on("click", function () {
									if (typeof NUSA_startRecording === 'function') {
										NUSA_startRecording();
									}
								});
							//});
						};*/
					}

					elem.html(editor);
					$compile(elem)(scope);
				}
			};
		}]);

})(opakeApp, angular);
