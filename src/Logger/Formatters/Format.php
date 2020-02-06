<?php

declare(strict_types=1);

namespace Shays\Logger\Formatters;

class Format
{
	/** @var string JSON format */
	const JSON = 'json';

	/**
	 * Get currently supported log formats (handled by the serializers)
	 *
	 * @return string[]
	 */
	public static function getSupportedFormats(): array
	{
		return [
			self::JSON,
		];
	}
}
