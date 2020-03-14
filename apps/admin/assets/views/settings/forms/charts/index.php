<div class="panel-data upload-forms" ng-controller="FormDocumentsCrtl as docsVm" ng-init="docsVm.init()" ng-cloak>
	<errors src="docsVm.errors"></errors>
	<div class="upload-forms--docs user" ng-repeat="(key, segment) in docsVm.docsSegments">
		<div class="upload-forms--title">
			<h4><b>{{ ::segment.NAME }}</b></h4> <a href="" ng-click="docsVm.printAllFiles(key)">Print All</a>
		</div>
		<table ng-if="docsVm.documents" class="upload-forms--table">
			<thead>
				<tr>
					<th></th>
					<th>Document Name</th>
					<th>Sites</th>
					<th>Settings</th>
				</tr>
			</thead>
			<tr ng-repeat="doc in docsVm.documents | filter: {segment: segment.NAME}">
				<td class="icon"><i class="icon-pdf"></i></td>
				<td>
					<a href="" ng-click="formDocuments.downloadTemplate(doc, null)" uib-tooltip="{{doc.file.original_filename}}" tooltip-placement="bottom" tooltip-class="white">{{ doc.name }}</a>
				</td>
				<td class="control">
					<a href="#" print-iframe="{{docsVm.getPrintFileUrl(doc.url)}}" uib-tooltip="Print"><i class="icon-circle-print"></i></a>
				</td>
				<td>
					<span ng-repeat="type in doc.case_types">{{ type.full_name }}{{ !$last ? ', ' : '' }}</span>
				</td>
				<td>
					<span ng-repeat="site in document.sites">{{ site.name }} {{ $last ? '' : ', ' }}</span>
				</td>
			</tr>
		</table>
	</div>
</div>