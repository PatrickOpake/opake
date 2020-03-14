<div class="content-block" ng-init="data = {}">
	<form name="import_form" action="" method='post' enctype='multipart/form-data'>
		<div class="form-group">
			<label>Procedure:</label>
			<opk-select ng-model="data.type" options="type.full_name for type in source.getCaseTypes($query)"></opk-select>
			<input type="hidden" name="type_id" value="{{data.type.id}}" />
		</div>
		<div class="form-group">
			<label>Room:</label>
			<opk-select ng-model="data.location" options="item.name for item in source.getLocations()"></opk-select>
			<input type="hidden" name="location_id" value="{{data.location.id}}" />
		</div>
		<div class="form-group">
			<label>Excel File:</label>
			<input type="file" name="file_import" />
		</div>
		<button class="btn btn-success" type="submit" value="">Submit</button>
	</form>
</div>