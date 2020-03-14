<?php

namespace OpakeAdmin\Helper\Printing;

use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart;
use OpakeAdmin\Helper\Printing\Document\CompileDocument;
use OpakeAdmin\Helper\Printing\Document\PDFCompileDocument;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartFileWithHeaderOptimizer;
use OpakeAdmin\Helper\Printing\Utils\Chart\ChartOwnTextOptimizer;
use OpakeAdmin\Helper\Printing\Utils\Chart\DocumentOptimizer;

class PrintPool
{

	/**
	 * @var int
	 */
	protected $maximumProcessesCount = 256;

	/**
	 * @var int
	 */
	protected $multipleFormsMaximumCount = 6;

	/**
	 * @return int
	 */
	public function getMaximumProcessesCount()
	{
		return $this->maximumProcessesCount;
	}

	/**
	 * @param int $maximumProcessesCount
	 */
	public function setMaximumProcessesCount($maximumProcessesCount)
	{
		$this->maximumProcessesCount = $maximumProcessesCount;
	}

	/**
	 * @return int
	 */
	public function getMultipleFormsMaximumCount()
	{
		return $this->multipleFormsMaximumCount;
	}

	/**
	 * @param int $multipleFormsMaximumCount
	 */
	public function setMultipleFormsMaximumCount($multipleFormsMaximumCount)
	{
		$this->multipleFormsMaximumCount = $multipleFormsMaximumCount;
	}

	/**
	 * @param Document[] $documents
	 * @return Document[]
	 * @throws \Exception
	 * @throws \rikanishu\multiprocess\exception\NonExecutedException
	 */
	public function runCompile($documents)
	{

		$documents = $this->tryToOptimizeDocuments($documents);

		$asyncCommands = [];
		$asyncDocuments = [];

		/** @var Document $document */
		foreach ($documents as $document) {
			if ($document instanceof CompileDocument) {
				if ($document instanceof PDFCompileDocument) {
					$document->generateFiles();
					$asyncCommands[] = $document->getPDFCompileCommand();
					$asyncDocuments[] = $document;
				} else {
					$document->runCompile();
				}
			}
		}

		if ($asyncCommands) {

			$commandsChunks = array_chunk($asyncCommands, $this->maximumProcessesCount);
			$documentsChunks = array_chunk($asyncDocuments, $this->maximumProcessesCount);

			foreach ($commandsChunks as $chunkIndex => $commands) {

				$pool = new \rikanishu\multiprocess\Pool($commands, [
					\rikanishu\multiprocess\Pool::OPTION_DEBUG => false,
					\rikanishu\multiprocess\Pool::OPTION_POLL_TIMEOUT => 10,
					\rikanishu\multiprocess\Pool::OPTION_EXECUTION_TIMEOUT => 120
				]);
				$pool->run();

				foreach ($pool->getCommands() as $index => $command) {
					$document = $documentsChunks[$chunkIndex][$index];
					try {
						$result = $command->getExecutionResult();
						if ($stderr = $result->getStderr()) {
							throw new \Exception('PDF execution error: ' . $stderr);
						}

						$document->loadContent();
						$document->cleanup();

					} catch (\Exception $e) {
						$document->cleanup();
						throw $e;
					}
				}
			}
		}

		return $documents;
	}

	protected function tryToOptimizeDocuments($documents)
	{
		$optimizer = new ChartOwnTextOptimizer($documents);
		$optimizer->setMultipleFormsMaximumCount($this->multipleFormsMaximumCount);
		$documents =  $optimizer->tryToOptimize();

		$optimizer = new ChartFileWithHeaderOptimizer($documents);
		$documents = $optimizer->tryToOptimize();

		return $documents;
	}


}