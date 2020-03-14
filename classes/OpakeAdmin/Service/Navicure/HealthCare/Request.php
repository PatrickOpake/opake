<?php

namespace OpakeAdmin\Service\Navicure\HealthCare;

use OpakeAdmin\Service\Navicure\HealthCare\Exception\ValidationException;
use SoapClient;
use SoapHeader;

class Request
{
	const TIMEOUT = 60;
	const TRANSACTION_TYPE = 'E';
	const SUBMIT_ANSI_VERSION = 270;
	const RESULT_ANSI_VERSION = '4010A1';
	const PROCESSING_OPTION = 'R';


	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var String
	 */
	protected $payload;

	/**
	 * @var []
	 */
	protected $submitParams;

	/**
	 * @var Object
	 */
	protected $response;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param \Opake\Application $pixie
	 * @param String $payload
	 */
	public function __construct($pixie, $payload, \Opake\Model\Cases\Item $case)
	{
		$this->response = null;
		$this->pixie = $pixie;
		$this->case = $case;
		$this->submitParams = [
			'timeout' => self::TIMEOUT,
			'transactionType' => self::TRANSACTION_TYPE,
			'submittedAnsiVersion' => self::SUBMIT_ANSI_VERSION,
			'resultAnsiVersion' => self::RESULT_ANSI_VERSION,
			'submitterSubmissionId' => '',
			'processingOption' => self::PROCESSING_OPTION,
			'payload' => $payload
		];
	}

	/**
	 * @return null|Object
	 */
	public function getResponse()
	{
		$this->fetchResponse();
		return $this->response;
	}

	/**
	 * @throws \Exception
	 */
	protected function fetchResponse()
	{
		$caseSite = $this->case->location->site;

		$options = [
			'trace'=>true,
			'exceptions'=>true,
		];

		if(empty($caseSite->navicure_submitter_id) || empty($caseSite->navicure_submitter_password)) {
			throw new ValidationException('Navicure Submitter ID or Submitter Pass is not set');
		}

		$auth = [
			'originatingIdentifier' => $caseSite->navicure_submitter_id,
			'submitterIdentifier' => $caseSite->navicure_submitter_id,
			'submitterPassword' => $caseSite->navicure_submitter_password,
			'submissionId'=> '1',
		];

		try {
			$header = new SoapHeader('http://www.navicure.com/2009/11/NavicureSubmissionService','SecurityHeaderElement', $auth, false);
			$soap = new SoapClient($this->pixie->config->get('app.navicure_api.soap.wsdl_url'), $options);
			$soap->__setSoapHeaders($header);
			$this->response = $soap->submitAnsiSingle($this->submitParams);
		} catch(\SoapFault $e) {
			throw new \HttpException($e->getMessage());
		} catch(\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

}