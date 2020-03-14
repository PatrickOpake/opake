<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;
use Opake\Model\Cases\Registration;

class ChangesFormatter extends DefaultFormatter
{

	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'bill_provider_id':
			case 'rendering_provider_id':
				return FormatterHelper::formatUser($this->pixie, $value);

			case 'final_diagnosis':
			case 'admit_diagnosis':
				return FormatterHelper::formatICDList($this->pixie, $value);

			case 'notes':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items\NotesRowFormatter');

			case 'apcs':
				return FormatterHelper::formatAPCList($this->pixie, $value);

			case 'drgs':
				return FormatterHelper::formatDRGList($this->pixie, $value);

			case 'place_of_service_id':
				return FormatterHelper::formatPlaceOfService($this->pixie, $value);

			case 'admission_type':
				return FormatterHelper::formatKeyValueSource($value, Registration::getAdmissionTypesList());

			case 'discharge_status_id':
				return FormatterHelper::formatDischarge($this->pixie, $value);

			case 'procedures':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items\ProcedureRowFormatter');

			case 'occurences':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items\OccurrenceRowFormatter');

			case 'supplies':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items\SupplyRowFormatter');


		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'rendering_provider_id' => 'Rendering Provider',
			'rendering_provider_npi' => 'Rendering Provider NPI',
			'bill_provider_id' => 'Bill Provider',
			'bill_provider_npi' => 'Bill Provider NPI',
			'pre_auth' => 'Pre-Auth #',
			'place_of_service_id' => 'Place of Service',
			'facility_name' => 'Facility Name',
			'prior_payments' => 'Prior Payments',
			'admission_type' => 'Admission Type',
			'discharge_status_id' => 'Discharge Status',
			'apcs' => 'APC',
			'drgs' => 'DRG',
			'final_diagnosis' => 'Final Diagnosis',
			'admit_diagnosis' => 'Admit Diagnosis',
			'procedures' => 'Procedure',
			'occurences' => 'Occurrence',
			'supplies' => 'Supplies',
			'notes' => 'Notes',
		];
	}
}