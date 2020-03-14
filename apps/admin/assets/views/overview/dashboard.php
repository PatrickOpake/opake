<div ng-controller="DashboardListCrtl as listVm" class="dashboard" ng-cloak>

	<div ng-controller="OverviewChartsCtrl as chartsVm">
		<div class="dashboard--header row" ng-if="!view.isPhone()">
			<div class="icon-group col-sm-3">
				<div ng-if="view.isPC()" ng-include="view.get('cases/dashboard/icons.html')"></div>
				<div ng-hide="listVm.isToday()" class="tool-item">
					<a ng-if="listVm.isViewTypeDay()" href="" ng-click="listVm.today()" class="today btn btn-grey">Today</a>
					<a ng-if="listVm.isViewTypeWeek()" href="" ng-click="listVm.today()" class="today btn btn-grey">This
						Week</a>
				</div>
				<div class="loading-wheel tool-item" ng-show="listVm.isShowLoading">
					<div class="loading-spinner"></div>
				</div>
			</div>
			<div class="dashboard-date col-sm-6">
				<div class="prev col-sm-1">
					<button type="button" class="prev-button" ng-click="listVm.previous()"><span
							class="dashboard-icon icon-left-single-arrow"></span></button>
				</div>
				<div class="center col-sm-10">
					<h2>{{ listVm.getDateDisplay() }}</h2>
				</div>
				<div class="next col-sm-1">
					<button type="button" class="next-button" ng-click="listVm.next()"><span
							class="dashboard-icon icon-right-single-arrow"></span></button>
				</div>
			</div>
			<div ng-if="view.isTablet()" class="icon-group icons-right col-sm-3">
				<div ng-include="view.get('cases/dashboard/icons.html')"></div>
			</div>
		</div>

		<div class="dashboard--header phone-header row" ng-if="view.isPhone()">
			<div class="col-xs-1 icon-group">
				<div class="case-calendar-period-select" ctrl="listVm"></div>
			</div>
			<div class="col-xs-10 dashboard-date">
				<div class="prev col-xs-1">
					<button type="button" class="prev-button" ng-click="listVm.previous()"><span
							class="dashboard-icon icon-left-single-arrow"></span></button>
				</div>
				<div class="center col-xs-10">
					<h2>{{ listVm.getDateDisplay() }}</h2>
				</div>
				<div class="next col-xs-1">
					<button type="button" class="next-button" ng-click="listVm.next()"><span
							class="dashboard-icon icon-right-single-arrow"></span></button>
				</div>
			</div>
			<div class="col-xs-1 more-icons">
				<a href="" ng-click="listVm.showIconsOnPhone = !listVm.showIconsOnPhone">
					<i class="glyphicon glyphicon-option-vertical"></i>
				</a>
				<div ng-if="listVm.showIconsOnPhone" class="more-icons-block">
					<div class="icon-group">
						<div class="cases-filter tool-item" ng-show="::(!loggedUser.isSatelliteOffice())">
							<a href="" class="btn-filters icon" ng-click="listVm.showFilter()">
								<i class="icon-filter" ng-class="{'tablet': view.isTablet()}" ng-class="{active: listVm.isFilterShowed}" uib-tooltip="Filter"></i>
							</a>
						</div>
						<div class="cases-settings tool-item" ng-show="permissions.hasAccess('schedule', 'view_settings')">
							<a href="" class="btn-settings icon" ng-click="listVm.showSettings()">
								<i class="icon-gear" ng-class="{'tablet': view.isTablet()}" ng-class="{active: listVm.isSettingsShowed}" uib-tooltip="Settings"></i>
							</a>
						</div>
						<div class="cases-print tool-item">
							<a href="" class="btn-print icon" ng-click="listVm.updateNotesForCases()" print-overview="{{listVm.getPrintUrl()}}">
								<i class="icon-circle-print-small" ng-class="{'tablet': view.isTablet()}" uib-tooltip="Print"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div ng-if="listVm.isSettingsShowed" class="dashboard--settings" ng-include="view.get('cases/dashboard/settings.html')"></div>

		<div ng-if="listVm.isFilterShowed" class="dashboard--filters">
			<div class="row group-by-types">
				<div class="col-sm-2">
					<label class="group-by">Group by</label>
				</div>
				<div class="col-sm-2">
					<div class="data-row radio">
						<input type="radio" ng-model="listVm.groupType" ng-change="listVm.changeGroupType()" id="groupBySurgeon" value="surgeon">
						<label for="groupBySurgeon">Surgeon</label>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="data-row radio">
						<input type="radio" ng-model="listVm.groupType" ng-change="listVm.changeGroupType()" id="groupByRoom" value="room">
						<label for="groupByRoom">Room</label>
					</div>
				</div>
			</div>
			<filters-panel ctrl="listVm">
				<div class="data-row">
					<label>Procedure</label>
					<opk-select class="long-options" ng-model="listVm.search_params.procedure"
								options="type.id as type.full_name for type in source.getCaseTypes($query)"></opk-select>
				</div>
				<div class="data-row" ng-show="permissions.hasAccess('cases', 'edit_assigned_users')">
					<label>Surgeon</label>
					<opk-select ng-model="listVm.search_params.doctor"
								options="doctor.id as doctor.fullname for doctor in source.getSurgeons()"></opk-select>
				</div>
				<div class="data-row">
					<label>Patient Name</label>
					<input type="text" ng-model="listVm.search_params.patient_name" class='form-control input-sm'
						   placeholder='Patient Name'/>
				</div>
				<div class="data-row">
					<label>Room</label>
					<opk-select ng-model="listVm.search_params.location"
								options="item.name as item.name for item in source.getLocations()"></opk-select>
				</div>
			</filters-panel>
		</div>

		<div ng-cloak show-loading-list="listVm.isShowLoading">
			<div class="table-wrap dashboard--table" ng-if="listVm.group_cases.length">
				<uib-accordion close-others="false">
					<uib-accordion-group ng-repeat="group_cases in listVm.group_cases" is-open="group_cases.open">
						<uib-accordion-heading>
							<i ng-class="{'icon-caret-down': group_cases.open, 'icon-caret-right': !group_cases.open}"></i>
							{{ ::group_cases.header }}
						</uib-accordion-heading>

						<table class="opake" ng-if="!view.isPhone()">
							<thead>
							<tr>
								<th ng-if="::listVm.isViewTypeDay()" class="case-time--day-view">Time</th>
								<th ng-if="::listVm.isViewTypeWeek()" class="case-time--week-view">DOS</th>
								<th class="case-surgeon" ng-if="::(listVm.groupType === 'room')">Surgeon</th>
								<th class="case-room" ng-if="::(listVm.groupType === 'surgeon')">Room</th>
								<th class="case-patient">Name</th>
								<th class="case-procedure">Procedure</th>
								<th ng-if="view.isPC() && !loggedUser.isAnesthesiologist() && !loggedUser.isBiller()" class="case-status">Status</th>
								<th ng-if="view.isPC()" class="text-center case-notes"></th>
								<th ng-if="view.isPC()" class="text-center case-transport"></th>
								<th ng-if="view.isPC() && listVm.displayPointOfContact" class="text-center case-phone"></th>
								<th ng-if="view.isPC()" class="text-center case-print"></th>
								<th ng-if="view.isTablet()" class="case-more"></th>
							</tr>
							</thead>
							<tbody ng-repeat="case in group_cases.cases" ng-controller="CaseIntakeCrtl as intakeVm" ng-init="intakeVm.init(case)">
								<!-- Case -->
								<tr ng-if="::!case.is_in_service">
									<td ng-class="::{'case-time--day-view': listVm.isViewTypeDay(),
											'case-time--week-view': listVm.isViewTypeWeek(),
											'text-red': listVm.isStartTimeHighlightInRed(case)}">
										{{ ::case.time_start | date: listVm.getStartTimeFormat() }} <br/>
										{{ ::case.time_start | timeLength : case.time_end }}
									</td>
									<td ng-if="listVm.groupType === 'room'" class="case-surgeon">
										{{ ::case.first_surgeon_for_dashboard }}
									</td>
									<td ng-if="listVm.groupType === 'surgeon'" class="case-room">
										{{ ::case.location.name }}
									</td>
									<td class="case-patient">
										<a href="" ng-if="::listVm.hasCaseManagementAccess"
										   ng-href="/cases/{{::org_id}}/cm/{{::case.id}}"
										   uib-tooltip-html="listVm.getPatientTooltipStr(case.patient)">
											{{ ::case.patient.full_name }}
										</a>
										<span ng-if="::!listVm.hasCaseManagementAccess"
											  uib-tooltip-html="listVm.getPatientTooltipStr(case.patient)">
											{{ ::case.patient.full_name }}
										</span>
									</td>
									<td class="case-procedure">
										<a href="" ng-if="::listVm.hasCaseManagementAccess" ng-href="/cases/{{::org_id}}/cm/{{::case.id}}">
											{{ ::case.procedure_name_for_dashboard }}
										</a>
										<span ng-if="::(!listVm.hasCaseManagementAccess)"> {{ ::case.type.full_name }} </span>
										<div class="italicized-text">{{ ::case.description }}</div>
									</td>
									<td class="case-status" ng-if="view.isPC() && !loggedUser.isAnesthesiologist() && !loggedUser.isBiller()">
										<div ng-if="listVm.hasCaseChangeStatusAccess && case.isAppointmentNew() && listVm.isShowHeavyElements">
											<opk-select class="small" ng-model="intakeVm.appointment_status" placeholder="Select"
														key-value-options="intakeVm.appointment_statuses" change="intakeVm.changeAppointmentStatusFromDashboard()">
											</opk-select>
										</div>
										<div class="status checked-in row" ng-if="case.isAppointmentCompleted()"
											 ng-click="intakeVm.openCheckInInfo()" ng-class="::{'openable': listVm.hasCaseChangeStatusAccess}">
											<div class="col-sm-3 checked-in-icon">
												<i class="icon-green-checkmark"></i>
											</div>
											<div class="col-sm-9 checked-in-text">
												Checked In {{ case.time_check_in | date:'h:mm a' }}
											</div>
										</div>
									</td>
									<td ng-if="view.isPC()" class="case-notes text-center" ng-controller="CaseNoteCrtl as noteVm">
										<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(case.id)">
											<i ng-class="{'icon-note': !noteVm.caseNotes.hasFlaggedNotes(case), 'icon-notes-red': noteVm.caseNotes.hasFlaggedNotes(case)}" 
											   uib-tooltip="Comments"></i>
											<span class="badge" ng-if="noteVm.caseNotes.getNotesCount(case)"
												  ng-class="{'blue': noteVm.caseNotes.hasUnreadNotes[case.id]}">
												{{ noteVm.caseNotes.getNotesCount(case) }}
											</span>
										</a>
									</td>
									<td ng-if="view.isPC()" class="text-center case-transport">
										<i ng-if="::case.transport" class="icon-taxi" uib-tooltip-html="case.transport"></i>
									</td>
									<td ng-if="view.isPC() && listVm.displayPointOfContact" class="text-center case-phone">
										<a ng-if="listVm.validatePointOfContactPhone(case)" href="" ng-click="listVm.sendSMS(case)">
											<i ng-class="{'icon-iphone': !case.is_sent_sms, 'icon-iphone-grey': case.is_sent_sms,}" tooltip-class="point-of-sms" uib-tooltip="Send SMS to Point-of-Contact"></i>
										</a>
									</td>
									<td ng-if="view.isPC()" class="text-center case-print">
										<i class="icon-circle-print" uib-tooltip="Charts" ng-click="chartsVm.openPrintModal(case)"></i>
									</td>
									<td ng-if="view.isTablet()" class="case-more">
										<a href="" ng-click="case.showMoreActions = !case.showMoreActions">
											<i class="glyphicon glyphicon-option-vertical"></i>
										</a>
										<div ng-if="case.showMoreActions" class="more-actions">
											<div class="header row">
												<div class="col-sm-11">More Actions</div>
												<div class="col-sm-1">
													<a href="" ng-click="case.showMoreActions = false">
														<i class="icon-close-x"></i>
													</a>
												</div>
											</div>
											<div class="body">
												<ul class="options">
													<li ng-if="!loggedUser.isAnesthesiologist()" class="select-status">
														<div class="icon"><i class="icon-blue-sign"></i></div>
														<div ng-if="listVm.hasCaseChangeStatusAccess && case.isAppointmentNew() && listVm.isShowHeavyElements">
															<opk-select ng-model="intakeVm.appointment_status" placeholder="Select Status"
																		key-value-options="intakeVm.appointment_statuses" 
																		change="intakeVm.changeAppointmentStatusFromDashboard(); case.showMoreActions = false;">
															</opk-select>
														</div>
														<div class="checked-in" ng-if="case.isAppointmentCompleted()"
															 ng-click="intakeVm.openCheckInInfo(); case.showMoreActions = false;"
															 ng-class="::{'openable': listVm.hasCaseChangeStatusAccess}">
																Checked In 
														</div>
													</li>
													<li ng-controller="CaseNoteCrtl as noteVm" ng-click="noteVm.openNotesDialog(case.id)" class="pointer">
														<div class="icon"><i class="icon-note"></i></div>
														<div>Comment</div>
														<span class="badge" ng-if="noteVm.caseNotes.getNotesCount(case)"
															  ng-class="{'blue': noteVm.caseNotes.hasUnreadNotes[case.id]}">
															{{ noteVm.caseNotes.getNotesCount(case) }}
														</span>
													</li>
													<li ng-if="listVm.displayPointOfContact && listVm.validatePointOfContactPhone(case)" ng-click="listVm.sendSMS(case)" class="pointer">
														<div class="icon"><i class="icon-iphone"></i></div>
														<div>Send SMS</div>
													</li>
													<li ng-click="chartsVm.openPrintModal(case)" class="pointer">
														<div class="icon"><i class="icon-print-blue"></i></div>
														<div>Print Charts</div>
													</li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
								<tr ng-if="intakeVm.showCheckInInfo == true">
									<td class="checkin-info" colspan="4">
										<div class="row accompanied-fields">
											<div class="col-sm-4 accompanied-by">
												<label>Accompanied By <span class="optional">(optional)</span></label>
												<input type="text" ng-model="intakeVm.case.accompanied_by" class="form-control input-sm" placeholder="Type"/>
											</div>
											<div class="col-sm-3 phone-row">
												<label>Phone Number <span class="optional">(optional)</span></label>
												<phone ng-model="intakeVm.case.accompanied_phone"></phone>
											</div>
											<div class="col-sm-5 email">
												<label>Email <span class="optional">(optional)</span></label>
												<input type="text" ng-model="intakeVm.case.accompanied_email" class="form-control input-sm" placeholder="Type"/>
											</div>
										</div>
										<div class="row uploaded-docs">
											<div class="col-sm-7">
												<div class="data-row underline-doc-upload-label">
													<label>Driver License <br> <span class="optional">(optional)</span></label>
													<div ng-show="intakeVm.case.drivers_license.url" class="uploaded-file">
														<a href="" class="icon doc-icon" ng-click="intakeVm.preview(intakeVm.case.drivers_license)">
															<i class="icon-note"></i>
														</a>
														<div class="uploaded-date">
															<a href="" ng-click="intakeVm.preview(intakeVm.case.drivers_license)">Uploaded: {{ intakeVm.case.drivers_license.uploaded_date | date:'M/d/yyyy'}}</a>
														</div>
														<div class="upload-new">
															<a href=""
															   uib-tooltip="Upload"
															   class="btn-file"
															   file-upload
															   upload-url="intakeVm.driverLicenseUploadUrl"
															   on-complete="intakeVm.refreshFiles()">
																Upload New
															</a>
														</div>
													</div>
													<div ng-show="!intakeVm.case.drivers_license.url" class="uploaded-file">
														<a href=""
														   uib-tooltip="Upload"
														   class="btn-file"
														   file-upload
														   upload-url="intakeVm.driverLicenseUploadUrl"
														   on-complete="intakeVm.refreshFiles()">
															<i class="icon-circle-upload"></i>
														</a>
													</div>
												</div>
											</div>
											<div class="col-sm-5">
												<div class="data-row underline-doc-upload-label">
													<label class="insurance-card">Insurance Card<br> <span class="optional">(optional)</span></label>
													<div ng-show="intakeVm.case.insurance_card.url" class="uploaded-file">
														<a href="" class="icon doc-icon" ng-click="intakeVm.preview(intakeVm.case.insurance_card)">
															<i class="icon-note"></i>
														</a>
														<div class="uploaded-date">
															<a href="" ng-click="intakeVm.preview(intakeVm.case.insurance_card)">Uploaded: {{ intakeVm.case.insurance_card.uploaded_date | date:'M/d/yyyy'}}</a>
														</div>
														<div class="upload-new">
															<a href=""
															   uib-tooltip="Upload"
															   class="btn-file"
															   file-upload
															   upload-url="intakeVm.insuranceCardUploadUrl"
															   on-complete="intakeVm.refreshFiles()">
																Upload New
															</a>
														</div>
													</div>
													<div ng-show="!intakeVm.case.insurance_card.url" class="uploaded-file">
														<a href=""
														   uib-tooltip="Upload"
														   class="btn-file"
														   file-upload
														   upload-url="intakeVm.insuranceCardUploadUrl"
														   on-complete="intakeVm.refreshFiles()">
															<i class="icon-circle-upload"></i>
														</a>
													</div>
												</div>
											</div>
										</div>
									</td>
									<td class="checkin-buttons" colspan="3">
										<div class="cancel-link">
											<a href="" ng-click="intakeVm.cancelCheckIn()">
												<i class="icon-close-x"></i>
											</a>
										</div>
										<div class="complete-button">
											<a ng-if="!intakeVm.case.isAppointmentCompleted()" href="" class="btn btn-success" ng-click="intakeVm.completeCheckInFromDashboard()">Complete</a>
											<a ng-if="intakeVm.case.isAppointmentCompleted()" href="" class="btn btn-primary" ng-click="intakeVm.reOpenCheckInFromDashboard()">Re-Open</a>
										</div>
									</td>
								</tr>

								<!-- In Service -->
								<tr ng-if="::case.is_in_service">
									<td ng-class="::{'case-time--day-view': listVm.isViewTypeDay(),
											'case-time--week-view': listVm.isViewTypeWeek(),
											'text-red': listVm.isStartTimeHighlightInRed(case)}">
										{{ ::case.time_start | date: listVm.getStartTimeFormat() }} <br/>
										{{ ::case.time_start | timeLength : case.time_end }}
									</td>
									<td class="case-room"></td>
									<td class="case-patient"></td>
									<td class="case-procedure">
										{{ ::case.title }}
										<div class="italicized-text">{{ ::case.description }}</div>
									</td>
									<td class="case-status" ng-if="view.isPC() && !loggedUser.isAnesthesiologist() && !loggedUser.isBiller()"></td>
									<td ng-if="view.isPC()" class="case-notes text-center" ng-controller="InServiceNoteCrtl as noteVm">
										<a class="add-note-link" href="" ng-click="noteVm.openNotesDialog(case.id)">
											<i class="icon-note" uib-tooltip="Comments"></i>
											<span class="badge" ng-if="noteVm.inServiceNotes.getNotesCount(case)"
												  ng-class="{'blue': noteVm.inServiceNotes.hasUnreadNotes[case.id]}">
												{{ noteVm.inServiceNotes.getNotesCount(case) }}
											</span>
										</a>
									</td>
									<td ng-if="view.isPC()"  class="case-transport"></td>
									<td ng-if="view.isPC() && listVm.displayPointOfContact" class="case-phone"></td>
									<td ng-if="view.isPC()" class="case-print"></td>
									<td ng-if="view.isTablet()" class="case-more">
										<a href="" ng-click="case.showMoreActions = !case.showMoreActions">
											<i class="glyphicon glyphicon-option-vertical"></i>
										</a>
										<div ng-if="case.showMoreActions" class="more-actions">
											<div class="header row">
												<div class="col-sm-11">More Actions</div>
												<div class="col-sm-1">
													<a href="" ng-click="case.showMoreActions = false">
														<i class="icon-close-x"></i>
													</a>
												</div>
											</div>
											<div class="body">
												<ul class="options">
													<li ng-controller="InServiceNoteCrtl as noteVm" ng-click="noteVm.openNotesDialog(case.id)" class="pointer">
														<div class="icon"><i class="icon-note"></i></div>
														<div>Comment</div>
														<span class="badge" ng-if="noteVm.inServiceNotes.getNotesCount(case)"
															  ng-class="{'blue': noteVm.inServiceNotes.hasUnreadNotes[case.id]}">
															{{ noteVm.inServiceNotes.getNotesCount(case) }}
														</span>
													</li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>

						<div ng-if="view.isPhone()" class="phone-items-block">
							<div ng-repeat="case in group_cases.cases" ng-controller="CaseIntakeCrtl as intakeVm" ng-init="intakeVm.init(case)">
							<!-- Case -->
								<div ng-if="::!case.is_in_service" class="phone-case-item row">
									<div class="col-xs-10">
										<div class="time">
											<span class="field-label">Time</span> <br/>
											<span class="text">
												{{ ::case.time_start | date: listVm.getStartTimeFormat() }},
												{{ ::case.time_start | timeLength : case.time_end }}
											</span>
										</div>
										<div class="time" ng-if="listVm.groupType === 'room'" >
											<span class="field-label">Surgeon</span> <br/>
											<span class="text">
												{{ ::case.first_surgeon_for_dashboard }}
											</span>
										</div>
										<div class="time" ng-if="listVm.groupType === 'surgeon'" >
											<span class="field-label">Room</span> <br/>
											<span class="text">
												{{ ::case.location.name }}
											</span>
										</div>
										<div class="procedure">
											<span class="field-label">Procedure</span> <br/>
											<a class="text" href="" ng-if="::listVm.hasCaseManagementAccess" ng-href="/cases/{{::org_id}}/cm/{{::case.id}}">
												{{ ::case.procedure_name_for_dashboard }}
											</a>
											<span class="text" ng-if="::(!listVm.hasCaseManagementAccess)"> {{ ::case.type.full_name }} </span>
											<br/>
											<span class="description">
												{{ ::case.description }}
											</span>
										</div>
									</div>
									<div class="col-xs-2 more-actions-link">
										<a href="" ng-click="case.showMoreActions = !case.showMoreActions">
											<i class="glyphicon glyphicon-option-vertical"></i>
										</a>
										<div ng-if="case.showMoreActions" class="more-actions">
											<div class="header row">
												<div class="col-xs-10">More Actions</div>
												<div class="col-xs-2">
													<a href="" ng-click="case.showMoreActions = false">
														<i class="icon-close-x"></i>
													</a>
												</div>
											</div>
											<div class="body">
												<ul class="options">
													<li ng-if="!loggedUser.isAnesthesiologist()" class="select-status">
														<div class="icon"><i class="icon-blue-sign"></i></div>
														<div ng-if="listVm.hasCaseChangeStatusAccess && case.isAppointmentNew() && listVm.isShowHeavyElements">
															<opk-select ng-model="intakeVm.appointment_status" placeholder="Select Status"
																		key-value-options="intakeVm.appointment_statuses"
																		change="intakeVm.changeAppointmentStatusFromDashboard(); case.showMoreActions = false;">
															</opk-select>
														</div>
														<div class="checked-in" ng-if="case.isAppointmentCompleted()"
															 ng-click="intakeVm.openCheckInInfo(); case.showMoreActions = false;"
															 ng-class="::{'openable': listVm.hasCaseChangeStatusAccess}">
															Checked In
														</div>
													</li>
													<li ng-controller="CaseNoteCrtl as noteVm" ng-click="noteVm.openNotesDialog(case.id)" class="pointer">
														<div class="icon"><i class="icon-note"></i></div>
														<div>Comment</div>
														<span class="badge" ng-if="noteVm.caseNotes.getNotesCount(case)"
															  ng-class="{'blue': noteVm.caseNotes.hasUnreadNotes[case.id]}">
															{{ noteVm.caseNotes.getNotesCount(case) }}
														</span>
													</li>
													<li ng-if="listVm.displayPointOfContact && listVm.validatePointOfContactPhone(case)" ng-click="listVm.sendSMS(case)" class="pointer">
														<div class="icon"><i class="icon-iphone"></i></div>
														<div>Send SMS</div>
													</li>
													<li ng-click="chartsVm.openPrintModal(case)" class="pointer">
														<div class="icon"><i class="icon-print-blue"></i></div>
														<div>Print Charts</div>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>

							<!-- In Service -->
								<div ng-if="::case.is_in_service" class="phone-case-item row">
									<div class="col-xs-10">
										<div class="time">
											<span class="field-label">Time</span> <br/>
											<span class="text">
												{{ ::case.time_start | date: listVm.getStartTimeFormat() }},
												{{ ::case.time_start | timeLength : case.time_end }}
											</span>
										</div>
										<div class="procedure">
											<span class="field-label">Procedure</span> <br/>
											<span class="text"> {{ ::case.title }} </span>
											<br/>
											<span class="description">
												{{ ::case.description }}
											</span>
										</div>
									</div>
									<div class="col-xs-2 more-actions-link">
										<a href="" ng-click="case.showMoreActions = !case.showMoreActions">
											<i class="glyphicon glyphicon-option-vertical"></i>
										</a>
										<div ng-if="case.showMoreActions" class="more-actions">
											<div class="header row">
												<div class="col-xs-10">More Actions</div>
												<div class="col-xs-2">
													<a href="" ng-click="case.showMoreActions = false">
														<i class="icon-close-x"></i>
													</a>
												</div>
											</div>
											<div class="body">
												<ul class="options">
													<li ng-controller="InServiceNoteCrtl as noteVm" ng-click="noteVm.openNotesDialog(case.id)" class="pointer">
														<div class="icon"><i class="icon-note"></i></div>
														<div>Comment</div>
														<span class="badge" ng-if="noteVm.inServiceNotes.getNotesCount(case)"
															  ng-class="{'blue': noteVm.inServiceNotes.hasUnreadNotes[case.id]}">
															{{ noteVm.inServiceNotes.getNotesCount(case) }}
														</span>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					</uib-accordion-group>
				</uib-accordion>
				<div class="scroll-to-top" ng-click="listVm.scrollToTop()">
					<h3>Scroll to top</h3>
				</div>
			</div>
			<div class="dashboard-no-cases" ng-if="listVm.group_cases && !listVm.group_cases.length">
				<h1>No Cases</h1>
			</div>
		</div>
	</div>

</div>
