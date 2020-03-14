<!DOCTYPE html>
<html lang="en" ng-app="opake">
	<head>
		<base href="/<?= isset($portal) ? ($portal->alias . '/') : '' ?>">
		<title>Opake</title>
		<link rel="shortcut icon" href="/common/i/favicon.ico">
		<link rel="icon" type="image/png" href="/common/i/logo-blue-inverse.png" sizes="40x40">
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= $this->getCssHtml() ?>
		<?= $this->getJsHtml(true) ?>
	</head>
	<body ui-view state="{{$state.current.name}}">

		<?= $this->getJSHtml(false) ?>

		<script type="text/javascript">
			(function () {
				angular.module('opake').value('appInitData',
					<?= json_encode([
						'portal' => isset($portal) ? $portal->toArray() : []
					]) ?>
				);
			}());
		</script>
	</body>
</html>