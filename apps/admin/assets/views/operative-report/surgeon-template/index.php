<div class="panel-data operative-report--list" ng-controller="OperativeReportSurgeonTemplateListCrtl as listVm" ng-init="listVm.init(<?php echo isset($user_id) && $user_id ? $user_id : '' ?>)" ng-cloak>
	<div class="main-control">
		<div class="pull-left">
			<?php if(isset($user) && $user):?>
				<h3 class="title"><?= $user->getFullName()?></h3>
			<?php endif;?>
		</div>
		<div class="pull-right" ng-show="permissions.hasAccess('surgeon_templates', 'create', listVm.user)">
			<a class="btn btn-success" href="" ng-click="listVm.createTemplate()">Create Template</a>
		</div>
	</div>

	<errors src="listVm.errors"></errors>

	<table class="opake highlight-rows" ng-if="listVm.items.length">
		<thead sorter="listVm.search_params" callback="listVm.search()">
			<tr>
				<th sort="case_type">Procedure</th>
				<th sort="name">Template Name</th>
				<th sort="updated">Last Edited</th>
				<?php if($loggedUser->isInternal()):?>
				<th sort="doctor">Doctor</th>
				<?php endif;?>
				<th ng-show="permissions.hasAccess('surgeon_templates', 'edit', listVm.user)"></th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="item in listVm.items">
				<td ng-click="listVm.view(item)">{{ ::item.case_type.full_name }}</td>
				<td ng-click="listVm.view(item)"><a ng-click="listVm.view(item)" href="">{{ ::item.name }}</a></td>
				<td ng-click="listVm.view(item)">{{ ::item.updated | date:'M/d/yyyy' }}</td>
				<?php if($loggedUser->isInternal()):?>
					<td>
						<span ng-repeat="user in item.surgeons">
							{{ ::user.fullname }} {{ $last ? '' : ', ' }}
						</span>
					</td>
				<?php endif;?>
				<td ng-show="permissions.hasAccess('surgeon_templates', 'edit', listVm.user)">
					<a href="" ng-click="listVm.removeTemplate(item.id)"><i class="icon-remove"></i></a>
				</td>
			</tr>
		</tbody>

	</table>
	<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l" callback="listVm.search()"></pages>
	<h4 ng-if="listVm.items && !listVm.items.length">Templates not found</h4>
</div>