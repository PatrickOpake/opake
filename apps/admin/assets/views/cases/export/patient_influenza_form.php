<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body>
		<div class="intake-patient-form influenza-form-export">
			<h4 class="form-main-title">Influenza Triage Questionnaire</h4>

			<div class="field travel-outside">
				<label>1. Have the patient had recent travel outside the country; visits from anyone residing outside the country?</label>
				<div class="inputs">
					<?php if(!is_null($form->travel_outside)):?>
						<div class="radio-button-value"><?= $form->travel_outside ? 'Yes' : 'No'; ?></div>
					<?php endif;?>
					<?php if($form->travel_outside_date):?>
						<span class="date-on-onset">Date of onset: <?= $_date($form->travel_outside_date); ?></span>
					<?php endif;?>
				</div>
			</div>

			<div class="field flu-vaccine">
				<label>2. Has the patient received any flu vaccines this season?</label>
				<div class="inputs">
					<?php if(!is_null($form->flu_vaccine)):?>
						<div class="radio-button-value"><?= $form->flu_vaccine ? 'Yes' : 'No'; ?></div>
					<?php endif;?>
					<?php if($form->flu_vaccine_month):?>
						<span class="month">Month: <?= \DateTime::createFromFormat('!m', $form->flu_vaccine_month)->format('F');?></span>
					<?php endif;?>
				</div>
			</div>

			<div class="field illnesses">
				<label>3. Has the patient had 1 or more of the following:</label>
				<div class="inputs">
					<?php $illnesses = $form->getIllnesses(); ?>
					<?php foreach (\Opake\Model\Patient\Appointment\Form\Influenza::getIllnessesFields() as $row):?>
					<div class="illness-row">
						<?php foreach ($row as $field):?>
						<div class="illness">
							<label><?= $field['label']; ?></label>
							<?php if(isset($illnesses[$field['name']])):?>
								<div>
									<?php if(isset($illnesses[$field['name']]['has'])):?>
										<div class="radio-button-value"><?= $illnesses[$field['name']]['has'] ? 'Yes' : 'No'; ?></div>
									<?php endif;?>
									<?php if(isset($illnesses[$field['name']]['date'])):?>
										<span class="date-on-onset">Date of onset: <?= $_date($illnesses[$field['name']]['date']); ?></span>
									<?php endif;?>
								</div>
								<?php if($field['name'] === 'cough'):?>
								<div class="illness-cough-options">
									<?php if(isset($illnesses['cough']['productive'])):?>
									<span class="productive">
										Productive?
										<div class="radio-button-value"><?= $illnesses['cough']['productive'] ? 'Yes' : 'No'; ?></div>
									</span>
									<?php endif;?>
									<?php if(isset($illnesses['cough']['color'])):?>
									<span class="color">
										Color:
										<?= $illnesses['cough']['color']; ?>
									</span>
									<?php endif;?>
								</div>
								<?php endif;?>
							<?php endif;?>
						</div>
						<?php endforeach;?>
					</div>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</body>
</html>