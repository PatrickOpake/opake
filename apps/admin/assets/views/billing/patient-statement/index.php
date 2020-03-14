<div ng-controller="PatientStatementListCtrl as listVm" class="billing-patient-statement--page" show-loading-list="listVm.isDocumentsLoading" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm" class="patient-statement--filter">
			<div class="data-row">
				<label>Last Name</label>
				<input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>First Name</label>
				<input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm' placeholder='Type' />
			</div>
			<div class="data-row">
				<label>Original DOS From</label>
				<div>
					<date-field ng-model="listVm.search_params.original_dos_from" without-calendar="true" placeholder="mm/dd/yyyy"
								small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>Original DOS To</label>
				<div>
					<date-field ng-model="listVm.search_params.original_dos_to" without-calendar="true" placeholder="mm/dd/yyyy"
					            small="true"></date-field>
				</div>
			</div>
			<div class="data-row">
				<label>MRN</label>
				<input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm'
							placeholder='#####-##'/>
			</div>
		</filters-panel>
	</div>

	<div class="content-block" show-loading-list="listVm.isInitLoading">
		<div class="list-control">
			<a href="" class="icon" ng-click="listVm.generateMultipleStatements()" ng-disabled="!listVm.toSelected.length">
				<i class="icon-print-grey" uib-tooltip="Print"></i>
			</a>
		</div>
		<table class="opake">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th>
					<div class="checkbox">
						<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
						<label for="print_all"></label>
					</div>
				</th>
				<th sort="last_name">Last Name</th>
				<th sort="first_name">First Name</th>
				<th sort="mrn">MRN</th>
				<th sort="original_dos">Original Date of Service</th>
				<th sort="outstanding_balance">Outstanding Balance</th>
				<th sort="patient_responsibility_balance">Outstanding Patient Responsible Balance</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td>
					<div>
						<div class="checkbox">
							<input id="print_{{$index}}"
								   type="checkbox"
								   class="styled"
								   ng-checked="listVm.isAddedToSelected(item.id)"
								   ng-click="listVm.addToSelected(item.id)">
							<label for="print_{{$index}}"></label>
						</div>
					</div>
				</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.last_name}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.first_name}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.mrn}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::(item.original_dos | date:'M/d/yyyy')}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.outstanding_balance}}</td>
				<td class="link" ng-click="listVm.openModalComment(item)">{{::item.outstanding_patient_responsible_balance}}</td>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 class="text-center" ng-if="!listVm.items.length">Items not found</h4>
	</div>

	<script type="text/ng-template" id="billing/patient-statement/add-comment.html">
		<div class="modal-body patient-statement-form" show-loading="listVm.isPrintWithCommentLoading">
			<a href="" ng-click="cancel()"><i class="glyphicon glyphicon-remove pull-right"></i></a>
			<div>
				<div>
					<label>Choose Comment</label>
					<opk-select class="small" ng-model="listVm.statementForm.chosen_comment"
								options="item.title for item in listVm.statement_comment_options"></opk-select>
				</div>
				<div>
					<label>Write Custom Comment (limit 120 characters)</label>
					<textarea maxlength="120" ng-model="listVm.statementForm.custom_comment"></textarea>
				</div>
				<div class="totals-buttons">
					<button class="btn btn-grey" ng-click="listVm.generateIndividualStatement()">Generate Patient Statement</button>
				</div>
			</div>
		</div>
		<div class="modal-footer"></div>
	</script>
</div>