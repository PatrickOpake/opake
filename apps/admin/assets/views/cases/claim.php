<div ng-controller="CaseCrtl as caseVm" ng-init="caseVm.init(<?= $case->id ?>)" ng-cloak>

	<errors src="caseVm.errors.case"></errors>

	<div class="panel-data case-panel" ng-include="view.get('cases/' + caseVm.action + '.html')"></div>
</div>

<div class="panel-data claim-page">
	<div class="row">
		<div class="col-sm-6">
			<div class="row">
				<div class="col-sm-6 no-padding-right">
					<table class="claim-table ">
						<tr>
							<td class="title">1
							<td><?= $claim->provider_name ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->provider_address ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->provider_city->name ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->provider_state->name ? $claim->provider_state->name . ', ' : '' ?><?= $claim->provider_zip_code ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->provider_phone ? $claim->provider_phone . ', ' : '' ?><?= $claim->provider_fax ?></td>
						</tr>

					</table>
				</div>
				<div class="col-sm-6 no-padding">
					<table class="claim-table ">
						<tr>
							<td class="title">2</td>
							<td><?= $claim->pay_name ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->pay_address ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->pay_city->name ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->pay_state->name ?></td>
						</tr>
						<tr>
							<td class="title">&nbsp;</td>
							<td><?= $claim->pay_zip_code ?></td>
						</tr>

					</table>
				</div>
			</div>
		</div>

		<div class="col-sm-6 no-padding-left">
			<div class="row">
				<div class="col-sm-12">
					<table class="claim-table">
						<tr>
							<th style="width: 110px;">3a PAT. CNTL #</th>
							<td><?= $case->id ?></td>
							<th class="bgr-black w120p">4 TYPE OF BILL</th>
						</tr>
						<tr>
							<th class="bgr-grey">b. MED. REC. #</th>
							<td><?= $patient->getFullMrn() ?></td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<table class="claim-table">
						<tr>
							<th class="bgr-grey w200p">5 FED. TAX NO.</th>
							<th class="bgr-grey w300p" colspan="2">
								<div class="row">
									<div class="col-sm-12">6 STATEMENT COVERS PERIOD</div>
								</div>
								<div class="row">
									<div class="col-sm-6">FROM</div>
									<div class="col-sm-6">THROUGH</div>
								</div>
							</th>
							<td rowspan="2" colspan="2" class="title">7</td>
							<td rowspan="2" colspan="2" class="w200p">&nbsp;</td>
						</tr>

						<tr>
							<td><?= $claim->federal_tax ?></td>
							<td><?= \Opake\Helper\TimeFormat::getDate($case->time_start) ?></td>
							<td><?= \Opake\Helper\TimeFormat::getDate($case->time_end) ?></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<table class="w1200p">
		<tr>
			<td>
				<table class="claim-table">
					<tr>
						<th class="bgr-grey w150p">8 PATIENT NAME</th>
						<th class="title bgr-grey">a</th>
						<td></td>
						<th class="bgr-grey w150p">9 PATIENT ADDRESS</th>
						<th class="title bgr-grey">a</th>
						<td colspan="7"><?= $patient->home_address ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table class="claim-table">
					<th class="title bgr-grey">b</th>
					<td class="w570p"><?= $patient->getFullName() ?></td>
					<th class="title bgr-grey">b</th>
					<td><?= $patient->home_city->name ?></td>
					<th class="title bgr-grey">c</th>
					<td><?= $patient->home_state->name ?></td>
					<th class="title bgr-grey">d</th>
					<td><?= $patient->home_zip_code ?></td>
					<th class="title bgr-grey">e</th>
					<td><?= $patient->home_country->iso3 ?></td>
				</table>
			</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th>10 BIRTHDATE</th>
			<th>11 SEX</th>
			<th class="bgr-grey" colspan="4">
				<div class="row">
					<div class="col-sm-12 text-center">
						ADMISSION
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">12 DATE</div>
					<div class="col-sm-3">13 HR</div>
					<div class="col-sm-3">14 TYPE</div>
					<div class="col-sm-3">15 SRC</div>
				</div>
			</th>
			<th>16 DHR</th>
			<th>17 STAT</th>
			<th class="bgr-grey" colspan="11">
				<div class="row text-center">
					<div class="col-sm-11">CONDITION CODES</div>
				</div>
				<div class="row">
					<div class="col-sm-1">18</div>
					<div class="col-sm-1">19</div>
					<div class="col-sm-1">20</div>
					<div class="col-sm-1">21</div>
					<div class="col-sm-1">22</div>
					<div class="col-sm-1">23</div>
					<div class="col-sm-1">24</div>
					<div class="col-sm-1">25</div>
					<div class="col-sm-1">26</div>
					<div class="col-sm-1">27</div>
					<div class="col-sm-1">28</div>
				</div>
			</th>
			<th class="bgr-grey">29 ACDT STATE</th>
			<th class="bgr-grey">30</th>
		</tr>
		<tr>
			<td><?= $patient->dob ?></td>
			<td><?= $patient->getGender() ?></td>
			<td><?= \Opake\Helper\TimeFormat::getDate($case->time_start) ?></td>
			<td>&nbsp;</td>
			<td><?= $coding->getAdmissionType() ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><?= $coding->discharge_status->code ?></td>
			<?php for ($i = 0; $i < 11; $i++) {
				if (isset($condition_codes[$i])) {
					echo '<td>' . $condition_codes[$i] . '</td> ';
				} else {
					echo '<td>&nbsp;</td>';
				}
			} ?>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey" colspan="2">
				<div class="row">
					<div class="col-sm-12 text-center">31 OCCURRENCE</div>
				</div>
				<div class="row">
					<div class="col-sm-6">CODE</div>
					<div class="col-sm-6">DATE</div>
				</div>
			</th>
			<th class="bgr-black" colspan="2">
				<div class="row">
					<div class="col-sm-12 text-center">32 OCCURRENCE</div>
				</div>
				<div class="row">
					<div class="col-sm-6">CODE</div>
					<div class="col-sm-6">DATE</div>
				</div>
			</th>
			<th class="bgr-grey" colspan="2">
				<div class="row">
					<div class="col-sm-12 text-center">33 OCCURRENCE</div>
				</div>
				<div class="row">
					<div class="col-sm-6">CODE</div>
					<div class="col-sm-6">DATE</div>
				</div>
			</th>
			<th class="bgr-black" colspan="2">
				<div class="row">
					<div class="col-sm-12 text-center">34 OCCURRENCE</div>
				</div>
				<div class="row">
					<div class="col-sm-6">CODE</div>
					<div class="col-sm-6">DATE</div>
				</div>
			</th>
			<th class="bgr-grey" colspan="3">
				<div class="row">
					<div class="col-sm-12 text-center">35 OCCURRENCE SPAN</div>
				</div>
				<div class="row">
					<div class="col-sm-4">CODE</div>
					<div class="col-sm-4">FROM</div>
					<div class="col-sm-4">THROUGH</div>
				</div>
			</th>
			<th class="bgr-grey" colspan="3">
				<div class="row">
					<div class="col-sm-12 text-center">36 OCCURRENCE SPAN</div>
				</div>
				<div class="row">
					<div class="col-sm-4">CODE</div>
					<div class="col-sm-4">FROM</div>
					<div class="col-sm-4">THROUGH</div>
				</div>
			</th>
			<th class="bgr-grey">37</th>
		</tr>
		<tr>
			<?php for ($i = 0; $i < 4; $i++) {
				if (isset($occurrence_codes[$i])) {
					echo '<td>' . $occurrence_codes[$i]['code'] . '</td>';
					echo '<td>' . $occurrence_codes[$i]['date'] . '</td>';
				} else {
					echo '<td></td><td></td>';
				}

			} ?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<?php for ($i = 4; $i < 8; $i++) {
				if (isset($occurrence_codes[$i])) {
					echo '<td>' . $occurrence_codes[$i]['code'] . '</td>';
					echo '<td>' . $occurrence_codes[$i]['date'] . '</td>';
				} else {
					echo '<td></td><td></td>';
				}

			} ?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>

			<td class="title" rowspan="5">
				<div class="row">
					<div class="col-sm-12">
						a
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						b
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						c
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						d
					</div>
				</div>
			</td>

			<td class="no-padding">
				<table class="">
					<tr>
						<th class="bgr-grey">39 CODE</th>
						<th class="bgr-grey">VALUE CODES AMOUNT</th>
						<th class="bgr-black">40 CODE</th>
						<th class="bgr-black">VALUE CODES AMOUNT</th>
						<th class="bgr-grey">41 CODE</th>
						<th class="bgr-grey">VALUE CODES AMOUNT</th>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th>42 REV. CD</th>
			<th>43 DESCRIPTION</th>
			<th>44 HCPCS / RATE / HIPPS CODE</th>
			<th>45 SERV. DATE</th>
			<th>46 SERV. UNITS</th>
			<th>47 TOTAL CHARGES</th>
			<th>48 NON-COVERED CHARGES</th>
			<th>49</th>
		</tr>
		<?php foreach ($case->coding->procedures->find_all() as $procedure): ?>
			<tr>
				<?php $charge_master = $procedure->getChargeMaster() ?>
				<td> <?= isset($charge_master->revenue_code) ? $charge_master->revenue_code : '' ?> </td>
				<td> <?= isset($charge_master->desc) ? $charge_master->desc : '' ?> </td>
				<td><?= $procedure->cpt->code ?></td>
				<td><?= \Opake\Helper\TimeFormat::getDate($procedure->date) ?></td>
				<td><?= $procedure->qty ?></td>
				<td><?= \Opake\Helper\Currency::formatUSD((int)$procedure->qty * (float)$procedure->cost) ?></td>
				<td> <?= isset($charge_master->amount) ? \Opake\Helper\Currency::formatUSD($charge_master->amount) : '' ?> </td>
				<td></td>
			</tr>
		<?php endforeach; ?>
		<?php foreach ($case->coding->supplies->find_all() as $supply): ?>
			<tr>
				<?php
				$charge_service = $this->pixie->services->get('master_charges');
				$charge_master = $charge_service->getChargeByCPT($supply->hcpcs->hcpcs, $case->organization_id);
				?>
				<td> <?= isset($charge_master->revenue_code) ? $charge_master->revenue_code : '' ?> </td>
				<td> <?= isset($charge_master->desc) ? $charge_master->desc : '' ?> </td>
				<td><?= $supply->hcpcs->hcpcs ?></td>
				<td><?= \Opake\Helper\TimeFormat::getDate($supply->date) ?></td>
				<td><?= $supply->qty ?></td>
				<td><?= \Opake\Helper\Currency::formatUSD((int)$supply->qty * (float)$supply->cost) ?></td>
				<td><?= isset($charge_master->amount) ? \Opake\Helper\Currency::formatUSD($charge_master->amount) : '' ?> </td>
				<td></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td>&nbsp;</td>
			<th colspan="2"></th>
			<td>&nbsp;</td>
			<th class="bgr-black">TOTALS</th>
			<td><?= \Opake\Helper\Currency::formatUSD((double)$coding->total_cost) ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey">50 PAYER NAME</th>
			<th class="bgr-grey">51 HEALTH PLAN ID</th>
			<th class="bgr-grey">52 REL. INFO</th>
			<th class="bgr-grey" rowspan="4">&nbsp;</th>
			<th class="bgr-grey">53 ASG. BEN.</th>
			<th class="bgr-grey">54 PRIOR PAYMENTS</th>
			<th class="bgr-grey">55 EST. AMOUNT DUE</th>
			<th class="bgr-grey">56 NPI</th>
			<th class="bgr-grey"><?= $claim->npi ?></th>
		</tr>
		<?php foreach ($registration->getSelectedInsurances() as $insurance): ?>
			<tr>
				<td><?= $insurance->insurance->name; ?></td>
				<td><?= $insurance->insurance->remote_payor_id; ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<th rowspan="3">57 OTHER PRV ID</th>
				<td></td>

			</tr>
		<?php endforeach; ?>
	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey">58 INSURED’S NAME</th>
			<th class="bgr-grey">59 P.REL</th>
			<th class="bgr-grey">60 INSURED’S UNIQUE ID</th>
			<th class="bgr-grey">61 GROUP NAME</th>
			<th class="bgr-grey">62 INSURANCE GROUP NO.</th>
		</tr>
		<?php foreach ($registration->getSelectedInsurances() as $insurance): ?>
			<tr>
				<td><?= $registration->getInsuredName() ?></td>
				<td><?= $registration->getRelationshipToInsured() ?></td>
				<td><?= $insurance->member_id; ?></td>
				<td><?= $insurance->insurance->payor_group; ?></td>
				<td>&nbsp;</td>
			</tr>
		<?php endforeach; ?>

	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey">63 TREATMENT AUTHORIZATION CODES</th>
			<th class="bgr-grey">64 DOCUMENT CONTROL NUMBER</th>
			<th class="bgr-grey">65 EMPLOYER NAME</th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey title">66 DX</th>
			<?php for ($i = 0; $i < 10; $i++) {
				if (isset($icds[$i])) {
					echo '<td>' . $icds[$i]->code . '</td>';
				} else {
					echo '<td></td>';
				}
			} ?>
			<th class="title">68</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
	<table class="claim-table">
		<tr>
			<th class="bgr-grey w150p">69 ADMIT DX</th>
			<td>
				<?php foreach ($registration->admitting_diagnosis->find_all() as $diagnosis): ?>
					<?= $diagnosis->code?>
				<?php endforeach;?>
			</td>
			<th class="bgr-grey w200p">70 PATIENT REASON DX</th>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<th class="bgr-grey w150p">71 PPS CODE
			</td>
			<td>&nbsp;</td>
			<th class="bgr-grey w150p">72 ECI
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<th class="title">73</th>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="row">
		<div class="col-sm-6 no-padding-right">
			<table class="claim-table">
				<tr>
					<th class="bgr-grey" colspan="2">
						<div class="row">
							<div class="col-sm-12">74 PRINCIPAL PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
					<th class="bgr-black" colspan="2">
						<div class="row">
							<div class="col-sm-12">a. OTHER PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
					<th class="bgr-grey" colspan="2">
						<div class="row">
							<div class="col-sm-12">b. OTHER PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
					<td rowspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<th class="bgr-black" colspan="2">
						<div class="row">
							<div class="col-sm-12">c. OTHER PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
					<th class="bgr-grey" colspan="2">
						<div class="row">
							<div class="col-sm-12">d. OTHER PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
					<th class="bgr-black" colspan="2">
						<div class="row">
							<div class="col-sm-12">e. OTHER PROCEDURE</div>
						</div>
						<div class="row">
							<div class="col-sm-6">CODE</div>
							<div class="col-sm-6">DATE</div>
						</div>
					</th>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table class="claim-table">
				<tr>
					<th class="w150p"><span>80 REMARKS</span></th>
					<td><?= $case->type->code ?></td>
					<th>
						81 CC
						a
					</th>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<th class="bgr-grey title">b</th>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<th class="title">c</th>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
					<th class="bgr-grey title">d</th>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<div class="col-sm-6 no-padding-left">
			<table class="w100">
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<th class="bgr-grey w150p">76 ATTENDING</th>
								<td class="title"><span>NPI</span></td>
								<td></td>
								<th class="bgr-grey w150p">QUAL</th>
								<td></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<td class="title"><span>LAST</span></td>
								<td><?= $patient->last_name ?></td>
								<td class="title"><span>FIRST</span></td>
								<td><?= $patient->first_name ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<th class="bgr-grey w150p">77 OPERATING</th>
								<td class="title"><span>NPI</span></td>
								<td></td>
								<td class="bgr-grey w150p">QUAL</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<td class="title"><span>LAST</span></td>
								<td></td>
								<td class="title"><span>FIRST</span></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<th class="bgr-grey w150p">78 OPERATING</th>
								<td class="title"><span>NPI</span></td>
								<td></td>
								<td class="bgr-grey w150p">QUAL</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<td class="title"><span>LAST</span></td>
								<td></td>
								<td class="title"><span>FIRST</span></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<th class="bgr-grey w150p">79 OPERATING</th>
								<td class="title"><span>NPI</span></td>
								<td></td>
								<th class="bgr-grey w150p">QUAL</th>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table class="claim-table">
							<tr>
								<td class="title"><span>LAST</span></td>
								<td></td>
								<td class="title"><span>FIRST</span></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>