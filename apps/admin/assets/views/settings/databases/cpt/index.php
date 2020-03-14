<div ng-controller="SettingsCPTYearsListCtrl as listVm" class="content-block" ng-cloak>
	<div class="list-control">
		<a class='btn btn-success btn-file' href='' ng-click="listVm.openUploadDocumentModal()">
			Upload
		</a>
	</div>
	<table class="opake">
		<thead>
		<tr>
			<th>CPT Year</th>
			<th>Notes</th>
		</tr>
		</thead>
		<tbody>
		<tr ng-repeat="item in listVm.items">
			<td><a href="/settings/databases/cpt/viewYear/{{::item.id}}">{{::item.year}}</a></td>
			<td>{{::item.note}}</td>
		</tr>
		</tbody>
	</table>
	<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
</div>