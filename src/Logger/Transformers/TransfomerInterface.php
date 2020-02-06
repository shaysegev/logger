<?php

declare(strict_types=1);

namespace Shays\Logger\Transformers;

interface TransfomerInterface
{
	/**
	 * Transform a given data for the required data handler
	 *
	 * @param mixed $data
	 * @return mixed
	 */
	public static function transform($data);
}
