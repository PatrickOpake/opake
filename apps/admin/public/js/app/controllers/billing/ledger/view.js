// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingLedgerViewCtrl', [
		'$scope',
		'$http',
		'$q',
		'$controller',
		'View',
		'Tools',
		'BillingConst',
		'PatientConst',
		'BillingPaymentPostingNote',
		function ($scope, $http, $q, $controller, View, Tools, BillingConst, PatientConst, BillingPaymentPostingNote) {

			$scope.BillingConst = BillingConst;
			$scope.PatientConst = PatientConst;

			var vm = this;
			vm.patientId = null;
			vm.initOptions = {};
			vm.ledger = null;
			vm.cases = null;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.newPayment = {};
			vm.statementForm = {};
			vm.bulkPreviousPaymentsAmount = 0;
			vm.isBulkPaymentLocked = false;
			vm.isEditingAllowed = true;
			vm.isDiagnosisShown = false;
			vm.isResponsibilityShown = false;
			vm.isInterestPaymentsManagement = false;
			vm.isInitDone = false;


			vm.errors = null;

			vm.init = function (patientId, options) {

				vm.isShowLoading = true;

				var def = $q.defer();

				options = options || {};

				vm.initOptions = options;

				vm.errors = null;
				vm.patientId = patientId;

				if (!options.dontClearPayment) {
					vm.newPayment = {
						date: new Date(),
						bulk_payment: false
					};
				}


				var ledgerDef = $q.defer();
				var patientCasesDef = $q.defer();

				$http.get('/billings/ledger/ajax/' + $scope.org_id + '/ledger/' + patientId).then(function (response) {
					if (response.data.success) {
						vm.ledger = response.data.ledger;
						ledgerDef.resolve();
					}
				});

				$http.get('/billings/ledger/ajax/' + $scope.org_id + '/patientCases/' + vm.patientId).then(function (response) {
					if (response.data.success) {
						vm.cases = response.data.cases;
						angular.forEach(vm.cases, function(caseItem) {
							if (!caseItem.interests) {
								caseItem.interests = [];
							}
						});
						patientCasesDef.resolve();
					}
				});

				$q.all([ledgerDef.promise, patientCasesDef.promise]).then(function() {
					refreshTableRows();
					if (vm.initOptions.dontClearPayment) {
						recalculateRemainingAmount();
					}

					vm.isShowLoading = false;
					vm.isInitLoading = false;

					vm.isEditingAllowed = true;
					vm.isInitDone = true;

					def.resolve();
				});


				return def.promise;
			};

			vm.search = function() {

				var def = $q.defer();

				return defer.promise;

			};

			vm.toggleCollapse = function(caseItem) {
				caseItem._isCollapsed = !caseItem._isCollapsed;
			};

			vm.toggleDisplayDiagnosis = function() {
				vm.isDiagnosisShown = !vm.isDiagnosisShown;
			};

			vm.toggleDisplayResponsibility = function() {
				vm.isResponsibilityShown = !vm.isResponsibilityShown;
			};

			vm.paymentMethodChanged = function() {
				var newPayment = vm.newPayment;
				if (newPayment.method) {
					//Check
					if (newPayment.method.id == BillingConst.LEDGER_PAYMENT_METHODS.CHECK) {
						$scope.dialog(
							'billing/ledger/check-number.html',
							$scope, {
								size: 'md',
								backdrop: 'static',
								controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

									var vm = this;

									newPayment.authorization_number = null;
									vm.number = newPayment.check_number;

									vm.ok = function() {
										newPayment.check_number = vm.number;
										$uibModalInstance.close();
									};

									vm.cancel = function() {
										$uibModalInstance.close();
									};

								}],
								controllerAs: 'modalVm'
							});
						//Card
					} else if (newPayment.method.id == BillingConst.LEDGER_PAYMENT_METHODS.CREDIT_CARD) {
						$scope.dialog(
							'billing/ledger/authorization-number.html',
							$scope, {
								size: 'md',
								backdrop: 'static',
								controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

									var vm = this;

									newPayment.check_number = null;
									vm.number = newPayment.authorization_number;

									vm.ok = function() {
										newPayment.authorization_number = vm.number;
										$uibModalInstance.close();
									};

									vm.cancel = function() {
										$uibModalInstance.close();
									};

								}],
								controllerAs: 'modalVm'
							});
					} else {
						newPayment.check_number = null;
						newPayment.authorization_number = null;
					}

					if (vm.cases) {
						var isPaymentsChanged = false;
						angular.forEach(vm.cases, function(caseItem) {
							angular.forEach(caseItem.procedures, function(procedure) {
								angular.forEach(procedure.payments, function(payment) {
									// Don't change the payment method for additional payment detials
									if (payment._isNew && !payment.is_additional_payment) {
										isPaymentsChanged = true;
										payment.payment_method = vm.newPayment.method;
									}
								});
							});
						});
					}
				}
			};

			vm.newPaymentAmountChanged = function() {
				recalculateRemainingAmount();
			};

			vm.paymentAmountChanged = function() {
				recalculateRemainingAmount();
			};

			vm.paymentResponsibilityAmountChanged = function() {

			};

			vm.dateChanged = function() {
				if (vm.cases) {
					var isPaymentsChanged = false;
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								if (payment._isNew) {
									isPaymentsChanged = true;
									payment.date = moment(vm.newPayment.date).format('M/D/YYYY');
								}
							});
						});
						angular.forEach(caseItem.interests, function(interest) {
							if (interest._isNew) {
								isPaymentsChanged = true;
								interest.date = moment(vm.newPayment.date).format('M/D/YYYY');
							}
						});
					});
				}
			};

			vm.paymentSourceChanged = function() {
				if (vm.cases) {
					var isPaymentsChanged = false;
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								// Don't change payment source for additional payment details
								if (payment._isNew && !payment.is_additional_payment) {
									isPaymentsChanged = true;
									payment.payment_source = vm.newPayment.source;
								}
							});
						});
					});
				}
			};

			vm.isReadyToApplyPayments = function() {
				if (vm.newPayment.method && vm.newPayment.source && vm.newPayment.date) {
					if (vm.newPayment.amount === '' || vm.newPayment.remaining_amount === '') {
						return false;
					}

					var enteredAmount = parseFloat(vm.newPayment.amount);
					var remainingAmount = parseFloat(vm.newPayment.remaining_amount);

					// it's allowed to apply payments when user has a positive remaining amount
					// when the bulk payment checkbox is selected
					if (vm.newPayment.bulk_payment) {
						if (enteredAmount > 0 && remainingAmount >= 0) {
							if (isAllAddedPaymentsPositive()) {
								return true;
							}
						}
						return false;
					}

					return !!(enteredAmount > 0 && remainingAmount == 0);
				}

				return false;
			};

			vm.isReadyToAddPayment = function() {
				if (vm.newPayment.method && vm.newPayment.source && vm.newPayment.date) {
					if (vm.newPayment.amount === '' || vm.newPayment.remaining_amount === '') {
						return false;
					}

					var enteredAmount = parseFloat(vm.newPayment.amount);
					var remainingAmount = parseFloat(vm.newPayment.remaining_amount);

					return (enteredAmount > 0 && remainingAmount > 0);
				}

				return false;
			};

			vm.addNewPayment = function(procedure) {
				if (vm.isReadyToAddPayment()) {
					vm.isEditingAllowed = false;
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								if (payment._isEdit) {
									payment._isEdit = false;
									payment.amount = payment.originalAmount;
								}
							});
						});
					});

					var newPayment = {
						_isNew: true,
						date: moment(vm.newPayment.date).format('M/D/YYYY'),
						payment_source: vm.newPayment.source,
						payment_method: vm.newPayment.method,
						amount: 0.00,
						resp_co_pay_amount: null,
						resp_co_ins_amount: null,
						resp_deduct_amount: null
					};

					procedure.payments.push(newPayment);

					if (vm.newPayment.source.id == BillingConst.LEDGER_PAYMENT_SOURCES.INSURANCE) {

						procedure.payments.push({
							_isNew: true,
							date: moment(vm.newPayment.date).format('M/D/YYYY'),
							payment_source: {
								id: BillingConst.LEDGER_PAYMENT_SOURCES.ADJUSTMENT,
								title: 'Adjustment'
							},
							payment_method: {
								id: null,
								title: ''
							},
							amount: 0.00,
							is_additional_payment: true,
							parent_payment: newPayment
						});

						procedure.payments.push({
							_isNew: true,
							date: moment(vm.newPayment.date).format('M/D/YYYY'),
							payment_source: {
								id: BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF,
								title: 'Write-Off'
							},
							payment_method: {
								id: null,
								title: ''
							},
							amount: 0.00,
							is_additional_payment: true,
							parent_payment: newPayment
						});

					}

					refreshTableRows();
				}
			};

			vm.cancelNewPayment = function(cancelledPayment) {
				angular.forEach(vm.cases, function(caseItem) {
					angular.forEach(caseItem.procedures, function(procedure) {
						procedure.payments = procedure.payments.filter(function(payment) {
							return !(payment === cancelledPayment ||
								(payment.is_additional_payment && payment.parent_payment === cancelledPayment));
						});
					});
				});
				refreshTableRows();
				recalculateRemainingAmount();
			};

			vm.applyPayments = function() {
				if (vm.isReadyToApplyPayments()) {
					if (vm.cases) {

						var data = {};
						data.payment_info = {};
						data.applied_payments = [];

						data.payment_info.date_of_payment = moment(vm.newPayment.date).format('YYYY-MM-DD');
						data.payment_info.payment_method = vm.newPayment.method.id;
						data.payment_info.total_amount = vm.newPayment.amount;
						data.payment_info.authorization_number = vm.newPayment.authorization_number;
						data.payment_info.check_number = vm.newPayment.check_number;

						data.payment_info.payment_source = vm.newPayment.source.id;
						if (vm.newPayment.source.id == BillingConst.LEDGER_PAYMENT_SOURCES.INSURANCE) {
							data.payment_info.selected_patient_insurance_id = vm.newPayment.source.patient_insurance_id
						}

						angular.forEach(vm.cases, function(caseItem) {
							angular.forEach(caseItem.procedures, function(procedure) {
								angular.forEach(procedure.payments, function(payment) {
									if (payment._isNew && (!payment.is_additional_payment || payment.amount > 0)) {
										var appliedPayment = {
											coding_bill_id: procedure.id,
											amount: payment.amount,
											resp_co_pay_amount: payment.resp_co_pay_amount,
											resp_co_ins_amount: payment.resp_co_ins_amount,
											resp_deduct_amount: payment.resp_deduct_amount,
											is_additional_payment: payment.is_additional_payment
										};

										if (payment.is_additional_payment) {
											appliedPayment.custom_payment_info = {
												date_of_payment: data.payment_info.date_of_payment,
												payment_method: payment.payment_method.id,
												payment_source: payment.payment_source.id,
												total_amount: payment.amount
											};
										}

										data.applied_payments.push(appliedPayment);
									}
								});
							});
						});

						$http.post('/billings/ledger/ajax/' + $scope.org_id + '/applyPayments/', $.param({
							data: angular.toJson(data)
						})).then(function (res) {
							if (res.data.success) {
								if (!vm.newPayment.bulk_payment) {
									$scope.$emit('flashAlertMessage', 'The payment has been successfully applied');
									vm.init(vm.patientId);
								} else {
									var remaining = parseFloat(vm.newPayment.remaining_amount);
									if (remaining > 0) {
										openNextPatientModal();
									} else {
										vm.isBulkPaymentLocked = false;
										vm.bulkPreviousPaymentsAmount = 0;
										$scope.$emit('flashAlertMessage', 'The payment has been successfully applied');
										vm.init(vm.patientId);
									}
								}
							} else {
								vm.errors = res.data.errors;
							}
						});

					}
				}
			};

			vm.editPayment = function(payment) {
				if (vm.isEditingAllowed) {
					vm.isEditingAllowed = false;
					payment._isEdit = true;
					payment.originalAmount = payment.amount;
				}
			};

			vm.cancelEditPayment = function(payment) {
				vm.isEditingAllowed = true;
				payment._isEdit = false;
				payment.amount = payment.originalAmount;
			};

			vm.saveEditPayment = function(payment) {

				$http.post('/billings/ledger/ajax/' + $scope.org_id + '/updatePayment/', $.param({
					data: angular.toJson({
						payment_id: payment.id,
						amount: payment.amount,
						payment_source: payment.payment_source,
						payment_method: payment.payment_method,
						authorization_number: payment.authorization_number,
						check_number: payment.check_number,
						resp_co_pay_amount: payment.resp_co_pay_amount,
						resp_co_ins_amount: payment.resp_co_ins_amount,
						resp_deduct_amount: payment.resp_deduct_amount
					})
				})).then(function (res) {
					if (res.data.success) {
						vm.init(vm.patientId, {
							dontClearPayment: true
						});
					} else {
						vm.errors = res.data.errors;
					}
				});
			};

			vm.deletePaymentDlg = function(payment) {
				$scope.dialog(View.get('billing/ledger/confirm-delete-payment.html'), $scope, {windowClass: 'alert'}).result.then(function() {
					$http.post('/billings/ledger/ajax/' + $scope.org_id + '/deletePayment/', $.param({
						data: angular.toJson({
							payment_id: payment.id
						})
					})).then(function (res) {
						if (res.data.success) {
							$scope.$emit('flashAlertMessage', 'The payment has been successfully deleted');
							vm.init(vm.patientId);
						} else {
							vm.errors = res.data.errors;
						}
					});
				});
			};

			vm.generatePatientStatement = function() {
				vm.isDocumentsLoading = true;
				$http.post('/billings/patient-statement/ajax/' + $scope.org_id + '/generatePatientStatement/' + vm.ledger.patient.id, $.param({
					data: angular.toJson(vm.statementForm)
				})).then(function (res) {
					vm.isDocumentsLoading = false;
					if (res.data.success) {
						vm.statementForm = {};
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				}, function () {
					vm.isDocumentsLoading = false;
				});
			};

			vm.paymentMethodEditChanged = function(payment) {
				if (payment.payment_method) {
					//Check
					if (payment.payment_method.id == BillingConst.LEDGER_PAYMENT_METHODS.CHECK) {
						$scope.dialog(
							'billing/ledger/check-number.html',
							$scope, {
								size: 'md',
								backdrop: 'static',
								controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

									var vm = this;

									payment.authorization_number = null;
									vm.number = payment.check_number;

									vm.ok = function() {
										payment.check_number = vm.number;
										$uibModalInstance.close();
									};

									vm.cancel = function() {
										$uibModalInstance.close();
									};

								}],
								controllerAs: 'modalVm'
							});
						//Card
					} else if (payment.payment_method.id == BillingConst.LEDGER_PAYMENT_METHODS.CREDIT_CARD) {
						$scope.dialog(
							'billing/ledger/authorization-number.html',
							$scope, {
								size: 'md',
								backdrop: 'static',
								controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

									var vm = this;

									payment.check_number = null;
									vm.number = payment.authorization_number;

									vm.ok = function() {
										payment.authorization_number = vm.number;
										$uibModalInstance.close();
									};

									vm.cancel = function() {
										$uibModalInstance.close();
									};

								}],
								controllerAs: 'modalVm'
							});
					} else {
						payment.check_number = null;
						payment.authorization_number = null;
					}
				}
			};

			vm.manageInterestPayments = function() {
				vm.isInterestPaymentsManagement = true;
			};

			vm.cancelManageInterestPayments = function() {
				angular.forEach(vm.cases, function(caseItem) {
					caseItem.interests = caseItem.interests.filter(function(interest) {
						return (!interest._isNew);
					});
					angular.forEach(caseItem.interests, function(interest) {
						if (interest._isEdit) {
							if (interest.originalAmount) {
								interest.amount = interest.originalAmount;
								interest._isEdit = false;
							}
						}
					});
				});
				vm.isInterestPaymentsManagement = false;
			};

			vm.addNewInterestChargePayment = function(caseItem) {
				caseItem.interests.unshift({
					_isNew: true,
					amount: 0,
					date: moment(vm.newPayment.date).format('M/D/YYYY')
				});
			};

			vm.cancelNewInterestPayment = function(cancelledInterest) {
				angular.forEach(vm.cases, function(caseItem) {
					caseItem.interests = caseItem.interests.filter(function(interest) {
						return !(interest === cancelledInterest);
					});
				});
			};

			vm.editInterestPayment = function(interest) {
				interest._isEdit = true;
				interest.originalAmount = interest.amount;
			};

			vm.saveEditInterestPayment = function(interest) {
				$http.post('/billings/ledger/ajax/interests/' + $scope.org_id + '/updateInterest/', $.param({
					data: angular.toJson({
						id: interest.id,
						amount: interest.amount
					})
				})).then(function (res) {
					if (res.data.success) {
						vm.init(vm.patientId, {
							dontClearPayment: true
						});
					} else {
						vm.errors = res.data.errors;
					}
				});
			};

			vm.cancelEditInterestPayment = function(interest) {
				interest._isEdit = false;
				interest.amount = interest.originalAmount;
				interest.originalAmount = null;
			};

			vm.removeInterestPayment = function(interest) {
				$scope.dialog(
					'billing/ledger/remove-interest-confirmation.html',
					$scope, {
						size: 'md',
						backdrop: 'static',
						controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

							var modalVm = this;

							modalVm.ok = function() {
								vm.isShowLoading = true;
								$http.post('/billings/ledger/ajax/interests/' + $scope.org_id + '/removeInterest/' , $.param({
									data: angular.toJson({
										id: interest.id
									})
								})).then(function (res) {
									if (res.data.success) {
										vm.init(vm.patientId, {
											dontClearPayment: true
										});
									} else {
										vm.errors = res.data.errors;
									}
									vm.isShowLoading = false;
								});
								$uibModalInstance.close();
							};

							modalVm.cancel = function() {
								$uibModalInstance.close();
							};

						}],
						controllerAs: 'modalVm'
					});
			};

			vm.applyInterestPayments = function() {
				if (vm.isReadyToApplyInterestPayments()) {
					if (vm.cases) {

						var data = {};
						data.interests = [];

						angular.forEach(vm.cases, function(caseItem) {

							angular.forEach(caseItem.interests, function(interest) {
								if (interest._isNew) {
									var interestData = {
										date: moment(vm.newPayment.date).format('YYYY-MM-DD'),
										amount: interest.amount,
										case_id: caseItem.id
									};

									data.interests.unshift(interestData);
								}
							});
						});

						$http.post('/billings/ledger/ajax/interests/' + $scope.org_id + '/applyInterests/', $.param({
							data: angular.toJson(data)
						})).then(function (res) {
							if (res.data.success) {
								vm.init(vm.patientId, { dontClearPayment: true }).then(function() {
									$scope.$emit('flashAlertMessage', 'The payment has been successfully applied');
									vm.isInterestPaymentsManagement = false;
								});
							} else {
								vm.errors = res.data.errors;
							}
						});

					}
				}
			};

			vm.isReadyToApplyInterestPayments = function() {
				var isReady = false;
				angular.forEach(vm.cases, function(caseItem) {
					// has at least one new interest charge/payment
					angular.forEach(caseItem.interests, function(interest) {
						if (interest._isNew) {
							isReady = true;
							return false;
						}
					});
					if (isReady) {
						return false;
					}
				});

				return isReady;
			};

			vm.forceAssign = function (options) {
				var balance = (options.case) ? options.case.totals.balance : options.procedure.totals.balance;

				$scope.dialog(
					'billing/ledger/force-assign-confirmation.html',
					$scope, {
						size: 'md',
						backdrop: 'static',
						controller: ['$scope', '$uibModalInstance', function ($scope, $uibModalInstance) {

							var modalVm = this;

							modalVm.isInsurance = options.assignToInsurance;
							modalVm.isPatient = options.assignToPatient;

							modalVm.amount = balance;

							modalVm.ok = function() {
								vm.isShowLoading = true;
								var procedures = [];
								if (options.case) {
									angular.forEach(options.case.procedures, function(procedure) {
										procedures.push(procedure);
									});
								}
								if (options.procedure) {
									procedures.push(options.procedure);
								}
								var method = options.assignToInsurance ? 'forceAssignToInsurance' : 'forceAssignToPatient';
								$http.post('/billings/ledger/ajax/' + $scope.org_id + '/' + method + '/' + vm.patientId, $.param({
									data: angular.toJson({
										procedures: procedures.map(function(procedure) {
											return procedure.id;
										})
									})
								})).then(function(res) {
									if (res.data.success) {
										vm.ledger.totals.patient_responsible_balance = res.data.new_patient_responsible_balance;
										angular.forEach(procedures, function(procedure) {
											procedure.has_force_patient_resp = !!options.assignToPatient;
										});
										refreshTableRows();
										vm.isShowLoading = true;
									}
								});
								$uibModalInstance.close();
							};

							modalVm.cancel = function() {
								$uibModalInstance.close();
							};

						}],
						controllerAs: 'modalVm'
					});
			};

			function openNextPatientModal() {

				$scope.dialog(
					'billing/ledger/select-next-patient.html',
					$scope, {
						size: 'lg',
						backdrop: 'static',
						windowClass: 'select-next-patient-modal',
						keyboard: false,
						controller: [
							'$scope',
							'$uibModalInstance',
							function ($scope, $uibModalInstance) {
								var modalVm = this;
								var appliedPaymentSum = getAddedPaymentsSum();

								modalVm.patientId = vm.patientId;
								modalVm.isShowPaymentAlert = (appliedPaymentSum > 0);

								modalVm.selectNewPatient = function(patientId) {
									vm.isBulkPaymentLocked = true;
									vm.bulkPreviousPaymentsAmount += appliedPaymentSum;
									vm.init(patientId, {
										dontClearPayment: true
									});
									$uibModalInstance.close();
								};

								modalVm.dismiss = function() {
									vm.bulkPreviousPaymentsAmount += appliedPaymentSum;
									vm.init(modalVm.patientId, {
										dontClearPayment: true
									});
									$uibModalInstance.dismiss();
								};
							}
						],
						controllerAs: 'modalVm'
					});
			}

			function refreshTableRows() {
				recalculateTableRows();
			}

			function recalculateTableRows () {
				if (vm.cases) {
					angular.forEach(vm.cases, function(caseItem) {

						caseItem.totals = {
							insurancePayments: 0,
							patientPayments: 0,
							adjustments: 0,
							writeOffs: 0,
							balance: 0,
							charges: 0
						};

						caseItem.responsibilityAmounts = {
							insurance: 0.00,
							coPay: 0.00,
							coIns: 0.00,
							deductible: 0.00,
							oop: 0.00
						};

						angular.forEach(caseItem.procedures, function(procedure) {

							procedure.totals = {
								insurancePayments: 0,
								patientPayments: 0,
								adjustments: 0,
								writeOffs: 0,
								balance: procedure.amount
							};

							procedure.responsibilityAmounts = {
								insurance: 0.00,
								coPay: 0.00,
								coIns: 0.00,
								deductible: 0.00,
								oop: 0.00
							};

							var procedureHasInsurancePaymentWithResp = false;

							angular.forEach(procedure.payments, function(payment) {

								var isInsurancePayment = false;
								var isPatientPayment = false;
								var isAdjustment = false;
								var isWriteOff = false;

								var order = '';

								if (payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.INSURANCE) {
									isInsurancePayment = true;
									if (payment.payment_source.patient_insurance_id) {
										if (caseItem.patient_insurances[payment.payment_source.patient_insurance_id]) {
											var orderNum = caseItem.patient_insurances[payment.payment_source.patient_insurance_id];
											if (PatientConst.INSURANCE_PRIMARY[orderNum]) {
												order = ' - ' + PatientConst.INSURANCE_PRIMARY[orderNum];
											}
										}
									}
								} else if (
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_CO_PAY ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_DEDUCTIBLE ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_CO_INSURANCE ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_OOP
								) {
									isPatientPayment = true;
								} else if (payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.ADJUSTMENT) {
									isAdjustment = true;
								} else if (
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_CO_PAY ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_CO_INSURANCE ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_DEDUCTIBLE ||
									payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_OOP
								) {
									isWriteOff = true;
								}

								payment._isInsurancePayment = isInsurancePayment;
								payment._isPatientPayment = isPatientPayment;
								payment._isAdjustment = isAdjustment;
								payment._isWriteOff = isWriteOff;

								payment.payment_source_title = payment.payment_source.title + order;

								payment.responsibilityAmounts = {
									coPay: 0.00,
									coIns: 0.00,
									deductible: 0.00
								};

								if (!payment._isNew) {
									var amount = parseFloat(payment.amount);
									if (payment._isInsurancePayment) {
										procedure.totals.insurancePayments += amount;
									} else if (payment._isPatientPayment) {
										procedure.totals.patientPayments += amount;
									} else if (payment._isAdjustment) {
										procedure.totals.adjustments += amount;
									} else if (payment._isWriteOff) {
										procedure.totals.writeOffs += amount;
									}

									if (payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_CO_PAY ||
										payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_CO_PAY) {
										procedure.responsibilityAmounts.coPay -= amount;
									} else if (payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_CO_INSURANCE ||
										payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_CO_INSURANCE) {
										procedure.responsibilityAmounts.coIns -= amount;
									} else if (payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.PATIENT_DEDUCTIBLE ||
										payment.payment_source.id == BillingConst.LEDGER_PAYMENT_SOURCES.WRITE_OFF_DEDUCTIBLE) {
										procedure.responsibilityAmounts.deductible -= amount;
									}

									var respAmount;
									if (payment.resp_co_pay_amount) {
										respAmount = parseFloat(payment.resp_co_pay_amount);
										if (!isNaN(respAmount) && isFinite(respAmount)) {
											payment.responsibilityAmounts.coPay = (respAmount);
											procedure.responsibilityAmounts.coPay += (respAmount);
										}
									}
									if (payment.resp_co_ins_amount) {
										respAmount = parseFloat(payment.resp_co_ins_amount);
										if (!isNaN(respAmount) && isFinite(respAmount)) {
											payment.responsibilityAmounts.coIns = (respAmount);
											procedure.responsibilityAmounts.coIns += (respAmount);
										}
									}
									if (payment.resp_deduct_amount) {
										respAmount = parseFloat(payment.resp_deduct_amount);
										if (!isNaN(respAmount) && isFinite(respAmount)) {
											payment.responsibilityAmounts.deductible = (respAmount);
											procedure.responsibilityAmounts.deductible += (respAmount);
										}
									}

									if (isInsurancePayment && (
											payment.responsibilityAmounts.coPay > 0 ||
											payment.responsibilityAmounts.coIns > 0 ||
											payment.responsibilityAmounts.deductible > 0
										)) {
										procedureHasInsurancePaymentWithResp = true;

									}

								}

							});

							caseItem.totals.insurancePayments += procedure.totals.insurancePayments;
							caseItem.totals.patientPayments += procedure.totals.patientPayments;
							caseItem.totals.adjustments += procedure.totals.adjustments;
							caseItem.totals.writeOffs += procedure.totals.writeOffs;

							var totalProcedureCharges = parseFloat(procedure.amount);
							procedure.totals.balance = (
								totalProcedureCharges - (procedure.totals.insurancePayments
								+ procedure.totals.patientPayments
								+ procedure.totals.adjustments
								+ procedure.totals.writeOffs)
							);

							if (caseItem.is_self_pay_insurance || procedure.has_force_patient_resp) {
								procedure.responsibilityAmounts.insurance = 0.00;
								procedure.responsibilityAmounts.oop = procedure.totals.balance;
							} else {

								if (vm.ledger.patient_has_insurances && !procedureHasInsurancePaymentWithResp) {
									procedure.responsibilityAmounts.insurance = procedure.totals.balance;
									caseItem.responsibilityAmounts.insurance += procedure.totals.balance;
								}

								procedure.responsibilityAmounts.oop = (
									procedure.totals.balance - (procedure.responsibilityAmounts.insurance
									+ procedure.responsibilityAmounts.coIns
									+ procedure.responsibilityAmounts.coPay
									+ procedure.responsibilityAmounts.deductible)
								);
							}


							caseItem.responsibilityAmounts.coIns += procedure.responsibilityAmounts.coIns;
							caseItem.responsibilityAmounts.coPay += procedure.responsibilityAmounts.coPay;
							caseItem.responsibilityAmounts.deductible += procedure.responsibilityAmounts.deductible;
						});

						var totalProcedureCharges = parseFloat(caseItem.total_charges);

						angular.forEach(caseItem.interests, function(interest) {
							var amount = parseFloat(interest.amount);
							totalProcedureCharges += amount;
							caseItem.totals.insurancePayments += amount;
						});

						caseItem.totals.charges = totalProcedureCharges;
						caseItem.totals.balance = (totalProcedureCharges - (caseItem.totals.insurancePayments
							+ caseItem.totals.patientPayments
							+ caseItem.totals.adjustments
							+ caseItem.totals.writeOffs));

						if (caseItem.is_self_pay_insurance) {
							caseItem.responsibilityAmounts.insurance = 0.00;
							caseItem.responsibilityAmounts.oop = caseItem.totals.balance;
						} else {
							caseItem.responsibilityAmounts.oop = (
								caseItem.totals.balance - (caseItem.responsibilityAmounts.insurance
								+ caseItem.responsibilityAmounts.coIns
								+ caseItem.responsibilityAmounts.coPay
								+ caseItem.responsibilityAmounts.deductible)
							);
						}
					});
				}
			}

			function recalculateRemainingAmount() {

				if (!vm.newPayment || vm.newPayment.amount === '') {
					vm.newPayment.remaining_amount = '';
					return;
				}

				var value = parseFloat(vm.newPayment.amount);

				if (vm.bulkPreviousPaymentsAmount) {
					value -= vm.bulkPreviousPaymentsAmount;
				}

				if (vm.cases) {
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								// Don't count additional payment details in calculation of remaining amount
								if (payment._isNew && !payment.is_additional_payment) {
									if (payment.amount) {
										var paymentAmount = parseFloat(payment.amount);
										if (!isNaN(paymentAmount) && isFinite(paymentAmount)) {
											value -= paymentAmount;
										}
									}
								}
							});
						});
					})
				}

				vm.newPayment.remaining_amount = value.toFixed(2);
			}

			function getAddedPaymentsSum() {
				var value = 0;
				if (vm.cases) {
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								// Don't count additional payments in moving of applied sum while bulk payment addition.
								if (payment._isNew && !payment.is_additional_payment) {
									if (payment.amount) {
										var paymentAmount = parseFloat(payment.amount);
										if (!isNaN(paymentAmount) && isFinite(paymentAmount)) {
											value += paymentAmount;
										}
									}
								}
							});
						});
					});
				}

				return value;
			}

			function isAllAddedPaymentsPositive() {
				var isAllPaymentsPositive = true;
				if (vm.cases) {
					angular.forEach(vm.cases, function(caseItem) {
						angular.forEach(caseItem.procedures, function(procedure) {
							angular.forEach(procedure.payments, function(payment) {
								if (payment._isNew) {
									var amount;

									// Disable any validation for additional payment details.
									// The only thing - it should not be a negative number.
									if (payment.is_additional_payment) {
										if (payment.amount) {
											amount = parseFloat(payment.amount);
											if (amount < 0) {
												isAllPaymentsPositive = false;
												return false;
											}

										}
									} else {
										if (!payment.amount) {
											isAllPaymentsPositive = false;
											return false;
										}

										amount = parseFloat(payment.amount);
										if (!(amount > 0)) {
											isAllPaymentsPositive = false;
											return false;
										}
									}
								}
							});
							if (!isAllPaymentsPositive) {
								return false;
							}
						});
						if (!isAllPaymentsPositive) {
							return false;
						}
					});
				}

				return isAllPaymentsPositive
			}

		}]);

})(opakeApp, angular);