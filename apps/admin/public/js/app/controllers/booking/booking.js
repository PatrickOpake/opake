// Booking Sheet
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingSheetCtrl', [

		'$scope',
		'$timeout',
		'$http',
		'$window',
		'$q',
		'$location',
		'$filter',
		'config',
		'View',
		'Source',
		'BeforeUnload',
		'PatientConst',
		'CaseRegistrationConst',
		'Insurances',
		'Patient',
		'Booking',
		'BookingNotes',
		'Bookings',
		'Tools',
		'InsurancesWidgetService',

		function ($scope, $timeout, $http, $window, $q, $location, $filter, config,
		          View, Source, BeforeUnload, PatientConst, CaseRegistrationConst, Insurances, Patient,
		          Booking, BookingNotes, Bookings, Tools, InsurancesWidgetService) {

			$scope.patientConst = PatientConst;
			$scope.caseRegistrationConst = CaseRegistrationConst;
			$scope.insurances = Insurances;

			$scope.$on('Booking.ExistingPatientSelected', function (e, patientId) {
				vm.changePatient(patientId);
				vm.selectPatient();
			});

			$scope.$on('BookingChartsUpdatedForUnsavedBooking', function (e, bookingCharts) {
				vm.bookingCharts = bookingCharts;
				if (bookingCharts) {
					vm.booking.charts_count = bookingCharts.length;
				}
				if (vm.booking && vm.booking.id) {
					vm.init(vm.booking);
				}
			});

			$scope.$on('BookingChartsUpdated', function (e, bookingCharts) {
				if (bookingCharts) {
					vm.booking.charts_count = bookingCharts.length;
				}
			});

			var vm = this;
			$scope.ctrl = vm;

			vm.bookingNotes = BookingNotes;
			vm.isCreatingPatient = false;
			vm.case = vm.model = {};
			vm.isFormContentLoaded = false;
			vm.actionButtonsDisabled = false;
			vm.template = null;
			vm.availableTemplates = null;
			vm.isCreate = true;
			vm.isFormLoading = true;
			vm.bookingCharts = [];

			vm.init = function (id, initOptions) {
				var def = $q.defer();
				var params = $location.search();
				vm.actionButtonsDisabled = false;
				initOptions = initOptions || {};

				if (id) {
					vm.isCreate = false;
					$http.get('/booking/ajax/' + $scope.org_id + '/bookingWithTemplate/' + id).then(function (result) {
						if (result.data) {
							vm.model = vm.booking = new Booking(result.data.booking);
							if (!(vm.booking.patient && vm.booking.patient.id) && vm.booking.booking_patient.id) {
								vm.booking.patient = vm.booking.booking_patient;
							}
							vm.template = result.data.template;
							vm.hangWatchToPointOfSMS();
							def.resolve();
						}
					});
				} else {
					vm.isCreate = true;
					vm.model = vm.booking = new Booking();
					if (!initOptions.isTemplatePreview) {
						if (params.location) {
							Source.getLocations().then(function (locations) {
								var room = $filter('filter')(locations, {id: params.location});
								if (room.length) {
									vm.booking.room = room[0];
								}
							});
						}
						if (params.start) {
							vm.booking.time_start = moment(params.start).toDate();
						}
						if (params.end) {
							vm.booking.time_end = moment(params.end).toDate();
						}
						if (params.patient) {
							vm.changePatient(params.patient);
						}

						$http.get('/booking/ajax/' + $scope.org_id + '/getNewBookingInfo/').then(function (result) {
							if (result.data.success) {
								if (result.data.templates.length == 1) {
									vm.template = result.data.templates[0]
								} else {
									vm.availableTemplates = result.data.templates;
								}
								vm.model.display_point_of_contact = result.data.display_point_of_contact;
							}
						});

						vm.hangWatchToPointOfSMS();
					} else {
						vm.model.display_point_of_contact = true;
					}

					def.resolve();
				}

				return def.promise;
			};

			vm.hangWatchToPointOfSMS = function () {
				if(!vm.booking.patient.point_of_contact_phone) {
					$scope.$watch(function () {
						if(vm.booking.patient) {
							return vm.booking.patient.point_of_contact_phone;
						}
					}, function(val) {
						if(val) {
							if(val.length === 10 && !vm.booking.patient.point_of_contact_phone_type) {
								vm.booking.patient.point_of_contact_phone_type = PatientConst.TYPE_PHONE_NAMES.CELL + '';
							}
						}
					}, true);
				}
			};

			vm.createPatient = function () {
				vm.isCreatingPatient = true;
				vm.booking.patient = new Patient({});

				vm.booking.insurances = [];

				$scope.$broadcast('Booking.PatientDeselect', null, vm.booking.patient, vm.booking);
			};

			vm.selectPatient = function () {
				vm.isCreatingPatient = false;
			};

			vm.selectTemplate = function(template) {
				vm.template = template;
			};

			vm.addInsurance = function (index) {

			};

			vm.changePatient = function (patientId) {
				var patient_id = patientId || vm.booking.patient.id;
				$http.get('/patients/ajax/' + $scope.org_id + '/patient/' + patient_id).then(function (result) {
					vm.booking.patient = new Patient(result.data);
					if (vm.booking.patient.insurances) {
						vm.booking.insurances = [];
					}
					$scope.$broadcast('Booking.PatientChanged', vm.booking.patient.id, vm.booking.patient, vm.booking);
				});
			};

			vm.save = function (isSubmit) {
				if (!vm.actionButtonsDisabled) {
					vm.actionButtonsDisabled = true;

					saveCurrentInsurance().then(function () {
						vm.errors = null;
						Insurances.checkRelationship(vm.patient);
						vm.booking.is_submit = isSubmit;
						vm.booking.template = vm.template;
						Bookings.save(vm.booking, function (result) {
							if (result.data.id) {
								BeforeUnload.reset(true);
								if (!vm.booking.id && vm.bookingCharts && vm.bookingCharts.length) {
									saveBookingCharts(result.data.id).then(function () {
										if (BookingNotes.notes && BookingNotes.notes.length) {
											var notes = [];
											angular.forEach(BookingNotes.notes, function (item) {
												item.booking_id = result.data.id;
												if (item.flagged) {
													item.patient_id = result.data.patient_id;
												}
												notes.push(item);
											});
											BookingNotes.addAllNotes(notes).then(function () {
												$window.location = '/booking/' + $scope.org_id + '/';
												return;
											});
										} else {
											$window.location = '/booking/' + $scope.org_id + '/';
											return;
										}
									});
								} else {
									if (BookingNotes.notes && BookingNotes.notes.length) {
										var notes = [];
										angular.forEach(BookingNotes.notes, function (item) {
											item.booking_id = result.data.id;
											if (item.flagged) {
												item.patient_id = result.data.patient_id;
											}
											notes.push(item);
										});
										BookingNotes.addAllNotes(notes).then(function () {
											$window.location = '/booking/' + $scope.org_id + '/';
											return;
										});
									} else {
										$window.location = '/booking/' + $scope.org_id + '/';
										return;
									}
								}
							} else if (result.data.errors) {
								vm.errors = result.data.errors;
								vm.actionButtonsDisabled = false;
							}
						});
					}, function () {
						vm.actionButtonsDisabled = false;
					});
				}
			};

			function saveBookingCharts (bookingId) {
				var def = $q.defer();

				vm.errors = [];
				angular.forEach(vm.bookingCharts, function (chart) {
					if (chart.name) {
						var fd = new FormData();
						angular.forEach(chart, function (value, key) {
							fd.append(key, value);
						});
						$http.post('/booking/ajax/charts/' + $scope.org_id + '/upload/' + bookingId, fd, {
							withCredentials: true,
							headers: {'Content-Type': undefined},
							transformRequest: angular.identity
						}).then( function() {
							def.resolve();
						});
					}
				});

				return def.promise;
			}

			vm.isDateTimeValid = function () {
				return vm.booking.time_start && vm.booking.time_end && vm.booking.time_start < vm.booking.time_end;
			};

			vm.isLengthOfCaseInvalid = function () {
				return (moment.isDate(vm.booking.time_end) && (vm.booking.time_start >= vm.booking.time_end));
			};

			vm.isRequiredFieldsFilled = function () {
				return vm.booking.patient.last_name
					&& vm.booking.patient.first_name
					&& vm.booking.patient.dob
					&& vm.booking.patient.home_address
					&& vm.booking.patient.home_state
					&& vm.booking.patient.home_city
					&& vm.booking.patient.point_of_contact_phone
					&& vm.booking.patient.point_of_contact_phone_type
					&& vm.booking.patient.gender
					&& vm.booking.users.length
					//&& vm.booking.additional_cpts.length
					//&& vm.booking.admitting_diagnosis.length
					&& vm.isDateTimeValid();
			};

			vm.isRequiredFieldsForSubmitFilled = function () {
				return vm.booking.patient.last_name
					&& vm.booking.patient.first_name
					&& vm.booking.patient.dob
					&& vm.booking.patient.home_address
					&& vm.booking.patient.home_state
					&& vm.booking.patient.home_city
					&& vm.booking.patient.point_of_contact_phone
					&& vm.booking.patient.point_of_contact_phone_type
					&& vm.booking.patient.gender
					//&& vm.booking.additional_cpts.length
					//&& vm.booking.admitting_diagnosis.length
					&& vm.booking.users.length
					&& vm.booking.pre_op_required_data.length
					&& vm.booking.studies_ordered.length
					&& vm.isDateTimeValid();
			};

			vm.schedule = function () {
				if (!vm.actionButtonsDisabled) {
					vm.actionButtonsDisabled = true;

					saveCurrentInsurance().then(function () {
						Bookings.schedule(vm.booking, function (result) {
							if (result.data.id) {
								BeforeUnload.reset(true);
								if (!vm.booking.id && vm.bookingCharts && vm.bookingCharts.length) {
									saveBookingCharts(result.data.id).then(function () {
										if (BookingNotes.notes && BookingNotes.notes.length) {
											var notes = [];
											angular.forEach(BookingNotes.notes, function (item) {
												item.booking_id = result.data.id;
												if (item.flagged) {
													item.patient_id = result.data.patient_id;
												}
												notes.push(item);
											});
											BookingNotes.addAllNotes(notes).then(function () {
												$window.location = '/cases/' + $scope.org_id + '#?booking_id=' + result.data.id + '&date=' + $filter('date')(vm.booking.time_start, 'yyyy-MM-dd');
												return;
											});
										} else {
											$window.location = '/cases/' + $scope.org_id + '#?booking_id=' + result.data.id + '&date=' + $filter('date')(vm.booking.time_start, 'yyyy-MM-dd');
											return;
										}
									});
								} else {
									if (BookingNotes.notes && BookingNotes.notes.length) {
										var notes = [];
										angular.forEach(BookingNotes.notes, function (item) {
											item.booking_id = result.data.id;
											if (item.flagged) {
												item.patient_id = result.data.patient_id;
											}
											notes.push(item);
										});
										BookingNotes.addAllNotes(notes).then(function () {
											$window.location = '/cases/' + $scope.org_id + '#?booking_id=' + result.data.id + '&date=' + $filter('date')(vm.booking.time_start, 'yyyy-MM-dd');
											return;
										});
									} else {
										$window.location = '/cases/' + $scope.org_id + '#?booking_id=' + result.data.id + '&date=' + $filter('date')(vm.booking.time_start, 'yyyy-MM-dd');
										return;
									}
								}
							} else if (result.data.error) {
								vm.errors = [result.data.error];
							}
							vm.actionButtonsDisabled = false;
						});
					}, function () {
						vm.actionButtonsDisabled = false;
					});
				}
			};

			vm.cancel = function () {
				window.history.back();
			};

			vm.isExistOfOtherInStudiesOrdered = function () {
				if (angular.isArray(vm.booking.studies_ordered)) {
					return vm.booking.studies_ordered.indexOf('9') !== -1;
				}
				return false;
			};

			vm.print = function () {
				vm.isDocumentsLoading = true;
				$http.post('/booking/ajax/' + $scope.org_id + '/exportBooking/', $.param({data: JSON.stringify(vm.booking)})).then(function (res) {
					vm.isDocumentsLoading = false;
					if (res.data.success) {
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				}, function () {
					vm.isDocumentsLoading = false;
				});
			};

			$scope.$on('BookingFormWidget.compiled', function () {
				vm.isFormContentLoaded = true;
			});

			vm.getYearAddingForCPTs = function () {
				if (vm.booking.time_start && (moment(vm.booking.time_start).isBefore('2017-1-1'))) {
					return 2016;
				} else {
					return 2017;
				}
			};

			vm.getYearAddingForICDs = function () {
				if (vm.booking.time_start) {
					var momentDate =  moment(vm.booking.time_start);
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

			vm.clearTemplateSelection = function () {
				vm.template = null;
				window.location.reload();
			};

			function saveCurrentInsurance() {
				return InsurancesWidgetService.tryToSaveOpenedInsurance();
			}

		}]);

})(opakeApp, angular);
