<script src='/vendors/tinymce/js/tinymce/tinymce.min.js' type='text/javascript'></script>

<style>
	.opk-editor-inline .form-control {
		height: auto;
	}
</style>

<div ng-init="obj = {}" id="dictation-container">
	<label>Field 1</label>
	<editor-inline ng-model="obj.text1" dictation></editor-inline>
	<br />
	<label>Field 2</label>
	<editor-inline ng-model="obj.text2" dictation></editor-inline>
	<br />
	<label>Field 3</label>
	<editor-inline ng-model="obj.text3" dictation></editor-inline>
	<br />
	<label>Field 4</label>
	<editor-inline ng-model="obj.text4" dictation></editor-inline>
	<br />
	<label>Field 5</label>
	<editor-inline ng-model="obj.text5" dictation></editor-inline>
	<br />
	<label>Field 6</label>
	<editor-inline ng-model="obj.text6" dictation></editor-inline>
	<br />
	<label>Field 7</label>
	<editor-inline ng-model="obj.text7" dictation></editor-inline>
	<br />
	<label>Field 8</label>
	<editor-inline ng-model="obj.text8" dictation></editor-inline>
	<br />
	<label>Field 9</label>
	<editor-inline ng-model="obj.text9" dictation></editor-inline>
	<br />
	<label>Field 10</label>
	<editor-inline ng-model="obj.text10" dictation></editor-inline>
</div>
