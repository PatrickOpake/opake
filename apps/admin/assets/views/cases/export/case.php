<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
	</head>
	<body class="case-export">
	<?php if($printPatientDetails): ?>
	<div class="page">
		<table class="no-border">
			<tbody>
			<tr>
				<td>
					<table>
						<td colspan="2"><h2>Patient Details</h2></td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Title</b><br/>
							<?php if($registration->title):?>
								<?= Opake\Model\Patient::getTitlesList()[$registration->title]; ?>
							<?php endif;?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['last_name']) ? 'class="bordered"' : '' ?>>
							<b>Last Name</b><br/>
							<?= $registration->last_name; ?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['first_name']) ? 'class="bordered"' : '' ?>>
							<b>First Name</b><br/>
							<?= $registration->first_name; ?>
						</td>
						<td>
							<b>M.I</b><br/>
							<?= $registration->middle_name; ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['ssn']) ? 'class="bordered"' : '' ?>>
							<b>Social Security No.</b><br/>
							<?= $_ssn($registration->ssn) ?>
						</td>
						<td>
							<b>Marital Status</b><br/>
							<?php if($registration->status_marital):?>
								<?= Opake\Model\Patient::getMartialStatusesList()[$registration->status_marital]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Suffix</b><br/>
							<?php if($registration->suffix):?>
								<?= \Opake\Model\Patient::getSuffixesList()[$registration->suffix]; ?>
							<?php endif;?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['gender']) ? 'class="bordered"' : '' ?>>
							<b>Gender</b><br/>
							<?php if($registration->gender):?>
								<?= \Opake\Model\Patient::getGendersList()[$registration->gender]; ?>
							<?php endif;?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['dob']) ? 'class="bordered"' : '' ?>>
							<b>Date of Birth</b><br/>
							<?= $_date($registration->dob); ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['ethnicity']) ? 'class="bordered"' : '' ?>>
							<b>Ethnicity</b><br/>
							<?php if($registration->ethnicity):?>
								<?= \Opake\Model\Patient::getEthnicityList()[$registration->ethnicity]; ?>
							<?php endif;?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['race']) ? 'class="bordered"' : '' ?>>
							<b>Race</b><br/>
							<?php if($registration->race):?>
								<?= \Opake\Model\Patient::getRacesList()[$registration->race]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['home_address']) ? 'class="bordered"' : '' ?>>
							<b>Address</b><br/>
							<?= $registration->home_address; ?>
						</td>
						<td>
							<b>Apt</b><br/>
							<?= $registration->home_apt_number; ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['language_id']) ? 'class="bordered"' : '' ?>>
							<b>Preferred Language</b><br/>
							<?= $registration->language->name; ?>
						</td>
						<td>
							<b>Occupation</b><br/>
							<?php if($registration->status_employment):?>
								<?= Opake\Model\Patient::getEmploymentStatusesList()[$registration->status_employment]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['home_state_id']) ? 'class="bordered"' : '' ?>>
							<b>State</b><br/>
							<?php if($registration->home_country->id == 235):?>
								<?= $registration->home_state->name; ?>
							<?php else:?>
								<?= $registration->custom_home_state; ?>
							<?php endif;?>
						</td>
						<td <?= (isset($validationErrors['cases_registration']['home_city_id'])
							|| isset($validationErrors['cases_registration']['custom_home_city'])) ? 'class="bordered"' : '' ?>>
							<b>City</b><br/>
							<?php if($registration->home_country->id == 235):?>
								<?= $registration->home_city->name; ?>
							<?php else:?>
								<?= $registration->custom_home_city; ?>
							<?php endif;?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['home_zip_code']) ? 'class="bordered"' : '' ?>>
							<b>Zip</b><br/>
							<?= $registration->home_zip_code; ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td>
							<b>Employer/School</b><br/>
							<?= $registration->employer; ?>
						</td>
						<td>
							<b>Employer Phone No.</b><br/>
							<?= $_phone($registration->employer_phone); ?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['home_country_id']) ? 'class="bordered"' : '' ?>>
							<b>Country</b><br/>
							<?= $registration->home_country->name; ?>
						</td>
						<td>
							<b>Email</b><br/>
							<?= $registration->home_email; ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['ec_name']) ? 'class="bordered"' : '' ?>>
							<b>Emergency Contact</b><br/>
							<?= $registration->ec_name; ?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['ec_phone_number']) ? 'class="bordered"' : '' ?>>
							<b>Emergency Phone No.</b><br/>
							<?= $_phone($registration->ec_phone_number); ?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['home_phone']) ? 'class="bordered"' : '' ?>>
							<b>Phone No.</b><br/>
							<?= $_phone($registration->home_phone); ?>
						</td>
						<td <?= isset($validationErrors['cases_registration']['additional_phone']) ? 'class="bordered"' : '' ?>>
							<b>Additional Phone No.</b><br/>
							<?= $_phone($registration->additional_phone); ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['ec_relationship']) ? 'class="bordered"' : '' ?>>
							<b>Relationship to Patient</b><br/>
							<?php if(isset($registration->status_employment)):?>
								<?php
									$retaionshipList = \Opake\Model\Patient::getRelationshipToInsuredList();
								?>
								<?= (isset($retaionshipList[$registration->ec_relationship])) ? $retaionshipList[$registration->ec_relationship] : ''; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Admitting Diagnosis</b><br/>
							<?php
							$admitting_diagnosis = $registration->admitting_diagnosis->find_all()->as_array();
							foreach ($admitting_diagnosis as $key => $item): ?>
								<?= $item->code; ?> - <?= $item->desc; ?><?= count($admitting_diagnosis) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Secondary Diagnosis</b><br/>
							<?php
							$secondary_diagnosis = $registration->secondary_diagnosis->find_all()->as_array();
							foreach ($secondary_diagnosis as $key => $item): ?>
								<?= $item->code; ?> - <?= $item->desc; ?><?= count($secondary_diagnosis) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
					</table>
				</td>
				<td>
					<table>
						<td <?= isset($validationErrors['cases_registration']['admission_type']) ? 'class="bordered"' : '' ?>>
							<b>Admission Type</b><br/>
							<?php if($registration->admission_type):?>
								<?= \Opake\Model\Cases\Registration::getAdmissionTypesList()[$registration->admission_type]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="signatures">
			<div class="left"><h2>Signature: ____________________</h2> </div>
			<div class="right"><h2>Date: ________________</h2> </div>
		</div>
	</div>
	<?php endif; ?>

	<?php if($printInsurances): ?>
	<?php /** @var \Opake\Model\Cases\Registration\Insurance $insurance */
		foreach($registration->getSelectedInsurances() as $key => $insurance):?>
		<?php $insuranceData = $insurance->getInsuranceDataModel() ?>
		<div class="page">
			<table class="no-border">
					<tbody>
					<tr>
						<td class="insurance-title">
							<table>
								<td><h2><?= $insurance->getTitle();?></h2></td>
							</table>
						</td>
					</tr>
					<?php if ($insurance->isWorkersCompanyInsurance()): ?>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_name']) ? 'class="bordered"' : '' ?>>
										<b>Workers Comp Insurance Name</b><br/>
										<?= $insuranceData->insurance_name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['adjuster_name']) ? 'class="bordered"' : '' ?>>
										<b>Workers Comp Adjusters Name</b><br/>
										<?= $insuranceData->adjuster_name; ?>
									</td>
								</table>
							</td>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['comp_claim']) ? 'class="bordered"' : '' ?>>
										<b>Workers Comp Claim #</b><br/>
										<?= $insuranceData->claim; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['adjuster_phone']) ? 'class="bordered"' : '' ?>>
										<b>Workers Comp Adjuster Phone #</b><br/>
										<?= $_phone($insuranceData->adjuster_phone); ?>
									</td>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_address']) ? 'class="bordered"' : '' ?>>
										<b>Workers Comp Insurance Address</b><br/>
										<?= $insuranceData->insurance_address; ?>
									</td>
								</table>
							</td>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['state_id']) ? 'class="bordered"' : '' ?>>
										<b>State</b><br/>
										<?= $insuranceData->state->name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['city_id']) ? 'class="bordered"' : '' ?>>
										<b>City</b><br/>
										<?= $insuranceData->city->name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['zip']) ? 'class="bordered"' : '' ?>>
										<b>ZIP</b><br/>
										<?= $insuranceData->zip; ?>
									</td>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['accident_date']) ? 'class="bordered"' : '' ?>>
										<b>Accident Date</b><br/>
										<?= $_date($insuranceData->accident_date); ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_company_phone']) ? 'class="bordered"' : '' ?>>
										<b>Insurance Company Phone #</b><br/>
										<?= $_phone($insuranceData->insurance_company_phone); ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['authorization_number']) ? 'class="bordered"' : '' ?>>
										<b>Authorization #</b><br/>
					    				<?= $insuranceData->authorization_number; ?>
									</td>
								</table>
							</td>
						</tr>
					<?php endif;?>
					<?php if ($insurance->isAutoAccidentInsurance()): ?>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_name']) ? 'class="bordered"' : '' ?>>
										<b>Auto Insurance Name</b><br/>
										<?= $insuranceData->insurance_name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['adjuster_name']) ? 'class="bordered"' : '' ?>>
										<b>Auto Adjuster Name</b><br/>
										<?= $insuranceData->adjuster_name; ?>
									</td>
								</table>
							</td>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['claim']) ? 'class="bordered"' : '' ?>>
										<b>Auto Claim #</b><br/>
										<?= $insuranceData->claim; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['adjuster_phone']) ? 'class="bordered"' : '' ?>>
										<b>Auto Adjuster Phone #</b><br/>
										<?= $_phone($insuranceData->adjuster_phone); ?>
									</td>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_address']) ? 'class="bordered"' : '' ?>>
										<b>Auto Insurance Address</b><br/>
										<?= $insuranceData->insurance_address; ?>
									</td>
								</table>
							</td>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['state_id']) ? 'class="bordered"' : '' ?>>
										<b>State</b><br/>
										<?= $insuranceData->state->name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['city_id']) ? 'class="bordered"' : '' ?>>
										<b>City</b><br/>
										<?= $insuranceData->city->name; ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['zip']) ? 'class="bordered"' : '' ?>>
										<b>ZIP</b><br/>
										<?= $insuranceData->zip; ?>
									</td>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['accident_date']) ? 'class="bordered"' : '' ?>>
										<b>Accident Date</b><br/>
										<?= $_date($insuranceData->accident_date); ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['attorney_name']) ? 'class="bordered"' : '' ?>>
										<b>Attorney Name</b><br/>
										<?= $insuranceData->attorney_name; ?>
									</td>
								</table>
							</td>
							<td>
								<table>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['attorney_phone']) ? 'class="bordered"' : '' ?>>
										<b>Attorney Phone</b><br/>
										<?= $_phone($insuranceData->attorney_phone); ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_company_phone']) ? 'class="bordered"' : '' ?>>
										<b>Insurance Company Phone #</b><br/>
										<?= $_phone($insuranceData->insurance_company_phone); ?>
									</td>
									<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['authorization_number']) ? 'class="bordered"' : '' ?>>
										<b>Authorization #</b><br/>
										<?= $insuranceData->authorization_number; ?>
									</td>
								</table>
							</td>
						</tr>
					<?php endif; ?>
				<?php if ($insurance->isRegularInsurance()): ?>
					<tr>
						<td>
							<table>
								<td>
									<b>Rel. to Patient</b><br />
									<?php if (isset($insuranceData->relationship_to_insured) && !is_null($insuranceData->relationship_to_insured)): ?>
										<?= \Opake\Model\Cases\Registration::getRelationshipInsuredList()[$insuranceData->relationship_to_insured];?>
									<?php endif;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['last_name']) ? 'class="bordered"' : '' ?>>
									<b>Last Name</b><br />
									<?= $insuranceData->last_name;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['first_name']) ? 'class="bordered"' : '' ?>>
									<b>First Name</b><br />
									<?= $insuranceData->first_name;?>
								</td>
								<td>
									<b>M.I</b><br />
									<?= $insuranceData->middle_name;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Suffix</b><br />
									<?php if($insuranceData->suffix):?>
										<?= \Opake\Model\Patient::getSuffixesList()[$insuranceData->suffix];?>
									<?php endif;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['gender']) ? 'class="bordered"' : '' ?>>
									<b>Gender</b><br />
									<?php if($insuranceData->gender):?>
										<?= \Opake\Model\Patient::getGendersList()[$insuranceData->gender];?>
									<?php endif;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['dob']) ? 'class="bordered"' : '' ?>>
									<b>Date of Birth</b><br />
									<?= $_date($insuranceData->dob);?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['insurance_id']) && $insurance->type != 2 ? 'class="bordered"' : '' ?>>
									<b>Insurance Company</b><br />
									<span><?= $insurance->getTitle() ?></span>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['address']) ? 'class="bordered"' : '' ?>>
									<b>Address</b><br />
									<?= $insuranceData->address;?>
								</td>
								<td>
									<b>Apt</b><br />
									<?= $insuranceData->apt_number;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['policy_number']) ? 'class="bordered"' : '' ?>>
									<b>Policy #</b><br />
									<?= $insuranceData->policy_number;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['group_number']) ? 'class="bordered"' : '' ?>>
									<b>Group #</b><br />
									<?= $insuranceData->group_number;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['state_id']) ? 'class="bordered"' : '' ?>>
									<b>State</b><br />
									<?php if($insuranceData->country->id == 235):?>
										<?= $insuranceData->state->name;?>
									<?php else:?>
										<?= $insuranceData->custom_state;?>
									<?php endif;?>
								</td>
								<td <?= (isset($validationErrors['cases_registration_insurance'][$key + 1]['city_id'])
									|| isset($validationErrors['cases_registration_insurance'][$key + 1]['custom_city'])) ? 'class="bordered"' : '' ?>>
									<b>City</b><br />
									<?php if($insuranceData->country->id == 235):?>
										<?= $insuranceData->city->name;?>
									<?php else:?>
										<?= $insuranceData->custom_city;?>
									<?php endif;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['zip_code']) ? 'class="bordered"' : '' ?>>
									<b>Zip</b><br />
									<?= $insuranceData->zip_code;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['country_id']) ? 'class="bordered"' : '' ?>>
									<b>Country</b><br />
									<?= $insuranceData->country->name;?>
								</td>
								<td <?= isset($validationErrors['cases_registration_insurance'][$key + 1]['phone']) ? 'class="bordered"' : '' ?>>
									<b>Phone No.</b><br />
									<?= $_phone($insuranceData->phone);?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Provider Phone No.</b><br />
									<?= $_phone($insuranceData->provider_phone);?>
								</td>
							</table>
						</td>
					</tr>
				<?php endif ?>
					<?php if ($insurance->isDescriptionInsurance()): ?>
						<tr>
							<td colspan="2">
								<b>Description</b>
								<?php if(isset($insuranceData->description)):?>
									<div class="insurance-description"><?= $insuranceData->description; ?></div>
								<?php endif;?>
							</td>
						</tr>
					<?php endif;?>

				</tbody>
			</table>
			<table class="no-border">
				<tr>
					<td colspan="2">
						<table>
							<td>
								<b>Insurance Verified?</b><br />
								<?= $registration->insurance_verified == 1 ? 'Yes' : 'No';?>
							</td>
							<td>
								<b>Pre-Authorization Completed?</b><br />
								<?= $registration->is_pre_authorization_completed ? 'Yes' : 'No';?>
							</td>
						</table>
					</td>
				</tr>
				<tr>
					<td>
					</td>
				</tr>
			</table>
			<div class="signatures">
				<div class="left"><h2>Signature: ____________________</h2> </div>
				<div class="right"><h2>Date: ________________</h2> </div>
			</div>
		</div>
		<div class="page-break"></div>
		<?php endforeach;?>

		<div class="page-break"></div>
		<div class="page">
			<table class="no-border">
				<tbody>
				<tr>
					<td>
						<table>
							<td colspan="2"><h2>Eligibility & Pre-Auth</h2></td>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table>
							<td>
								<b>Out of Network Benefits</b><br />
								<?= $registration->oon_benefits == 1 ? 'Yes' : 'No';?>
							</td>
							<?php if ($registration->oon_benefits == 1): ?>
								<td >
									<b>Pre-Certification Required?</b><br />
									<?= $registration->pre_certification_required ? 'Yes' : 'No';?>
								</td>
								<td>
									<b>Pre-Certification Obtained?</b><br />
									<?= $registration->pre_certification_obtained ? 'Yes' : 'No';?>
								</td>
								<td>
									<b>Self-Funded?</b><br />
									<?= $registration->self_funded ? 'Yes' : 'No';?>
								</td>
							<?php endif; ?>
						</table>
					</td>
				</tr>
				<?php if ($registration->oon_benefits == 1): ?>
					<tr>
						<td>
							<table>
								<td>
									<b>Coverage Type</b><br />
									<?= $registration->getCoverageTypeName();?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Effective Date</b><br />
									<?= $registration->effective_date;?>
								</td>
								<td>
									<b>Term Date</b><br />
									<?= $registration->term_date;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Renewal Date</b><br />
									<?= $registration->renewal_date;?>
								</td>
								<td></td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Co-Pay ($)</b><br />
									<?= $registration->co_pay;?>
								</td>
								<td>
									<b>Co-Insurance (%)</b><br />
									<?= $registration->co_insurance;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Patients Responsibility (%)</b><br />
									<?= $registration->patients_responsibility ;?>
								</td>
								<td></td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Individual Deductible ($)</b><br />
									<?= $registration->individual_deductible;?>
								</td>
								<td>
									<b>Met-to-Date ($)</b><br />
									<?= $registration->individual_met_to_date;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Remaining ($)</b><br />
									<?= $registration->individual_remaining_1;?>
								</td>
								<td>
									<b>Out of Pocket Maximum ($)</b><br />
									<?= $registration->individual_out_of_pocket_maximum;?>
								</td>
								<td>
									<b>Remaining ($)</b><br />
									<?= $registration->individual_remaining_2;?>
								</td>
							</table>
						</td>
					</tr>

					<tr>
						<td>
							<table>
								<td>
									<b>Family Deductible ($)</b><br />
									<?= $registration->family_deductible ;?>
								</td>
								<td>
									<b>Met-to-Date ($)</b><br />
									<?= $registration->family_met_to_date ;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Remaining ($)</b><br />
									<?= $registration->family_remaining_1;?>
								</td>
								<td>
									<b>Out of Pocket Maximum ($)</b><br />
									<?= $registration->family_out_of_pocket_maximum;?>
								</td>
								<td>
									<b>Remaining ($)</b><br />
									<?= $registration->family_remaining_2;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>

								<td>
									<b>Yearly Maximum ($)</b><br />
									<?= $registration->yearly_maximum;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Lifetime Maximum ($)</b><br />
									<?= $registration->lifetime_maximum;?>
								</td>
								<td></td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Is there an OON Benefits Cap on Patient's Policy?</b><br />
									<?= $registration->is_oon_benefits_cap ? 'Yes' : 'No';?>
									<?= $registration->oon_benefits_cap;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Is there an ASC Benefits Cap on Patient's Policy?</b><br />
									<?= $registration->is_asc_benefits_cap ? 'Yes' : 'No';?>
									<?= $registration->asc_benefits_cap;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Are there any pre-existing clauses under Patient's Policy?</b><br />
									<?= $registration->is_pre_existing_clauses ? 'Yes' : 'No';?>
									<?= $registration->pre_existing_clauses;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>If Yes, what body part?</b><br />
									<?= $registration->body_part;?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Are there any clauses pertaining to Medicare entitlement?</b><br />
									<?= $registration->is_clauses_pertaining ? 'Yes' : 'No';?>
								</td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Subscribers Name</b><br />
									<?= $registration->subscribers_name;?>
								</td>
								<td>
									<b>Authorization No.</b><br />
									<?= $registration->authorization_number;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Expiration</b><br />
									<?= $registration->expiration;?>
								</td>
								<td></td>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<td>
									<b>Spoke With</b><br />
									<?= $registration->spoke_with;?>
								</td>
								<td>
									<b>Reference No</b><br />
									<?= $registration->reference_number;?>
								</td>
							</table>
						</td>
						<td>
							<table>
								<td>
									<b>Staff Member Name</b><br />
									<?= $registration->staff_member_name;?>
								</td>
								<td>
									<b>Date</b><br />
									<?= $registration->date;?>
								</td>
							</table>
						</td>
					</tr>
				<?php endif; ?>

				</tbody>
			</table>
			<div class="signatures">
				<div class="left"><h2>Signature: ____________________</h2> </div>
				<div class="right"><h2>Date: ________________</h2> </div>
			</div>

		</div>
	<?php endif; ?>
	</body>
</html>