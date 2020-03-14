<!doctype HTML>
<html>
<head>
	<title>Opake admin</title>
	<link rel="shortcut icon" href="/common/i/favicon.ico">
	<meta charset='utf-8'>
	<meta http-equiv='X-UA-Compatible' content='IE=edge'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<?= $this->getCssHtml() ?>
	<?= $this->getJSHtml(true) ?>
</head>
<body>

<?php include(__DIR__ . sprintf('/%s.php', $subview)) ?>

<?= $this->getJSHtml(false) ?>
</body>
</html>