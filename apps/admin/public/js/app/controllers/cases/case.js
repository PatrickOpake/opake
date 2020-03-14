// Case view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseCrtl', [
		'$rootScope',
		'$scope',
		'$http',
		'$window',
		'$q',
		'$timeout',
		'View',
		'Source',
		'Cases',
		'Case',
		'CaseNotes',
		'CaseRegistrationConst',
		'Permissions',
		'OpReports',
		'Insurances',
		'uiCalendarConfig',
		'OperativeReportTemplateConst',
		'BeforeUnload',
		'InsurancesWidgetService',
		'PatientConst',

		function ($rootScope, $scope, $http, $window, $q, $timeout, View, Source,
		          Cases, Case, CaseNotes, CaseRegistrationConst, Permissions, OpReports,
		          Insurances, uiCalendarConfig, OperativeReportTemplateConst, BeforeUnload,
		          InsurancesWidgetService, PatientConst) {

			$scope.cases = Cases;
			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;

			vm.templateConst = OperativeReportTemplateConst;
			vm.case = null;
			vm.toedit = null;
			vm.action = 'view';
			vm.errors = [];
			vm.hasCaseManagementAccess = Permissions.hasAccess('case_management', 'view');
			vm.hasCaseEditAccess =  Permissions.hasAccess('cases', 'edit');
			vm.patientDetailsClose = false;
			vm.surgeryDetailsOpen = true;
			vm.caseNotes = CaseNotes;

			vm.tabActivity = {patient: true, insurance: false, forms: false};
			vm.initOptions = {};

			vm.init = function (caseid, options) {
				var def = $q.defer();

				var afterCaseLoad = function () {
					$timeout(function() {
						$scope.$broadcast('caseLoaded', vm.case, function () {
							if (vm.initOptions.openInEditMode) {
								vm.edit();
								vm.initOptions.openInEditMode = false;
							}
						});
					});

					CaseNotes.getUnreadNotes(vm.case.id);

					def.resolve();
				};

				if (options) {
					vm.initOptions = options;
				}

				if(options && options.case) {
					vm.case = new Case(options.case);
					afterCaseLoad();
				} else {

					var params = {};
					if (vm.initOptions && vm.initOptions.isOperativeReport) {
						params.isOperativeReport = true;
					}

					$http.get('/cases/ajax/' + $scope.org_id + '/case/' + caseid, {params: params}).then(function (result) {
						var data = result.data;
						vm.case = new Case(data);
						afterCaseLoad();
					});
				}

				return def.promise;
			};

			vm.edit = function () {
				vm.action = 'edit';
				vm.toedit = angular.copy(vm.case);
				hangWatchToPointOfSMS();
				BeforeUnload.addForms(vm.toedit, vm.case, 'case');
				BeforeUnload.add(function () {
					if (!BeforeUnload.compareForms(vm.toedit, vm.case)) {
						return 'Case form has been changed. All changes will not be saved.';
					}
				});
			};

			vm.save = function (callback) {
				var def = $q.defer();
				saveCurrentInsurance().then(function() {
					Cases.save(vm.toedit, function (result) {
						if (result.data.id) {
							angular.forEach(vm.toedit, function (val, key) {
								vm.case[key] = val;
							});
							vm.case.id = result.data.id;
							vm.action = 'view';
							if(callback) {
								callback();
							}
							BeforeUnload.clearForms('case');
							BeforeUnload.reset();
							vm.init(vm.case.id).then(function() {
								$scope.$broadcast('Case.PatientSaved',
									vm.toedit.registration.patient.id,
									vm.toedit.registration.patient,
									vm.toedit);
								showScheduledMessage(vm.case);
								def.resolve();
							});
							vm.errors = [];

						} else if (result.data.errors) {
							vm.errors = result.data.errors.split(';');
							def.reject();
						}
					});
				});

				return def.promise;
			};

			vm.saveCaseDetails = function () {
				vm.save().then(function() {
					vm.edit();
				});
			};

			vm.saveEdit = function () {
				if (Cases.case.time_start <= new Date()) {
					$scope.dialog(View.get('cases/passed_confirm.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						saveEditCase();
					});
				} else {
					saveEditCase();
				}
			};

			vm.cancel = function () {
				vm.action = 'view';
				vm.errors = [];
			};

			vm.isCaseChanged = function () {
				return !BeforeUnload.compareForms(vm.toedit, vm.case);
			};

			vm.cancelCaseDetails = function () {
				vm.toedit = angular.copy(vm.case);
			};

			vm.delete = function() {
				$scope.dialog(View.get('cases/confirm_delete.html'), $scope, {
					windowClass: 'alert',
					controller: [
						'$scope', '$uibModalInstance',
						function($scope, $uibModalInstance) {
							var modalVm = this;
							modalVm.case = vm.case || Cases.case;
							modalVm.confirm = function() {
								Cases.delete(modalVm.case.id, function(result) {
									$window.location = '/cases/' + $scope.org_id;
								});
								$uibModalInstance.close();
							};
							modalVm.cancel = function() {
								$uibModalInstance.dismiss('cancel');
							};
						}],
					controllerAs: 'modalVm'
				});
			};

			vm.isShowAppointmentButtons = function() {
				return $rootScope.topMenuActive === 'intake'
			};

			vm.reschedule = function() {
				$window.location = '/cases/' + $scope.org_id + '#?case=' + vm.case.id;
			};

			vm.goToCalendar = function() {
				$window.location = '/cases/' + $scope.org_id + '#?date=' + moment(vm.case.time_start).format('YYYY-MM-DD');
			};

			vm.getStaffs = function(field_code) {
				if(field_code === 'other_staff' || field_code === 'assistant') {
					return Source.getUsers();
				} else if(field_code === 'anesthesiologist') {
					return Source.getAnesthesiologists();
				} else {
					return Source.getSurgeons();
				}
			};

			vm.changeAdditionalCpts = function (caseItem) {
				$scope.$broadcast('changedAdditionalCpts', caseItem);
			};

			vm.addInsurance = function(index) {

			};

			vm.isExistOfOtherInStudiesOrdered = function () {
				if (angular.isArray(vm.toedit.studies_ordered)) {
					return vm.toedit.studies_ordered.indexOf('9') !== -1;
				}

				return false;
			};

			vm.hasAdditionalInsurances = function () {
				return (vm.case.registration.insurances.length > 2);
			};

			vm.hasAutoAccidentOrWorkersCompInsurance = function () {
				var result = false;
				angular.forEach(vm.case.registration.insurances, function (insurance) {
					if ((insurance.type == 6) || (insurance.type == 8)) {
						result = true;
					}
				});

				return result;
			};

			vm.getYearAddingForCPTs = function () {
				if (vm.case.time_start && (moment(vm.case.time_start).isBefore('2017-1-1'))) {
					return 2016;
				} else {
					return 2017;
				}
			};

			vm.getYearAddingForICDs = function () {

				if (vm.case.time_start) {
					var momentDate =  moment(vm.case.time_start);
					var caseYear = momentDate.format('YYYY');
					if (momentDate.isAfter(caseYear + '-10-1')) {
						caseYear = parseInt(caseYear);
						caseYear += 1;

						return caseYear.toString();
					}

					return caseYear;
				}

				//current year by default
				return (moment().format('YYYY'));
			};

			function hangWatchToPointOfSMS () {
				if(!vm.toedit.registration.point_of_contact_phone) {
					$scope.$watch(function () {
						if(vm.toedit.registration) {
							return vm.toedit.registration.point_of_contact_phone;
						}
					}, function(val) {
						if(val) {
							if(val.length === 10 && !vm.toedit.registration.point_of_contact_phone_type) {
								vm.toedit.registration.point_of_contact_phone_type = PatientConst.TYPE_PHONE_NAMES.CELL + '';
							}
						}
					}, true);
				}
			}

			function saveEditCase () {
				var data = Cases.case;
				Cases.save(data, function (result) {
					if (result.data.id) {
						showScheduledMessage(Cases.case);
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			}

			function showScheduledMessage(caseItem) {
				if (!vm.initOptions.isOperativeReport) {
					Cases.showScheduledAlert(caseItem);
				}

			}

			function saveCurrentInsurance() {
				return InsurancesWidgetService.tryToSaveOpenedInsurance();
			}

			$scope.$on('Billing.CodingSaved', function() {
				vm.init(vm.case.id)
			});

		}]);

})(opakeApp, angular);
