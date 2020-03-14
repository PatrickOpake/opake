<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class AAA extends Segment
{
	const FIELD_SIZE = 4;
	const NAME = 'AAA';

	protected $rejectReasonCodes = [
		'04' => 'Authorized Quantity Exceeded',
		'41' => 'Authorization/Access Restrictions',
		'42' => 'Unable to Respond at Current Time',
		'79' => 'Invalid Participant Identification',
		'15' => 'Required application data missing',
		'43' => 'Invalid/Missing Provider Identification',
		'44' => 'Invalid/Missing Provider Name',
		'45' => 'Invalid/Missing Provider Specialty',
		'46' => 'Invalid/Missing Provider Phone Number',
		'47' => 'Invalid/Missing Provider State',
		'48' => 'Invalid/Missing Referring Provider Identification Number',
		'50' => 'Provider Ineligible for Inquiries',
		'51' => 'Provider Not on File',
		'97' => 'Invalid or Missing Provider Address',
		'T4' => 'Payer Name or Identifier Missing',
		'35' => 'Out of Network',
		'49' => 'Provider is Not Primary Care Physician',
		'52' => 'Service Dates Not Within Provider Plan Enrollment',
		'56' => 'Inappropriate Date',
		'57' => 'Invalid/Missing Date(s) of Service',
		'58' => 'Invalid/Missing Date-of-Birth',
		'60' => 'Date of Birth Follows Date(s) of Service',
		'61' => 'Date of Death Precedes Date(s) of Service',
		'62' => 'Date of Service Not Within Allowable Inquiry Period',
		'63' => 'Date of Service in Future',
		'64' => 'Invalid/Missing Patient ID',
		'65' => 'Invalid/Missing Patient Name',
		'66' => 'Invalid/Missing Patient Gender Code',
		'67' => 'Patient Not Found',
		'68' => 'Duplicate Patient ID Number',
		'71' => 'Patient Birth Date Does Not Match That for the Patient on the Database',
		'72' => 'Invalid/Missing Subscriber/Insured ID',
		'73' => 'Invalid/Missing Subscriber/Insured Name',
		'74' => 'Invalid/Missing Subscriber/Insured Gender Code',
		'75' => 'Subscriber/Insured Not Found',
		'76' => 'Duplicate Subscriber/Insured ID Number',
		'77' => 'Subscriber Found, Patient Not Found',
		'78' => 'Subscriber/Insured Not in Group/Plan Identified',
		'33' => 'Input Errors',
		'53' => 'Inquired Benefit Inconsistent with Provider Type',
		'54' => 'Inappropriate Product/Service ID Qualifier',
		'55' => 'Inappropriate Product/Service ID',
		'69' => 'Inconsistent with Patient’s Age',
		'70' => 'Inconsistent with Patient’s Gender',
		'98' => 'Experimental Service or Procedure',
		'AA' => 'Authorization Number Not Found',
		'AE' => 'Requires Primary Care Physician Authorization',
		'AF' => 'Invalid/Missing Diagnosis Code(s)',
		'AG' => 'Invalid/Missing Procedure Code(s)',
		'AO' => 'Invalid/Missing Procedure Code(s)',
		'CI' => 'Certification Information Does Not Match Patient',
		'E8' => 'Requires Medical Review',
		'IA' => 'Invalid Authorization Number Format',
		'MA' => 'Missing Authorization Number',
	];

	protected $followUpActionCodes = [
		'C' => 'Please Correct and Resubmit',
		'N' => 'Resubmission Not Allowed',
		'P' => 'Please Resubmit Original Transaction',
		'R' => 'Resubmission Allowed',
		'S' => 'Do Not Resubmit; Inquiry Initiated to a Third Party',
		'Y' => 'Do Not Resubmit; We Will Hold Your Request and Respond Again Shortly',
		'W' => 'Please Wait 30 Days and Resubmit',
		'X' => 'Please Wait 10 Days and Resubmit',
	];

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getYesNoConditionOrResponseCode()
	{
		return $this->collection[1];
	}

	public function getAgencyQualifierCode()
	{
		return $this->collection[2];
	}

	public function getRejectReasonCode()
	{
		return $this->collection[3];
	}

	public function getFollowupActionCode()
	{
		return $this->collection[4];
	}

	public function setYesNoConditionOrResponseCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setAgencyQualifierCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setRejectReasonCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setFollowupActionCode($s)
	{
		$this->collection[4] = $s;
	}

	public function toArray()
	{
		return [
			'response_code' => $this->getYesNoConditionOrResponseCode(),
			'agency_qualifier_code' => $this->getAgencyQualifierCode(),
			'reject_reason_code' => $this->getRejectReasonCode(),
			'followup_action_code' => $this->getFollowupActionCode(),
		];
	}

	public function getRejectReasonMsg()
	{
		if(isset($this->rejectReasonCodes[$this->getRejectReasonCode()])) {
			return $this->rejectReasonCodes[$this->getRejectReasonCode()];
		}
		return '';
	}

	public function getFollowUpActionMsg()
	{
		if(isset($this->followUpActionCodes[$this->getFollowupActionCode()])) {
			return $this->followUpActionCodes[$this->getFollowupActionCode()];
		}
		return '';
	}
}