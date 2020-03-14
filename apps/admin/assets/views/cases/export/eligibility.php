<?php

function getAdditionalInfoSubscriber($subscriber, $ref_id_qualifier)
{
	$groupNumKey = array_search($ref_id_qualifier, array_column($subscriber['additionalIdentifications'], 'ref_id_qualifier'));
	if($groupNumKey !== false) {
		return $subscriber['additionalIdentifications'][$groupNumKey]['ref_id'];
	}
}

function getGenderName($code)
{
	$genderName = [
		'F' => 'Female',
		'M' => 'Male',
		'U' => 'Unknown'
	];
	return $genderName[$code];
}

function getDateTimeQualifier($patient, $dateTimeQualifier)
{
	$key = array_search($dateTimeQualifier, array_column($patient['dates'], 'dateTimeQualifier'));
	if($key !== false) {
	    $date = DateTime::createFromFormat('Ymd', $patient['dates'][$key]['dateTimePeriod']);
	    if($date) {
		    return $date->format('Y-m-d');
	    }
	}
}

function getProviderAdditionalInfo($ref)
{

	if(isset(\Opake\Model\Eligible\CaseCoverage::$RECEIVER_ADDITIONAL_IDENTIFICATION[$ref['ref_id_qualifier']] )) {
		return \Opake\Model\Eligible\CaseCoverage::$RECEIVER_ADDITIONAL_IDENTIFICATION[$ref['ref_id_qualifier']];
	}

	return '';
}

function getEligibilityStatus($code)
{
	if(isset(\Opake\Model\Eligible\CaseCoverage::$ELIGIBILITY_INFO_CODE[$code] )) {
		return \Opake\Model\Eligible\CaseCoverage::$ELIGIBILITY_INFO_CODE[$code];
	}
	return '';
}

function getEligibilityCoverage($code)
{
	if(isset(\Opake\Model\Eligible\CaseCoverage::$COVERAGE_LEVEL_CODE[$code] )) {
		return \Opake\Model\Eligible\CaseCoverage::$COVERAGE_LEVEL_CODE[$code];
	}
	return '';
}

function getEligibilityInsuranceType($code)
{
	if(isset(\Opake\Model\Eligible\CaseCoverage::$INSURANCE_TYPE_CODE[$code] )) {
		return \Opake\Model\Eligible\CaseCoverage::$INSURANCE_TYPE_CODE[$code];
	}
	return '';
}

function getEligibilityServiceType($code)
{
	if(isset(\Opake\Model\Eligible\CaseCoverage::$SERVICE_TYPE_CODE[$code] )) {
		return \Opake\Model\Eligible\CaseCoverage::$SERVICE_TYPE_CODE[$code];
	}
	return '';
}

function getTimePeriodQualifier($code)
{
		if(isset(\Opake\Model\Eligible\CaseCoverage::$TIME_PERIOD_QUALIFIER[$code] )) {
		return \Opake\Model\Eligible\CaseCoverage::$TIME_PERIOD_QUALIFIER[$code];
	}
	return '';
}

function getFamilyBenefit($eligibilities)
{
	$result = [];
	foreach ($eligibilities as $item) {
		if($item['eligibility']['eligibilityOrBenefitInformationCode'] == '1') {
			$result[] = $item;
		}
	}

	return $result;
}

function getFamilyPrimaryCareProvider($eligibilities)
{
	$result = [];
	foreach ($eligibilities as $item) {
		if($item['eligibility']['eligibilityOrBenefitInformationCode'] == 'L') {
			$result[] = $item;
		}
	}

	return $result;
}

function getEligibilitiesByParams($eligibilities, $params)
{
	$result = [];
	foreach ($eligibilities as $item) {
		if($item['eligibility']['eligibilityOrBenefitInformationCode'] == $params['eligibilityOrBenefitInformationCode']
		&& $item['eligibility']['yesNoConditionOrResponseCode2'] == $params['yesNoConditionOrResponseCode2']
		&& $item['eligibility']['coverageLevelCode'] == $params['coverageLevelCode']) {
			$result[] = $item;
		}
	}

	return $result;
}

