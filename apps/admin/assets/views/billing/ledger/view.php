<div ng-controller="BillingLedgerViewCtrl as ledgerVm" ng-init="ledgerVm.init(<?= $patientId ?>)" class="billing-ledger-page billing-ledger-view" show-loading="ledgerVm.isDocumentsLoading">

	<div class="panel-data">

		<div>
			<a href="" ng-href="/billings/ledger/{{::org_id}}/" class="back"><i class="glyphicon glyphicon-chevron-left"></i>Back</a>
			<div class="patient-info-header">
				<div class="row">
					<div class="col-sm-4">
						<div class="verification-header">
							<div ng-if="ledgerVm.ledger.patient.first_name" class="data-row case-notes">
								<label>Patient Name:</label>
								<span>{{ (ledgerVm.ledger.patient.first_name + ' ' + ledgerVm.ledger.patient.last_name) }}</span>
							</div>
							<div ng-if="ledgerVm.ledger.patient.age" class="data-row">
								<label>Age:</label>
								{{ ledgerVm.ledger.patient.age }}
							</div>
							<div ng-if="ledgerVm.ledger.patient.gender" class="data-row">
								<label>Sex:</label>
								{{ ledgerVm.ledger.patient.gender }}
							</div>
							<div ng-if="ledgerVm.ledger.patient.home_phone" class="data-row">
								<label>Phone:</label>
								{{ ledgerVm.ledger.patient.home_phone | phone }}
							</div>
							<div ng-if="ledgerVm.ledger.patient.dob" class="data-row">
								<label>Date of Birth:</label>
								<div>{{ ledgerVm.ledger.patient.dob }}</div>
							</div>
							<div ng-if="ledgerVm.ledger.patient.home_address" class="data-row">
								<label>Address:</label>
								<div>{{ ledgerVm.ledger.patient.home_address }}</div>
							</div>
							<div ng-if="(ledgerVm.ledger.patient.home_city && ledgerVm.ledger.patient.home_state)" class="data-row">
								<label>City, State:</label>
								<div>{{ ledgerVm.ledger.patient.home_city.name }}, {{ ledgerVm.ledger.patient.home_state.name }}</div>
							</div>
							<div ng-if="ledgerVm.ledger.patient.home_zip_code" class="data-row">
								<label>Zip:</label>
								<div>{{ ledgerVm.ledger.patient.home_zip_code }}</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div ng-if="ledgerVm.ledger.totals">
							<table class="opake total-amounts-table">
								<thead>
									<tr>
										<th colspan="2">Totals</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Billed Amount</td>
										<td>{{ledgerVm.ledger.totals.total_charges | money}}</td>
									</tr>
									<tr>
										<td>Insurance Payments</td>
										<td>{{ledgerVm.ledger.totals.insurance_payments | money}}</td>
									</tr>
									<tr>
										<td>Patient Payments</td>
										<td>{{ledgerVm.ledger.totals.patient_payments | money}}</td>
									</tr>
									<tr>
										<td>Adjustments</td>
										<td>{{ledgerVm.ledger.totals.adjustments | money}}</td>
									</tr>
									<tr>
										<td>Write-Offs</td>
										<td>{{ledgerVm.ledger.totals.write_offs | money}}</td>
									</tr>
									<tr>
										<td>Outstanding Balance</td>
										<td>{{ledgerVm.ledger.totals.outstanding_balance | money}}</td>
									</tr>
									<tr>
										<td>Patient Responsible Balance</td>
										<td>{{ledgerVm.ledger.totals.patient_responsible_balance | money}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="patient-statement-form-container">
							<div class="loading-wheel" ng-if="ledgerVm.isInitLoading">
								<div class="loading-spinner"></div>
							</div>
							<table class="opake patient-statement-form" ng-show="ledgerVm.cases.length">
								<thead>
								<tr>
									<th>Patient Statement</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>
										<div>
											<div class="statement-comment-title">Statement Comment</div>
											<div class="statement-comment-options">
												<opk-select ng-model="ledgerVm.statementForm.chosen_comment"
												            options="item.title for item in ledgerVm.ledger.statement_comment_options"></opk-select>
											</div>
											<div>
												<label>Write Custom Comment (limit 120 characters)</label>
												<textarea maxlength="120" ng-model="ledgerVm.statementForm.custom_comment"></textarea>
											</div>
											<div class="totals-butons">
												<button class="btn btn-grey" ng-click="ledgerVm.generatePatientStatement()">Generate Patient Statement</button>
											</div>
										</div>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="ledger-table-container" show-loading-list="ledgerVm.isInitLoading">
			<div class="list-control">
				<table class="new-payment-form" ng-show="ledgerVm.cases.length || ledgerVm.newPayment.bulk_payment">
					<tbody>
					<tr>
						<td class="date-of-payment-column">
							<label>Date of Payment</label>
							<date-field
								ng-model="ledgerVm.newPayment.date"
								ng-change="ledgerVm.dateChanged()"
								placeholder="mm/dd/yyyy"
								small="true"
								icon="true"></date-field>
						</td>
						<td class="payment-source-column">
							<label>Payment Source</label>
							<div ng-show="!ledgerVm.isInterestPaymentsManagement" >
								<opk-select class="small" ng-model="ledgerVm.newPayment.source"
								            ng-change="ledgerVm.paymentSourceChanged()"
								            options="item.title for item in ledgerVm.ledger.payment_sources"></opk-select>
							</div>
							<div ng-show="ledgerVm.isInterestPaymentsManagement">
								<div class="form-control input-sm">Interest Payment</div>
							</div>
						</td>
						<td class="payment-method-column"  ng-class="{'invisible-column': ledgerVm.isInterestPaymentsManagement}">
							<div>
								<label>Payment Method</label>
								<opk-select class="small" ng-model="ledgerVm.newPayment.method"
								            ng-change="ledgerVm.paymentMethodChanged()"
								            options="item.title for item in BillingConst.LEDGER_PAYMENT_METHOD_OPTIONS"></opk-select>
							</div>
						</td>
						<td class="payment-amount-column"  ng-class="{'invisible-column': ledgerVm.isInterestPaymentsManagement}">
							<div>
								<label>Payment Amount</label>
								<input type="text" ng-model="ledgerVm.newPayment.amount" valid-number type-number="float" digits-max-length="12" class='form-control input-sm' ng-change="ledgerVm.newPaymentAmountChanged()" />
							</div>
						</td>
						<td class="amount-remaining-column"  ng-class="{'invisible-column': ledgerVm.isInterestPaymentsManagement}">
							<div>
								<label>Amount Remaining</label>
								<div class="form-control input-sm">{{ledgerVm.newPayment.remaining_amount | money}}</div>
							</div>
						</td>
						<td ng-class="{'invisible-column': ledgerVm.isInterestPaymentsManagement}">
							<div>
								<label>Bulk Payment</label>
								<div class="checkbox">
									<input id="bulk-payment-checkbox" ng-disabled="ledgerVm.isBulkPaymentLocked" type="checkbox" ng-model="ledgerVm.newPayment.bulk_payment" />
									<label for="bulk-payment-checkbox"></label>
								</div>
							</div>
						</td>
						<td class="apply-payments-button-column">
							<div ng-if="!ledgerVm.isInterestPaymentsManagement">
								<button class="btn btn-success" ng-disabled="!ledgerVm.isReadyToApplyPayments()" ng-click="ledgerVm.applyPayments()">Apply Payments</button>
								<a href="" ng-click="ledgerVm.manageInterestPayments()" ng-if="ledgerVm.isEditingAllowed" class="manage-interest-payments-link">
									<span>Manage Interest Payments</span>
								</a>
							</div>
							<div ng-if="ledgerVm.isInterestPaymentsManagement">
								<button class="btn btn-success" ng-disabled="!ledgerVm.isReadyToApplyInterestPayments()" ng-click="ledgerVm.applyInterestPayments()">Apply Payments</button>
								<a href="" ng-click="ledgerVm.cancelManageInterestPayments()" class="manage-interest-payments-link">
									<span>Cancel Interest Payments</span>
								</a>
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td ng-class="{'invisible-column': ledgerVm.isInterestPaymentsManagement}">
							<span class="card-info" ng-if="ledgerVm.newPayment.authorization_number">
								Authorization Number: {{ledgerVm.newPayment.authorization_number}}
							</span>
							<span class="card-info" ng-if="ledgerVm.newPayment.check_number">
								Check Number: {{ledgerVm.newPayment.check_number}}
							</span>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
			</div>
			<errors src="ledgerVm.errors"></errors>

			<table class="opake payments-table">
				<thead>
				<tr class="responsibility-label-row" ng-show="ledgerVm.isResponsibilityShown">
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th ng-show="!ledgerVm.isDiagnosisShown"></th>
					<th ng-show="ledgerVm.isDiagnosisShown"></th>
					<th ng-show="ledgerVm.isDiagnosisShown"></th>
					<th ng-show="ledgerVm.isDiagnosisShown"></th>
					<th ng-show="ledgerVm.isDiagnosisShown"></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th ng-show="ledgerVm.isResponsibilityShown" class="responsibility-label-column" colspan="5">Responsibility</th>
					<th></th>
				</tr>
				<tr>
					<th></th>
					<th>DOS</th>
					<th class="acc-num-column">Acc. #</th>
					<th>Activity</th>
					<th>Code</th>
					<th class="diagnosis-column" ng-show="!ledgerVm.isDiagnosisShown"><span>Dx</span><a href="" ng-click="ledgerVm.toggleDisplayDiagnosis()"><i class="glyphicon glyphicon-triangle-right"></i></a></th>
					<th class="diagnosis-column" ng-show="ledgerVm.isDiagnosisShown">Dx1</th>
					<th class="diagnosis-column" ng-show="ledgerVm.isDiagnosisShown">Dx2</th>
					<th class="diagnosis-column" ng-show="ledgerVm.isDiagnosisShown">Dx3</th>
					<th class="diagnosis-column" ng-show="ledgerVm.isDiagnosisShown"><span>Dx4</span><a href="" ng-click="ledgerVm.toggleDisplayDiagnosis()"><i class="glyphicon glyphicon-triangle-left"></i></a></th>
					<th>Charges</th>
					<th>Source</th>
					<th>Method</th>
					<th>Insurance</th>
					<th>Patient</th>
					<th>Adj.</th>
					<th>Writeoff</th>
					<th class="balance-column"><span>Balance</span> <a href="" ng-if="!ledgerVm.isResponsibilityShown" ng-click="ledgerVm.toggleDisplayResponsibility()"><i class="glyphicon glyphicon-triangle-right"></i></a></th>
					<th ng-show="ledgerVm.isResponsibilityShown">Ins.</th>
					<th ng-show="ledgerVm.isResponsibilityShown">Co-Pay</th>
					<th ng-show="ledgerVm.isResponsibilityShown">Co-Ins</th>
					<th ng-show="ledgerVm.isResponsibilityShown">Deduct.</th>
					<th class="oop-column" ng-if="ledgerVm.isResponsibilityShown"><span>OOP</span> <a href="" ng-click="ledgerVm.toggleDisplayResponsibility()"><i class="glyphicon glyphicon-triangle-left"></i></a></th>
					<th></th>
				</tr>
				</thead>
				<tbody ng-repeat="case in ledgerVm.cases">
					<tr class="case-row">
						<td>
							<a ng-click="ledgerVm.toggleCollapse(case)"><i class="glyphicon" ng-class="{'glyphicon-triangle-top': !case._isCollapsed, 'glyphicon-triangle-bottom': case._isCollapsed}"></i></a>
						</td>
						<td ng-bind="::case.dos"></td>
						<td>
							<a ng-href="/cases/{{::org_id}}/cm/{{::case.id}}" target="_blank" ng-bind="::case.id"></a>
						</td>
						<td ng-bind="case.dos"></td>
						<td></td>
						<td ng-show="!ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-bind="case.totals.charges|money"></td>
						<td></td>
						<td></td>
						<td ng-bind="case.totals.insurancePayments|money"></td>
						<td ng-bind="case.totals.patientPayments|money"></td>
						<td ng-bind="case.totals.adjustments|money"></td>
						<td ng-bind="case.totals.writeOffs|money"></td>
						<td>
							<a href="/billings/{{::org_id}}/view/{{::case.id}}" target="_blank" ng-bind="case.totals.balance|money"></a>
						</td>
						<td ng-if="ledgerVm.isResponsibilityShown">
							<a href="" class="" ng-if="::(!case.is_self_pay_insurance)" ng-click="ledgerVm.forceAssign({case: case, assignToInsurance: true})" uib-tooltip="Click to assign balance of {{(case.totals.balance|money)}} to Insurance"
							   ng-bind="case.responsibilityAmounts.insurance|money">
							</a>
							<span ng-if="::(case.is_self_pay_insurance)" ng-bind="case.responsibilityAmounts.insurance|money"></span>
						</td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="case.responsibilityAmounts.coPay|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="case.responsibilityAmounts.coIns|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="case.responsibilityAmounts.deductible|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<a href="" class="" ng-if="::(!case.is_self_pay_insurance)" ng-click="ledgerVm.forceAssign({case: case, assignToPatient: true})" uib-tooltip="Click to assign balance of {{(case.totals.balance|money)}} to Patient"
							   ng-bind="case.responsibilityAmounts.oop|money">
							</a>
							<span ng-if="::(case.is_self_pay_insurance)" ng-bind="case.responsibilityAmounts.oop|money"></span>
						</td>
						<td>
							<div ng-if="!ledgerVm.isInterestPaymentsManagement">
								<span ng-if="permissions.hasAccess('financial_documents', 'index')" class="billing-financial-documents" ng-controller="CaseFinancialDocumentsCtrl as chartsVm" ng-init="chartsVm.init(case.id, case.financial_doc_count)">
									<a href="" ng-click="chartsVm.open()">
										<i ng-if="!chartsVm.docsCount" class="icon-cloud-upload-grey" uib-tooltip="Upload Files"></i>
										<i ng-if="chartsVm.docsCount" class="icon-cloud-upload-blue" uib-tooltip="Files Uploaded"></i>
									</a>
								</span>
								<span ng-if="permissions.hasAccess('billing', 'notes')" class="case-notes" ng-controller="BillingNoteCrtl as noteVm">
									<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(case.id)">
										<i ng-class="{'icon-note': !noteVm.billingNotes.hasFlaggedNotes(case, case.id), 'icon-notes-red': noteVm.billingNotes.hasFlaggedNotes(case, case.id)}"></i>
										<span class="badge" ng-if="noteVm.billingNotes.getNotesCount(case.id, case.notes_count)"
										      ng-class="{'blue': noteVm.billingNotes.hasUnreadNotes[case.id]}"
										      ng-bind="noteVm.billingNotes.getNotesCount(case.id, case.notes_count)">
										</span>
									</a>
								</span>
							</div>
							<div ng-if="ledgerVm.isInterestPaymentsManagement">
								<button class="btn btn-sm btn-grey" ng-click="ledgerVm.addNewInterestChargePayment(case)">Add Payment</button>
							</div>
						</td>
					</tr>
					<tr ng-show="!case._isCollapsed" class="procedure-row" ng-repeat="interest in case.interests">
						<td></td>
						<td></td>
						<td></td>
						<td ng-bind="interest.date"></td>
						<td>INT</td>
						<td ng-show="!ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-bind="interest.amount|money"></td>
						<td>Interest Payment</td>
						<td></td>
						<td>
							<span ng-if="!interest._isEdit && !interest._isNew" ng-bind="interest.amount|money"></span>
							<span ng-if="interest._isEdit || interest._isNew" class="payment-amount-input-container">
								$<input type="text" class="form-control input-sm amount-input" ng-model="interest.amount" valid-number type-number="float" digits-max-length="12" />
							</span>
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td>
							<div ng-if="ledgerVm.isInterestPaymentsManagement">
								<div ng-if="interest._isNew">
									<button class="btn btn-sm btn-grey" ng-click="ledgerVm.cancelNewInterestPayment(interest)">Cancel</button>
								</div>
								<div ng-if="interest._isEdit" class="interest-edit-buttons">
									<button class="btn btn-sm btn-primary" ng-click="ledgerVm.saveEditInterestPayment(interest)">Save</button>
									<button class="btn btn-sm btn-grey" ng-click="ledgerVm.cancelEditInterestPayment(interest)">Cancel</button>
								</div>
								<div ng-if="!interest._isNew && !interest._isEdit" class="interest-entry-links">
									<a href="" ng-click="ledgerVm.editInterestPayment(interest)">Edit</a>
									<a href="" ng-click="ledgerVm.removeInterestPayment(interest)" class="remove-link">Remove</a>
								</div>
							</div>
						</td>
					</tr>
					<tr ng-show="!case._isCollapsed" class="procedure-row" ng-repeat-start="procedure in case.procedures" opk-log="procedure.payments">
						<td></td>
						<td></td>
						<td></td>
						<td ng-bind="::case.dos"></td>
						<td ng-bind="::procedure.code"></td>
						<td ng-show="!ledgerVm.isDiagnosisShown" ng-bind="::procedure.dx1"></td>
						<td ng-show="ledgerVm.isDiagnosisShown" ng-bind="::procedure.dx1"></td>
						<td ng-show="ledgerVm.isDiagnosisShown" ng-bind="::procedure.dx2"></td>
						<td ng-show="ledgerVm.isDiagnosisShown" ng-bind="::procedure.dx3"></td>
						<td ng-show="ledgerVm.isDiagnosisShown" ng-bind="::procedure.dx4"></td>
						<td ng-bind="procedure.amount|money"></td>
						<td></td>
						<td></td>
						<td ng-bind="procedure.totals.insurancePayments|money"></td>
						<td ng-bind="procedure.totals.patientPayments|money"></td>
						<td ng-bind="procedure.totals.adjustments|money"></td>
						<td ng-bind="procedure.totals.writeOffs|money"></td>
						<td ng-bind="procedure.totals.balance|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<a href="" class="" ng-if="::(!case.is_self_pay_insurance)" ng-click="ledgerVm.forceAssign({procedure: procedure, assignToInsurance: true})" uib-tooltip="Click to assign balance of {{(procedure.totals.balance|money)}} to Insurance"
								ng-bind="procedure.responsibilityAmounts.insurance|money">
							</a>
							<span ng-if="::(case.is_self_pay_insurance)" ng-bind="procedure.responsibilityAmounts.insurance|money"></span>
						</td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="procedure.responsibilityAmounts.coPay|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="procedure.responsibilityAmounts.coIns|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown" ng-bind="procedure.responsibilityAmounts.deductible|money"></td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<a href="" class="" ng-if="::(!case.is_self_pay_insurance)" ng-click="ledgerVm.forceAssign({procedure: procedure, assignToPatient: true})" uib-tooltip="Click to assign balance of {{(procedure.totals.balance|money)}} to Patient"
							   ng-bind="procedure.responsibilityAmounts.oop|money">
							</a>
							<span ng-if="::(case.is_self_pay_insurance)" ng-bind="procedure.responsibilityAmounts.oop|money"></span>
						</td>
						<td>
							<div ng-if="!ledgerVm.isInterestPaymentsManagement">
								<button class="btn btn-sm btn-grey" ng-click="ledgerVm.addNewPayment(procedure)" ng-disabled="!ledgerVm.isReadyToAddPayment()">Add Payment</button>
							</div>
						</td>
					</tr>
					<tr ng-show="!case._isCollapsed" ng-repeat-end ng-repeat="payment in procedure.payments">
						<td></td>
						<td></td>
						<td></td>
						<td ng-bind="payment.date"></td>
						<td></td>
						<td ng-show="!ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td ng-show="ledgerVm.isDiagnosisShown"></td>
						<td></td>
						<td>
							<div ng-if="payment._isEdit">
								<opk-select ng-model="payment.payment_source"
								            options="item.title for item in ledgerVm.ledger.payment_sources"></opk-select>
							</div>
							<span ng-if="!payment._isEdit" ng-bind="payment.payment_source_title"></span>
						</td>
						<td>
							<div ng-if="payment._isEdit">
								<opk-select ng-model="payment.payment_method"
								            ng-change="ledgerVm.paymentMethodEditChanged(payment)"
								            options="item.title for item in BillingConst.LEDGER_PAYMENT_METHOD_OPTIONS"></opk-select>
							</div>
							<span ng-if="!payment._isEdit" ng-bind="payment.payment_method.title"></span>
						</td>
						<td>
							<span ng-if="payment._isInsurancePayment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.amount" ng-change="ledgerVm.paymentAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td>
							<span ng-if="payment._isPatientPayment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.amount" ng-change="ledgerVm.paymentAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td>
							<span ng-if="payment._isAdjustment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.amount" ng-change="ledgerVm.paymentAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td>
							<span ng-if="payment._isWriteOff">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.amount" ng-change="ledgerVm.paymentAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td></td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<span ng-if="payment._isInsurancePayment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.resp_co_pay_amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="resp-payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.resp_co_pay_amount" ng-change="ledgerVm.paymentResponsibilityAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<span ng-if="payment._isInsurancePayment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.resp_co_ins_amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="resp-payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.resp_co_ins_amount" ng-change="ledgerVm.paymentResponsibilityAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td ng-show="ledgerVm.isResponsibilityShown">
							<span ng-if="payment._isInsurancePayment">
								<span ng-if="!payment._isEdit && !payment._isNew" ng-bind="payment.resp_deduct_amount|money"></span>
								<span ng-if="payment._isEdit || payment._isNew" class="resp-payment-amount-input-container">
									$<input type="text" class="form-control input-sm amount-input" ng-model="payment.resp_deduct_amount" ng-change="ledgerVm.paymentResponsibilityAmountChanged(payment)" valid-number type-number="float" digits-max-length="12" />
								</span>
							</span>
						</td>
						<td ng-show="ledgerVm.isResponsibilityShown"></td>
						<td>
							<div class="payment-buttons" ng-if="!ledgerVm.isInterestPaymentsManagement">
								<div ng-if="payment._isNew">
									<button class="btn btn-sm btn-grey" ng-click="ledgerVm.cancelNewPayment(payment)">Cancel</button>
								</div>
								<div ng-if="!payment._isNew">
									<div ng-if="payment._isEdit">
										<button class="btn btn-sm btn-primary" ng-click="ledgerVm.saveEditPayment(payment)">Save</button>
										<button class="btn btn-sm btn-grey" ng-click="ledgerVm.cancelEditPayment(payment)">Cancel</button>
									</div>
									<div class="edit-links" ng-if="!payment._isEdit && ledgerVm.isEditingAllowed">
										<span><a href="" ng-click="ledgerVm.editPayment(payment)">Edit</a></span>
										<span><a href="" class="remove-link" ng-click="ledgerVm.deletePaymentDlg(payment)">Delete</a></span>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tbody>
				<tbody ng-if="!ledgerVm.cases.length">
					<tr>
						<td colspan="19">No billings for this patient</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

</div>


<script type="text/ng-template" id="billing/ledger/check-number.html">
	<div class="billing-ledger-modal">
		<div class="modal-body">
			<div class="field-row">
				<label>Check Number:</label>
				<input type="text" ng-model="modalVm.number" class='form-control input-sm' />
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.ok()">Save</button>
			<button class="btn btn-grey" ng-click="modalVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="billing/ledger/authorization-number.html">
	<div class="billing-ledger-modal">
		<div class="modal-body">
			<div class="field-row">
				<label>Authorization Number:</label>
				<input type="text" ng-model="modalVm.number" class='form-control input-sm' />
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.ok()">Save</button>
			<button class="btn btn-grey" ng-click="modalVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="billing/ledger/force-assign-confirmation.html">
	<div class="billing-ledger-modal force-assign-confirmation-modal">
		<div class="modal-body">
			You are about to assign the balance of {{::(modalVm.amount|money)}} to <span ng-if="::modalVm.isPatient">Patient</span><span ng-if="::modalVm.isInsurance">Insurance</span>. Would you like to proceed?
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.ok()">Confirm</button>
			<button class="btn btn-grey" ng-click="modalVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>

<script type="text/ng-template" id="billing/ledger/remove-interest-confirmation.html">
	<div class="billing-ledger-modal force-assign-confirmation-modal">
		<div class="modal-body">
			Are you sure you want to remove this Interest Payment?
		</div>
		<div class="modal-footer">
			<button class="btn btn-success" ng-click="modalVm.ok()">Confirm</button>
			<button class="btn btn-grey" ng-click="modalVm.cancel()" type="button">Cancel</button>
		</div>
	</div>
</script>


<script type="text/ng-template" id="billing/ledger/select-next-patient.html">
	<div>
		<div class="alert alert-success" ng-show="modalVm.isShowPaymentAlert">
			The payment has been successfully applied
		</div>
		<div class="select-next-patient-table-conatiner">
			<div class="title">Select the Next Patient</div>
			<div ng-controller="BillingLedgerBulkPatientSearchCtrl as listVm" ng-init="listVm.init(modalVm.patientId,insurance=ledgerVm.newPayment.source)">
				<filters-panel ctrl="listVm">
					<div class="data-row">
						<label>Patient Name</label>

						<div class="group-field">
							<div><input type="text" ng-model="listVm.search_params.last_name" class='form-control input-sm'
							            placeholder='Last Name'/></div>
							<div><input type="text" ng-model="listVm.search_params.first_name" class='form-control input-sm'
							            placeholder='First Name'/></div>
						</div>
					</div>
					<div class="data-row">
						<label>DOB</label>
						<div>
							<date-field ng-model="listVm.search_params.dob" without-calendar="true" placeholder="mm/dd/yyyy"
							            small="true"></date-field>
						</div>
					</div>
					<div class="data-row">
						<label>MRN</label>
						<div><input type="text" ng-model="listVm.search_params.mrn"  class='form-control input-sm'
						            placeholder='#####-##'/></div>
					</div>
				</filters-panel>
				<div show-loading-list="listVm.isInitLoading">
					<table class="opake patient-table">
						<thead>
						<tr>
							<th>Patient Name</th>
							<th>DOB</th>
							<th>MRN</th>
						</tr>
						</thead>
						<tbody>
						<tr ng-repeat="item in listVm.items">
							<td class="link"><a href="" ng-click="modalVm.selectNewPatient(item.id)">{{ ::item.name }}</a></td>
							<td class="link"><a href="" ng-click="modalVm.selectNewPatient(item.id)">{{ ::item.dob }}</a></td>
							<td class="link"><a href="" ng-click="modalVm.selectNewPatient(item.id)">{{ ::item.mrn }}</a></td>
						</tr>
						</tbody>
					</table>
					<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
					       callback="listVm.search()"></pages>
					<h4 ng-if="!listVm.items.length">Patients not found</h4>
					<div class="buttons">
						<button class="btn btn-grey" ng-click="modalVm.dismiss()">Back to the Ledger</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>