<div class="content-block uploaded-form" ng-controller="FormUploadedCrtl as docVm" ng-init="docVm.init(<?= $model->id; ?>)" ng-cloak>

	<errors src="docVm.errors"></errors>

	<div class="uploaded-form--fields">
		<div class="data-row">
			<label>Name*:</label>
			<input type="text" class="form-control" name="name" ng-model="docVm.form.name" />
		</div>
		<div class="checkbox">
			<input id="custom-header" type="checkbox" class="styled" name="include_header" ng-model="docVm.form.include_header" />
			<label for="custom-header" tooltip-placement="bottom" tooltip-class="white" uib-tooltip="Check box to use custom header details below in form. Default header includes only organization info.">Include custom header details</label>
		</div>
	</div>
	<div class="uploaded-form--actions">
		<button class="btn btn-success" ng-click="docVm.save()">Save</button>
		<button class="btn btn-success" ng-if="docVm.loaded && docVm.form.is_pdf" ng-click="docVm.preview()">Preview</button>
		<a href="/settings/forms/charts/{{ ::org_id }}">Cancel</a>
	</div>

	<document-preview ng-if="docVm.loaded && docVm.form.is_pdf"
			  src="{{::docVm.getPreviewUrl()}}" page-count="{{::docVm.form.page_count}}" preview-area="document-area">
		<label>Dynamic Fields</label>
		<div class="uploaded-form--dynamic-fields" doc-fields="docVm.form.dynamic_fields" options="docVm.options"
		     current-page="previewVm.currentPage" page-preview="previewVm.pagePreview">
			<div class="options-list">
				<div class="options-list--search">
					<i class="glyphicon glyphicon-search"></i>
					<input class="options-list--search-input" ng-model="optionsVm.search" placeholder="Search tags" />
				</div>
				<div>Drag and drop tags to document to input data</div>
				<ul>
					<li ng-repeat="option in optionsVm.options| filter: {title: optionsVm.search}"
					    class="options-list--option"
					    draggable="true"
					    key="{{::option.key}}">
						{{::option.title}}
					</li>
				</ul>
			</div>
			<div class="document-area-wrap">
				<div class="document-area">
					<div ng-repeat="field in optionsVm.pageFields"
					     class="dynamic-field"
					     uib-tooltip="Right click to delete or select right corner to resize" tooltip-append-to-body="true"
					     draggable="true"
					     ondragstart="angular.element(this).scope().dragstart(event, angular.element(this).scope().field)"
					     oncontextmenu="angular.element(this).scope().remove(event, angular.element(this).scope().field)"
					     ng-style="{left: field.x, top: field.y, width: field.width, height: field.height}">
						<div class="dynamic-field--content">
							{{::field.title}}
							<div class="dynamic-field--resize" field="field"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</document-preview>
</div>