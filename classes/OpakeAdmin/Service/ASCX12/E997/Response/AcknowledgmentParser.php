<?php

namespace OpakeAdmin\Service\ASCX12\E997\Response;

use OpakeAdmin\Service\ASCX12\AbstractParser;

class AcknowledgmentParser extends AbstractParser
{

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
						'class' => '\OpakeAdmin\Service\ASCX12\General\Response\GSHeader',
						'endSegments' => ['GE'],
						'children' => [
							[
								'segments' => ['ST'],
								'endSegments' => ['SE'],
								'children' => [
									[
										'segments' => ['AK1'],
										'endSegments' => ['AK9'],
										'children' => [
											[
												'segments' => ['AK2'],
												'class' => '\OpakeAdmin\Service\ASCX12\E997\Response\Headers\AkTransactionSetHeader',
												'endSegments' => ['AK5'],
												'children' => [
													[
														'segments' => ['AK3', 'CTX'],
														'class' => '\OpakeAdmin\Service\ASCX12\E997\Response\Segments\AkErrorId',
														'children' => [
															[
																'segments' => ['AK4', 'CTX'],
																'class' => '\OpakeAdmin\Service\ASCX12\E997\Response\Segments\AkImplNote',
															]
														]
													]
												]
											]
										]
									]
								]
							]
						]
					],
					[
						'segments' => ['TA1']
					]
				]
			]
		];
	}


}