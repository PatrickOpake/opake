<?php

namespace OpakeAdmin\Service\ASCX12\E277\Response;

use OpakeAdmin\Service\ASCX12\AbstractParser;

class AcknowledgmentParser extends AbstractParser
{

	/**
	 * @return array
	 */
	protected function getParsingConfig()
	{
		return [
			[
				'segments' => ['ISA'],
		        'endSegments' => ['IEA'],
				'class' => '\OpakeAdmin\Service\ASCX12\General\Response\ISAHeader',
				'children' => [
					[
						'segments' => ['GS'],
						'endSegments' => ['GE'],
						'class' => '\OpakeAdmin\Service\ASCX12\General\Response\GSHeader',
						'children' => [
							[
								'segments' => ['ST'],
								'endSegments' => ['SE'],
								'children' => [
									[
										'segments' => ['BHT'],
										'childrenMethod' => 'getDetailSourceConfig'
									]
								]
							]
						]
					]
				]
			]
		];
	}

	protected function getDetailSourceConfig($segmentDefinition, $line)
	{
		if ($segmentDefinition === 'HL') {
			if (isset($line[3])) {
				$source = $line[3];
				if ($source == '20') {
					return [
						'segments' => ['HL'],
						'class' => '',
						'children' => [
							[
								'segments' => ['NM1']
							], [
								'segments' => ['TRN', 'DTP', 'DTP']
							]
						]
					];
				}
				if ($source == '21') {
					return [
						'segments' => ['HL'],
						'class' => '',
						'children' => [
							[
								'segments' => ['NM1']
							], [
								'segments' => ['TRN', 'STC', 'QTY', 'AMT']
							]
						]
					];
				}
				if ($source == '19') {
					return [
						'segments' => ['HL'],
						'class' => '',
						'children' => [
							[
								'segments' => ['NM1']
							], [
								'segments' => ['TRN', 'STC', 'REF', 'QTY', 'AMT']
							]
						]
					];
				}
				if ($source == 'PT') {
					return [
						'segments' => ['HL'],
						'class' => '\OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails',
						'children' => [
							[
								'segments' => ['NM1']
							], [
								'segments' => ['TRN', 'STC', 'REF', 'DTP'],
								'class' => '\OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ClaimStatusTracking',
							    'children' => [
								    [
									    'segments' => ['SVC', 'STC', 'REF', 'DTP'],
								        'class' => 'OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails\ServiceStatusTracking'
								    ]
							    ]
							]
						]
					];
				}
			}
		}

		return null;
	}
}