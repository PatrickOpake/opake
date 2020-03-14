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

<div class='container'>
	<?= isset($message) ? $message : ''; ?>
	<?php
	if (isset($subview)) {
		include(__DIR__ . sprintf('/%s.php', $subview));
	}
	?>
</div>


<?= $this->getJSHtml(false) ?>
</body>
</html>