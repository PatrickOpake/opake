<div class="panel-data upload-forms individual-charts-settings-page" ng-controller="FormDocumentsCrtl as docsVm" ng-init="docsVm.init()" ng-cloak>
	<div>
		<errors src="docsVm.errors"></errors>
		<div class="upload-forms--docs" ng-repeat="segment in formDocumentsConst.SEGMENTS">
			<div class="upload-forms--title">
				<a href="" ng-click="docsVm.printSelectedCharts(segment)" class="icon print-button">
					<i class="icon-circle-print" uib-tooltip="Print"></i>
				</a>
				<h4><b>{{ ::segment.NAME }}</b></h4>
				<div class="loading-wheel" ng-if="docsVm.isShowLoading">
					<div class="loading-spinner"></div>
				</div>
			<span class="right-buttons">
				<a href="/settings/forms/charts/{{::org_id}}/view#?segment={{::segment.KEY}}" class="btn btn-success">Create Chart</a>
				<a href="" ng-click="docsVm.openUploadDialog(segment.KEY)" class="upload">Upload Chart</a>
			</span>
			</div>

			<div show-loading-list="docsVm.isShowLoading && docsVm.isInitLoading">
				<table ng-if="docsVm.documents" class="opake upload-forms--table">
					<thead>
					<tr>
						<th class="print-checkbox">
							<div class="checkbox">
								<input id="print_all_{{segment.NAME}}" type="checkbox" class="styled" ng-checked="docsVm.isPrintAllSelected(segment)" ng-click="docsVm.addAllToPrintQueue(segment)">
								<label for="print_all_{{segment.NAME}}"></label>
							</div>
						</th>
						<th class="icon"></th>
						<th class="form-name">Document Name</th>
						<th class="form-type">Type</th>
						<th class="form-sites">Sites</th>
						<th class="form-settings">Settings</th>
					</tr>
					</thead>
					<tbody>

					<tr ng-repeat="doc in docsVm.documents | filter: {segment: segment.NAME}">
						<td class="print-checkbox">
							<div class="checkbox">
								<input id="print_{{segment.NAME}}_{{$index}}"
								       type="checkbox"
								       class="styled"
								       ng-checked="docsVm.isAddedToPrintQueue(segment, doc)"
								       ng-click="docsVm.addChartToPrintQueue(segment, doc)">
								<label for="print_{{segment.NAME}}_{{$index}}"></label>
							</div>
						</td>
						<td class="icon">
							<a ng-click="formDocuments.downloadTemplate(doc, null)" href=""><i class="icon-pdf"></i></a>
						</td>
						<td class="form-name">
							<a ng-click="formDocuments.downloadTemplate(doc, null)" href="" uib-tooltip="{{doc.filename_for_export}}" tooltip-placement="bottom" tooltip-class="white">{{ doc.name }}</a>
						</td>
						<td class="form-type">{{ docsVm.getTypeName(doc) }}</td>
						<td class="form-sites">
							<span ng-if="docsVm.isAllSitesChecked(doc)">All</span>
							<span ng-if="docsVm.isNoOneSitesChecked(doc)">None</span>
					<span ng-if="!docsVm.isAllSitesChecked(doc) && !docsVm.isNoOneSitesChecked(doc)" ng-repeat="site in doc.sites">
						{{ site.name }} {{ $last ? '' : ', ' }}
					</span>
						</td>
						<td class="control form-settings">
					<span class="dropdown" uib-dropdown dropdown-append-to-body>
						<a href="#" class="upload-forms--control-link" id="simple-dropdown" uib-dropdown-toggle>
							<i class="icon-grey-gear"></i>
							<span class="glyphicon glyphicon-triangle-bottom"></span>
						</a>
						<ul class="dropdown-menu dropdown-menu-right form-settings-dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="simple-dropdown">
							<li role="menuitem"><a href="#" ng-click="docsVm.renameForm(doc)">Rename</a></li>
							<li role="menuitem"><a href="#" ng-click="docsVm.assignForm(doc)">Assign</a></li>
							<li ng-if="doc.uploaded_file_id" role="menuitem">
								<a href="/settings/forms/charts/{{::org_id}}/uploadedView/{{::doc.id}}">Edit</a>
							</li>
							<li ng-if="!doc.uploaded_file_id" role="menuitem">
								<a href="/settings/forms/charts/{{::org_id}}/view/{{::doc.id}}">Edit</a>
							</li>
							<li role="menuitem"><a href="#" ng-click="docsVm.deleteForm(doc)">Delete</a></li>
							<li role="menuitem"><a href="#" ng-click="docsVm.moveForm(doc)">Move</a></li>
							<li role="menuitem"><a href="#" ng-click="docsVm.reupload(doc)">Reupload</a></li>
						</ul>
					</span>
						</td>
					</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>

