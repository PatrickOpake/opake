// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Cases', [
		'$http',
		'$rootScope',
		'$q',
		'$filter',
		'Case',
		'CaseBlocking',
		'CaseBlockingItem',
		'CaseRegistrationConst',

		function ($http, $rootScope, $q, $filter, Case, CaseBlocking, CaseBlockingItem, CaseRegistrationConst) {

			this.save = function (data, callback, isReschedule, bookingId) {
				if (angular.isUndefined(isReschedule)) {
					isReschedule = false;
				}
				if(angular.isDefined(data.time_start)) {
					data.time_end = moment(data.time_start).hours(data.time_end.getHours()).minutes(data.time_end.getMinutes()).toDate();
				}
				return $http.post('/cases/ajax/save/' + $rootScope.org_id + '/case/', $.param({
					data: JSON.stringify(data),
					isReschedule: isReschedule,
					bookingId: bookingId
				})).then(function (result) {
					if (callback) {
						callback(result);
					}
				});
			};

			this.get = function (caseId, useCache) {
				var def = $q.defer();
				$http.get('/cases/ajax/' + $rootScope.org_id + '/case/' + caseId, {cache: useCache}).then(function (result) {
					var caseItem = new Case(result.data);
					def.resolve(caseItem);
				}, function (error) {
					def.reject(error);
				});
				return def.promise;
			};

			this.cancel = function (caseCancellation) {
				return $http.post('/cases/ajax/' + $rootScope.org_id + '/changeAppointmentStatus/' +  caseCancellation.case_id, $.param({
					newStatus: CaseRegistrationConst.APPOINTMENT_STATUS.CANCELED,
					isRemainedInBilling: caseCancellation.is_remained_in_billing,
					data: JSON.stringify(caseCancellation)
				}))
			};

			this.delete = function (case_id, callback) {
				$http.post('/cases/ajax/' + $rootScope.org_id + '/delete/' + case_id).then(function (result) {
					if (callback) {
						callback(result);
					}
				});
			};

			this.updateViewDate = function (date) {
				return $http.get('/cases/ajax/' + $rootScope.org_id + '/updateViewDate/',
					{params: {date: moment(date).format('YYYY-MM-DD')}});
			};

			this.showScheduledAlert = function(caseItem) {
				if (caseItem) {
					var res = '<b>Case Scheduled</b>';
					res += ' for ';
					res += caseItem.patient.last_name + ', ';
					res += caseItem.patient.first_name + ' ';
					res += 'on ' + moment(caseItem.time_start).format('MMMM D hh:mma');
					res += ' - ' + moment(caseItem.time_end).format('hh:mma');
					res += ' with ' + caseItem.getSurgeonNames();

					$rootScope.$emit('flashAlertMessage', res);
				}
			};

			this.showSendedSMSMessage = function(caseItem) {
				if (caseItem) {
					var res = 'Message has been sent to ';
					res += '<b>' + $filter('phone')(caseItem.point_of_contact_phone) + '</b>';

					$rootScope.$emit('flashAlertMessage', res);
				}
			};

			this.getBlocking = function (id, useCache) {
				var def = $q.defer();
				$http.get('/cases/ajax/blocking/' + $rootScope.org_id + '/blocking/' + id, {cache: useCache}).then(function (result) {
					var blocking = new CaseBlocking(result.data);
					def.resolve(blocking);
				}, function (error) {
					def.reject(error);
				});
				return def.promise;
			};

			this.getBlockingItem = function (id, useCache) {
				var def = $q.defer();
				$http.get('/cases/ajax/blocking/' + $rootScope.org_id + '/blockingItem/' + id, {cache: useCache}).then(function (result) {
					var blocking = new CaseBlockingItem(result.data);
					def.resolve(blocking);
				}, function (error) {
					def.reject(error);
				});
				return def.promise;
			};

			this.getCaseFromBooking = function (booking) {
				var item =  new Case({
					id: null,
					location: booking.room,
					time_start: booking.time_start,
					time_end: booking.time_end,
					users: booking.users,
					assistant: booking.assistant,
					other_staff: booking.other_staff,
					studies_other: booking.studies_other,
					additional_cpts: booking.additional_cpts,
					anesthesia_type: booking.anesthesia_type,
					anesthesia_other: booking.anesthesia_other,
					description: booking.description,
					locate: booking.location,
					transportation: booking.transportation,
					transportation_notes: booking.transportation_notes,
					pre_op_required_data: booking.pre_op_required_data,
					studies_ordered: booking.studies_ordered,
					special_equipment_flag: booking.special_equipment_flag,
					special_equipment_implants: booking.special_equipment_implants,
					implants_flag: booking.implants_flag,
					implants: booking.implants,
					equipments: booking.equipments,
					implant_items: booking.implant_items,
					point_of_origin: booking.point_of_origin,
					referring_provider_name: booking.referring_provider_name,
					referring_provider_npi: booking.referring_provider_npi,
					date_of_injury: booking.date_of_injury,
					is_unable_to_work: booking.is_unable_to_work,
					unable_to_work_from: booking.unable_to_work_from,
					unable_to_work_to: booking.unable_to_work_to
				});

				if (booking.patient && booking.patient.id) {
					item.patient = booking.patient;
				} else if (booking.booking_patient && booking.booking_patient.id) {
					item.patient = booking.booking_patient;
				}

				if(booking.additional_cpts && booking.additional_cpts.length) {
					item.type = booking.additional_cpts[0];
				}

				item.registration = {};
				item.registration.admitting_diagnosis = booking.admitting_diagnosis;
				item.registration.secondary_diagnosis = booking.secondary_diagnosis;
				item.registration.admission_type = booking.admission_type;
				item.registration.insurances = [];
				angular.forEach(booking.insurances, function (insurance) {
					insurance.id = null;
					insurance.data.id = null;
					insurance.patient_id = null;
					item.registration.insurances.push(insurance);
				});
				return item;
			};

			this.replaceDynamicFieldsSMSTemplate = function (caseItem, templateMsg) {
				if(!templateMsg) {
					return '';
				}
				var replaceArr = {
					'{Appointment}': moment(caseItem.time_start).format('h:mma on MMM D, YYYY')
				};
				angular.forEach(replaceArr, function (replace, find) {
					templateMsg = templateMsg.replace(new RegExp(find, 'ig'), replace);
				});
				return templateMsg;
			}

		}]);
})(opakeApp, angular);
