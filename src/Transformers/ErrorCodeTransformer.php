<?php

declare(strict_types=1);

namespace Shays\Transformers;

use Shays\Logger\LogLevel;

class ErrorCodeTransformer implements TransfomerInterface
{
	/**
	 * @var mixed[] Native PHP errors mapped to log levels
	 */
	const PHP_ERRORS = [
		E_ERROR => LogLevel::ERROR,
		E_WARNING => LogLevel::WARNING,
		E_PARSE => LogLevel::ERROR,
		E_NOTICE => LogLevel::NOTICE,
		E_CORE_ERROR => LogLevel::ERROR,
		E_CORE_WARNING => LogLevel::WARNING,
		E_COMPILE_ERROR => LogLevel::ERROR,
		E_COMPILE_WARNING => LogLevel::WARNING,
		E_USER_ERROR => LogLevel::ERROR,
		E_USER_WARNING => LogLevel::WARNING,
		E_USER_NOTICE => LogLevel::NOTICE,
		E_DEPRECATED => LogLevel::NOTICE,
		E_USER_DEPRECATED => LogLevel::NOTICE,
	];

	/**
	 * Transform PHP error codes to Logger log levels
	 *
	 * {@inheritDoc}
	 */
	public static function transform($code)
	{
		return self::PHP_ERRORS[$code];
	}
}