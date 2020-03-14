<div ng-controller="BillingListCrtl as listVm" ng-cloak>
	<div class="content-block billing-list">
		<ng-include src="view.get('billing/filters.html')"></ng-include>
		<div class="top-icons">
			<div class="checkbox">
				<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
				<label for="print_all"></label>
			</div>
			<button class="btn btn-grey" ng-click="listVm.sendClaims()">Submit Claims</button>
		</div>

		<errors src="listVm.errors"></errors>

		<div show-loading-list="listVm.isShowLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th></th>
					<th sort="patient">Patient Name</th>
					<th sort="id">Account #</th>
					<th sort="dos">DOS</th>
					<th>Procedure</th>
					<th>Insurance(s)</th>
					<th ng-if="permissions.hasAccess('financial_documents', 'index')"></th>
					<th ng-if="permissions.hasAccess('billing', 'notes')"></th>
					<th>Status</th>
					<th>Submitted Claims</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td>
						<div class="checkbox">
							<input id="print_{{$index}}"
								   type="checkbox"
								   class="styled"
								   ng-checked="listVm.isAddedToSelected(item)"
								   ng-click="listVm.addToSelected(item)"
								   ng-disabled="!listVm.isItemStatusReady(item)">
							<label for="print_{{$index}}"></label>
						</div>
					</td>
					<td>{{ item.patient.last_name + ', ' + item.patient.first_name }}</td>
					<td>{{ ::item.id }}</td>
					<td>{{ ::item.dos | date:'M/d/yyyy' }}</td>
					<td>{{ ::item.procedure_name_for_dashboard }}</td>
					<td>{{::item.insurances}}</td>
					<td ng-if="permissions.hasAccess('financial_documents', 'index')" class="billing-financial-documents" ng-controller="CaseFinancialDocumentsCtrl as chartsVm" ng-init="chartsVm.init(item.id, item.financial_doc_count)">
						<a href="" ng-click="chartsVm.open(item.id)">
							<i ng-if="!chartsVm.docsCount" class="icon-cloud-upload-grey" uib-tooltip="Upload Files"></i>
							<i ng-if="chartsVm.docsCount" class="icon-cloud-upload-blue" uib-tooltip="Files Uploaded"></i>
						</a>
					</td>
					<td ng-if="permissions.hasAccess('billing', 'notes')" class="case-notes" ng-controller="BillingNoteCrtl as noteVm">
						<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(item.id)">
							<i ng-class="{'icon-note': !noteVm.billingNotes.hasFlaggedNotes(item, item.id), 'icon-notes-red': noteVm.billingNotes.hasFlaggedNotes(item, item.id)}"></i>
							<span class="badge" ng-if="noteVm.billingNotes.getNotesCount(item.id, item.notes_count)"
								  ng-class="{'blue': noteVm.billingNotes.hasUnreadNotes[item.id]}">
								{{ noteVm.billingNotes.getNotesCount(item.id, item.notes_count) }}
							</span>
						</a>
					</td>
					<td>
						<a href="" ng-if="!listVm.isItemStatusComplete(item) && !listVm.isItemStatusContinue(item) && !listVm.isItemStatusReady(item)" ng-href="/billings/{{::org_id}}/view/{{::item.id}}" target="_blank" class="btn status-button btn-success">
							Begin
						</a>
						<a href="" ng-if="!listVm.isItemStatusComplete(item) && listVm.isItemStatusContinue(item) && !listVm.isItemStatusReady(item)"  ng-href="/billings/{{::org_id}}/view/{{::item.id}}" target="_blank" class="btn status-button btn-primary">
							Continue
						</a>
						<a href="" ng-if="listVm.isItemStatusComplete(item)"  ng-href="/billings/{{::org_id}}/view/{{::item.id}}" target="_blank" class="btn status-button btn-primary">
							Submitted
						</a>
						<a href="" ng-if="listVm.isItemStatusReady(item) && !listVm.isItemStatusComplete(item)"  ng-href="/billings/{{::org_id}}/view/{{::item.id}}" target="_blank" class="btn status-button btn-primary">
							Ready
						</a>
					</td>
					<td class="submitted-claims">
						<div ng-if="listVm.isItemStatusComplete(item) && item.submitted_claims.length">
							<div ng-repeat="claim in item.submitted_claims track by $index">
								{{::claim}}
							</div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			       callback="listVm.search(true)"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
		</div>
	</div>
</div>