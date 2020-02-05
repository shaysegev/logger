<?php

declare(strict_types=1);

namespace Shays\Handlers;

use Shays\Logger\LogInterface;

interface LogHandlerInterface
{
	/**
	 * Handles a custom log entry
	 *
	 * @param LogInterface $log
	 */
	public function handle(LogInterface $log): void;

	/**
	 * Should the log entry be handler by the handler
	 *
	 * @param LogInterface $log
	 * @return bool
	 */
	public function shouldHandle(LogInterface $log): bool;
}
