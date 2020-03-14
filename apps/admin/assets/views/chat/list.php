<div ng-controller="ChatListCrtl as listVm" class="chat-log" ng-cloak>
	<div class="chat--header row">
		<div class="icon-group col-sm-3">
			<div class="tool-item">
				<date-icon ctrl="listVm" ng-model="listVm.date"></date-icon>
			</div>
			<div ng-hide="listVm.isToday()" class="tool-item">
				<a href="" ng-click="listVm.today()" class="today btn btn-grey">Today</a>
			</div>
		</div>
		<div class="chat-date col-sm-6">
			<div class="prev col-sm-1">
				<button type="button" class="prev-button" ng-click="listVm.previous()"><span
						class="chat-icon icon-left-single-arrow"></span></button>
			</div>
			<div class="center col-sm-10">
				<h2>{{ listVm.getDateDisplay() }}</h2>
			</div>
			<div class="next col-sm-1">
				<button type="button" class="next-button" ng-click="listVm.next()"><span
						class="chat-icon icon-right-single-arrow"></span></button>
			</div>
		</div>
	</div>

	<div class="table-wrap chat--table">
		<div class="chat-log-header">Chat Log</div>
		<table class="opake" ng-if="listVm.messages.length">
			<thead>
			<tr>
				<th>User</th>
				<th>Time</th>
				<th>Text</th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="message in listVm.messages">
				<td>{{ message.user_full_name }}</td>
				<td>{{ message.date | date:'h:mm a' }}</td>
				<td>{{ message.text }}</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="chat--bottom">
		<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
			   callback="listVm.search()"></pages>
		<h4 ng-if="!listVm.messages.length">No messages found</h4>
	</div>
</div>
