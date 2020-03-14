<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="case-reconciliation-export">
			<h3 class="main-title">Medication Reconciliation</h3>
			<table class="no-border">
				<tr>
					<td><input type="checkbox" <?= $reconciliation->no_known_allergies ? 'checked' : ''; ?>/> <span class="bold-text">No known allergies</span> </td>
					<td><input type="checkbox" <?= $reconciliation->copy_given_to_patient ? 'checked' : ''; ?>/> <span class="bold-text">Copy given to patient</span> </td>
				</tr>
			</table>

			<h4 class="sub-title">Allergy and/or Sensitivity/Reaction</h4>
			<table class="allergies border">
				<?php for ($i = 0; $i <= count($allergies); $i += 2): ?>
					<tr>
						<?php if (isset($allergies[$i])): ?>
							<td><?= $allergies[$i]->name; ?> / <?= $allergies[$i]->description; ?></td>
						<?php endif; ?>
						<?php if (isset($allergies[$i + 1])): ?>
							<td><?= $allergies[$i + 1]->name; ?> / <?= $allergies[$i + 1]->description; ?></td>
						<?php endif; ?>
					</tr>
				<?php endfor; ?>
			</table>
			This list does not constitute a physician’s order

			<table class="no-border">
				<tr>
					<td><input type="checkbox" <?= $reconciliation->patient_denies ? 'checked' : ''; ?>/> <span class="bold-text">Patient denies medication use</span> </td>
					<td><input type="checkbox" <?= $reconciliation->pre_op_call ? 'checked' : ''; ?>/> <span class="bold-text">Pre-Op call</span> </td>
					<td><input type="checkbox" <?= $reconciliation->admission ? 'checked' : ''; ?>/> <span class="bold-text">Admission</span> </td>
				</tr>
			</table>

			<table class="medications border">
				<thead>
				<tr>
					<th>
						<span class="bold-upper-text"> Medication Name Include:  </span> <br/>
						OTC, vitamins, herbal supplements
					</th>
					<th class="vertical"><span class="bold-upper-text"> Dose </span></th>
					<th class="vertical"><span class="bold-upper-text"> Route  </span></th>
					<th class="vertical"><span class="bold-upper-text"> Frequency or Time  </span></th>
					<th class="vertical"><span class="bold-upper-text"> Current  </span></th>
					<th class="vertical"><span class="bold-upper-text"> Pre-Op  </span></th>
					<th class="vertical"><span class="bold-upper-text"> Post-Op  </span></th>
					<th class="vertical"><span class="bold-upper-text"> RX </span></th>
					<th class="vertical"><span class="bold-upper-text"> Verify with prescribing MD </span></th>
					<th class="vertical"><span class="bold-upper-text"> Resume / Post-Op  </span></th>
					<th class="vertical"><span class="bold-upper-text"> Discontinue  </span></th>
					<th>
						<span class="bold-upper-text"> Medication Indications, Additional Instructions or Comments  </span> <br/>
						(Date Pain Management Changes)
					</th>
				</tr>
				</thead>
				<tbody>
					<?php foreach ($medications as $medication): ?>
						<tr>
							<td><?= $medication->name; ?></td>
							<td><?= $medication->dose; ?></td>
							<td><?= $medication->route; ?></td>
							<td><?= $medication->frequency; ?></td>
							<td><?php if (!is_null($medication->current)) {echo $medication->current ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->pre_op)) {echo $medication->pre_op ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->post_op)) {echo $medication->post_op ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->rx)) {echo $medication->rx ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->verify)) {echo $medication->verify ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->resume)) {echo $medication->resume ? 'Yes' : 'No'; }; ?></td>
							<td><?php if (!is_null($medication->discontinue)) { echo $medication->discontinue ? 'Yes' : 'No'; }; ?></td>
							<td><?= $medication->comments; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<table class="no-border">
				<tr>
					<?php if ($reconciliation->anesthesia_type): ?>
						<td colspan="2"> <span class="bold-text">Anesthesia Type:</span> <?= Opake\Model\Cases\Item::getAnesthesiaTypeList()[$reconciliation->anesthesia_type]; ?></td>
					<?php endif; ?>
				</tr>
				<tr>
					<?php if ($reconciliation->anesthesia_drugs): ?>
						<td><span class="bold-text">Anesthesia Drugs:</span> <?= Opake\Model\Cases\Registration\Reconciliation::getAnesthesiaDrugsList()[$reconciliation->anesthesia_drugs]; ?></td>
					<?php endif; ?>
					<td>
						<?php if ($reconciliation->anesthesia_drugs == Opake\Model\Cases\Registration\Reconciliation::ANESTHESIA_DRUGS_OTHER): ?>
							<span class="bold-text">Anesthesia Drugs Other:</span> <?= $reconciliation->anesthesia_drugs_other; ?>
						<?php endif; ?>
					</td>
				</tr>
			</table>

			<div class="signatures">
				<div class="signature">
					Medication reviewed with patient .
					<div class="signature-field"></div>
					<span class="bold-text">RN signature</span>
				</div>
				<div class="signature">
					I verify that the above list of medications is complete and accurate to the best of my knowledge .
					<div class="signature-field"></div>
					<span class="bold-text">Patient signature</span>
				</div>
				The reconciliation and decision to continue or omit with reason,
				the use of these medications as appropriate to the care of the patient, is made by the prescribing physician.
				Vitamins and herbal supplements are not reconciled unless specifically ordered by the physician.
			</div>

			<h4 class="sub-title">Return Visit Update: Pain Management Patients Only</h4>
			<table class="no-border visit-updates">
				<?php foreach ($visit_updates as $visit): ?>
					<tr>
						<td><input type="checkbox" <?= $visit->no_change ? 'checked' : ''; ?>/> <span class="bold-text">No Change</span> </td>
						<td><input type="checkbox" <?= $visit->change_listed ? 'checked' : ''; ?>/> <span class="bold-text">Change Listed</span> </td>
						<td><?= $_date($visit->date); ?></td>
						<td class="signature"><span class="bold-text">Signature: </span> <div class="signature-field"></div></td>
					</tr>
				<?php endforeach; ?>
			</table>

		</div>
	</body>
</html>