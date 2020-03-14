<div class="operative-report--site-template operative-report panel-data" ng-controller="OperativeReportSiteTemplateCtrl as templateVm" ng-init="templateVm.init()" show-loading="templateVm.isShowLoading" ng-cloak>
	<div class="row">
		<errors src="templateVm.errors"></errors>
		<?php if ($_check_access('site_template', 'edit')): ?>
			<div class="col-sm-3 col-sm-offset-9 text-right">
				<button ng-if="templateVm.action === 'view'" class="btn btn-success" ng-click="templateVm.edit();">Edit Template</button>
				<button ng-if="templateVm.action === 'edit'" class="btn btn-success" ng-click="templateVm.save();">Save Template</button>
				<button ng-if="templateVm.action === 'edit'" class="btn btn-grey" ng-click="templateVm.cancel();">Cancel</button>
			</div>
		<?php endif;?>
	</div>
	<div class="row header">
		<div class="col-sm-5">
			<h4>Case Information</h4>
		</div>
	</div>

	<div class="case-data">
		<div class="row">
			<div class="col-sm-6">
				<div dnd-list="templateVm.chunkedSurgeons[0]"
					 dnd-allowed-types="[templateVm.templateConst.GROUPS.CASEINFO]">
					<div ng-repeat="item in templateVm.chunkedSurgeons[0]"
						 dnd-draggable="item"
						 dnd-moved="templateVm.chunkedSurgeons[0].splice($index, 1);"
						 dnd-effect-allowed="move"
						 dnd-dragend="templateVm.reindexSurgeonColumns()"
						 dnd-type="item.group_id"
						 dnd-nodrag
						class="row">
						<div class="col-sm-12">
							<div class="data-row">
								<div class="switch-container"><switch ng-disabled="templateVm.action === 'view'" class="green" ng-model="item.active"></switch><label>{{item.name}}</label></div>
								<date-field ng-if="item.type === 'date'" ng-disabled="true" class="" ng-model="item" icon="true" placeholder=""></date-field>
								<div class="input-container"><input disabled  ng-if="item.type !== 'date'" class="form-control disabled"></div>
								<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div dnd-list="templateVm.chunkedSurgeons[1]"
					 dnd-allowed-types="[templateVm.templateConst.GROUPS.CASEINFO]">
					<div ng-repeat="item in templateVm.chunkedSurgeons[1]"
						 dnd-draggable="item"
						 dnd-moved="templateVm.chunkedSurgeons[1].splice($index, 1)"
						 dnd-dragend="templateVm.reindexSurgeonColumns()"
						 dnd-effect-allowed="move"
						 dnd-type="item.group_id"
						 dnd-nodrag>
							<div class="row">
								<div class="col-sm-12">
									<div class="data-row">
										<div class="switch-container"><switch ng-disabled="templateVm.action === 'view'" class="green" ng-model="item.active"></switch><label>{{item.name}}</label></div>
										<date-field ng-if="item.type === 'date'" ng-disabled="true" class="" ng-model="item" icon="true" placeholder=""></date-field>
										<div class="input-container"><input disabled  ng-if="item.type !== 'date'" class="form-control disabled"></div>
										<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="report-fields">
		<div class="row header">
			<div class="col-sm-5">
				<h4>Descriptions</h4>
			</div>
		</div>
		<div dnd-list="templateVm.template[templateVm.templateConst.GROUPS.DESCRIPTIONS]"
			 dnd-allowed-types="templateVm.allowedTypes">
			<div class="row"
				 ng-repeat="item in templateVm.template[templateVm.templateConst.GROUPS.DESCRIPTIONS]"
				 dnd-draggable="item"
				 dnd-moved="templateVm.template[templateVm.templateConst.GROUPS.DESCRIPTIONS].splice($index, 1);"
				 dnd-effect-allowed="move"
				 dnd-type="item.group_id"
				 dnd-nodrag>
				<div class="col-sm-12">
					<div class="data-row">
						<switch class="green" ng-disabled="templateVm.action === 'view'" ng-model="item.active"></switch>
						<label>{{item.name}}</label>
					</div>
					<div class="data-row">
						<div class="form-control disabled"></div>
						<a ng-if="item.field === 'custom' && templateVm.action === 'edit'" href="" class="delete"><i class="icon-remove" uib-tooltip="Delete" tooltip-class="red" ng-click="templateVm.removeCustomField(item);"></i></a>
						<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
					</div>
				</div>
			</div>
		</div>

		<div ng-if="templateVm.action === 'edit'" class="data-row" >
			<a href="" ng-click="templateVm.addCustomField(templateVm.templateConst.GROUPS.DESCRIPTIONS)">+Add Custom Field</a>
		</div>

		<div class="row header">
			<div class="col-sm-5">
				<h4>Materials</h4>
			</div>
		</div>
		<div dnd-list="templateVm.template[templateVm.templateConst.GROUPS.MATERIALS]"
			 dnd-allowed-types="templateVm.allowedTypes">
			<div class="row"
				 ng-repeat="item in templateVm.template[templateVm.templateConst.GROUPS.MATERIALS]"
				 dnd-draggable="item"
				 dnd-moved="templateVm.template[templateVm.templateConst.GROUPS.MATERIALS].splice($index, 1);"
				 dnd-effect-allowed="move"
				 dnd-type="item.group_id"
				 dnd-nodrag>
				<div class="col-sm-12">
					<div class="data-row">
						<switch class="green" ng-disabled="templateVm.action === 'view'" ng-model="item.active" ></switch>
						<label>{{item.name}}</label>
					</div>
					<div class="data-row">
						<div class="form-control disabled"></div>
						<a ng-if="item.field === 'custom' && templateVm.action === 'edit'" href="" class="delete"><i class="icon-remove" uib-tooltip="Delete" tooltip-class="red" ng-click="templateVm.removeCustomField(item);"></i></a>
						<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
					</div>
				</div>
			</div>
		</div>
		<div ng-if="templateVm.action === 'edit'" class="data-row" >
			<a href="" ng-click="templateVm.addCustomField(templateVm.templateConst.GROUPS.MATERIALS)">+Add Custom Field</a>
		</div>

		<div class="row header">
			<div class="col-sm-5">
				<h4>Conclusions</h4>
			</div>
		</div>
		<div dnd-list="templateVm.template[templateVm.templateConst.GROUPS.CONCLUSIONS]"
			 dnd-allowed-types="templateVm.allowedTypes">
			<div class="row"
				 ng-repeat="item in templateVm.template[templateVm.templateConst.GROUPS.CONCLUSIONS]"
				 dnd-draggable="item"
				 dnd-moved="templateVm.template[templateVm.templateConst.GROUPS.CONCLUSIONS].splice($index, 1);"
				 dnd-effect-allowed="move"
				 dnd-type="item.group_id"
				 dnd-nodrag>
				<div class="col-sm-12">
					<div class="data-row">
						<switch class="green" ng-disabled="templateVm.action === 'view'" ng-model="item.active" ></switch>
						<label>{{item.name}}</label>
					</div>
					<div class="data-row">
						<div class="form-control disabled"></div>
							<a ng-if="item.field === 'custom' && templateVm.action === 'edit'" href="" class="delete"><i class="icon-remove" uib-tooltip="Delete" tooltip-class="red" ng-click="templateVm.removeCustomField(item);"></i></a>
							<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
					</div>
				</div>
			</div>
		</div>

		<div ng-if="templateVm.action === 'edit'" class="data-row" >
			<a href="" ng-click="templateVm.addCustomField(templateVm.templateConst.GROUPS.CONCLUSIONS)">+Add Custom Field</a>
		</div>

		<div class="row header">
			<div class="col-sm-5">
				<h4>Follow Up</h4>
			</div>
		</div>
		<div dnd-list="templateVm.template[templateVm.templateConst.GROUPS.FOLLOW_UP]"
			 dnd-allowed-types="templateVm.allowedTypes">
			<div class="row"
				 ng-repeat="item in templateVm.template[templateVm.templateConst.GROUPS.FOLLOW_UP]"
				 dnd-draggable="item"
				 dnd-moved="templateVm.template[templateVm.templateConst.GROUPS.FOLLOW_UP].splice($index, 1);"
				 dnd-effect-allowed="move"
				 dnd-type="item.group_id"
				 dnd-nodrag>
				<div class="col-sm-12">
					<div class="data-row">
						<switch class="green" ng-disabled="templateVm.action === 'view'" ng-model="item.active" ></switch>
						<label>{{item.name}}</label>
					</div>
					<div class="data-row">
						<div class="form-control disabled"></div>
						<a ng-if="item.field === 'custom' && templateVm.action === 'edit'" href="" class="delete"><i class="icon-remove" uib-tooltip="Delete" tooltip-class="red" ng-click="templateVm.removeCustomField(item);"></i></a>
						<div ng-if="templateVm.action === 'edit'" dnd-handle class="handle"><i class="icon-drag-n-drop"></i></div>
					</div>
				</div>
			</div>
		</div>
		<div ng-if="templateVm.action === 'edit'" class="data-row">
			<a href="" ng-click="templateVm.addCustomField(templateVm.templateConst.GROUPS.FOLLOW_UP)">+Add Custom Field</a>
		</div>
	</div>
</div>