(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('caseCalendar', [
		'$rootScope',
		'$compile',
		'$timeout',
		'$filter',
		'$http',
		'$document',
		'$location',
		'$templateRequest',
		'View',
		'ViewState',
		'Calendar',
		'Cases',
		'Case',
		'CaseInService',
		'CaseBlocking',
		'Permissions',
		'Source',
		'CaseRegistrationConst',

		function ($rootScope, $compile, $timeout, $filter, $http, $document, $location, $templateRequest, View, ViewState, Calendar, Cases, Case, CaseInService, CaseBlocking, Permissions, Source, CaseRegistrationConst) {
			return {
				restrict: "A",
				replace: true,
				scope: {
					caseCalendar: '=',
					roomsFullList: '=',
					selectedRooms: '=',
					surgeonsSrc: '=',
					newCase: '=',
					editedCase: '='
				},
				controller: function () {
					var vm = this;
					vm.errors = [];

					vm.update = function (id, data) {
						return $http.post('/cases/ajax/' + $rootScope.org_id + '/updateByCalendar/' + id, $.param({
							data: JSON.stringify(data)
						})).then(function (result) {
							vm.errors = [];
							if (!result.data.success) {
								vm.errors = result.data.errors;
							}
						});
					};
				},
				controllerAs: 'calendarCtrl',
				template: '<div class="cases-calendar-wrap">' +
						'<errors src="calendarCtrl.errors"></errors>' +
						'<div class="cases-calendar"></div>' +
					'</div>',
				link: function (scope, elem, attrs, ctrl) {

					var calendar = elem.find('div:last-child');

					var minTime = '09:00',
						maxTime = '19:00',
						newEvent = null,
						currentRooms = null,
						eventDetails,
						hoveredEventId,
						viewState = ViewState.getState(),
						viewType = viewState.cases_view_type || 'agendaWeek';

						if (viewType === 'day' || viewType === 'week') {
							viewType = ('agenda' + viewType.charAt(0).toUpperCase() + viewType.slice(1));
						}

					function getDayTitleFormat() {
						if (View.isTablet()) {
							return 'ddd - MMM. D, YYYY';
						} else {
							return 'dddd MMMM D, YYYY';
						}
					}

					function getWeekTitleFormat() {
						if (View.isTablet()) {
							return 'MMM. D';
						} else {
							return 'MMMM D';
						}
					}

					scope.calendarConfig = {
						height: 'auto',
						timezone: 'local',
						defaultTimedEventDuration: '00:00:01',
						slotEventOverlap: false,
						defaultView: viewType,
						slotDuration: '01:00:00',
						snapDuration: '00:05:00',
						minTime: minTime,
						maxTime: maxTime,
						allDaySlot: false,
						displayEventEnd: false,
						fixedWeekCount: false,
						axisFormat: 'h:mma',
						nowIndicator: true,
						eventResourceField: 'location_id',
						endParam: 'end_exclude',
						eventConstraint: {
							start: '0:00',
							end: '24:00'
						},
						header: {
							left: 'today',
							center: 'prev title next',
							right: ''
						},
						views: {
							day: {
								titleFormat: getDayTitleFormat()
							},
							week: {
								titleFormat: getWeekTitleFormat(),
								columnFormat: 'dddd D'
							},
							month: {
								columnFormat: 'dddd',
								timeFormat: 'h:mma'
							}
						},
						loading: function (isLoading, view) {

							var events = calendar.fullCalendar('clientEvents');

							if (scope.newCase && events.indexOf(newEvent) === -1) {
								var data = {
									title: 'New Case',
									start: scope.newCase.time_start,
									end: scope.newCase.time_end,
									allDay: false,
									startEditable: true
								};
								if (scope.newCase.location) {
									data.location_id = scope.newCase.location.id;
								}
								var obj = calendar.fullCalendar('renderEvent', data, true)[0];
								editCase(scope.newCase, obj);
								newEvent = obj;
							}

							if (!editableEvent && scope.editedCase) {
								var editedData = {
									id: scope.editedCase.id,
									start: scope.editedCase.time_start,
									end: scope.editedCase.time_end,
									allDay: false,
									startEditable: true
								};
								if (scope.editedCase.location) {
									editedData.location_id = scope.editedCase.location.id;
								}
								var editedObj = calendar.fullCalendar('renderEvent', editedData, true)[0];
								editCase(scope.editedCase, editedObj);
								newEvent = editedObj;
							}

							if (editableEvent && scope.editedCase) {
								if (!$filter('filter')(events, {id: editableEvent.id}).length) {
									events.push(editableEvent);
								}

								var eventsIds = [];
								angular.forEach(events, function (event) {
									if ($filter('filter')(eventsIds, event.id, true).length) {
										var index = events.indexOf(event);
										events.splice(index, 1);
									} else {
										eventsIds.push(event.id);
									}
								});

								editCase(scope.editedCase, editableEvent);
							}
						},
						eventRender: function (event, block, view) {
							block = $(block);
							var content = block.find(".fc-content");
							$(".fc-time", content).remove();

							if(event.type === 'case') {
								var event_class = "";
								if(scope.caseCalendar[0].data.doctor) {
									event_class = "filtered user-event";
								} else if(scope.caseCalendar[0].data.location) {
									event_class = "filtered location-event";
								}

								if (event.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.NEW) {
									block.addClass("appointment-new");
								}
								else if (event.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.CANCELED) {
									block.addClass("appointment-canceled");
								}
								else if (event.appointment_status == CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED) {
									block.addClass("appointment-completed");
								}

								if(angular.isDefined(event.running_alert_class) && event.running_alert_class) {
									block.addClass(event.running_alert_class);
									var text = '';
									if(event.running_alert_class === 'in-progress') {
										text = '<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>' + ' In Progress';
									} else if(event.running_alert_class === 'running-late') {
										text = '<i class="fa fa-exclamation-circle"></i>' + ' Running Late';
									}
									block.prepend('<div class="event-header">' + text + '</div>');
								}

								if (View.isWaitingRoom) {
									var titleArray = event.title.split(', ');
									var shortTitleArray = [];
									angular.forEach(titleArray, function (val) {
										shortTitleArray.push(val.substr(0, 3) + '.');
									});
									var shortEventTitle = shortTitleArray.join(', ');
									$(".fc-title", content).replaceWith("<span class='title "+event_class+"'>" + shortEventTitle + "</span>");
								} else {
									$(".fc-title", content).replaceWith("<span class='title "+event_class+"'>" + event.title + "</span>");
								}

								if (view.intervalUnit === "day") {
									content.addClass("day-case");

									if (View.isWaitingRoom) {
										content.append("<div class='doctor'>" + event.staff[0] + "</div>");
										content.append("<div><b>(" + event.start.format('h:mma') + ' - ' + event.end.format('h:mma') + ")</b></div>");
									} else {
										var dobInfo = event.patient.dob ? (event.patient.dob + " - " + event.patient.age + "yo |") : '',
											desc = event.description ? (" - " + event.description) : '';
										content.append(" | " + dobInfo + " MRN: " + event.patient.full_mrn);
										var icon = '<i class="icon-red-warning-pastel"></i>';
										if (event.alerts.length) {
											content.append(icon);
										}
										content.append("<div><b>(" + event.start.format('h:mma') + ' - ' + event.end.format('h:mma') + ") (" + $filter('timeLength')(event.start, event.end) + ")</b> | " + event.staff.join(', ') + "</div>");
										content.append("<div>" + event.case_type + desc + "</div>");
									}

								} else if (view.intervalUnit === "week" && event.staff) {
									if (View.isWaitingRoom) {
										content.append("<div class='doctor'>" + event.staff[0] + "</div>");
									} else {
										content.append(
											"<div class='location'>" + event.location + "</div>" +
											"<div class='doctor'>" + event.staff.join(", ") + "</div>"
										);
									}
								}

							} else if(event.type === 'block') {
								$(".fc-title", content).replaceWith("<span class='title'>" + event.title + "</span>");
								if (!View.isWaitingRoom) {
									content.append("<div class='location'>" + event.location.name + "</div>");
								}
							}

							if(view.intervalUnit !== "day") {
								block.addClass('restrict-content');
							}

							block.addClass(event.type);
						},
						eventMouseover: function (event) {
							if (event.id) {
								hoveredEventId = event.id;
								var block = $(this);
								var previewScope = $rootScope.$new();
								if (event.type === 'case') {
									Cases.get(event.id, false).then(function (item) {
										previewScope.item = item;
										$templateRequest(View.get('cases/calendar/case_view.html')).then(function (html) {
											showEventDetails(event.id, block, html, previewScope);
										});
									});
								} else if (event.type === 'in_service') {

									previewScope.item = new CaseInService(event);
									$templateRequest(View.get('cases/calendar/in_service_view.html')).then(function (html) {
										showEventDetails(event.id, block, html, previewScope);
									});
								}
							}

							scope.$watch('editedCase', function () {
								if (scope.editedCase) {
									editCase(scope.editedCase, event);
								}
							});
							scope.$watch('editedCase', function (newVal, oldVal) {
								if (!newVal && oldVal && editableEvent) {
									calendar.fullCalendar('removeEvents', editableEvent._id);
									calendar.fullCalendar('refetchEvents');
								}
							});
						},
						eventClick: function (event) {
							if (event.id) {
								hoveredEventId = event.id;
								var block = $(this);
								var previewScope = $rootScope.$new();
								if (event.type === 'block') {
									Cases.getBlocking(event.blocking_id, false).then(function (blocking) {
										Cases.getBlockingItem(event.id, false).then(function (blockingItem) {
											previewScope.blocking = blocking;
											previewScope.blockingItem = blockingItem;
											$templateRequest(View.get('cases/calendar/blocking/view.html')).then(function (html) {
												showEventDetails(event.id, block, html, previewScope, event.type);
											});
										});
									});
								}
							}
						},
						eventMouseout: function (event, jsEvent) {
							if (event.id && (event.type !== 'block')) {
								hoveredEventId = null;
								if (!$(jsEvent.relatedTarget).closest('.cases-calendar--preview').length) {
									hideEventDetails(event.id);
								}
							}
						},
						eventAfterAllRender: function (view) {
							var needUpdate = false;
							if (view.type !== "month") {
								needUpdate = updateTimeLimits();
							}

							if (!needUpdate) {
								calendar.find('.fc-left').removeClass('day month week').addClass(view.intervalUnit);
								if (view.intervalUnit !== "day") {
									calendar.find('.fc-today-button').html('this ' + view.intervalUnit);
								} else {
									calendar.find('.fc-today-button').html('today');
								}

								var surgeonsSrc = angular.copy(scope.surgeonsSrc);
								surgeonsSrc.data['start'] = moment(view.start).toDate();
								surgeonsSrc.data['end'] = moment(view.end).subtract(1, 'days').toDate();

								$timeout(function () {
									$rootScope.$broadcast('CaseCalendarLoaded', view, surgeonsSrc);
								}, 500);

								updateTtipNowIndicator();

								$timeout(function () {
									if (viewState.cases_view_date !== calendar.fullCalendar('getDate').format('YYYY-MM-DD')) {
										ViewState.updateCasesViewDate(calendar.fullCalendar('getDate'));
									}
								}, 500);
							}

							calendar.find('.fc-center h2').click(function (e) {
								scope.$apply(function () {
									View.isWaitingRoom = !View.isWaitingRoom;
									if (View.isWaitingRoom == true) {
										$('body').addClass('waiting-room');
									} else {
										$('body').removeClass('waiting-room');
									}
									calendar.fullCalendar('refetchEvents');
								});
								e.preventDefault();
							});

							$rootScope.$on('CalendarWaitingRoomDeactivated', function() {
								View.isWaitingRoom = false;
								$('body').removeClass('waiting-room');
								calendar.fullCalendar('refetchEvents');
							});
						},
						eventDrop: function (e, delta, revertFunc) {
							if (e.id) {
								dropConfirm(function () {
									ctrl.update(e.id, {
										time_start: e.start.toDate(),
										time_end: e.end.toDate(),
										location_id: e.location_id
									}).then(function () {
										if (!ctrl.errors.length) {
											if (e.drop) {
												e.drop();
											}
											updateTimeLimits();
											calendar.fullCalendar('refetchEvents');
										} else {
											revertFunc();
										}
									});
								}, revertFunc);
							} else if (e.drop) {
								e.drop();
							}
						},
						eventResize: function (e) {
							if (e.resize) {
								e.resize();
							}
						}
					};

					if (Permissions.hasAccess('cases', 'create')) {
						scope.calendarConfig.selectable = true;
						scope.calendarConfig.selectConstraint = {
							start: '0:00',
							end: '24:00'
						};
						scope.calendarConfig.select = function (start, end, p3, p4, room) {
							if (calendar.fullCalendar('getView').intervalUnit === 'month') {
								start = moment(start.local().toDate()).set({hour: 11, minute: 0});
								end = moment(start).add(15, 'minute');
							}
							var newCase = new CaseInService();
							if (currentRooms && room) {
								var room = $filter('filter')(currentRooms, {id: room.id});
								if (room.length) {
									newCase.location = room[0];
								}
							}
							newCase.start = start.toDate();
							newCase.end = end.toDate();
							Calendar.showModalWithButtons(newCase);
						};
						scope.calendarConfig.selectHelper = true;

						scope.$watch('newCase', function (newVal, oldVal) {
							if (!newVal && newEvent) {
								calendar.fullCalendar('removeEvents', newEvent._id);
							}
						});
					}

					var params = $location.search();
					if (params.date) {
						var date = moment(params.date);
						if (date.isValid()) {
							updateDate(date);
							$location.search('date', null);
						}
					} else if (viewState.cases_view_date) {
						scope.calendarConfig.defaultDate = moment(viewState.cases_view_date);
					}

					scope.$watch('selectedRooms', function (newVal, oldVal) {
						if (newVal !== oldVal) {
							fillGroups(newVal);
						}
					});
					fillGroups(scope.selectedRooms);

					scope.$on('calendarCaseRescheduled', function(event, reschCase) {
						scope.calendarConfig.defaultDate = reschCase.time_start;
					});

					$rootScope.$on('calendarDateChanged', function(event, date) {
						updateDate(date);
					});

					$rootScope.$on('CaseSavedFromCalendar', function(event, caseId) {
						if (editableEvent && (editableEvent.id == caseId)) {
							calendar.fullCalendar('removeEvents', editableEvent._id);
							calendar.fullCalendar('refetchEvents');
						}
					});

					$rootScope.$on('CaseChangedFromCalendarCancelled', function(event, caseId) {
						if (editableEvent && masterEditableEvent && (editableEvent.id == caseId) 
							&& (masterEditableEvent.id == caseId)) {
							resetEditableCase();
						}
					});

					// Initialization
					calendar.attr("ui-calendar", "calendarConfig");
					calendar.attr("calendar", "case-calendar");
					calendar.attr("ng-model", "caseCalendar");
					$compile(calendar)(scope);

					function updateOptions(options) {
						angular.forEach(options, function (val, key) {
							scope.calendarConfig[key] = val;
						});
						var view = calendar.fullCalendar('getView');
						if (angular.isDefined(view.name)) {
							scope.calendarConfig.defaultView = view.name;

							var defaultDate = scope.calendarConfig.defaultDate ? moment(scope.calendarConfig.defaultDate) : moment();
							if (!defaultDate.isSame(calendar.fullCalendar('getDate'), 'd')) {
								scope.calendarConfig.defaultDate = calendar.fullCalendar('getDate');
							}
						}
					}

					function fillGroups(rooms) {
						if (!angular.isArray(rooms) || !rooms.length) {
							rooms = scope.roomsFullList;
						}
						currentRooms = rooms;
						var groups = [];
						angular.forEach(rooms, function (room) {
							groups.push({
								id: room.id,
								title: room.name
							});
						});
						updateOptions({
							resources: groups
						});
					}

					function updateDate(date) {
						scope.calendarConfig.defaultDate = date;
						scope.calendarConfig.defaultView = 'agendaDay';
						ViewState.update('cases_view_type', 'day');
					}

					function updateTimeLimits() {
						var events = calendar.fullCalendar('clientEvents'),
							view = calendar.fullCalendar('getView'),
							viewStart = moment(view.start.format()).local(),
							viewEnd = moment(view.end.format()).local(),
							min = minTime,
							max = maxTime,
							needUpdate = false;

						events.forEach(function (item) {
							if (item.end) {
								if (item.start >= viewStart && item.end <= viewEnd) {
									var start = moment(item.start).startOf('hour').format("HH:mm"),
										end = moment(item.end).add(1, 'hour').startOf('hour').format("HH:mm");
									if (start > end) {
										end = '24:00';
									}
									if (min > start) {
										min = start;
									}
									if (max < end) {
										max = end;
									}
								}
							}
						});

						if (min !== calendar.fullCalendar('option', 'minTime')) {
							needUpdate = true;
							calendar.fullCalendar('option', 'minTime', min);
						}
						if (max !== calendar.fullCalendar('option', 'maxTime')) {
							needUpdate = true;
							calendar.fullCalendar('option', 'maxTime', max);
						}
						return needUpdate;
					}

					function showEventDetails(id, block, html, previewScope, eventType) {
						if (hoveredEventId !== id) {
							return;
						}
						if (eventDetails) {
							eventDetails.remove();
						}
						var template = angular.element(html),
							offset = block.offset();
						$compile(template)(previewScope);

						template.mouseleave(function () {
							if (eventType !== 'block') {
								hideEventDetails();
							}
						});
						$(document).click( function(e) {
							if ((!$(e.target).closest('.cases-calendar--preview').length) && (eventType === 'block')) {
								hideEventDetails();
							}
						});

						template.find('.close-preview').click(function(){
							eventDetails = null;
							template.remove();
						});
						eventDetails = template;
						template.appendTo('body');

						$timeout(function(){
							var modalWidth = template[0].offsetWidth,
								modalHeight = template[0].offsetHeight,
								blockWidth = block[0].offsetWidth,
								blockHeight = block[0].offsetHeight,
								leftIndent = Math.min(120, blockWidth),
								topIndent = 120;
							var leftSide = ($document.width() - offset.left - leftIndent) < modalWidth,
								onTop = ($document.height() - offset.top + topIndent) < modalHeight;
							template.css({
								top: onTop ? (offset.top - modalHeight + blockHeight) : (offset.top - topIndent),
								left: leftSide ? (offset.left - modalWidth) : (offset.left + leftIndent)
							});
						});
					}

					function hideEventDetails() {
						if (eventDetails) {
							eventDetails.remove();
							eventDetails = null;
						}
					}

					function dropConfirm(confirm, reject) {
						$rootScope.dialog(View.get('/cases/calendar/drop_confirm.html'), scope, {windowClass: 'alert', size: 'md'}).
							result.then(confirm, reject);
					}

					var masterEditableEvent = null,
						editableEvent = null,
						editUnreg = null,
						editUpdatePromise;
					function editCase(caseItem, event) {
						resetEditableCase();

						masterEditableEvent = angular.copy(event);
						editableEvent = event;

						editUnreg = scope.$watch(
							function () {
								return caseItem;
							},
							function (newVal) {
								$timeout.cancel(editUpdatePromise);
								editUpdatePromise = $timeout(function () {
									if (event.id) {
										event.title = newVal.patient ? newVal.patient.last_name + ', ' + newVal.patient.first_name : '';
										event.location = newVal.location ? newVal.location.name : '';
										event.staff = [];
										var className = [];
										angular.forEach(newVal.users, function (item) {
											if (!className.length) {
												className.push('color-' + item.case_color);
											}
											event.staff.push(item.fullname);
										});
										event.className = className;
									}
									if (!event.start || !event.end ||
										event.start.toDate().getTime() !== newVal.time_start.getTime() ||
										event.end.toDate().getTime() !== newVal.time_end.getTime()) {

										event.start = moment(newVal.time_start);
										event.end = moment(newVal.time_end);
									}
									event.location_id = newVal.location ? newVal.location.id : null;
									calendar.fullCalendar('updateEvent', event);
								});
							},
							true);
					}

					function resetEditableCase() {
						if (masterEditableEvent) {
							editUnreg();
							angular.copy(masterEditableEvent, editableEvent);
							calendar.fullCalendar('updateEvent', editableEvent);
							masterEditableEvent = null;
							editableEvent = null;
						}
					}

					// Костыль пока в fullcalendar не добавят событие на рендер NowIndicator
					var nowIndicatorTimeoutID = null,
						nowIndicatorIntervalID = null;
					function updateTtipNowIndicator() {
						if (nowIndicatorTimeoutID) {
							clearTimeout(nowIndicatorTimeoutID);
						}
						if (nowIndicatorIntervalID) {
							clearTimeout(nowIndicatorIntervalID);
						}

						var unit = 'minute';
						var view = calendar.fullCalendar('getView');
						var initialNow = view.calendar.getNow();
						var update_ttip = function () {
							var currentTooltip = calendar.find('.tooltip');
							if (currentTooltip) {
								currentTooltip.remove();
							}
							var now_indicator = calendar.find('.fc-now-indicator-line');
							now_indicator = angular.element(now_indicator);
							now_indicator.attr('uib-tooltip', view.calendar.getNow().format('HH:mm'));
							$compile(now_indicator)(scope);
						};
						update_ttip();

						var delay = initialNow.clone().startOf(unit).add(1, unit) - initialNow;
						nowIndicatorTimeoutID = setTimeout(function () {
							update_ttip();
							delay = +moment.duration(1, unit);
							delay = Math.max(100, delay);
							nowIndicatorIntervalID = setInterval(update_ttip, delay);
						}, delay);
					}

				}
			};
		}]);

})(opakeApp, angular);
