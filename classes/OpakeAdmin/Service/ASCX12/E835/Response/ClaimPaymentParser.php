<?php

namespace OpakeAdmin\Service\ASCX12\E835\Response;

use OpakeAdmin\Service\ASCX12\AbstractParser;
use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class ClaimPaymentParser extends AbstractParser
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
										'segments' => ['BPR', 'TRN', 'CUR', 'REF', 'DTM'],
										'class' => '\OpakeAdmin\Service\ASCX12\E835\Response\Segments\FinancialInformation',
									],
									[
										'segments' => ['N1', 'N3', 'N4', 'REF', 'PER'],
									],
									[
										'segments' => ['N1', 'N3', 'N4', 'REF', 'RDM'],
									],
									[
										'segments' => ['LX', 'TS3', 'TS2'],
										'class' => '\OpakeAdmin\Service\ASCX12\E835\Response\Segments\DetailSummary',
										'children' => [
											[
												'segments' => ['CLP', 'CAS', 'NM1', 'MIA', 'MOA', 'REF', 'DTM', 'PER', 'AMT', 'QTY'],
												'class' => '\OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation',
												'children' => [
													[

														'segments' => ['SVC', 'DTM', 'CAS', 'REF', 'AMT', 'QTY', 'LQ'],
														'class' => '\OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation\ServiceInformation',
													]
												]
											]
										]
									],
									[
										'segments' => ['PLB']
									]
								]
							]
						]
					]
				]
			]
		];
	}
}