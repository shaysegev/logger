<?php

declare(strict_types=1);

namespace Shays\Logger\Handlers;

use Shays\Logger\Contracts\LoggerContract;
use Shays\Logger\Transformers\ErrorCodeTransformer;

class ErrorHandler
{
	/** @var LoggerContract */
	private $logger;

	public function __construct(LoggerContract $logger)
	{
		$this->logger = $logger;
		set_error_handler([$this, 'handleError']);
	}

	/**
	 * Handles native PHP errors, warnings and notices
	 *
	 * @see ErrorCodeTransformer
	 * @param int $errorCode
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param string $errorLine
	 */
	public function handleError(
		int $errorCode,
		string $errorMessage,
		string $errorFile,
		string $errorLine
	): void {
		$logLevel = ErrorCodeTransformer::transform($errorCode);

		$this->logger->log(
			$logLevel,
			$errorMessage,
			[
				'file' => $errorFile,
				'line' => $errorLine,
			]
		);
	}
}
