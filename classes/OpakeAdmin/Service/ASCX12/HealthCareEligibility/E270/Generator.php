<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\BHT;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\DMG;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\EQ;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\GE;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\GS;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\HL;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\IEA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\ISA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\NM1;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\SE;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\ST;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E270\Header\TRN;

class Generator
{
	protected $header_array;
	protected $time;
	protected $interchange_control_number;
	protected $edi_string;
	protected $hl_counter;
	protected $requestParams;

	public function __construct ($requestParams)
	{
		$this->interchange_control_number = date("mdHi");
		$this->time = time();
		$this->hl_counter = 1;
		$this->requestParams = $requestParams;

	}

	public function getEDI()
	{
		$this->createAllHeaders();
		$this->stringifyEdi();
		// Remove after tests
//		$this->createOutputFile();
		return $this->edi_string;
	}

	protected function createAllHeaders(){
		$this->isaHeader();
		$this->gsHeader();
		$this->stHeader();
		$this->bhtHeader();
		$this->infoSourceLevel();
		$this->infoReceiverLevel();
		$this->infoSubscriberDetail();
		if(!$this->requestParams->isSelfRelation()) {
			$this->infoDependentDetail();
		}
		$this->seHeader();
		$this->geHeader();
		$this->ieaHeader();
	}

	protected function isaHeader()
	{
		$date_edi_created = date("ymd",$this->time);
		$time_edi_created = date("Hi",$this->time);
		$interchange_control_number = '0'.$this->interchange_control_number;
		$test_or_production = 'T';
		$this->header_array[] = new ISA($date_edi_created, $time_edi_created, $interchange_control_number, $test_or_production);
	}

	protected function gsHeader()
	{
		$date_edi_created = date("Ymd",$this->time);
		$time_edi_created = date("Hi",$this->time);
		$group_control_number = '9'.$this->interchange_control_number;
		$this->header_array[] = new GS($date_edi_created, $time_edi_created, $group_control_number);
	}

	protected function stHeader()
	{
		$asn_id = $this->interchange_control_number;
		$this->header_array[] = new ST($asn_id);
	}

	protected function bhtHeader()
	{
		$date_edi_created = date("Ymd",$this->time);
		$time_edi_created = date("Hi",$this->time);
		$this->header_array[] = new BHT($date_edi_created, $time_edi_created);
	}

	protected function infoSourceLevel()
	{
		$this->hlInfoSourceLevelHeader();
		$this->nmInfoSourceHeader();
	}

	protected function hlInfoSourceLevelHeader()
	{
		$hl_id = $this->hl_counter++;
		$hl_parent_id = '';
		$hl_code = '2O';
		$child_node = '0';
		$this->header_array[] = new HL($hl_id, $hl_parent_id, $hl_code, $child_node);
	}

	protected function nmInfoSourceHeader()
	{
		$id_code = 'PR';
		$type_qualifier = '2';
		$org_name = $this->requestParams->getInsurancePayor()->name;
		$id_code_qualifier = 'PI';
		$identification_code = $this->requestParams->getNavicurePayorId();
		$this->header_array[] = new NM1($id_code, $type_qualifier, $org_name, '', $id_code_qualifier, $identification_code);
	}

	protected function infoReceiverLevel()
	{
		$this->hlInfoReceiverLevelHeader();
		$this->nmInfoReceiverHeader();
	}

	protected function hlInfoReceiverLevelHeader()
	{
		$hl_id = $this->hl_counter++;
		$hl_parent_id = '1';
		$hl_code = '21';
		$child_node = '1';
		$this->header_array[] = new HL($hl_id, $hl_parent_id, $hl_code, $child_node);
	}

	protected function nmInfoReceiverHeader()
	{
		$id_code = '1P';
		$type_qualifier = '2';
		$org_name = $this->requestParams->getOrganizationName();
		$id_code_qualifier = 'SV';
		$identification_code = $this->requestParams->getOrganizationNpi();
		$this->header_array[] = new NM1($id_code, $type_qualifier, $org_name, '', $id_code_qualifier, $identification_code);
	}

	protected function infoSubscriberDetail()
	{
		$this->hlSubscriberDetailHeader();
		$this->trnSubscriberDetailHeader();
		$this->nmSubscriberDetailHeader();
		$this->dmgSubscriberDetailHeader();
		$this->eqSubscriberDetailHeader();
	}

