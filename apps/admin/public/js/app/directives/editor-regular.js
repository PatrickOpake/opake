(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('editorRegular', ['$compile', function ($compile) {
		return {
			restrict: "EA",
			replace: true,
			require: 'ngModel',
			scope: {
				ngModel: "=",
				ngDisabled: "=?",
				editorRegular: "=?",
				rows: "=?",
				name: "@?"
			},
			template: '<div class="opk-editor-regular"></div>',
			link: function (scope, elem, attrs) {
				//var editor = angular.element('<div ng-disabled="ngDisabled" ui-tinymce="options" ng-model="ngModel" ng-style="{}" class="form-control"></div>');
				var editor = angular.element('<textarea name="{{name}}" rows="{{rows}}" ui-tinymce="options" ng-model="ngModel"></textarea>');

				var defaultOptions = {
					body_class: 'opk-editor-regular-content',
					height: 500,
					plugins: [
						'advlist autolink autoresize lists link image charmap print preview hr anchor pagebreak',
						'searchreplace wordcount visualblocks visualchars code fullscreen',
						'insertdatetime nonbreaking save table contextmenu directionality',
						'template paste textcolor colorpicker textpattern'
					],
					toolbar1: 'insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
					toolbar2: 'print preview | forecolor backcolor',
					image_advtab: true,
					//needed for image plugin and printing
					remove_script_host: false,
					convert_urls: false,
					readonly: scope.ngDisabled,
					table_cell_class_list: [
						{title: 'None', value: ''},
						{title: 'Border', value: 'border'},
						{title: 'Bold Border', value: 'border-bold'},
						{title: 'Dashed Border', value: 'border-dashed'},
						{title: 'Dashed Bold Border', value: 'border-dashed-bold'},
						{title: 'Top Border', value: 'border-top'},
						{title: 'Bottom Border', value: 'border-bottom'},
						{title: 'Left Border', value: 'border-left'},
						{title: 'Right Border', value: 'border-right'},
						{title: 'Top Left Border', value: 'border-top-left'},
						{title: 'Top Right Border', value: 'border-top-right'},
						{title: 'Bottom Left Border', value: 'border-bottom-left'},
						{title: 'Bottom Right Border', value: 'border-bottom-right'},
						{title: 'Struck-through', value: 'struck-through'},
						{title: 'Struck-through With Border', value: 'struck-through-with-border'}
					],
					charmap_append: [
						['9633', 'white square'],
					],
					style_formats: [
						{
							title: 'Line spacing',
							items: [
								{title: '1', selector: 'p,div,h1,h2,h3,h4,h5,h6', styles: {lineHeight: '1'}},
								{title: '1.5', selector: 'p,div,h1,h2,h3,h4,h5,h6', styles: {lineHeight: '1.5'}},
								{title: '2', selector: 'p,div,h1,h2,h3,h4,h5,h6', styles: {lineHeight: '2'}}
							]
						}
					],
					style_formats_merge: true,
					content_css: '/css/editor.css',
					file_browser_callback: function (field_name, url, type, win) {
						if (type == 'image') {
							$('#uiMceImageUpload').trigger('click');
						}
					}
				};

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

				if (!scope.editorRegular) {
					scope.editorRegular = {};
				}

				if (!scope.rows) {
					scope.rows = 10;
				}

				if (!scope.name) {
					scope.name = 'editor-regular';
				}

				scope.options = angular.extend(defaultOptions, scope.editorRegular);

				scope.$watch('editorRegular', function(newVal){
					if (newVal) {
						scope.options = angular.extend(defaultOptions, newVal);
						var editorCopy = angular.copy(editor);
						elem.html(editorCopy);
						$compile(editorCopy)(scope)
					}
				}, true);
			}
		};
	}]);

})(opakeApp, angular);
