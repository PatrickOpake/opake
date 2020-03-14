<div ng-controller="PatientsPortalUserDatabaseCtrl as listVm" ng-cloak class="patients-user-database">
	<div class="content-block insurance-database-list patient-list">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Name</label>
				<input type="text" class="form-control input-sm" ng-model="listVm.search_params.name" />
			</div>
			<div class="data-row">
				<label>Email</label>
				<input type="text" class="form-control input-sm" ng-model="listVm.search_params.email" />
			</div>
		</filters-panel>
	</div>

	<div ng-show="listVm.isDataLoaded">
		<table class="opake">
			<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th sort="name" class="text-center">Full Name</th>
				<th sort="email" class="text-center">Email</th>
				<th sort="first_login_date" class="text-center">First Login Date</th>
				<th sort="last_login_date" class="text-center">Last Login Date</th>
				<th sort="organization" class="text-center" width="250px">Organization Name</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="item in listVm.items">
				<td class="text-center"><a href="/patient-users/internal/view/{{::item.id}}" >{{::item.full_name}}</a></td>
				<td class="text-center">{{::item.email}}</a></td>
				<td class="text-center">{{::item.first_login_date |  date:'M/d/yyyy hh:mm a'}}</td>
				<td class="text-center">{{::item.last_login_date |  date:'M/d/yyyy hh:mm a'}}</td>
				<td class="text-center">{{::item.organization_name}}</td>
			</tr>
			<tr ng-if="!listVm.items.length">
				<th colspan="5" class="text-center">
					<h4>Users not found</h4>
				</th>
			</tr>
			</tbody>
		</table>
		<pages count="listVm.totalCount" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
	</div>

</div>

