<div ng-controller="BillingCollectionsListCtrl as listVm" class="billing-collections" show-loading="listVm.isExportGenerating || listVm.isSaveBillingStatuses" ng-cloak>
	<div class="content-block">
		<ng-include src="view.get('billing/collections/filters.html')"></ng-include>
		<a href="" class="btn-print icon" ng-click="listVm.export()">
			<i class="icon-export-xls"></i>
		</a>
		<a class="btn btn-success pull-right" ng-click="listVm.saveBillingStatuses()" ng-disabled="!listVm.isChangedBillingStatuses()">Save</a>
        <div class="loading-wheel" ng-if="listVm.isLoading">
            <div class="loading-spinner"></div>
        </div>
    </div>

	<div show-loading-list="listVm.isLoading" class=" billing-collections-list">
		<div ng-show="listVm.isDataLoaded" class="table-wrap content-block">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr class="responsibility-label-row" ng-if="listVm.isResponsibilityShown">
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th ng-if="listVm.isPrimaryTypeShown"></th>
					<th ng-if="listVm.isPrimaryTypeShown"></th>
					<th ng-if="listVm.isPrimaryTypeShown"></th>
					<th ng-if="listVm.isPrimaryTypeShown"></th>
					<th></th>
					<th></th>
					<th></th>
					<th ng-if="listVm.isResponsibilityShown"></th>
					<th ng-if="listVm.isResponsibilityShown"></th>
					<th ng-if="listVm.isResponsibilityShown"></th>
					<th ng-if="listVm.isResponsibilityShown" class="responsibility-label-column" colspan="5">Responsibility</th>
					<th></th>
				</tr>
				<tr>
					<th class="mrn" sort="mrn">MRN</th>
					<th sort="patient_last_name">Last Name</th>
					<th sort="patient_first_name">First Name</th>
					<th sort="dos">DOS</th>
					<th class="case_id">
						Case #
						<a href="" ng-if="!listVm.isPrimaryTypeShown" ng-click="listVm.isPrimaryTypeShown= !listVm.isPrimaryTypeShown" class="icon"><span class="glyphicon glyphicon-triangle-right"></span></a>
					</th>

					<th ng-show="listVm.isPrimaryTypeShown" sort="primary_payer_type">Primary Payer Type</th>
					<th ng-show="listVm.isPrimaryTypeShown" sort="primary_payer_name">Primary Payer Name</th>
					<th ng-show="listVm.isPrimaryTypeShown" sort="secondary_payer_type">Secondary Payer Type</th>
					<th ng-show="listVm.isPrimaryTypeShown" sort="secondary_payer_name">
						Secondary Payer Name
						<a href="" ng-click="listVm.isPrimaryTypeShown = !listVm.isPrimaryTypeShown; ; $event.stopPropagation();"><i class="glyphicon glyphicon-triangle-left"></i></a>
					</th>

					<th>HCPCS/CPT</th>
					<th sort="provider">Provider</th>
					<th>
						<span class="balance-title" sort="balance">Balance</span>
						<a href="" ng-if="!listVm.isResponsibilityShown" ng-click="listVm.toggleDisplayResponsibility()" class="icon"><span class="glyphicon glyphicon-triangle-right"></span></a>
					</th>

					<th ng-show="listVm.isResponsibilityShown" sort="charges">Total Charges</th>
					<th ng-show="listVm.isResponsibilityShown" sort="payment">Total Payments</th>
					<th ng-show="listVm.isResponsibilityShown" sort="adjustments">Total Adjustments/Write-Offs</th>

					<th ng-if="listVm.isResponsibilityShown">Ins.</th>
					<th ng-if="listVm.isResponsibilityShown" class="co-pay">Co-Pay</th>
					<th ng-if="listVm.isResponsibilityShown">Co-Ins</th>
					<th ng-if="listVm.isResponsibilityShown">Deduct.</th>
					<th ng-if="listVm.isResponsibilityShown"><span>OOP</span> <a href="" ng-click="listVm.toggleDisplayResponsibility()"><i class="glyphicon glyphicon-triangle-left"></i></a></th>
					<th sort="billing_status">Billing Status</th>
					<th ng-if="permissions.hasAccess('billing', 'notes')" class="case-notes">Billing Notes</th>
				</tr>
				</thead>
				<tbody >
				<tr ng-repeat-start="item in listVm.items"  class="accordion-header">
					<td class="mrn" ng-click="item.isOpen = !item.isOpen">{{item.patient.full_mrn}} <i ng-class="{'icon-caret-down': item.isOpen, 'icon-caret-right': !item.isOpen}"></i></td>
					<td>{{item.patient.last_name}}</td>
					<td>{{item.patient.first_name}}</td>
					<td>{{item.time_start | date:'M/d/yyyy'}}</td>
					<td>{{item.case_id}}</td>
					<td ng-if="listVm.isPrimaryTypeShown">{{item.primary_payer_type}}</td>
					<td ng-if="listVm.isPrimaryTypeShown"><a href="" uib-tooltip-html="'Phone: {{item.primary_payer_phone | phone}}<br> Policy or ID number: {{item.primary_payer_policy_id}}'">{{item.primary_payer_name}}</a></td>
					<td ng-if="listVm.isPrimaryTypeShown">{{item.secondary_payer_type}}</td>
					<td ng-if="listVm.isPrimaryTypeShown"><a href="" uib-tooltip-html="'Phone: {{item.secondary_payer_phone | phone}}<br> Policy or ID number: {{item.secondary_payer_policy_id}}'">{{item.secondary_payer_name}}</a></td>
					<td></td>
					<td>{{item.surgeon}}</td>
					<td>{{item.balance}}</td>

					<td ng-if="listVm.isResponsibilityShown">{{item.charges}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{item.payments}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{item.sum_adjustment_writeoff}}</td>

					<td ng-if="listVm.isResponsibilityShown">{{ item.responsibility.insurance | currency:"$" }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ item.responsibility.coPay | currency:"$"}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ item.responsibility.coIns | currency:"$"}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ item.responsibility.deductible | currency:"$"}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ item.responsibility.oop | currency:"$"}}</td>
					<td class="billing-status">
						<div ng-if="permissions.user.isDoctor()">
							<a href="" uib-tooltip="{{billingConst.MANUAL_BILLING_STATUSES_DESC[item.billing_status]}}" tooltip-placement="top">{{billingConst.MANUAL_BILLING_STATUSES[item.billing_status]}}</a>
						</div>
						<div ng-if="!permissions.user.isDoctor()" class="billing-status--container">
							<opk-select class="small"
										ng-model="item.billing_status"
										select-options="{appendToBody: true, searchFilter: 'opkSelectBillingStatus'}"
										key-value-options="billingConst.MANUAL_BILLING_STATUSES"
										placeholder=""
										ng-change="listVm.changeBillingStatus(item)">
							</opk-select>
						</div>
					</td>
					<td ng-if="permissions.hasAccess('billing', 'notes')" class="case-notes" ng-controller="BillingNoteCrtl as noteVm">
						<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(item.case_id)">
							<i ng-class="{'icon-note': !noteVm.billingNotes.hasFlaggedNotes(item, item.case_id), 'icon-notes-red': noteVm.billingNotes.hasFlaggedNotes(item, item.case_id)}"></i>
							<span class="badge" ng-if="noteVm.billingNotes.getNotesCount(item.case_id, item.notes_count)"
								  ng-class="{'blue': noteVm.billingNotes.hasUnreadNotes[item.case_id]}">
									{{ noteVm.billingNotes.getNotesCount(item.case_id, item.notes_count) }}
								</span>
						</a>
					</td>
				</tr>
				<tr ng-repeat-end ng-repeat="procedure in item.procedures" ng-if="item.isOpen">
					<td colspan="5"></td>
					<td ng-if="listVm.isPrimaryTypeShown"></td>
					<td ng-if="listVm.isPrimaryTypeShown"></td>
					<td ng-if="listVm.isPrimaryTypeShown"></td>
					<td ng-if="listVm.isPrimaryTypeShown"></td>
					<td>{{procedure.code}}</td>
					<td></td>
					<td>{{procedure.balance}}</td>
					<td ng-if="listVm.isResponsibilityShown">{{procedure.charge }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{procedure.payment  }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{procedure.writeoff_adj }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ procedure.responsibility.insurance | currency:"$" }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ procedure.responsibility.coPay | currency:"$" }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ procedure.responsibility.coIns | currency:"$" }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ procedure.responsibility.deductible | currency:"$" }}</td>
					<td ng-if="listVm.isResponsibilityShown">{{ procedure.responsibility.oop | currency:"$" }}</td>
					<td></td>
					<td></td>
					<td ng-if="permissions.hasAccess('billing', 'notes')" class="case-notes"></td>
				</tr>
				</tbody>
			</table>
			<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
		</div>
	</div>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
		   callback="listVm.search()"></pages>
</div>