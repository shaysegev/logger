<?php

declare(strict_types=1);

namespace Shays\Logger\Stream;

use Shays\Logger\LogInterface;

interface StreamInterface
{
	/**
	 * Save a log entry
	 *
	 * @param string $log
	 */
	public function write(string $log): void;

	/**
	 * Should the log entry should be saved to the streamer
	 *
	 * @param LogInterface $log
	 * @return bool
	 */
	public function shouldWrite(LogInterface $log): bool;
}
