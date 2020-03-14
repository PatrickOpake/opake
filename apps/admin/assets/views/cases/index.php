<div ng-controller="CaseListCrtl as listVm" ng-init="listVm.init(); calendar.iniCase();" ng-cloak>

	<div class="cases-calendar-main" ng-if="listVm.loaded">
		<div class="cases-calendar--header main-control" ng-if="!view.isWaitingRoom">
			<a ng-if="view.isTablet()" class="btn btn-grey more-button" ng-click="listVm.showMoreActions = !listVm.showMoreActions">
				More <i class="glyphicon glyphicon-triangle-bottom"></i>
			</a>
			<div ng-if="listVm.showMoreActions" class="cases-calendar--more">
				<?php include('_more.php'); ?>
			</div>

			<?php if ($_check_access('booking', 'create')) { ?>
				<a class="btn btn-success" ng-show="calendar.getAction() === 'calendar'" ng-click="listVm.createBooking()">Create Booking</a>
			<?php } ?>

			<div ng-if="view.isPC()">
				<?php include('_icons.php'); ?>
			</div>
		</div>
		<div class="cases-calendar--header main-control" ng-if="view.isWaitingRoom">
			<div class="icon-group">
				<a href="" class="back" ng-click="listVm.deactivateWaitingRoomMode()">
					<i class="glyphicon glyphicon-chevron-left"></i>
					Back
				</a>
			</div>
		</div>

		<div ng-if="calendar.getAction() === 'filters'">
			<ng-include class="content-block cases-calendar--filters" src="view.get('cases/filters.html')"></ng-include>
		</div>

		<div ng-if="calendar.getAction() === 'setting'" ng-controller="CaseSettingCtrl as settingVm"
			 ng-init="settingVm.init()">
			<ng-include class="panel-data cases-calendar--setting" src="view.get('cases/setting.html')"></ng-include>
		</div>

		<div case-calendar="listVm.cases_src"
		     rooms-full-list="listVm.rooms_full_list"
		     selected-rooms="listVm.search_params.room_list"
			 surgeons-src="listVm.surgeons_src"
			 new-case="calendar.newCase"
			 edited-case="calendar.editedCase"
		    ></div>
	</div>
</div>
