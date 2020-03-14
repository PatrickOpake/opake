<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link href='http://<?= $_SERVER['HTTP_HOST']?>/css/export.css' type='text/css' rel='stylesheet'/>
</head>
<body class="case-export">
	<div class="page">
		<table class="no-border">
			<tbody>
			<tr>
				<td>
					<table>
						<td colspan="2"><h2>Patient Information</h2></td>
						<td>
							<div class="mrn-field">
								<b>MRN</b>
								<?php if(isset($patient->fullMrn)):?>
									<?= $patient->fullMrn;?>
								<?php endif;?>
							</div>
						</td>
					</table>

				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Patient Name</b><br/>
							<?php if(isset($patient->fullName)):?>
								<?= $patient->fullName ?>
							<?php endif;?>
						</td>
						<td>
							<b>M.I.</b><br/>
							<?php if(isset($patient->middle_name)):?>
								<?= $patient->middle_name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Suffix</b><br/>
							<?php if(isset($patient->suffix) && $patient->suffix):?>
								<?= \Opake\Model\Patient::getSuffixesList()[$patient->suffix]; ?>
							<?php endif;?>
						</td>
						<td>
							<b>If Minor, Parent's Name </b><br/>
							<?php if(isset($patient->parents_name)):?>
								<?= $patient->parents_name; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Address</b><br/>
							<?php if(isset($patient->home_address)):?>
								<?= $patient->home_address ?>
							<?php endif;?>
						</td>
						<td>
							<b>APT # </b><br/>
							<?php if(isset($patient->home_apt_number)):?>
								<?= $patient->home_apt_number; ?>
							<?php endif;?>
						</td>
						<td>
							<b>State</b><br/>
							<?php if(isset($patient->home_state) || isset($patient->custom_home_state)):?>
								<?php if($patient->home_country->id == 235):?>
									<?= $patient->home_state->name; ?>
								<?php else:?>
									<?= $patient->custom_home_state; ?>
								<?php endif;?>
							<?php endif;?>
						</td>
						<td>
							<b>City</b><br/>
							<?php if(isset($patient->home_city) || isset($patient->custom_home_city)):?>
								<?php if($patient->home_country->id == 235):?>
									<?= $patient->home_city->name; ?>
								<?php else:?>
									<?= $patient->custom_home_city; ?>
								<?php endif;?>
							<?php endif;?>
						</td>
						<td>
							<b>ZIP code</b><br/>
							<?php if(isset($patient->home_zip_code)):?>
								<?= $patient->home_zip_code; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Country</b><br/>
							<?php if(isset($patient->home_country)):?>
								<?= $patient->home_country->name; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Phone #</b><br/>
							<?php if(isset($patient->home_phone)):?>
								<?= $_phone($patient->home_phone) ?>
							<?php endif;?>
						</td>
						<td>
							<b>Type</b><br/>
							<?php if(isset($patient->home_phone_type) && $patient->home_phone_type):?>
								<?= \Opake\Model\Patient::getPhoneTypes()[$patient->home_phone_type];?>
							<?php endif;?>
						</td>
						<td>
							<b>Additional Phone #</b><br/>
							<?php if(isset($patient->additional_phone) && $patient->additional_phone):?>
								<?= $_phone($patient->additional_phone) ?>
							<?php endif;?>
						</td>
						<td>
							<b>Type</b><br/>
							<?php if(isset($patient->additional_phone_type) && $patient->additional_phone_type):?>
								<?= \Opake\Model\Patient::getPhoneTypes()[$patient->additional_phone_type];?>
							<?php endif;?>
						</td>
						<td>
							<b> Email</b><br/>
							<?php if(isset($patient->home_email) && $patient->home_email):?>
								<?= $patient->home_email; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Date of Birth*</b><br/>
							<?php if(isset($patient->dob)):?>
								<?= $_date($patient->dob) ?>
							<?php endif;?>
						</td>
						<td>
							<b>Social Securty # </b><br/>
							<?php if(isset($patient->ssn)):?>
								<?= $_ssn($patient->ssn) ?>
							<?php endif;?>
						</td>
						<td>
							<b>Gender </b><br/>
							<?php if(isset($patient->gender) && $patient->gender):?>
								<?= \Opake\Model\Patient::getGendersList()[$patient->gender]; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Marital Status</b><br/>
							<?php if(isset($patient->status_marital) && $patient->status_marital):?>
								<?= \Opake\Model\Patient::getMartialStatusesList()[$patient->status_marital];?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Emergency Contact </b><br/>
							<?php if(isset($patient->ec_name)):?>
								<?= $patient->ec_name ?>
							<?php endif;?>
						</td>
						<td>
							<b>Relationship</b><br/>
							<?php if(isset($patient->relationship) && $patient->relationship):?>
								<?= \Opake\Model\Patient::getRelationshipList()[$patient->relationship];?>
							<?php endif;?>
						</td>
						<td>
							<b>Emergancy Phone # </b><br/>
							<?php if(isset($patient->ec_phone_number)):?>
								<?= $_phone($patient->ec_phone_number); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Type</b><br/>
							<?php if(isset($patient->ec_phone_type) && $patient->ec_phone_type):?>
								<?= \Opake\Model\Patient::getPhoneTypes()[$patient->ec_phone_type];?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>

			<tr>
				<td>
					<table>
						<td colspan="2"><h2>Case Information</h2></td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Surgeon</b><br/>
							<?= implode(', ', array_map(function($user) {
								return $user->fullname;
							}, $surgeons)); ?>
						</td>
						<td>
							<b>Surgeon Assistant</b><br/>
							<?= implode(', ', array_map(function($user) {
								return $user->fullname;
							}, $assistants)); ?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Admission Type</b><br/>
							<?php if(isset($booking->admission_type) && $booking->admission_type):?>
								<?= \Opake\Model\Cases\Registration::getAdmissionTypesList()[$booking->admission_type]; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Room</b><br/>
							<?php if(isset($booking->room) && $booking->room):?>
								<?= $booking->room->name; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Date of Sevice</b><br/>
							<?php if(isset($booking->time_start)):?>
								<?= $_date($booking->time_start); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Time Start</b><br/>
							<?php if(isset($booking->time_start)):?>
								<?= $_time($booking->time_start); ?>
							<?php endif;?>

						</td>
						<td>
							<b>Length of case</b><br/>
							<?php if(isset($booking->time_start) && isset($booking->time_end)):?>
								<?= $_timeLength($booking->time_start, $booking->time_end); ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Proposed Procedure Codes</b><br/>
							<?php
							foreach ($additional_cpts as $key => $item): ?>
								<?= $item->fullname; ?><?= count($additional_cpts) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
						<td>
							<b>Location</b><br/>
							<?php if(isset($booking->location)):?>
								<?= \Opake\Model\Booking::getLocationList()[$booking->location]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Primary Diagnosis</b><br/>
							<?php foreach ($admitting_diagnosis as $key => $item): ?>
								<?= $item->code; ?> - <?= $item->desc; ?><?= count($admitting_diagnosis) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
						<td>
							<b>Secondary Diagnosis</b><br/>
							<?php foreach ($secondary_diagnosis as $key => $item): ?>
								<?= $item->code; ?> - <?= $item->desc; ?><?= count($secondary_diagnosis) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Pre-Op Data Required</b><br/>
							<?php foreach ($pre_op_required_data as $key => $item): ?>
								<?= \Opake\Model\Booking::getPreOpRequiredList()[$item]; ?><?= count($pre_op_required_data) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
						<td>
							<b>Studies Ordered</b><br/>
							<?php foreach ($studies_ordered as $key => $item): ?>
								<?= \Opake\Model\Booking::getStudiesOrderedList()[$item]; ?><?= count($studies_ordered) - 1 === $key ? '' : ', '?>
							<?php endforeach; ?>
						</td>
						<td>
							<b>Anesthesia Type</b><br/>
							<?php if(isset($booking->anesthesia_type)):?>
								<?= \Opake\Model\Cases\Item::getAnesthesiaTypeList()[$booking->anesthesia_type]; ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<?php
						if(in_array(\Opake\Model\Booking::STUDIES_ORDERED_OTHER, $studies_ordered)):?>
						<td>
							<b>Studies (other)</b><br/>
							<?= $booking->studies_other;?>
						</td>
						<?php endif;?>
						<?php if(\Opake\Model\Cases\Item::ANESTHESIA_TYPE_OTHER == $booking->anesthesia_type):?>
						<td>
							<b>Anesthesia (other)</b><br/>
							<?= $booking->anesthesia_other?>
						</td>
						<?php endif;?>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Special Equipment/Implants</b><br/>
							<?php if(isset($booking->special_equipment_implants)):?>
								<?= $booking->special_equipment_implants ?>
							<?php endif;?>
						</td>
						<td>
							<b>Transport</b><br/>
							<?php if(isset($booking->transportation_notes )):?>
								<?= $booking->transportation_notes ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table>
						<td>
							<b>Description</b><br/>
							<?php if(isset($booking->description )):?>
								<?= $booking->description ?>
							<?php endif;?>
						</td>
					</table>
				</td>
			</tr>

			</tbody>
		</table>
		<?php foreach($insurances as $key => $insurance):?>
			<?php $insuranceData = $insurance->data ?>
			<table class="no-border">
				<?php if($key == 0):?>
					<tr>
						<td>
							<table>
								<td colspan="2"><h2>Insurance Information</h2></td>
							</table>
						</td>
					</tr>
				<?php endif;?>
				<?php if(isset($insurance->title)):?>
				<tr>
					<td>
						<table>
							<td><h2><?= $insurance->title;?></h2></td>
						</table>
					</td>
				</tr>
				<?php endif;?>
				<tr>
					<td>
						<b>Insurance Order</b><br>
						<?php if(isset($insurance->order) && $insurance->order):?>
							<?= \Opake\Model\Booking\Insurance::getPrimaryInsurancesList()[$insurance->order]?>
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td>
						<b>Insurance Type</b><br>
						<?php if(isset($insurance->type) && $insurance->type):?>
							<?= \Opake\Model\Insurance\AbstractType::getInsuranceTypesList()[$insurance->type]?>
						<?php endif;?>
					</td>
				</tr>
				<?php if ($insurance->isRegularInsurance): ?>
					<tr>
						<td>
							<b>Relationship to Patient</b><br>
							<?php if (isset($insuranceData->relationship_to_insured) && !is_null($insuranceData->relationship_to_insured)): ?>
								<?= \Opake\Model\Cases\Registration::getRelationshipInsuredList()[$insuranceData->relationship_to_insured];?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Insurance Company</b><br>
							<?php if ($insurance->isInsuranceCompanyEqualsType):?>
								<span><?= $insurance->insuranceTypeTitle ?></span>
							<?php else:?>
								<?php if(isset($insuranceData->insurance)):?>
									<?= $insuranceData->insurance->name;?>
								<?php endif;?>
							<?php endif;?>
						</td>
						<td>
							<b>Policy #</b><br>
							<?php if(isset($insuranceData->policy_number)):?>
								<?= $insuranceData->policy_number;?>
							<?php endif;?>
						</td>
						<td>
							<b>Group #</b><br>
							<?php if(isset($insuranceData->group_number)):?>
								<?= $insuranceData->group_number;?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Address</b><br>
							<?php if(isset($insuranceData->address_insurance)):?>
								<?= $insuranceData->address_insurance;?>
							<?php endif;?>
						</td>
						<td>
							<b>Provider Phone #</b><br>
							<?php if(isset($insuranceData->provider_phone)):?>
								<?= $_phone($insuranceData->provider_phone);?>
							<?php endif;?>
						</td>
					</tr>

					<?php if(isset($insuranceData->relationship_to_insured ) && $insuranceData->relationship_to_insured != \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_SELF):?>
						<tr>
							<td>
								<b>Insured Last Name</b><br>
								<?php if(isset($insuranceData->last_name)):?>
									<?= $insuranceData->last_name;?>
								<?php endif;?>
							</td>
							<td>
								<b>Insured First Name</b><br>
								<?php if(isset($insuranceData->first_name)):?>
									<?= $insuranceData->first_name;?>
								<?php endif;?>
							</td>
							<td>
								<b>M.I.</b><br>
								<?php if(isset($insuranceData->middle_name)):?>
									<?= $insuranceData->middle_name;?>
								<?php endif;?>
							</td>
							<td>
								<b>Address</b><br>
								<?php if(isset($insuranceData->address)):?>
									<?= $insuranceData->address;?>
								<?php endif;?>
							</td>
							<td>
								<b>Apt #</b><br>
								<?php if(isset($insuranceData->apt_number)):?>
									<?= $insuranceData->apt_number;?>
								<?php endif;?>
							</td>
						</tr>
						<tr>
							<td>
								<b>Date of Birth</b><br>
								<?php if(isset($insuranceData->dob)):?>
									<?= $_date($insuranceData->dob);?>
								<?php endif;?>
							</td>
							<td>
								<b>Gender</b><br>
								<?php if(isset($insuranceData->gender) && $insuranceData->gender):?>
									<?= \Opake\Model\Patient::getGendersList()[$insuranceData->gender];?>
								<?php endif;?>
							</td>
							<td>
								<b>Suffix</b><br>
								<?php if(isset($insuranceData->suffix) && $insuranceData->suffix):?>
									<?= \Opake\Model\Patient::getSuffixesList()[$insuranceData->suffix];?>
								<?php endif;?>
							</td>
							<td>
								<b>State</b><br>
								<?php if( (isset($insuranceData->state) && $insuranceData->state) || (isset($insuranceData->custom_state) && $insuranceData->custom_state)):?>
									<?php if($insuranceData->country->id == 235):?>
										<?= $insuranceData->state->name;?>
									<?php else:?>
										<?= $insuranceData->custom_state;?>
									<?php endif;?>
								<?php endif;?>
							</td>
							<td>
								<b>City</b><br>
								<?php if( (isset($insuranceData->city) && $insuranceData->city) || (isset($insuranceData->custom_city) && $insuranceData->custom_city)):?>
									<?php if($insuranceData->country->id == 235):?>
											<?= $insuranceData->city->name;?>
										<?php else:?>
											<?= $insuranceData->custom_city;?>
										<?php endif;?>
								<?php endif;?>
							</td>
							<td>
								<b>ZIP code</b><br>
								<?php if(isset($insuranceData->zip_code) && $insuranceData->zip_code):?>
									<?= $insuranceData->zip_code;?>
								<?php endif;?>
							</td>
						</tr>
						<tr>
							<td>
								<b>Country</b><br>
								<?php if(isset($insuranceData->country) && $insuranceData->country):?>
									<?= $insuranceData->country->name;?>
								<?php endif;?>
							</td>
							<td>
								<b>Phone #</b><br>
								<?php if(isset($insuranceData->phone) && $insuranceData->phone):?>
									<?= $_phone($insuranceData->phone);?>
								<?php endif;?>
							</td>
						</tr>
					<?php endif;?>
				<?php endif;?>

				<?php if($insurance->isAutoAccidentInsurance):?>
					<tr>
						<td>
							<b>Auto Insurance Name</b><br>
							<?php if(isset($insuranceData->insurance_name) && $insuranceData->insurance_name):?>
								<?= $insuranceData->insurance_name;?>
							<?php endif;?>
						</td>
						<td>
							<b>Auto Adjuster Name</b><br>
							<?php if(isset($insuranceData->adjuster_name) && $insuranceData->adjuster_name):?>
								<?= $insuranceData->adjuster_name;?>
							<?php endif;?>
						</td>
						<td>
							<b>Auto Claim #</b><br>
							<?php if(isset($insuranceData->claim) && $insuranceData->claim):?>
								<?= $insuranceData->claim;?>
							<?php endif;?>
						</td>
						<td>
							<b>Auto Adjuster Phone #</b><br>
							<?php if(isset($insuranceData->adjuster_phone) && $insuranceData->adjuster_phone):?>
								<?= $_phone($insuranceData->adjuster_phone);?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Auto Insurance Address</b><br>
							<?php if(isset($insuranceData->insurance_address) && $insuranceData->insurance_address):?>
								<?= $insuranceData->insurance_address; ?>
							<?php endif;?>
						</td>
						<td>
							<b>State</b><br>
							<?php if(isset($insuranceData->state) && $insuranceData->state):?>
								<?= $insuranceData->state->name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>City</b><br>
							<?php if(isset($insuranceData->city) && $insuranceData->city):?>
								<?= $insuranceData->city->name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Zip</b><br>
							<?php if(isset($insuranceData->zip) && $insuranceData->zip):?>
								<?= $insuranceData->zip; ?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Accident Date</b><br>
							<?php if(isset($insuranceData->accident_date) && $insuranceData->accident_date):?>
								<?= $_date($insuranceData->accident_date); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Attorney Name</b><br>
							<?php if(isset($insuranceData->attorney_name) && $insuranceData->attorney_name):?>
								<?= $insuranceData->attorney_name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Attorney Phone</b><br>
							<?php if(isset($insuranceData->attorney_phone) && $insuranceData->attorney_phone):?>
								<?= $_phone($insuranceData->attorney_phone); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Insurance Company Phone #</b><br>
							<?php if(isset($insuranceData->insurance_company_phone) && $insuranceData->insurance_company_phone):?>
								<?= $_phone($insuranceData->insurance_company_phone); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Authorization #</b><br>
							<?php if(isset($insuranceData->authorization_number) && $insuranceData->authorization_number):?>
								<?= $insuranceData->authorization_number; ?>
							<?php endif;?>
						</td>
					</tr>
				<?php endif;?>
				<?php if ($insurance->isWorkersCompanyInsurance): ?>
					<tr>
						<td>
							<b>Workers Comp Insurance Name</b><br/>
							<?php if(isset($insuranceData->insurance_name) && $insuranceData->insurance_name):?>
								<?= $insuranceData->insurance_name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Workers Comp Adjusters Name</b><br/>
							<?php if(isset($insuranceData->adjuster_name) && $insuranceData->adjuster_name):?>
								<?= $insuranceData->adjuster_name; ?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Workers Comp Claim #</b><br/>
							<?php if(isset($insuranceData->claim) && $insuranceData->claim):?>
								<?= $insuranceData->claim; ?>
							<?php endif;?>
						</td>
						<td>
							<b>Workers Comp Adjuster Phone #</b><br/>
							<?php if(isset($insuranceData->adjuster_phone) && $insuranceData->adjuster_phone):?>
								<?= $_phone($insuranceData->adjuster_phone); ?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Workers Comp Insurance Address</b><br/>
							<?php if(isset($insuranceData->insurance_address) && $insuranceData->insurance_address):?>
								<?= $insuranceData->insurance_address; ?>
							<?php endif;?>
						</td>
						<td>
							<b>State</b><br/>
							<?php if(isset($insuranceData->state) && $insuranceData->state):?>
								<?= $insuranceData->state->name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>City</b><br/>
							<?php if(isset($insuranceData->city) && $insuranceData->city):?>
								<?= $insuranceData->city->name; ?>
							<?php endif;?>
						</td>
						<td>
							<b>ZIP</b><br/>
							<?php if(isset($insuranceData->zip) && $insuranceData->zip):?>
								<?= $insuranceData->zip; ?>
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<b>Accident Date</b><br/>
							<?php if(isset($insuranceData->accident_date) && $insuranceData->accident_date):?>
								<?= $_date($insuranceData->accident_date); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Insurance Company Phone #</b><br>
							<?php if(isset($insuranceData->insurance_company_phone) && $insuranceData->insurance_company_phone):?>
								<?= $_phone($insuranceData->insurance_company_phone); ?>
							<?php endif;?>
						</td>
						<td>
							<b>Authorization #</b><br>
							<?php if(isset($insuranceData->authorization_number) && $insuranceData->authorization_number):?>
								<?= $insuranceData->authorization_number; ?>
							<?php endif;?>
						</td>
					</tr>
				<?php endif;?>
				<?php if ($insurance->isDescriptionInsurance): ?>
					<tr>
						<td colspan="2">
							<b>Description</b>
							<?php if(isset($insuranceData->description)):?>
								<div class="insurance-description"><?= $insuranceData->description; ?></div>
							<?php endif;?>
						</td>
					</tr>
				<?php endif;?>
			</table>
		<?php endforeach;?>
	</div>
</body>
</html>