<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
		<style type="text/css">
		</style>
	</head>
	<body class="operative-report-export-header">
		<table class="header">
			<tr>
				<td width="25%">
					RE: <?= $patient->last_name; ?>, <?= $patient->first_name; ?> <br/>
					Operative Report <br/>
					Page <span id="pageNumber"></span>
				</td>
				<td class="organization-name" width="50%">
					<?= $organization->name; ?> <br/>
					<span class="pre-wrap"><?= $case->getProvider(); ?></span>
				</td>
				<td class="account" width="25%">
					MRN #: <?= $patient->getFullMrn(); ?><br/>
					Account #: <?= $case->id; ?>
				</td>
			</tr>
		</table>
		<script>
			var vars = {};
			var x = window.location.search.substring(1).split('&');
			for (var i in x) {
				var z = x[i].split('=', 2);
				vars[z[0]] = unescape(z[1]);
			}
			document.getElementById('pageNumber').innerHTML = vars.page;
		</script>
	</body>
</html>