	protected function hlSubscriberDetailHeader()
	{
		$hl_id = $this->hl_counter++;
		$hl_parent_id = '2';
		$hl_code = '22';
		$child_node = '1';
		if($this->requestParams->isSelfRelation()) {
			$child_node = '0';
		}
		$this->header_array[] = new HL($hl_id, $hl_parent_id, $hl_code, $child_node);
	}

	protected function trnSubscriberDetailHeader()
	{
		$trace_type_code  = '1';
		$ref_id  = $this->interchange_control_number;
		$company_id  = $this->interchange_control_number;
		$this->header_array[] = new TRN($trace_type_code, $ref_id, $company_id);
	}

	protected function nmSubscriberDetailHeader()
	{
		$id_code = 'IL';
		$type_qualifier = '1';
		$last_name = $this->requestParams->getMemberLastName();
		$first_name = $this->requestParams->getMemberFirstName();
		if($this->requestParams->isSelfRelation()) {
			$last_name = $this->requestParams->getPatientLastName();
			$first_name = $this->requestParams->getPatientFirstName();
		}
		$id_code_qualifier = 'MI';
		$identification_code = $this->requestParams->getMemberId();
		$this->header_array[] = new NM1($id_code, $type_qualifier, $last_name, $first_name, $id_code_qualifier, $identification_code);
	}

	protected function dmgSubscriberDetailHeader()
	{
		$dob = null;
		if($this->requestParams->getMemberDateOfBirth()) {
			$dob = (new \DateTime($this->requestParams->getMemberDateOfBirth()))->format('Ymd');
		} elseif($this->requestParams->isSelfRelation()) {
			$dob = (new \DateTime($this->requestParams->getPatientDateOfBirth()))->format('Ymd');
		}
		$this->header_array[] = new DMG($dob);
	}

	protected function eqSubscriberDetailHeader()
	{
		$code = '30';
		$this->header_array[] = new EQ($code);
	}

	protected function infoDependentDetail()
	{
		$this->hlDependentDetailHeader();
		$this->trnDependentDetailHeader();
		$this->nmDependentDetailHeader();
		$this->dmgDependentDetailHeader();
		$this->eqDependentDetailHeader();
	}

	protected function hlDependentDetailHeader()
	{
		$hl_id = $this->hl_counter++;
		$hl_parent_id = '3';
		$hl_code = '23';
		$child_node = '0';
		$this->header_array[] = new HL($hl_id, $hl_parent_id, $hl_code, $child_node);
	}

	protected function trnDependentDetailHeader()
	{
		$trace_type_code  = '1';
		$ref_id  = $this->interchange_control_number;
		$company_id  = $this->interchange_control_number;
		$this->header_array[] = new TRN($trace_type_code, $ref_id, $company_id);
	}

	protected function nmDependentDetailHeader()
	{
		$id_code = '03';
		$type_qualifier = '1';
		$last_name = $this->requestParams->getPatientLastName();
		$first_name = $this->requestParams->getPatientFirstName();
		$id_code_qualifier = '';
		$identification_code = '';
		$this->header_array[] = new NM1($id_code, $type_qualifier, $last_name, $first_name, $id_code_qualifier, $identification_code);
	}

	protected function dmgDependentDetailHeader()
	{
		$dob = (new \DateTime($this->requestParams->getPatientDateOfBirth()))->format('Ymd');
		$this->header_array[] = new DMG($dob);
	}

	protected function eqDependentDetailHeader()
	{
		$this->eqSubscriberDetailHeader();
	}


	protected function seHeader()
	{
		$num_of_segments = count($this->header_array);
		$transaction_set_control_num = $this->interchange_control_number;
		$this->header_array[] = new SE($num_of_segments, $transaction_set_control_num);
	}

	protected function geHeader()
	{
		$number_of_transaction_sets = 1;
		$group_control_number = '9'.$this->interchange_control_number;
		$this->header_array[] = new GE($number_of_transaction_sets, $group_control_number);
	}

	protected function ieaHeader()
	{
		$num_of_functional_groups = 1;
		$interchange_control_number = '0'.$this->interchange_control_number;
		$this->header_array[] = new IEA($num_of_functional_groups, $interchange_control_number);
	}

	protected function stringifyEdi()
	{
		$array = $this->header_array;
		foreach($array as $header){
			$this->edi_string .= $header->__toString();
		}
	}

	protected function createOutputFile()
	{
		file_put_contents('testFile', $this->edi_string);
	}
}