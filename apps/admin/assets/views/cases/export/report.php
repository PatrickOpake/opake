<?php
	use Opake\Helper\StringHelper;
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body class="operative-report-export">
		<table class="case-info no-border">
			<tbody>
				<tr>
					<td>
						<b>Patient Name:</b> <?= $registration->getFullName() ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Age/Sex:</b> <?= $patient->getAge(); ?> / <?= $patient->getGender(); ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Date of Birth:</b> <?= $_date($patient->dob); ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>MRN:</b> <?= $patient->getFullMrn(); ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Room:</b> <?= $case->location->name; ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Date of Service:</b> <?= $_date($case->time_start); ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Time Scheduled:</b> <?= $_time($case->time_start); ?> - <?= $_time($case->time_end); ?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Surgeon:</b>
						<?= implode(', ', array_map(function($user) {
							return $user->getFullName();
						}, $case->getUsers())); ?>
					</td>
				</tr>
				<?php foreach ($surgeons as $key => $value) { ?>
					<tr>
						<td>
							<b><?= $key; ?>:</b>
							<?= implode(', ', array_map(function($user) {
								return $user->getFullName();
							}, $value['value']->as_array())); ?>
						</td>
					</tr>
				<?php } ?>

			</tbody>
		</table>

		<table class="operative-report-info no-border">
			<tbody>
			<?php foreach ($template as $group_id => $groupFields):?>
				<?php if($group_id != \Opake\Model\Cases\OperativeReport\SiteTemplate::GROUP_CASE_INFO_ID):?>
					<?php foreach ($groupFields as $key1 => $fieldReport):?>
							<?php if($fieldReport['active']):?>
								<tr>
									<td>
										<b><?= $fieldReport['name']?>: </b>

										<?php if($fieldReport['type'] === 'case_type'):?>
											<?php if ($case->getAdditionalCpts()): ?>
												<?php foreach($case->getAdditionalCpts() as $key => $item):?>
													<?= $item->code?> - <?= $item->name ?><?= count($case->getAdditionalCpts()) - 1 === $key ? '' : '<br>'?>
												<?php endforeach;?>
											<?php endif; ?>
										<?php endif;?>

										<?php if($fieldReport['field'] === 'pre_op_diagnosis'):?>
											<?php if ($pre_op_diagnosis): ?>
												<?php foreach($pre_op_diagnosis as $key => $diagnosis):?>
													<?= $diagnosis->code?> - <?= $diagnosis->desc ?><?= count($pre_op_diagnosis) - 1 === $key ? '' : '<br>'?>
												<?php endforeach;?>
											<?php endif; ?>
										<?php endif;?>
										<?php if($fieldReport['field'] === 'post_op_diagnosis'):?>
											<?php if ($post_op_diagnosis): ?>
												<?php foreach($post_op_diagnosis as $key => $diagnosis):?>
													<?= $diagnosis->code?> - <?= $diagnosis->desc ?><?= count($post_op_diagnosis) - 1 === $key ? '' : '<br>'?>
												<?php endforeach;?>
											<?php endif ?>
										<?php endif;?>

										<?php if($fieldReport['type'] === 'text' && $fieldReport['field'] !== 'custom' && $fieldReport['field'] !== 'list' && isset($op_report->{$fieldReport['field']}) && $op_report->{$fieldReport['field']}):?>
											<?= StringHelper::removeBrTagFromEnd(StringHelper::stripHtmlTags($op_report->{$fieldReport['field']}, '<br>'))?>
										<?php endif;?>

										<?php if($fieldReport['field'] === 'custom' && isset($fieldReport['custom_value'])):?>
											<?= StringHelper::removeBrTagFromEnd(StringHelper::stripHtmlTags($fieldReport['custom_value'], '<br>'))?>
										<?php endif;?>
										<?php if($fieldReport['field'] === 'list' && isset($fieldReport['list_value'])):?>
											<table>
												<tr>
													<td class="list-column">
														<table>
														<?php foreach ($fieldReport['list_value']['column1'] as $key2 => $item):?>
															<tr>

																<td><input  type="checkbox" <?=$item['active'] ? 'checked' : ''?>  />
																<span><?php echo $item['text']?></span>
																<?php if($item['active']):?>
																	<div><?php echo $item['description']?></div>
																<?php endif;?>
																</td>
															</tr>
														<?php endforeach;?>
														</table>
													</td>
													<?php if(isset($fieldReport['list_value']['column2'])):?>
														<td class="list-column">
															<table>
															<?php foreach ($fieldReport['list_value']['column2'] as $key2 => $item):?>
																<tr>
																	<td><input  type="checkbox" <?=$item['active'] ? 'checked' : ''?>  />
																	<span><?php echo $item['text']?></span>
																	<?php if($item['active']):?>
																		<div><?php echo $item['description']?></div>
																	<?php endif;?>
																	</td>
																</tr>
															<?php endforeach;?>
															</table>
														</td>
													<?php endif;?>
												</tr>
											</table>
										<?php endif;?>
									</td>
								</tr>
							<?php endif;?>
						<?php endforeach;?>
				<?php endif;?>
			<?php endforeach;?>
			</tbody>
		</table>

		<div class="signatures">
			<div class="left"> Signed By: <b><?= $op_report->signed_user->loaded()? $op_report->signed_user-> getFullName() : ''?></b></div>
			<div class="right"> Date: <b><?= $op_report->time_signed ? $_date($op_report->time_signed) : '' ?></b></div>
		</div>

		<?php if($amendments):?>
			<?php foreach ($amendments as $amendment):?>
				<table class="operative-report-info no-border top-buffer">
					<tbody>
						<tr>
							<td><b>Amendment - <?= $_date($amendment->time_signed) ?></b></td>
						</tr>
						<tr>
							<td><?= $amendment->text ?></td>
						</tr>
					</tbody>
				</table>
				<div class="signatures">
					<div class="left"> Signed By: <b><?= $amendment->signed_user->getFullName()?></b></div>
					<div class="right"> Date: <b><?= $_date($amendment->time_signed) ?></b></div>
				</div>
			<?php endforeach;?>
		<?php endif;?>
	</body>
</html>