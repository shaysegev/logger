<?php

declare(strict_types=1);

namespace Shays\Logger\Serializer;

use Shays\Logger\LogInterface;

interface SerializerInterface
{
	/**
	 * Serialize a log entry with a defined format
	 *
	 * @param LogInterface $log
	 * @return mixed
	 */
	public function serialize(LogInterface $log);
}
