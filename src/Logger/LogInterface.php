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
	 * Serialize the log data, which would be used by the streamers to store the serialized data
	 * this provides flexibility to reduce to log size and exclude potentially log entries included big data
	 *
	 * @return mixed[]
	 */
	public function serialize(): array;
}
