<?php

if (!function_exists('formatMoney')) {
	function formatMoney($float)
	{
		return '$' . number_format((float)$float, 2, '.', ',');
	}
}

$countOfAllRows = 0;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?=$_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body class="patient-statement-export itemized-bill">
		<h1 class="text-center">Itemized Bill</h1>
		<?php foreach ($chunkedRows as $key => $part):?>
			<div class="page">
				<table>
					<tbody>
						<tr>
							<td class="main-info-block">
								<table>
									<tr>
										<td><?= $patient->organization->name?></td>
									</tr>
									<tr>
										<td><?= $patient->organization->address?></td>
									</tr>

									<tr>
										<td> <div><?= $patient->organization->contact_phone ? 'Phone: ' . $patient->organization->contact_phone : ''?></div>
											<div><?= $patient->organization->contact_phone ? 'Fax: ' . $patient->organization->contact_phone : ''?></div>
										</td>
									</tr>
								</table>

								<div class="patient-info-block">
									<table>
										<tr>
											<td>Patient:</td>
										</tr>
										<tr>
											<td><?= $patient->getFullName()?></td>
										</tr>
										<tr>
											<td><?= $patient->home_address?>, <?= $patient->home_apt_number?></td>
										</tr>
										<tr>
											<td><?= $patient->home_city->name?>, <?= $patient->home_state->name?>, <?= $patient->home_zip_code?></td>
										</tr>
									</table>
								</div>
							</td>
							<td class="top-right-block">
								<table class="border">
									<tr class="grey-background">
										<td>Date Range</td>
										<td>MRN</td>
										<td>Page #</td>
									</tr>
									<tr>
										<td><?= $dateFrom->format('m/d/Y') ?> - <?= $dateTo->format('m/d/Y') ?></td>
										<td><?= $patient->getFullMrn()?></td>
										<td><?= $key + 1?></td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>

				<table class="border">
						<thead>
							<tr class="responsibility-label-row">
								<th colspan="7"></th>
								<th colspan="4" class="responsibility-label-column">Patient Responsible Amount</th>
							</tr>
							<tr>
								<th>Date</th>
								<th>Code</th>
								<th>Description</th>
								<th>Charges</th>
								<th>Credits</th>
								<th>Balance</th>
								<th>Insurance Responsible Amount</th>
								<th>Co-Pay</th>
								<th>Co-Ins</th>
								<th>Deductible</th>
								<th>OOP</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($part as $item):?>
							<tr>
								<td><?= $_date($item['date'])?></td>
								<td><?= $item['type'] === 'case' ? $item['procedure'] : ''?></td>
								<td><?= $item['desc']?></td>
								<td class="money-cell"><?= $item['type'] === 'case' ? formatMoney($item['amount']) : ''?></td>
								<td class="money-cell"><?= $item['type'] === 'payment' ? formatMoney($item['credit']) : ''?></td>
								<td class="money-cell">
									<?php if ($item['type'] === 'case'):?>
										<?php

										$balance = (float) $item['amount'];
										$activityEntries = $item['applied_payments'];

										foreach ($activityEntries as $entry) {
											$entryAmount = (float) $entry->amount;
											$balance -= $entryAmount;
										}

										echo formatMoney($balance);

										?>
									<?php endif;?>
								</td>
								<td class="money-cell">
									<?php if (!empty($item['responsibility']['insurance'])): ?>
										<?= formatMoney($item['responsibility']['insurance']) ?>
									<?php endif ?>
								</td>
								<td class="money-cell">
									<?php if (!empty($item['responsibility']['coPay'])): ?>
										<?= formatMoney($item['responsibility']['coPay']) ?>
									<?php endif ?>
								</td>
								<td class="money-cell">
									<?php if (!empty($item['responsibility']['coIns'])): ?>
										<?= formatMoney($item['responsibility']['coIns']) ?>
									<?php endif ?>
								</td>
								<td class="money-cell">
									<?php if (!empty($item['responsibility']['deductible'])): ?>
										<?= formatMoney($item['responsibility']['deductible']) ?>
									<?php endif ?>
								</td>
								<td class="money-cell">
									<?php if (!empty($item['responsibility']['oop'])): ?>
										<?= formatMoney($item['responsibility']['oop']) ?>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
			</div>
			<?php if ($key == count($chunkedRows) - 1):?>
				<table>
					<tr>
						<td>
							<table class="border">
								<tr class="grey-background">
									<td>Total Charges</td>
									<td>Total Insurance Payments</td>
									<td>Total Patient Payments</td>
									<td>Total Balance Outstanding</td>
								</tr>
								<tr>
									<td><?= formatMoney($totalCharges)?></td>
									<td><?= formatMoney($insurancePaymentsAmount)?></td>
									<td><?= formatMoney($patientPaymentsAmount)?></td>
									<td><?= formatMoney($outstandingBalance)?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			<?php else:?>
				<div>Continued on next page</div>
				<div class="page-break"></div>
			<?php endif;?>
		<?php endforeach;?>
	</body>
</html>