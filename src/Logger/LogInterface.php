<?php

declare(strict_types=1);

namespace Shays\Logger;

interface LogInterface
{
	/**
	 * Gets the log level, which would be used by the log handlers to
	 * determine whether they should handle the given log entry
	 *
	 * @return int
	 */
	public function getLevel(): int;

	/**
	 * Transform the log data to an array, which would be used by the streamers to easily store the data.
	 * This can provide flexibility to reduce the log size and exclude log entries included big data.
	 *
	 * @return mixed[]
	 */
	public function toArray(): array;
}