function getBenefitDate($item)
{
	$key = array_search('292', array_column($item['eligibilityDates'], 'dateTimeQualifier'));
	if($key !== false) {
		$benefitDate = $item['eligibilityDates'][$key];
	    if($benefitDate['dateTimePeriodFormatQualifier'] === 'D8') {
			$date = DateTime::createFromFormat('Ymd', $benefitDate['dateTimePeriod']);
			if($date) {
				return $date->format('Y-m-d');
			}
	    }
	    if($benefitDate['dateTimePeriodFormatQualifier'] === 'RD8') {
		    $dates = explode('-', $benefitDate['dateTimePeriod']);
		    return DateTime::createFromFormat('YYYYMMDD', $dates[0])->format('YYYY-MM-DD') . ' - ' .
		    DateTime::createFromFormat('YYYYMMDD', $dates[1])->format('YYYY-MM-DD');
	    }
	}
}


$subscriber = $coverage['subscriber'][0];

if(isset($coverage['dependent'])) {
	$dependent = $coverage['dependent'][0];
}

$eligibilities = [];
foreach ($subscriber['eligibilities'] as $item) {
	$item['patientType'] = 'Subscriber';
	$eligibilities[] = $item;
}

if(isset($dependent)) {
	foreach ($dependent['eligibilities'] as $item) {
		$item['patientType'] = 'Dependent';
		$eligibilities[] = $item;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
</head>
<body>
<div class="eligibility-print">
	<div class="main-table">
		<div class="page">
			<table>
				<tr>
					<td>
						<h4>Subscriber</h4>
						<table>
							<tr>
								<td>Name:</td>
								<td><?= $subscriber['name']['first_name']?> <?= $subscriber['name']['last_name']?></td>
							</tr>
							<tr>
								<td>Member ID:</td>
								<td><?= getAdditionalInfoSubscriber ($subscriber, '1W')?></td>
							</tr>
							<tr>
								<td>Plan Number:</td>
								<td><?= getAdditionalInfoSubscriber ($subscriber, '18')?></td>
							</tr>
							<tr>
								<td>Group Number:</td>
								<td><?= getAdditionalInfoSubscriber ($subscriber, '6P')?></td>
							</tr>
							<tr>
								<td>Plan Network ID:</td>
								<td><?= getAdditionalInfoSubscriber ($subscriber, 'N6')?></td>
							</tr>
							<tr>
								<td>DOB:</td>
								<td><?php
					$date = DateTime::createFromFormat('Ymd', $subscriber['demographic']['date_time_period']);
					if($date) {
						echo $date->format('Y-m-d');
					}?></td>
							</tr>
							<tr>
								<td>Gender:</td>
								<td><?= getGenderName($subscriber['demographic']['gender_code'])?></td>
							</tr>
							<tr>
								<td>Plan Begin Date:</td>
								<td>
					<?php if(isset($dependent)) {
						echo getDateTimeQualifier($dependent, '346');
					} else {
						echo getDateTimeQualifier($subscriber, '346');
					}?>
								</td>
							</tr>
							<tr>
								<td>Plan End Date:</td>
								<td>
					<?php if(isset($dependent)) {
						echo getDateTimeQualifier($dependent, '347');
					} else {
						echo getDateTimeQualifier($subscriber, '347');
					}?>
								</td>
							</tr>
							<tr>
								<td>Service Date:</td>
								<td>
					<?php if(isset($dependent)) {
						echo getDateTimeQualifier($dependent, '472');
					} else {
						echo getDateTimeQualifier($subscriber, '472');
					}?>
								</td>
							</tr>
							<tr>
								<td>Eligibility Begin Date:</td>
								<td>
					<?php if(isset($dependent)) {
						echo getDateTimeQualifier($dependent, '356');
					} else {
						echo getDateTimeQualifier($subscriber, '356');
					}?>
								</td>
							</tr>
							<tr>
								<td>Trace Number:</td>
								<td><?= $subscriber['traceNumbers'][0]['ref_id']?></td>
							</tr>
						</table>
					</td>
					<td>
						<h4>Response Information</h4>
						<table>
							<tr>
								<td>Batch ID:</td>
								<td></td>
							</tr>
							<tr>
								<td>Response ID:</td>
								<td>
					<?php if(!empty($coverage['beginTransaction'])) {
						echo $coverage['beginTransaction'][0]['referenceIdentification'];
					}?>
								</td>
							</tr>
							<tr>
								<td>Member Name:</td>
								<td>
					<?php
					if(isset($dependent)) {
						echo $dependent['name']['first_name'] . ' ' . $dependent['name']['last_name'];
					} else {
						echo $subscriber['name']['first_name'] . ' ' . $subscriber['name']['last_name'];
					}
					?>
								</td>
							</tr>
							<tr>
								<td>Status:</td>
								<td>ELIGIBLE</td>
							</tr>
							<tr>
								<td>DOS:</td>
								<td>
					<?= $_date($eligible->updated)?>
								</td>
							</tr>
							<tr>
								<td>Payer Name:</td>
								<td><?= $coverage['info_source_detail'][0]['individualOrOrganizationalName']['last_name']?></td>
							</tr>
							<tr>
								<td>Group Name:</td>
								<td><?= $coverage['info_source_detail'][0]['individualOrOrganizationalName']['last_name']?></td>
							</tr>
							<tr>
								<td>Provider Name:</td>
								<td><?= $coverage['info_receiver_detail'][0]['individualOrOrganizationalName']['last_name']?></td>
							</tr>
							<tr>
								<td>Additional Info:</td>
								<td>
					<?php foreach ($coverage['info_receiver_detail'][0]['referenceInformations'] as $key => $item):?>
						<?= getProviderAdditionalInfo($item)?> : <?= $item['ref_id'] ?> <?= $item === end($coverage['info_receiver_detail'][0]['referenceInformations']) ? '' : ','?>
					<?php endforeach;?>
								</td>
							</tr>
							<tr>
								<td>Inquiry Date:</td>
								<td><?= $_date($eligible->updated)?> <?= $_time($eligible->updated)?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<table>
				<tr>
					<td colspan="2">
				<h4>Eligibility Status</h4>
				<table>
					<tr>
						<th class="text-left">Patient:</th>
						<th class="text-left">Status:</th>
						<th class="text-left">Coverage:</th>
						<th class="text-left">Insurance Type:</th>
						<th class="text-left">Service:</th>
						<th class="text-left">Plan:</th>
						<th class="text-left">Network:</th>
					</tr>
					<?php foreach ($eligibilities as $item):?>
					<tr>
						<td><?= $item['patientType']?></td>
						<td><?= getEligibilityStatus($item['eligibility']['eligibilityOrBenefitInformationCode'])?></td>
						<td><?= getEligibilityCoverage($item['eligibility']['coverageLevelCode'])?></td>
						<td><?= getEligibilityInsuranceType($item['eligibility']['insuranceTypeCode'])?></td>
						<td><?= getEligibilityServiceType($item['eligibility']['serviceTypeCode'])?></td>
						<td><?= $item['eligibility']['planCoverageDescription']?></td>
						<td></td>
					</tr>
					<?php endforeach;?>
				</table>
			</td>
				</tr>
			</table>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<table>
				<tr>
					<td>
						<table>
							<tr>
				    <?php $familyActiveCoverage = getFamilyBenefit($eligibilities);?>
								<td>
									<h4>Family Benefits</h4>
									<b>Active Coverage</b>
									<table>
										<tr>
											<td>Status:</td>
											<td>
						    <?= !empty($familyActiveCoverage) ? getEligibilityStatus($familyActiveCoverage[0]['eligibility']['eligibilityOrBenefitInformationCode']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Insurance Type:</td>
											<td><?= !empty($familyActiveCoverage) ? getEligibilityInsuranceType($familyActiveCoverage[0]['eligibility']['insuranceTypeCode']) : ''?></td>
										</tr>
										<tr>
											<td>Description:</td>
											<td><?= !empty($familyActiveCoverage) ? $familyActiveCoverage[0]['eligibility']['planCoverageDescription'] : ''?></td>
										</tr>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($familyActiveCoverage)):?>
							    <?php foreach ($familyActiveCoverage[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($familyActiveCoverage[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
									<b>Limitations</b>
									<table>
										<tr>
						<?php $familyLimitations = getEligibilitiesByParams($eligibilities, [
							'eligibilityOrBenefitInformationCode' => 'F',
							'yesNoConditionOrResponseCode2' => 'w',
							'coverageLevelCode' => 'FAM',
						]);?>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($familyLimitations)):?>
							    <?php foreach ($familyLimitations[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($familyLimitations[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<b>Primary Care Provider</b>
									<table>
					    <?php $primaryCareProviderFam = getFamilyPrimaryCareProvider($eligibilities)?>
										<tr>
											<td>Insurance Type:</td>
											<td>
						    <?= !empty($primaryCareProviderFam) ? getEligibilityInsuranceType($primaryCareProviderFam[0]['eligibility']['insuranceTypeCode']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($primaryCareProviderFam)):?>
							    <?php foreach ($primaryCareProviderFam[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($primaryCareProviderFam[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<h4>Individual Benefits</h4>
									<b>Limitations</b>
					<?php $limitationsInd = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'F',
						'yesNoConditionOrResponseCode2' => 'W',
						'coverageLevelCode' => 'IND',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($limitationsInd)):?>
							    <?php foreach ($limitationsInd[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($limitationsInd[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($limitationsInd) ? getTimePeriodQualifier($limitationsInd[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<h4>In-Network - Individual</h4>
									<b>Deductible</b>
					<?php $InNetworkIndDeductible = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'C',
						'yesNoConditionOrResponseCode2' => 'Y',
						'coverageLevelCode' => 'IND',
					]);?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkIndDeductible)):?>
							    <?php foreach ($InNetworkIndDeductible[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkIndDeductible[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkIndDeductible) ? getTimePeriodQualifier($InNetworkIndDeductible[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkIndDeductible) ? $InNetworkIndDeductible[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td><?= !empty($InNetworkIndDeductible) ? getBenefitDate($InNetworkIndDeductible[0]) : ''?></td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkIndDeductible) ? getTimePeriodQualifier($InNetworkIndDeductible[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkIndDeductible) ? $InNetworkIndDeductible[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
									<b>Limitations</b>
									<table>
					    <?php $InNetworkIndLimitations = getEligibilitiesByParams($eligibilities, [
						    'eligibilityOrBenefitInformationCode' => 'F',
						    'yesNoConditionOrResponseCode2' => 'Y',
						    'coverageLevelCode' => 'IND',
					    ])?>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkIndLimitations)):?>
							    <?php foreach ($InNetworkIndLimitations[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkIndLimitations[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<b>Out of Pocket (Stop Loss)</b>
					<?php $InNetworkIndOutOfPocket = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'G',
						'yesNoConditionOrResponseCode2' => 'Y',
						'coverageLevelCode' => 'IND',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkIndOutOfPocket)):?>
							    <?php foreach ($InNetworkIndOutOfPocket[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkIndOutOfPocket[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkIndOutOfPocket) ? getTimePeriodQualifier($InNetworkIndOutOfPocket[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkIndOutOfPocket) ? $InNetworkIndOutOfPocket[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td><?= !empty($InNetworkIndOutOfPocket) ? getBenefitDate($InNetworkIndOutOfPocket[0]) : ''?></td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkIndOutOfPocket) ? getTimePeriodQualifier($InNetworkIndOutOfPocket[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkIndOutOfPocket) ? $InNetworkIndOutOfPocket[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<h4>In-Network - Family</h4>
					<?php $InNetworkFamDeductible = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'C',
						'yesNoConditionOrResponseCode2' => 'Y',
						'coverageLevelCode' => 'FAM',
					])?>
									<b>Deductible</b>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkFamDeductible)):?>
							    <?php foreach ($InNetworkFamDeductible[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkFamDeductible[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkFamDeductible) ? getTimePeriodQualifier($InNetworkFamDeductible[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkFamDeductible) ? $InNetworkFamDeductible[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($InNetworkFamDeductible) ? getBenefitDate($InNetworkFamDeductible[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkFamDeductible) ? getTimePeriodQualifier($InNetworkFamDeductible[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkFamDeductible) ? $InNetworkFamDeductible[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
									<b>Limitations</b>
									<table>
										<tr>
						<?php $InNetworkFamLimitations = getEligibilitiesByParams($eligibilities, [
							'eligibilityOrBenefitInformationCode' => 'F',
							'yesNoConditionOrResponseCode2' => 'Y',
							'coverageLevelCode' => 'FAM',
						])?>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkFamLimitations)):?>
							    <?php foreach ($InNetworkFamLimitations[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkFamLimitations[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<b>Out of Pocket (Stop Loss)</b>
					<?php $InNetworkFamOutOfPocket = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'G',
						'yesNoConditionOrResponseCode2' => 'Y',
						'coverageLevelCode' => 'FAM',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($InNetworkFamOutOfPocket)):?>
							    <?php foreach ($InNetworkFamOutOfPocket[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($InNetworkFamOutOfPocket[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkFamOutOfPocket) ? getTimePeriodQualifier($InNetworkFamOutOfPocket[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkFamOutOfPocket) ? $InNetworkFamOutOfPocket[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($InNetworkFamOutOfPocket) ? getBenefitDate($InNetworkFamOutOfPocket[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($InNetworkFamOutOfPocket) ? getTimePeriodQualifier($InNetworkFamOutOfPocket[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($InNetworkFamOutOfPocket) ? $InNetworkFamOutOfPocket[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div class="page-break"></div>
		<div class="page">
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td>
									<h4>Out-of-Network - Individual</h4>
					<?php $OutNetworkIndDeductible = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'C',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'IND',
					])?>
									<b>Deductible</b>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkIndDeductible)):?>
							    <?php foreach ($OutNetworkIndDeductible[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkIndDeductible[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkIndDeductible) ? getTimePeriodQualifier($OutNetworkIndDeductible[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkIndDeductible) ? $OutNetworkIndDeductible[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($OutNetworkIndDeductible) ? getBenefitDate($OutNetworkIndDeductible[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkIndDeductible) ? getTimePeriodQualifier($OutNetworkIndDeductible[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkIndDeductible) ? $OutNetworkIndDeductible[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
									<b>Limitations</b>
					<?php $OutNetworkIndLimitations = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'F',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'IND',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkIndLimitations)):?>
							    <?php foreach ($OutNetworkIndLimitations[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkIndLimitations[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<b>Out of Pocket (Stop Loss)</b>
					<?php $OutNetworkIndOutOfPocket = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'G',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'IND',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkIndOutOfPocket)):?>
							    <?php foreach ($OutNetworkIndOutOfPocket[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkIndOutOfPocket[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkIndOutOfPocket) ? getTimePeriodQualifier($OutNetworkIndOutOfPocket[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkIndOutOfPocket) ? $OutNetworkIndOutOfPocket[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($OutNetworkIndOutOfPocket) ? getBenefitDate($OutNetworkIndOutOfPocket[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkIndOutOfPocket) ? getTimePeriodQualifier($OutNetworkIndOutOfPocket[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkIndOutOfPocket) ? $OutNetworkIndOutOfPocket[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<h4>Out-of-Network - Family</h4>
									<b>Deductible</b>
					<?php $OutNetworkFamDeductible = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'C',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'FAM',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkFamDeductible)):?>
							    <?php foreach ($OutNetworkFamDeductible[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkFamDeductible[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkFamDeductible) ? getTimePeriodQualifier($InNetworkFamDeductible[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkFamDeductible) ? $OutNetworkFamDeductible[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($OutNetworkFamDeductible) ? getBenefitDate($OutNetworkFamDeductible[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkFamDeductible) ? getTimePeriodQualifier($OutNetworkFamDeductible[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkFamDeductible) ? $OutNetworkFamDeductible[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
									<b>Limitations</b>
					<?php $OutNetworkFamLimitations = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'F',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'FAM',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkFamLimitations)):?>
							    <?php foreach ($OutNetworkFamLimitations[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkFamLimitations[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
									</table>
								</td>
								<td>
									<b>Out of Pocket (Stop Loss)</b>
					<?php $OutNetworkFamOutOfPocket = getEligibilitiesByParams($eligibilities, [
						'eligibilityOrBenefitInformationCode' => 'G',
						'yesNoConditionOrResponseCode2' => 'N',
						'coverageLevelCode' => 'FAM',
					])?>
									<table>
										<tr>
											<td>Message(s):</td>
											<td>
						    <?php if( !empty($OutNetworkFamOutOfPocket)):?>
							    <?php foreach ($OutNetworkFamOutOfPocket[0]['messageTexts'] as $key => $item):?>
								    <?= $item['freeFormMessageText']?>
								    <?= $key == (count($OutNetworkFamOutOfPocket[0]['messageTexts']) - 1) ? '' : ','?>
							    <?php endforeach;?>
						    <?php endif;?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkFamOutOfPocket) ? getTimePeriodQualifier($OutNetworkFamOutOfPocket[0]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkFamOutOfPocket) ? $OutNetworkFamOutOfPocket[0]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
										<tr>
											<td>Benefit Date:</td>
											<td>
						    <?= !empty($OutNetworkFamOutOfPocket) ? getBenefitDate($OutNetworkFamOutOfPocket[0]) : ''?>
											</td>
										</tr>
										<tr>
											<td>Period:</td>
											<td>
						    <?= !empty($OutNetworkFamOutOfPocket) ? getTimePeriodQualifier($OutNetworkFamOutOfPocket[1]['eligibility']['timePeriodQualifier']) : ''?>
											</td>
										</tr>
										<tr>
											<td>Amount:</td>
											<td>
						    <?= !empty($OutNetworkFamOutOfPocket) ? $OutNetworkFamOutOfPocket[1]['eligibility']['monetaryAmount'] : ''?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
</body>
</html>