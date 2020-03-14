<?php
	function getStartOfWeek($request)
	{
	    return (new \DateTime('sunday last week ' . $request['dos']))->getTimeStamp();
	}

	function getEndOfWeek($request)
	{
		return (new \DateTime('saturday this week ' . $request['dos']))->getTimeStamp();
	}

	function getDateDisplay($request)
	{
		if ($request['view_type'] === 'day') {
			return date('l F d, Y', (new \DateTime($request['dos']))->getTimeStamp());
		} else if ($request['view_type'] === 'week') {
			if (date('n', getStartOfWeek($request)) == date('n', getEndOfWeek($request))) {
				return (date('F j', getStartOfWeek($request)) . ' - ' . date('j', getEndOfWeek($request)));
			} else {
				return (date('F j', getStartOfWeek($request)) . ' - ' . date('F j', getEndOfWeek($request)));
			}
		} else if ($request['view_type'] === 'month') {
			return (date('F j', (new \DateTime($request['start']))->getTimeStamp()) . ' - ' . date('F j', (new \DateTime($request['end']))->getTimeStamp()));
		}
	}


	usort($groupedCases, function($a, $b) {
	    $groupCasesArraySortByCasesCount = function($a, $b)
	    {
		    if (count($a['cases']) === count($b['cases'])) {
			    return $a['header'] > $b['header'] ? 1 : -1;
		    }
		    return count($a['cases']) > count($b['cases']) ? 1 : -1;
	    };

		if (!$a['position'] && !$b['position']) {
			return $groupCasesArraySortByCasesCount($a, $b);
		} else if (!$a['position']) {
			return 1;
		} else if (!$b['position']) {
			return -1;
		} else if ($a['position'] > $b['position']) {
			return 1;
		} else if ($a['position'] < $b['position']) {
			return -1;
		}

		return $groupCasesArraySortByCasesCount($a, $b);
	});
?>
<div class="overview-print">
	<div class="overview-header">
		<table class="no-border">
			<tr>
				<td class="opake-logo">
					<img src="/common/i/opake_logo_overview_print.png"/>
				</td>
				<td class="org-name">
					<?php if ($display_timestamp) {
						echo 'Printed: ', date("m/d/Y g:i A"), '<br/>';
					} ?>
					<?= $org->name?> <br/>
					<?= getDateDisplay($request)?>
				</td>
			</tr>
		</table>
	</div>
	<div class="overview-print-table">
		<?php foreach($groupedCases as $group_case):?>
		<div class="group-block">
			<div class="surgeon-header">
				<?= $group_case['header'] ?>
			</div>

			<table class="no-border">
				<thead>
				<tr>
					<?php if($request['view_type'] == 'day'):?><th class="case-time--day-view bold-text">Time</th><?php endif;?>
					<?php if($request['view_type'] == 'week'):?><th class="case-time--week-view bold-text">DOS</th><?php endif;?>
					<?php if($groupType == 'surgeon'):?><th class="case-room bold-text">Room</th><?php endif;?>
					<?php if($groupType == 'room'):?><th class="case-surgeon bold-text">Surgeon</th><?php endif;?>
					<th class="case-patient bold-text">Name</th>
					<th class="bold-text<?php if($request['view_type'] == 'week'):?>case-procedure--week-view<?php endif;?> <?php if($request['view_type'] == 'day'):?>case-procedure--day-view<?php endif;?>">Procedure</th>
				</tr>
				</thead>
				<tbody>
				<?php
					if($groupType == 'room') {
						usort($group_case['cases'], function ($a, $b) {
							if (isset($a->time_start)) {
								$aTimeStart = $a->time_start;
							} else {
								$aTimeStart = $a->start;
							}
							if (isset($b->time_start)) {
								$bTimeStart = $b->time_start;
							} else {
								$bTimeStart = $b->start;
							}

							if ($aTimeStart > $bTimeStart) {
								return 1;
							}

							return -1;
						});
					}
				?>
				<?php foreach ($group_case['cases'] as $case): ?>
					<?php if (isset($case->is_in_service) && $case->is_in_service):?>
						<tr>
							<?php if($request['view_type'] == 'day'):?>
								<td class="case-time--day-view">
									<?= date('h:i A', (new \DateTime($case->start))->getTimestamp())?> - <?= date('h:i A', (new \DateTime($case->end))->getTimestamp())?><br/>
									<span><?= $_timeLength($case->start, $case->end)?></span>
								</td>
							<?php endif;?>
							<?php if($request['view_type'] == 'week'):?>
								<td class="case-time--week-view">
									<?= date('m/d/Y h:i A', (new \DateTime($case->start))->getTimestamp())?> - <?= date('h:i A', (new \DateTime($case->end))->getTimestamp())?>
									<span><?= $_timeLength($case->start, $case->end)?></span>
								</td>
							<?php endif;?>
							<td class="case-surgeon"></td>
							<td class="case-patient"></td>
							<td class="<?php if($request['view_type'] == 'day'):?>case-procedure--day-view<?php endif;?> <?php if($request['view_type'] == 'week'):?>case-procedure--week-view<?php endif;?>">
								<span><?= $case->getTitle()?></span>
								<?php if ($case->description): ?>
									<div class="italicized-text"><?= $case->description ?></div>
								<?php endif;?>
							</td>
						</tr>
					<?php else: ?>
					<tr>
						<?php if($request['view_type'] == 'day'):?>
						<td class="case-time--day-view">
							<?= date('h:i A', (new \DateTime($case->time_start))->getTimestamp())?> - <?= date('h:i A', (new \DateTime($case->time_end))->getTimestamp())?><br/>
							<span><?= $_timeLength($case->time_start, $case->time_end)?></span>
						</td>
						<?php endif;?>
						<?php if($request['view_type'] == 'week'):?>
						<td class="case-time--week-view">
							<?= date('m/d/Y h:i A', (new \DateTime($case->time_start))->getTimestamp())?> - <?= date('h:i A', (new \DateTime($case->time_end))->getTimestamp())?>
							<span><?= $_timeLength($case->time_start, $case->time_end)?></span>
						</td>
						<?php endif;?>
						<?php if($groupType == 'surgeon'):?>
						<td class="case-room">
							<?= $case->location->name?>
						</td>
						<?php endif;?>
						<?php if($groupType == 'room'):?>
						<td class="case-surgeon">
							<?= $case->getFirstSurgeonForDashboard()?>
						</td>
						<?php endif;?>
						<td class="case-patient">
							<span><?= $case->registration->last_name?>, <?= $case->registration->first_name?></span>
							<div class="patient-info">
								DOB: <?= $_date($case->registration->dob)?> - <?= $case->registration->getAge()?> <br/>
								MRN: <?= $case->registration->patient->getFullMrn()?> <?= $case->registration->patient->getSexLetter()?>
							</div>
						</td>
						<td class="<?php if($request['view_type'] == 'day'):?>case-procedure--day-view<?php endif;?> <?php if($request['view_type'] == 'week'):?>case-procedure--week-view<?php endif;?>">
							<span><?= $case->getProcedureNameForDashboardPrint()?></span>
							<?php if ($case->description): ?>
								<div class="italicized-text"><?= $case->description ?></div>
							<?php endif;?>
						</td>
					</tr>
					<?php endif; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endforeach;?>
	</div>
</div>