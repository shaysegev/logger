<?php

declare(strict_types=1);

namespace Shays\Contracts;

use Psr\Log\LoggerInterface;

interface LoggerContract extends LoggerInterface
{
	/**
	 * Create and handle a log entry with the given data, which
	 * would be then passed to all defined log handlers/steamers
	 *
	 * @param int $level The log entry level (@see LogLevel)
	 * @param string $message The log message
	 * @param array $context Additional log related information
	 */
	public function createLog(int $level, string $message, array $context = []): void;
}
