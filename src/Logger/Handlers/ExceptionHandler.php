<?php

declare(strict_types=1);

namespace Shays\Logger\Handlers;

use Shays\Logger\Contracts\LoggerContract;
use Shays\Logger\LogLevel;
use Throwable;

class ExceptionHandler
{
	/** @var LoggerContract */
	private $logger;

	public function __construct(LoggerContract $logger)
	{
		$this->logger = $logger;
		set_exception_handler([$this, 'handleException']);
	}

	/**
	 * Handles an uncaught exception
	 *
	 * @param Throwable $e
	 */
	public function handleException(Throwable $e): void
	{
		$this->logger->log(
			LogLevel::ERROR,
			'Uncaught exception: ' . $e->getMessage(),
			[
				'exception' => $e,
			]
		);
	}
}
