<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<table class="patient-labels">
			<?php for ($i = 1; $i <= 10; $i++): ?>
			<tr>
				<?php for ($j = 1; $j <= 3; $j++): ?>
				<td>
					<div class="patient">
						<span class="patient-name"> <?= $patient->getFullNameForLabel(); ?> </span> <br/>
						DOB: <?= date('m/d/Y', strtotime($patient->dob)); ?><span class="patient-age">AGE: <?= $patient->getAge(); ?></span> <br/>
						MRN#: <?= $patient->getFullMrn(); ?>
					</div>
					<div class="case">
						DOS: <?= date('m/d/Y', strtotime($case->time_start)); ?><br/>
						Dr. <span class="surgeon-name"><?= $case->getFirstSurgeonForLabel(); ?></span>
					</div>
				</td>
				<?php endfor; ?>
			</tr>
			<?php endfor; ?>
		</table>
	</body>
</html>