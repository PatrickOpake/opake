$(function () {

	$(document).keypress(function (e) {
		if (e.which == 13 && e.target.nodeName != "TEXTAREA") {
			if (!$(e.target).closest("form").hasClass("enter-submit")) {
				return false;
			}
		}
	});
        /*debugger;
        function onInitTinyMce(inst) {
            alert("Editor: " + inst.editorId + " is now initialized.");
            $(".mce-content-body ").attr('data-nusa-enabled','');
        }
        */
	if (typeof tinymce !== 'undefined') {
            /*debugger;
            function myCustomInitInstance(inst) {
                alert("Editor: " + inst.editorId + " is now initialized.");
            }*/

		tinymce.init({
			selector: "textarea.editor",
			plugins: [
				"code table link print anchor"
			],
                         //init_instance_callback : "onInitTinyMce"
                         //init_instance_callback : "myCustomInitInstance"


		});
                //$("iframe").contents().find(".mce-content-body").attr('data-nusa-enabled','');
               // debugger;
                 
	}

	// Костыль под баг IE calc for min-height
	if (isIE()) {
		var setMinHeight = function () {
			var minus = document.querySelector(".site-header").offsetHeight + document.querySelector(".site-footer").offsetHeight;
			document.querySelector(".site-body").style.minHeight = (document.body.offsetHeight - minus) + 'px';
		};
		setMinHeight();
		window.addEventListener("resize", setMinHeight, false);
	}

// TODO: разгрести остальное
	initImageUpload();

});

function isIE() {
	var ua = window.navigator.userAgent;
	return ua.indexOf('MSIE ') > 0 || ua.indexOf('Trident/') > 0 || ua.indexOf('Edge/') > 0;
}


var initImageUpload = function () {
	var $imageUploadButton = $('.logo.upload');
	var $fileuploadField = $('#fileupload');
	var onImageUploadClick = function (e) {
		$('#fileupload').trigger('click');
		e.preventDefault();
	};
	var fileUploadParams = {
		dataType: 'json',
		url: '/image/ajax/upload/?r=' + Math.random(),
		formData: function () {
			var uploadType = $fileuploadField.data('upload-type');

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
					// путь к input'у в который сложить загруженную картинку
					var $target = $($fileuploadField.data('target'));
					// путь к картинки которую нужно обновить
					var $newimage = $($target.data('target'));
					var targetSize = $newimage.data('size');
					var url = result.result;
					if (targetSize && result.thumbnails[targetSize]) {
						url = result.thumbnails[targetSize]
					} else {
						url = result.original;
					}
					$newimage.attr('src', url);
					$newimage.parent().filter('.hidden').removeClass('hidden');
					$target.val(result.image_id);
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
	$imageUploadButton.on('click', onImageUploadClick);
	if ($fileuploadField.length > 0) {
		$fileuploadField.fileupload(fileUploadParams);
	}
};
