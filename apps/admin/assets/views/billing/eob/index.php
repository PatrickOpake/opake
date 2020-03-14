<div ng-controller="EobManagementListCtrl as listVm" class="billing-eob-management" ng-cloak>
	<div class="content-block">
		<filters-panel ctrl="listVm">
			<div class="data-row">
				<label>Insurer</label>
				<opk-select ng-model="listVm.search_params.insurer"
							options="item.name for item in source.getInsurances($query, true)"></opk-select>
			</div>
			<div class="data-row">
				<label>HCPCS/CPT</label>
				<opk-select ng-model="listVm.search_params.charge"
							options="item.cpt for item in source.getMaserChargeCPT($query)"></opk-select>
			</div>
		</filters-panel>
	</div>

	<div ng-if="!listVm.docsToUpload.length" class="content-block eob-table-block">
		<div class="list-control">
			<a href="" class="icon" ng-click="listVm.print()" ng-disabled="!listVm.toSelected.length">
				<i class="icon-print-grey" uib-tooltip="Print"></i>
			</a>
			<div class="loading-wheel" ng-if="listVm.isShowLoading">
				<div class="loading-spinner"></div>
			</div>
			<div class="pull-right">
				<a ng-if="!listVm.uploadingMode" ng-click="listVm.openUploadingMode()" class="btn btn-success" href="">Upload EOB</a>
			</div>
		</div>

		<div show-loading-list="listVm.isShowLoading">
			<table class="opake" ng-if="listVm.items.length">
				<thead sorter="listVm.search_params" callback="listVm.search()">
				<tr>
					<th>
						<div class="checkbox">
							<input id="print_all" type="checkbox" class="styled" ng-checked="listVm.selectAll" ng-click="listVm.addToSelectedAll()">
							<label for="print_all"></label>
						</div>
					</th>
					<th></th>
					<th sort="insurer">Insurer</th>
					<th sort="cpt">HCPCS/CPT</th>
					<th sort="charge_master_amount">Charge Master</th>
					<th sort="amount_reimbursed">Amount Reimbursed</th>
				</tr>
				</thead>
				<tbody>
				<tr ng-repeat="item in listVm.items">
					<td>
						<div class="checkbox">
							<input id="print_{{$index}}"
								   type="checkbox"
								   class="styled"
								   ng-checked="listVm.isAddedToSelected(item)"
								   ng-click="listVm.addToSelected(item)">
							<label for="print_{{$index}}"></label>
						</div>
					</td>
					<td>
						<a href="" ng-click="listVm.previewEOB(item)">EOB</a>
					</td>
					<td>{{item.insurer_name}}</td>
					<td>{{item.cpt}}</td>
					<td>{{item.charge_master_amount}}</td>
					<td>{{item.amount_reimbursed}}</td>
				</tr>
				</tbody>
			</table>
			<pages count="listVm.total_count" page="listVm.search_params.p" limit="listVm.search_params.l"
				   callback="listVm.search()"></pages>
			<h4 ng-if="listVm.items && !listVm.items.length">Items not found</h4>
		</div>

		<div class="eob-uploading-block" ng-if="listVm.uploadingMode">
			<div class="file-drop-box" ngf-drop="listVm.uploadFiles($files)" ngf-drag-over-class="'drag-over'" ngf-multiple="true">
				<a href="" class="btn btn-grey cancel-upload" ng-click="listVm.closeUploadingMode()">Cancel</a>
				<div class="file-drop-box--help">
					<i class="icon-file-upload-cloud"></i> <br/>
					<span class="bold-text">Drag and drop files from your desktop <br/>
					or use the
					<button class="btn-file" select-file on-select="listVm.uploadFiles(files)"> file browser
						<input type="file" multiple name="fileDoc" />
					</button>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="eob-to-upload" ng-if="listVm.docsToUpload.length" ng-form name="EOBForUploadForm">
		<div class="text-right">
			<a class="btn btn-success" href="" ng-click="listVm.saveUploadedDocs()" ng-disabled="EOBForUploadForm.$invalid">Done</a>
		</div>
		<table class="opake">
			<thead>
			<tr>
				<th></th>
				<th>File Name</th>
				<th>Insurer</th>
				<th>HCPCS/CPT</th>
				<th>Charge Master</th>
				<th>Amount Paid</th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<tr ng-repeat="doc in listVm.docsToUpload">
				<td class="icon"><i class="icon-pdf"></i></td>
				<td class="name">{{ doc.name }}</td>
				<td class="insurer">
					<opk-select ng-model="doc.insurer"
								select-options="{appendToBody: true}"
								options="item.name for item in source.getInsurances($query, true)" ng-required="true" placeholder="Select Insurer"></opk-select>
				</td>
				<td class="cpt">
					<opk-select ng-model="doc.charge_master"
								select-options="{appendToBody: true}"
								options="item.cpt for item in source.getMaserChargeCPT($query)"
								ng-required="true"
								placeholder="Select CPT"
								ng-change="listVm.changeChargeCPT(doc)"></opk-select>
				</td>
				<td class="charge_master">
					<input type="text" class='form-control input-sm' valid-number type-number="float" ng-model="doc.charge_master_amount" ng-required="true" placeholder="Type">
				</td>
				<td class="amount_paid">
					<input type="text" class='form-control input-sm' valid-number type-number="float" ng-model="doc.amount_reimbursed" ng-required="true" placeholder="Type">
				</td>
				<td class="rename">
					<a href="" ng-click="listVm.renameUploadedDoc(doc)">
						Rename
					</a>
				</td>
				<td class="delete">
					<a href=""ng-click="listVm.removeUploadedDoc(doc)">
						<i class="icon-remove"></i>
					</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>