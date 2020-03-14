<script src='/vendors/tinymce/js/tinymce/tinymce.min.js' type='text/javascript'></script>

<form target="_blank" action='{{ docVm.previewUrl }}' method="POST" class="create-form-document"
      ng-controller="FormCustomCrtl as docVm" ng-init="docVm.init(<?= isset($model) ? $model->id : 'null' ?>)" ng-cloak>

	<errors src="docVm.errors"></errors>

	<div class="main-control">
		<div class="checkbox">
			<input id="custom-header" type="checkbox" class="styled" name="include_header" ng-model="docVm.form.include_header" />
			<label for="custom-header" tooltip-placement="bottom" tooltip-class="white" uib-tooltip="Check box to use custom header details below in form. Default header includes only organization info.">Include custom header details</label>
		</div>
		<div class="checkbox">
			<input id="landscape" type="checkbox" class="styled" name="is_landscape" ng-model="docVm.form.is_landscape" ng-change="docVm.updateWidth()" />
			<label for="landscape" tooltip-placement="bottom" tooltip-class="white">Landscape View</label>
		</div>
		<a href="" class="btn btn-success btn-shadow" ng-click="docVm.createDocument()">Save</a>
		<button class="btn btn-success btn-shadow">Preview</button>
		<div class="actions text-right">
			<a href="/settings/forms/charts/{{ ::org_id }}" class="create-form-close-x">
				<i class="icon-close-x"></i>
			</a>
		</div>
	</div>

	<div class="panel-data header">
		<div class="row">
			<div class="col-sm-3 border-right">
				<div class="data-row"><?= $org->name?></div>
				<div class="data-row"><?= $org->address?></div>
				<div class="data-row"><?= $_phone($org->contact_phone)?></div>
				<div class="data-row">
					<label>Dr. </label>
					<div class="form-control"></div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="data-row">
					<label>Patient Name: </label>
					<div class="form-control"></div>
				</div>
				<div class="data-row">
					<label>Date of Birth: </label>
					<div class="form-control"></div>
				</div>
				<div class="data-row">
					<label>Age/Sex: </label>
					<div class="form-control"></div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="data-row">
					<label>Date of Service: </label>
					<div class="form-control"></div>
				</div>
				<div class="data-row">
					<label>MRN: </label>
					<div class="form-control"></div>
				</div>
				<div class="data-row">
					<label>Account Number: </label>
					<div class="form-control"></div>
				</div>
			</div>
		</div>
	</div>


	<div class="panel-data">
		<div class="data-row">
			<label>Name*:</label>
			<input type="text" class="form-control" name="name" ng-model="docVm.form.name" />
		</div>
		<div style="text-align: right"><a href="#" ng-click="showDetails = ! showDetails">Add data tags</a> </div>
		<div class="dynamic-fields-tooltip" ng-show="showDetails">
			<a ng-click="showDetails = ! showDetails" class="close-button"><i class="glyphicon glyphicon-remove"></i></a>
			<div class="tooltip-header">
				To add data tags that will prefill with patient data from case details use the following tags in template text
				<br/>
				<br/>
			</div>
			<div class="tooltip-fields">
				<div class="tooltip-field">
					%LastName% - Patient's First Name <br/>
					%FirstName% - Patient's Last Name <br/>
					%Account% -  Patient's Account # <br/>
					%Age% - Patient's Age <br/>
					%DOB% - Patient's Date of Birth	<br/>
					%Gender% - Patient's Gender (Male/Female) <br/>
					%Gender2% - Patient's Gender (He/She) <br/>
					%Street% - Patient's Street Address <br/>
					%Apt% - Patient's Apt # <br/>
					%City% - City that the patient lives in <br/>
					%State% - State patient lives in <br/>
					%Country% - Country patient lives in <br/>
					%Zip% - Zip code patient lives in <br/>
				</div>
				<div class="tooltip-field">
					%MRN% - MRN of patient <br/>
					%Physician% - Name of the Physician assigned to the patient's case <br/>
					%DOS% - Date of Service for the patient's case <br/>
					%Insurance% - Name of the Primary Insurance company for patient <br/>
					%SiteName% - Name of the surgery center <br/>
					%SiteAddress% - Street address of surgery center <br/>
					%SiteCity% - City of the surgery center <br/>
					%SiteState% - State of the surgery center <br/>
					%SiteCountry% - Country of the surgery center <br/>
					%SiteZip% - Zip of the surgery center <br/>
					%SitePhone% - Phone number of the surgery center <br/>
				</div>
			</div>
		</div>
		<div class="data-row" ng-if="docVm.form">
			<div editor-regular="docVm.editorOptions" name="own_text" ng-model="docVm.form.own_text"></div>
		</div>
	</div>
</form>