<script type="text/ng-template" id="forms/upload-form.html">
	<div class="charts-form-modal">
		<div class="modal-header">
			<h4 class="modal-title">Upload Chart</h4>
			<a href="" ng-click="cancel()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-form-upload" show-loading="docsVm.isUploadLoading">
			<div class="modal-body" ng-form name="uploadForm" novalidate>
				<errors src="docsVm.modalErrors"></errors>
				<div ng-if="!docsVm.form.uploadedFile" class="file-drop-box"
				     ngf-drop="docsVm.uploadFile($files)" ngf-drag-over-class="'drag-over'">
					<div class="file-drop-box--help">
						<i class="icon-file-upload-cloud"></i> <br/>
						<span class="bold-text">Drag and drop a file here</span> <br/>
						or <br/>
						<button class="btn btn-grey btn-file" select-file on-select="docsVm.filesChanged(files)"> Select File
							<input type="file" name="fileDoc" />
						</button>
					</div>
				</div>
				<div ng-if="docsVm.form.uploadedFile" class="data-row upload-file--filename">
					{{docsVm.form.uploadedFile.name}}
					<a ng-click="docsVm.removeUploadedFile()" href="" class="remove">
						<i class="glyphicon glyphicon-remove-circle"></i>
					</a>
				</div>
				<div class="upload-forms--upload-inputs" >
					<div class="data-row">
						<label>Name:</label>
						<input type="text" class="form-control input-sm" ng-model="docsVm.form.name" name="formName" ng-required="true">
					</div>
					<div class="data-row">
						<div class="checkbox" ng-if="docsVm.isPDF(docsVm.form.uploadedFile.name)">
							<input id="custom-header" type="checkbox" class="styled" name="include_header" ng-model="docsVm.form.include_header" />
							<label for="custom-header">Include custom header details</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-grey" ng-click="cancel()" type="button">Cancel</button>
				<button class="btn btn-success" ng-click="docsVm.clickUpload(uploadForm)" ng-disabled="uploadForm.$invalid || !docsVm.form.uploadedFile">Upload</button>
			</div>
		</div>
	</div>
</script>

<script type="text/ng-template" id="forms/rename-form.html">
	<div class="charts-form-modal" ng-form name="renameForm">
		<div class="modal-header">
			<h4 class="modal-title">Rename</h4>
			<a href="" ng-click="modalVm.close()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<errors src="docsVm.modalErrors"></errors>
			<div class="data-row">
				<span class="icon"><i class="icon-pdf"></i></span>
				<input type="text" class="form-control" ng-model="docsVm.form.name" ng-required="true">
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.rename()" ng-disabled="renameForm.$invalid">Rename</button>
			<button class="btn btn-grey" ng-click="modalVm.close()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="forms/assign-form.html">
	<div class="charts-form-modal">
		<div class="modal-header">
			<h4 class="modal-title">Assign</h4>
			<a href="" ng-click="cancel()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body form-document--assign-modal">
			<div class="row">
				<div class="col-sm-4">
					<label>Sites:</label>
				</div>
				<div class="col-sm-8">
					<opk-select ng-model="docsVm.form.sites"
					            multiple
					            options="site.name for site in source.getSites()"></opk-select>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<label>Chart Groups:</label>
				</div>
				<div class="col-sm-8">
					<opk-select ng-model="docsVm.form.chart_group_ids"
					            multiple
					            options="item.id as item.name for item in docsVm.allChartGroups"></opk-select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success text-center " ng-click="ok()">Save</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="forms/delete-form.html">
	<div class="charts-form-modal">
		<div class="modal-header">
			<h4 class="modal-title">Delete Document - {{docsVm.form.name}}</h4>
			<a href="" ng-click="cancel()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<b>Are you sure you would like to delete the document?</b>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="ok()">Delete</button>
			<button class="btn btn-grey" ng-click="cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="forms/move-form.html">
	<div class="charts-form-modal">
		<div class="modal-header">
			<h4 class="modal-title">Move Document</h4>
			<a href="" ng-click="cancel()" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
		<div class="modal-body">
			<div class="data-row">
				<label>Destination:</label>
				<opk-select ng-model="docsVm.form.segment"
				            options="item.KEY as item.NAME for item in formDocumentsConst.SEGMENTS"></opk-select>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="ok()">Move</button>
			<button class="btn btn-grey" ng-click="cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>